<?php

namespace App\Services\GoGlobal;

use App\DTO\GoGlobal\MakeOffersDTO;
use App\DTO\GoGlobal\SearchHotelsDTO;
use App\Enums\Provider;
use App\Exceptions\GoGlobalApiException;
use App\Helpers\BookingSearchCodeHandler;
use App\Helpers\CurrencyConverter;
use App\Helpers\CurrencyStorage;
use App\Helpers\RoomBasisConverter;
use Carbon\Carbon;
use DOMException;
use Exception;
use Illuminate\Support\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

final class HotelSearchService extends GoGlobalService
{
    public string $operation;
    public int $code;
    private ?float $version;

    public function __construct()
    {
        parent::__construct();

        $requestType = config('goglobal.request_types.hotel_search');

        $this->operation = $requestType['operation'];
        $this->code      = $requestType['code'];
        $this->version   = $requestType['version'];
    }

    /**
     * @param SearchHotelsDTO $dto
     * @param string $searchSession
     * @return array
     * @throws ContainerExceptionInterface
     * @throws DOMException
     * @throws NotFoundExceptionInterface
     * @throws GoGlobalApiException
     */
    public function search(SearchHotelsDTO $dto, string $searchSession): array
    {
        $formattedData = $this->formatData($dto);

        $resultData = $this->sendRequest($formattedData);

        return $this->formatBack(
            $resultData,
            $searchSession,
            $dto->getDepartureDate()->diffInDays($dto->getArrivalDate())
        );
    }

    private function formatData(SearchHotelsDTO $dto): array
    {
        $result = [
            'SortOrder' => $dto->getSortOrder()->value,
            'MaximumWaitTime' => 15,
            'MaxResponses' => 1000,
            'Nationality' => $dto->getNationality(),
            'RoomFacilities' => true,
            'HotelFacilities' => true,
        ];

        $this->addHotels($result, $dto);
        $this->addCityCode($result, $dto);

        $this->addAttributes($result, $dto);
        $this->addPrices($result, $dto);
        $this->addRooms($result, $dto);
        $this->addDates($result, $dto);
        $this->addStars($result, $dto);
        $this->addRoomBasis($result, $dto);

        return $result;
    }

    private function addAttributes(array &$result, SearchHotelsDTO $dto)
    {
        $result['_attributes'] = [
            'Version' => $this->version,
            'ResponseFormat' => 'JSON',
            'IncludeGeo' => 'true',
            'Currency' => $dto->getCurrency()
        ];
    }

    private function addPrices(array &$result, SearchHotelsDTO $dto)
    {
        if (empty($dto->getPrices())) {
            return;
        }

        $prices = $dto->getPrices();

        $minPrice = $prices['min'] ?? null;
        $maxPrice = $prices['max'] ?? null;

        $result['FilterPriceMin'] = $minPrice;
        $result['FilterPriceMax'] = $maxPrice;
    }

