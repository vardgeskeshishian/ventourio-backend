<?php

namespace App\Services\Web\Hotel;

use App\DTO\CreateTransactionDTO;
use App\DTO\LeadPerson;
use App\DTO\ValuateBookingDTO;
use App\Enums\BookingStatus;
use App\Enums\Provider;
use App\Events\BookingCancelRequested;
use App\Events\BookingCreated;
use App\Exceptions\BusinessException;
use App\Helpers\BookingSearchCodeHandler;
use App\Helpers\CurrencyConverter;
use App\Helpers\UserHelper;
use App\Models\Booking;
use App\Models\Currency;
use App\Models\PaymentWay;
use App\Models\User;
use App\Services\Web\CertificateService;
use App\Services\Web\District\QueryHelper;
use App\Services\Web\TransactionService;
use App\Services\Web\WebService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class BookService extends WebService
{
    public function index(array $data, User $user): array
    {
        $bookings = $user->bookings()
            ->select([
                'id',
                'status',
                'arrival_date',
                'departure_date',
                'hotel_id',
                'user_id'
            ])
            ->with([
                'hotel' => function (Builder $query) {
                    $query
                        ->select([
                            'id',
                            'district_id',
                            'title_l->' . $this->locale . ' as title',
                        ])
                        ->with([
                            'district' => QueryHelper::relationForBreadcrumbs($this->locale),
                            'media'
                        ]);
                }
            ])
            ->get();

        $result = [
            'future' => [],
            'been' => [],
            'cancelled' => [],
        ];

        foreach ($bookings as $booking) {

            if (in_array($booking->status, [BookingStatus::CANCELLED, BookingStatus::CANCELLED_WITH_FEES, BookingStatus::REJECTED])) {
                $groupKey = 'cancelled';
            } elseif ($booking->arrival_date > now()) {
                $groupKey = 'future';
            } else {
                $groupKey = 'been';
            }

            $booking->hotel->append('breadcrumbs', 'location')
                ->setAttribute('image', $booking->hotel->getFirstMedia()->getFullUrl())
                ->makeHidden('district', 'media', 'page');

            $booking->arrival_date = Carbon::create($booking->arrival_date)->format('d.m.Y');
            $booking->departure_date = Carbon::create($booking->departure_date)->format('d.m.Y');

            $result[$groupKey][] = $booking;
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    public function book(array $data, User $user): array
    {
        # Форматируем данные
        $this->format($data, $user);

        # Проверяем не изменилось ли состояние (свободные ли комнаты, не поменялась ли цена)
        $this->checkState($data);

        $paymentWayId = PaymentWay::firstOrFail()->id;

        DB::beginTransaction();
        try {

            # Используем сертификат
            if (! empty($data['certificate_code'])) {
                (new CertificateService(false))->use($data['certificate_code'], $user->id);
            }

            # Создаем бронирование в БД
            $booking = $this->createBooking($data);

            # Проверяем достаточно ли баланса на счету у пользователя
            if (! UserHelper::hasEnoughBalance($data['money'], $user, $difference)) {

                $currencyId = Currency::getCached()->where('code', $this->currency)->first()->id;

                # Создаем платеж
                $dto = new CreateTransactionDTO(
                    paymentWayId: $paymentWayId,
                    morphInstance: ['instance_id' => $booking->id, 'instance_type' => $booking->getMorphClass()],
                    userId: $user->id,
                    amount: ceil($difference),
                    currencyId: $currencyId,
                );

                $transaction = (new TransactionService())->create($dto);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' | ' . $e->getMessage());
            throw new BusinessException(__('errors.app.booking.not_created'));
        }

        BookingCreated::dispatch($booking);

        return [
            'id' => $booking->id,
            'provider' => $booking->provider,
            'transaction_id' => $transaction->id ?? null
        ];
    }

    public function cancel(array $data, Booking $booking): void
    {
        $this->checkIfPossibleToCancel($booking, $data);

        $booking->update([
            'status' => BookingStatus::CANCEL_REQUEST,
        ]);

        BookingCancelRequested::dispatch($booking);
    }

    /**
     * @throws Exception
     */
    private function format(array &$data, User $user): void
    {
        $data['user_id'] = $user->id;

        $this->formatDates($data);
        $this->addPriceInMainCurrency($data);
        $this->parseSearchCode($data);
    }

    private function formatDates(array &$data)
    {
        $dates = $data['dates'] ?? null;

        $arrivalDate = Carbon::createFromFormat('d.m.Y', $dates['arrival']);
        $departureDate = Carbon::createFromFormat('d.m.Y', $dates['departure']);

        $data['dates']['arrival'] = $arrivalDate;
        $data['dates']['departure'] = $departureDate;
    }

    /**
     * @throws Exception
     */
    private function addPriceInMainCurrency(array &$data)
    {
        $data['money'] = CurrencyConverter::toMain($data['total_price'], $this->currency);
    }

    private function createBooking(array $data): Booking
    {
        $leadPerson = LeadPerson::create(
            firstName: $data['lead_person']['first_name'],
            lastName: $data['lead_person']['last_name'],
            email: $data['lead_person']['email']
        );

        $booking = Booking::create([
            'hotel_id' => $data['search_code_parsed']['hotel_id'] ?? null,
            'search_code' => $data['search_code'],
            'provider' => $data['provider'],
            'status' => BookingStatus::NEW,
            'user_id' => $data['user_id'],
            'arrival_date' => $data['dates']['arrival'],
            'departure_date' => $data['dates']['departure'],
            'extra' => $data,
            'lead_person' => $leadPerson->value(),
            'price' => $data['money']
        ]);

        if ( ! empty($data['search_code_parsed']['rooms'])) {
            $booking->rooms()->sync($data['search_code_parsed']['rooms']);
        }

        return $booking;
    }

    private function checkState(array $data)
    {
        $valuateDto = new ValuateBookingDTO(
            searchCode: $data['search_code'],
            provider: Provider::from($data['provider']),
            arrivalDate: $data['dates']['arrival'],
            departureDate: $data['dates']['departure'],
            currency: $this->currency,
            amount: (float) $data['total_price']
        );

        (new BookingValuationService())->valuate($valuateDto);
    }

    private function parseSearchCode(array &$data)
    {
        $data['search_code_parsed'] = BookingSearchCodeHandler::parse($data['search_code'], Provider::from($data['provider']));
    }

    private function checkIfPossibleToCancel(Booking $booking, array $data): void
    {
        # Проверяем статус бронирования
        match ($booking->status) {
            BookingStatus::CANCEL_REQUEST, BookingStatus::CANCELLATION_REQUESTED
                => throw new BusinessException(__('errors.app.booking.cancel.already_requested')),

            BookingStatus::CANCELLED, BookingStatus::CANCELLED_WITH_FEES, BookingStatus::REJECTED
                => throw new BusinessException(__('errors.app.booking.cancel.already_cancelled')),

            BookingStatus::VOUCHER_ISSUED, BookingStatus::VOUCHER_REQUESTED
                => throw new BusinessException(__('errors.app.booking.cancel.can_not')),

            default => null,
        };

        # Проверяем тот ли пользователь отменяет
        if ($booking->user_id !== auth()->id()) {
            throw new BusinessException(__('errors.app.booking.cancel.can_not'));
        }
    }
}
