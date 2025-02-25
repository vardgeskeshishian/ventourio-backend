<?php

namespace App\Services\Admin;

use App\DTO\GoGlobal\BookHotelDTO;
use App\Enums\BookingStatus;
use App\Enums\ExternalPaymentMethodType;
use App\Enums\Provider;
use App\Exceptions\BusinessException;
use App\Helpers\UserHelper;
use App\Http\Resources\Admin\BookingResource;
use App\Models\Booking;
use App\Models\CreditCard;
use App\Models\Transaction;
use App\Services\GoGlobal\BookingCancellationService;
use App\Services\GoGlobal\BookingCreationService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class BookingService
{
    public function index(array $data): array
    {
        $bookings = Booking::query();

        $page = $data['page'] ?? 1;
        $take = $data['count'] ?? 8;
        $count = $bookings->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $bookings = $bookings->take($take)->skip($skip);
        } else {
            $bookings = $bookings->take($take)->skip(0);
        }

        return [
            'success' => true,
            'data' => BookingResource::collection($bookings->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }

    public function update(array $data, Booking &$booking): void
    {
        DB::beginTransaction();
        try {

            $newStatus = BookingStatus::from($data['status']);

            if (! $booking->is_paid && $newStatus === BookingStatus::CONFIRMED) {

                # temporary functionality. Remove after normal payment will be
                $this->completeTransactions($booking);

                # Проверяем достаточно ли баланса на счету у пользователя
                if (! UserHelper::hasEnoughBalance($booking->price, $booking->user_id)) {
                    throw new Exception('User doesnt have enough balance');
                }

                UserHelper::decreaseBalance($booking);

                $booking->paid_at = now();
            }

            $booking->fill([
                'status' => $newStatus,
                'cancel_deadline' => $data['cancel_deadline'] ?? $booking->cancel_deadline,
                'external_code' => $data['external_code'] ?? $booking->external_code,
            ]);

            $booking->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new BusinessException($e->getMessage());
        }
    }

    public function storeExternal(array $data): Booking
    {
        $booking = Booking::findOrFail($data['booking_id']);

        if ( ! empty($booking->external_code)) {
            throw new BusinessException(__('errors.app.booking.external.already_created'));
        }

        $result = match ($booking->provider) {
            Provider::GOGLOBAL => $this->createByGoGlobal($data, $booking),
            Provider::DB => throw new BusinessException(__('errors.app.booking.external.not_external')),
        };

        $booking->externalPaymentMethod()->create([
            'type' => $data['type'],
            'credit_card_id' => $data['credit_card_id']
        ]);

        $this->update($result, $booking);

        return $booking;
    }

    public function cancelExternal(array $data): Booking
    {
        $booking = Booking::findOrFail($data['booking_id']);

        if (empty($booking->external_code)) {
            throw new BusinessException(__('errors.app.booking.external.not_created'));
        }

        $result = match ($booking->provider) {
            Provider::GOGLOBAL => $this->cancelByGoGlobal($booking),
            Provider::DB => throw new BusinessException(__('errors.app.booking.external.not_external')),
        };

        $this->update($result, $booking);

        return $booking;
    }

    private function createByGoGlobal(array $data, Booking $booking): array
    {
        try {

            if ((int) $data['type'] !== ExternalPaymentMethodType::CREDIT_CARD->value) {
                throw new Exception('not implemented');
            }

            $creditCard = CreditCard::findOrFail($data['credit_card_id']);

            $dto = new BookHotelDTO(
                searchCode: $booking->search_code,
                arrivalDate: Carbon::create($booking->arrival_date),
                departureDate: Carbon::create($booking->departure_date),
                rooms: $booking->extra['rooms'],
                paymentInfo: [
                    'card_holder_name' => $creditCard->holder_name,
                    'card_name' => Str::ucfirst($creditCard->type->value), // Master, Visa, Amex, Diners, etc.
                    'card_number' => Crypt::decryptString($creditCard->number),
                    'exp_month' => Crypt::decryptString($creditCard->exp_month),
                    'exp_year' => Crypt::decryptString($creditCard->exp_year),
                    'security_code' => Crypt::decryptString($creditCard->svc),
                    'email' => 'test@gmail.com' // todo add admin email
                ]
            );

            $result = (new BookingCreationService())->book($dto);

        } catch (Exception $e) {
            Log::error(__METHOD__ . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new BusinessException($e->getMessage());
        }

        return $result;
    }

    private function cancelByGoGlobal(Booking $booking): array
    {
        match ($booking->status) {
            BookingStatus::CANCELLATION_REQUESTED
                => throw new BusinessException(__('errors.app.booking.cancel.already_requested')),

            BookingStatus::CANCELLED, BookingStatus::CANCELLED_WITH_FEES, BookingStatus::REJECTED
                => throw new BusinessException(__('errors.app.booking.cancel.already_cancelled')),

            default => null,
        };

        try {

            $result = (new BookingCancellationService())->cancel(['external_code' => $booking->external_code]);

        } catch (Exception $e) {
            Log::error(__METHOD__ . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new BusinessException($e->getMessage());
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    private function completeTransactions(Booking $booking): void
    {
        $transactions = $booking->transactions()->get();
        if ($transactions->isEmpty()) {
            return;
        }

        $service = new TransactionService();

        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            $service->complete($transaction);
        }
    }
}