    private function addRooms(array &$result, SearchHotelsDTO $dto)
    {
        $rooms = [];
        foreach ($dto->getRooms() as $room) {
            $rooms[] = [
                '_attributes' => [
                    'Adults' => $room['adults'],
                    'RoomCount' => 1,
                    'ChildCount' => $rooms['children'] ?? 0
                ]
            ];
        }

        $result['Rooms'] = ['Room' => $rooms];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    private function formatBack(array $data, string $searchSession, int $nights): array
    {
        $hotels = $data['Hotels'] ?? null;
        if (empty($hotels)) {
            throw new Exception(__('errors.api.improper_format'));
        }

        $offers = [];

        $currency = app(CurrencyStorage::class)->get();

        foreach ($hotels as &$hotel) {

            $offersByHotel = $hotel['Offers'] ?? null;
            if (empty($offersByHotel)) {
                continue;
            }

            $dto = new MakeOffersDTO(
                offersByHotel: $offersByHotel,
                hotel: $hotel,
                searchSession: $searchSession,
                currency: $currency,
                nights: $nights
            );

            $this->addOffers($offers, $dto, $minPrice);

            $hotel = [
                'title' => $hotel['HotelName'],
                'external_code' => $hotel['HotelCode'],
                'country' => [
                    'external_code' => $hotel['CountryId']
                ],
                'city' => [
                    'external_code' => $hotel['CityId']
                ],
                'location' => $hotel['Location'],
                'image' => $hotel['HotelImage'],
                'min_price' => $minPrice
            ];

            unset($minPrice);
        }

        return [
            'hotels' => $hotels,
            'offers' => $offers
        ];
    }

    private function addDates(array &$result, SearchHotelsDTO $dto)
    {
        $arrivalDate = $dto->getArrivalDate();
        $departureDate = $dto->getDepartureDate();

        $nights = $departureDate->diffInDays($arrivalDate);

        $result['ArrivalDate'] = $arrivalDate->format('Y-m-d');
        $result['Nights'] = $nights;
    }

    /**
     * @throws Exception
     */
    private function addOffers(array &$offers, MakeOffersDTO $dto, float &$minPrice = null)
    {
        $provider = Provider::GOGLOBAL->value;

        foreach ($dto->getOffersByHotel() as $offer) {

            $roomBasis = RoomBasisConverter::convertFromGoGlobal($offer['RoomBasis'],Provider::DB);
            $totalPrice = CurrencyConverter::convert($offer['TotalPrice'], $offer['Currency'], $dto->getCurrency());

            if (empty($minPrice) || $totalPrice < $minPrice) {
                $minPrice = $totalPrice;
            }

            $offers[] = [
                'provider'          => $provider,
                'hotel' => [
                    'external_code' => $dto->getHotel()['HotelCode'],
                ],
                'search_code'       => BookingSearchCodeHandler::createGoGlobal(['search_code' => $offer['HotelSearchCode'], 'search_session' => $dto->getSearchSession()]),
                'cancel_deadline'   => Carbon::createFromFormat('d/M/Y', $offer['CxlDeadLine'])->format('d.m.Y'),
                'rooms'             => $offer['Rooms'],
                'room_basis'        => Str::lower($roomBasis->name ?? null),
                'room_basis_value'  => Str::lower($roomBasis->value ?? null),
                'is_available'      => (bool) $offer['Availability'],
                'total_price'       => $totalPrice,
                'price'             => $totalPrice / $dto->getNights(),
                'nights'            => $dto->getNights(),
                'stars'             => (int) $offer['Category'],
                'remark'            => $offer['Remark'],
                'is_preferred'      => (bool) $offer['Preferred']
            ];
        }
    }

    private function addHotels(array &$result, SearchHotelsDTO $dto)
    {
        if (empty($dto->getHotels())) {
            return;
        }

        $result['Hotels'] = [
            'HotelId' => $dto->getHotels()
        ];
    }

    private function addCityCode(array &$result, SearchHotelsDTO $dto)
    {
        if (empty($dto->getCityCode())) {
            return;
        }

        $result['CityCode'] = $dto->getCityCode();
    }

    private function addStars(array &$result, SearchHotelsDTO $dto)
    {
        if (empty($minStar = $dto->getMinStar())) {
            return;
        }

        if (empty($maxStar = $dto->getMaxStar())) {
            $stars = $minStar->value();
        } else {
            $stars = [
                '_attributes' => [
                    'MinStar' => $minStar->value(),
                    'MaxStar' => $maxStar->value()
                ]
            ];
        }

        $result['Stars'] = $stars;
    }

    private function addRoomBasis(array &$result, SearchHotelsDTO $dto)
    {
        if (empty($dto->getRoomBasises())) {
            return;
        }

        $result['FilterRoomBasises'] = [
            'FilterRoomBasis' => $dto->getRoomBasises()
        ];
    }
}
