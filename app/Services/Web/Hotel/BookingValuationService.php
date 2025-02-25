<?php

namespace App\Services\Web\Hotel;

use App\DTO\GoGlobal\ValuateGoGlobalBookingDTO;
use App\DTO\ValuateBookingDTO;
use App\Enums\Provider;
use App\Exceptions\BookingPriceHasChangedException;
use App\Exceptions\RoomAlreadyBookedException;
use App\Helpers\BookingSearchCodeHandler;
use App\Helpers\CurrencyConverter;
use App\Models\Currency;
use App\Models\Room;
use App\Services\GoGlobal\BookingValuationService as GoGlobalBookingValuationService;
use App\Services\Web\Filter\RoomFilterService;
use App\Services\Web\WebService;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ArrayShape;

final class BookingValuationService extends WebService
{
    /**
     * @throws Exception
     */
    public function valuate(ValuateBookingDTO $dto): void
    {
        // todo handle cancellation_date possible change
        $result = match ($dto->getProvider()) {
            Provider::DB => $this->valuateDB($dto),
            Provider::GOGLOBAL => $this->valuateGoGlobal($dto),
        };

        if(abs($dto->getAmount()) !== abs($result['price'])) {
            throw new BookingPriceHasChangedException($result['price']);
        }
    }

    /**
     * @throws Exception
     */
    #[ArrayShape(['price' => "float"])]
    private function valuateDB(ValuateBookingDTO $dto): array
    {
        $days = $dto->getArrivalDate()->diffInDays($dto->getDepartureDate());

        $searchCodeData = BookingSearchCodeHandler::parse($dto->getSearchCode(), Provider::DB);

        $roomIds = $searchCodeData['rooms'];

        $builder = Room::query();

        (new RoomFilterService([
            'ids' => $roomIds,
            'dates' => [
                'arrival' => $dto->getArrivalDate(),
                'departure' => $dto->getDepartureDate()
            ]
        ]))->filter($builder);

        $rooms = $builder->withSum('roomBase as price', 'price')->get(['id', 'room_base_id']);

        if (count($roomIds) !== $rooms->count()) {
            throw new RoomAlreadyBookedException();
        }

        $prices = $rooms->pluck('price');

        $totalPrice = $prices->sum(function ($value) use ($days) {
            return $value;
//            return $value * $days;
        });

        return [
            'price' => CurrencyConverter::convert($totalPrice, Currency::getMain(), $dto->getCurrency()),
        ];
    }

    #[ArrayShape(['price' => "float"])]
    private function valuateGoGlobal(ValuateBookingDTO $dto): array
    {
        $searchCodeData = BookingSearchCodeHandler::parse($dto->getSearchCode(), Provider::GOGLOBAL);

        $valuateDto = new ValuateGoGlobalBookingDTO(
            searchCode: $searchCodeData['external_code'],
            arrivalDate: $dto->getArrivalDate()
        );

        try {
            $result = (new GoGlobalBookingValuationService())->valuate($valuateDto);
        } catch (Exception $e) {
            Log::info(__METHOD__ . ' ' . $e->getMessage());
            throw new RoomAlreadyBookedException();
        }

        return [
            'price' => CurrencyConverter::convert($result['total_price'], $result['currency'], $dto->getCurrency()),
        ];
    }
}
