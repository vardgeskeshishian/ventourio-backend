<?php

namespace App\Services\Web\Hotel;

use App\DTO\GoGlobal\SearchHotelsDTO;
use App\DTO\MakeOffersDTO;
use App\Enums\RatingCategory;
use App\Exceptions\BusinessException;
use App\Helpers\CurrencyConverter;
use App\Models\Facility;
use App\Models\Hotel;
use App\Services\GoGlobal\HotelSearchService as GoGlobalHotelSearchService;
use App\Services\Web\District\QueryHelper as DistrictQueryHelper;
use App\Services\Web\Filter\RoomBaseFilterService;
use App\Services\Web\Filter\RoomFilterService;
use App\Services\Web\PageService;
use App\Services\Web\UserHelper;
use App\Services\Web\WebService;
use Carbon\Carbon;
use DOMException;
use Exception;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class GetHotelService extends WebService
{
    use HasSearchSession;

    /**
     * @throws Exception
     */
    public function get(array $data): array
    {
        $this->format($data);

        $hotelQuery = $this->getBuilder($data);

        /** @var Hotel $hotel */
        $hotel = $hotelQuery->first();
        if ( ! $hotel) {
            throw new BusinessException(__('errors.app.hotel.not_found'));
        }

        $this->addOffers($hotel, $data);
        $hotel->append('breadcrumbs');
        $hotel->setAttribute('house_rules', $this->buildHouseRules($hotel->house_rules));

        PageService::incrementViewCount($hotel->page->id);

        $result = $this->formatBack($hotel);

        $this->addSimilarHotels($result, $data);

        return $result;
    }

    private function getBuilder(array $data): Builder
    {
        $query = Hotel::query();

        $query->whereHas('page', function ($query) use ($data) {
            $query->where('slug', $data['slug']);
        });

        $query->select([
            'id', 'external_code', 'title_l->' . $this->locale . ' as title',
            'description_l->' . $this->locale . ' as description',
            'address', 'phone', 'fax', 'stars', 'geo', 'district_id', 'house_rules'
        ]);

        $query->with([
            'district' => DistrictQueryHelper::relationForBreadcrumbs($this->locale),
            'page' => function ($query) {
                $query->select([
                    'pages.id',
                    'pages.slug',
                    'pages.type',
                    'pages.instance_id',
                    'pages.instance_type',
                    'pages.heading_title_l->' . $this->locale . ' as heading_title',
                    'pages.meta_title_l->' . $this->locale . ' as meta_title',
                    'pages.meta_description_l->' . $this->locale . ' as meta_description',
                    'pages.content_l->' . $this->locale . ' as content',
                    'pages.view_count'
                ]);
            },
            'facilities' => function ($query) {
                $query->select([
                    'facilities.id',
                    'facilities.title_l->' . $this->locale . ' as title',
                    'category_id'
                ])
                    ->with([
                        'media',
                        'category' => function ($query) {
                            $query->select([
                                'facility_categories.id',
                                'facility_categories.title_l->' . $this->locale . ' as title'
                            ]);
                        }
                    ]);
            },
            'roomTypes' => function ($query) use ($data) {

                $query->select([
                    'room_types.id',
                    'room_types.hotel_id',
                    'room_types.title_l->' . $this->locale . ' as title'
                ])
                    ->with([
                        'roomBases' => function ($query) use ($data) {

                            $query->select([
                                'room_bases.id',
                                'room_bases.room_type_id',
                                'room_bases.title_l->' . $this->locale . ' as title',
                                'room_bases.basis',
                                'room_bases.booking_max_term',
                                'room_bases.booking_range',
                                'room_bases.cancel_range',
                                'room_bases.refundable',
                                'room_bases.remark_l->' . $this->locale . ' as remark',
                                'room_bases.price',
                                'room_bases.base_price',
                                'room_bases.adults_count',
                                'room_bases.children_count'
                            ]);

                            if (isset($data['dates'])) {
                                (new RoomBaseFilterService($data))->filter($query);
                            }

                            $query->with([
                                'rooms' => function ($query) use ($data) {
                                    $query->select([
                                        'rooms.id',
                                        'rooms.room_base_id',
                                    ]);

                                    if (isset($data['dates'])) {
                                        (new RoomFilterService($data))->filter($query);
                                    }
                                }
                            ]);
                        },
                        'media'
                    ]);
            },
            'media',
            'reviews' => function ($query) {
                $query
                    ->select([
                        'reviews.id',
                        'reviews.booking_id',
                        'reviews.body',
                        'reviews.rating',
                        'reviews.rating_avg',
                        'reviews.created_at',
                    ])
                    ->with([
                        'booking' => function ($query) {
                            $query
                                ->select([
                                    'bookings.id',
                                    'bookings.user_id'
                                ])
                                ->with([
                                    'user' => function ($query) {
                                        $query
                                            ->select([
                                                'users.id',
                                                'users.first_name'
                                            ])
                                            ->with('media');
                                    }
                                ]);
                        }
                    ])
                    ->limit(4);
            }
        ]);

        $query->withCount('reviews');
        $query->withAvg('reviews as rating_avg', 'rating_avg');

        foreach (RatingCategory::cases() as $ratingCategory) {
            $query->withAvg('reviews as rating' . '_' . $ratingCategory->value, 'rating->' . $ratingCategory->value);
        }

        return $query;
    }

    private function format(array &$data)
    {
        # Should be first
        $this->addSearchSession($data);

        $data['rooms_count'] = count($data['rooms'] ?? []);
        $this->formatDates($data);
    }

    private function formatDates(array &$data)
    {
        $dates = $data['dates'] ?? null;

        if (
            empty($arrivalDate = $dates['arrival'] ?? null)
            || empty($departureDate = $dates['departure'] ?? null)
        ) {
            return;
        }

        $arrivalDate = Carbon::createFromFormat('d.m.Y', $arrivalDate);
        $departureDate = Carbon::createFromFormat('d.m.Y', $departureDate);

        $data['dates']['arrival'] = $arrivalDate;
        $data['dates']['departure'] = $departureDate;
        $data['dates']['days'] = $arrivalDate->diffInDays($departureDate);
    }

    private function addRoomsIfEmpty(array &$data)
    {
        if (empty($data['rooms'])) {
            $data['rooms'] = [
                [
                    'adults' => 1,
                    'children' => 0
                ]
            ];
        }
    }

    private function addOffers(Hotel $hotel, array $data)
    {
        $arrivalDate = $data['dates']['arrival'] ?? null;
        $departureDate = $data['dates']['departure'] ?? null;
        $rooms = $data['rooms'] ?? null;

        if (empty($arrivalDate) || empty($departureDate) || empty($rooms)) {
            return;
        }

        $dto = new MakeOffersDTO(
            roomBases: $hotel->roomTypes->pluck('roomBases')->collapse(),
            stars: $hotel->stars,
            hotelExternalCode: $hotel->external_code,
            hotelId: $hotel->id,
            rooms: $rooms,
            arrivalDate: $arrivalDate,
            departureDate: $departureDate,
            searchSession: $data['search_session'] ?? null,
            prices: $data['prices'] ?? null
        );

        try {
            $offers = (new OfferService())->make($dto);
            $offers = collect($offers)->collapse()->toArray();
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' Error while making offers -' . $e->getMessage());
            return null;
        }

        $nationality = $data['nationality'] ?? null;
        if ( ! empty($nationality)) {
            $externalOffers = $this->makeExternalOffers($dto, $nationality);
            $offers = array_merge($offers, $externalOffers);
        }

        $hotel->setAttribute('offers', $offers);
    }

    private function makeExternalOffers(MakeOffersDTO $dto, string $nationality): array
    {
        $searchHotelsDto = new SearchHotelsDTO(
            nationality: $nationality,
            rooms: $dto->getRooms(),
            arrivalDate: $dto->getArrivalDate(),
            departureDate: $dto->getDepartureDate(),
            hotels: [$dto->getHotelExternalCode()],
            currency: Str::upper($this->currency)
        );

        try {
            $result = (new GoGlobalHotelSearchService())->search($searchHotelsDto, $dto->getSearchSession());

            $offers = $result['offers'] ?? null;
            if (empty($offers)) {
                throw new Exception('improper format');
            }

        } catch (DOMException|Exception $e) {
            Log::error(__METHOD__ . ' Error while getting info from goglobal -' . $e->getMessage());
            return [];
        }

        return $offers;
    }

    private function formatBack(Hotel $hotel): array
    {
        $rating = [];
        foreach (RatingCategory::cases() as $ratingCategory) {

            $key = 'rating_' . $ratingCategory->value;

            $rating[$ratingCategory->value] = round($hotel[$key] ?? null, 1);

            unset($hotel[$key]);
        }

        $hotel->setAttribute('rating', $rating);
        $hotel->rating_avg = round($hotel->rating_avg ?? null, 1);

        $hotel->append('location');

        Helper::addImages($hotel);

        $hotel->page->makeHidden(['instance_type', 'instance_id']);

        if ($hotel->roomTypes->isNotEmpty()) {

            foreach ($hotel->roomTypes as $index => $roomType) {

                $roomType->setAttribute('image', $roomType->getFirstMediaUrl());
                $roomType->makeHidden('media');

                if ($roomType->roomBases->isEmpty()) {
                    $hotel->roomTypes->forget($index);
                    continue;
                }

                $roomType->roomBases->each(function ($roomBase) {

                    $roomBase->price = CurrencyConverter::fromMain($roomBase->price, $this->currency);
                    $roomBase->base_price = CurrencyConverter::fromMain($roomBase->base_price, $this->currency);
                    $roomBase->setAttribute('rooms_count', $roomBase->rooms->count());
                    $roomBase->makeHidden('rooms');
                });

                $availableCount = $roomType->roomBases->sum('rooms_count');

                $roomType->setAttribute('message', [
                    'key' => 'rooms_left',
                    'data' => ['count' => $availableCount],
                ]);
            }
        }

        if ($hotel->facilities->isNotEmpty()) {

            $hotel->facilities->each( function ($facility) {

                $facility->setAttribute('image', $facility->getFirstMediaUrl());
                $facility->makeHidden(['media','pivot']);
            });

            $facilityGroups = $this->createFacilityGroups($hotel->facilities);
        }

        $hotel->setAttribute('facility_groups', $facilityGroups ?? []);

        $userFavorites = UserHelper::favoriteHotelIds(auth()->user() ?? auth('sanctum')->user());

        $hotel->setAttribute('is_favorite', $userFavorites->contains($hotel->id));

        if ($hotel->reviews->isNotEmpty()) {
            ReviewService::format($hotel->reviews);
        }

        return $hotel->toArray();
    }

    /**
     * @throws Exception
     */
    private function addSimilarHotels(array &$result, array $data)
    {
        $districtId = $result['district_id'] ?? null;
        $cityId     = $result['city_id'] ?? null;

        if (empty($districtId) && empty($cityId)) {
            throw new Exception('Empty required params');
        }

        if ( ! empty($districtId)) {
            $hotels = HotelsBlockService::byDistrict($districtId, $this->locale, $this->currency);
        } else {
            $hotels = HotelsBlockService::byCity($cityId, $this->locale, $this->currency);
        }

        $result['hotels'] = $hotels;
    }

    private function createFacilityGroups(Collection $facilities): array
    {
        $generalFacility = Facility::where('title_l->en', 'General')
            ->first(['title_l->' . $this->locale . ' as title']);

        $result = [];

        /** @var Facility $facility */
        foreach ($facilities as $facility) {

            $key = $facility['category_id'] ?? 0;

            if ( ! array_key_exists($key, $result)) {
                $result[$key]['title'] = $facility->category?->title ?? $generalFacility->title ?? 'General';
            }

            $result[$key]['facilities'][] = $facility->title;
        }

        ksort($result);

        return array_values($result);
    }

    private function buildHouseRules(array|null $houseRules): SupportCollection|null
    {
        if(empty($houseRules)){
            return null;
        }
        return collect($houseRules)->map(function ($item){
            return [
                'title' => array_key_exists('title', $item) ? $item['title'][$this->locale] : null,
                'body'  => array_key_exists('body', $item) ? $item['body'][$this->locale] : null,
            ];
        });
    }
}
