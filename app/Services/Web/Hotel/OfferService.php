<?php

namespace App\Services\Web\Hotel;

use App\DTO\MakeOffersDTO;
use App\Enums\Provider;
use App\Helpers\BookingSearchCodeHandler;
use App\Helpers\CurrencyConverter;
use App\Models\RoomBase;
use App\Services\Web\WebService;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class OfferService extends WebService
{
    /**
     * @throws Exception
     */
    public function make(MakeOffersDTO $dto): array
    {
        $offers = [];
        $roomBases = $dto->getRoomBases();

        foreach ($roomBases as $roomBase) {

            $offers[] = $this->makeOffersByRoomBase($roomBase, $dto);
        }

        return $offers;
    }

    /**
     * @throws Exception
     */
    private function makeOffersByRoomBase(RoomBase $roomBase, MakeOffersDTO $dto): Collection
    {
        $rooms = $roomBase->rooms;
        $rooms->append('title');

        if ($rooms->count() < $dto->getRoomsCount()) {
            return collect();
        }

        $suitableRoomsByRequested = collect();
        foreach ($dto->getRooms() as $index => $requestedRoom) {

            if ($roomBase->adults_count >= $requestedRoom['adults'] && $roomBase->children_count >= ($requestedRoom['children'] ?? 0)) {
                $suitableRoomsByRequested[$index] = $rooms;
            }
        }

        $offers = $this->matrix($suitableRoomsByRequested->toArray());

        $result = collect();

        $provider = Provider::DB->value;

        $arrivalDate    = clone $dto->getArrivalDate();
        $cancelDeadline = $arrivalDate->subDays($roomBase->cancel_range);
        $nights = $dto->getNights();

        foreach ($offers as $rooms) {

            $searchCode = BookingSearchCodeHandler::create(['hotel_id' => $dto->getHotelId(), 'rooms' => array_column($rooms, 'id'), 'search_session' => $dto->getSearchSession()]);

            $totalPriceInBaseCurrency = round(count($rooms) * $roomBase->price * $dto->getDaysCount(), '2');
            $totalPrice = CurrencyConverter::fromMain($totalPriceInBaseCurrency, $this->currency);

            # Фильтрация по цене
            if ( ! empty($prices = $dto->getPrices())) {

                $minPrice = $prices['min'] ?? 0;
                $maxPrice = $prices['max'] ?? 999999;

                if ($totalPrice <= $minPrice || $totalPrice >= $maxPrice) {
                    continue;
                }
            }

            $result[] = [
                'provider'          => $provider,
                'hotel' => [
                    'external_code' => $dto->getHotelExternalCode()
                ],
                'search_code'       => $searchCode,
                'cancel_deadline'   => $cancelDeadline->format('d.m.Y'),
                'rooms'             => array_column($rooms, 'title'),
                'room_basis'        => Str::lower($roomBase->basis->name),
                'is_available'      => true,
                'total_price'       => $totalPrice,
                'price'             => $totalPrice / $nights,
                'nights'            => $nights,
                'stars'             => $dto->getStars(),
                'remark'            => '',
                'is_preferred'      => true,
            ];
        }

        return $result;
    }

    private function matrix(array $arrays): array
    {
        $results = [[]];

        foreach ($arrays as $index => $array) {
            $append = [];

            foreach ($results as $product) {

                foreach ($array as $item) {
                    $product[$index] = $item;

                    $append[] = $product;
                }
            }

            $results = $append;
        }

        # убираем дублирующиеся значения по типу "1,2,3" == "1,3,2"
        foreach ($results as &$result) {
            asort($result);
            $result = array_values($result);
        }unset($result);
        $results = array_map("unserialize", array_unique(array_map("serialize", $results)));

        # Убираем дублирующиеся комнаты в рамках одного предложения
        foreach ($results as $index => $result) {

            if (count($result) !== count(array_unique(array_column($result, 'id')))) {
                unset($results[$index]);
            }
        }

        return array_values($results);
    }
}
