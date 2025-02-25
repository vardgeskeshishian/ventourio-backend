<?php

namespace App\Services\Web\Hotel;

use App\DTO\GoGlobal\SearchHotelsDTO as GoglobalSearchHotelsDTO;
use App\DTO\SearchHotelsDTO;
use App\Enums\SortOrder;
use App\Exceptions\BusinessException;
use App\Models\City;
use App\Models\District;
use App\Models\Hotel;
use App\Models\Page;
use App\Models\Region;
use App\Models\RoomBase;
use App\Services\GoGlobal\HotelSearchService as GoGlobalHotelSearchService;
use App\Services\Web\District\QueryHelper as DistrictQueryHelper;
use App\Services\Web\Filter\HotelFilterService;
use App\Services\Web\UserHelper;
use App\Services\Web\WebService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class SearchService extends WebService
{
    use HasSearchSession;

    public function search(SearchHotelsDTO $searchHotelsDTO): LengthAwarePaginator
    {
        $data = $searchHotelsDTO->toArray();

        $this->format($data);

        $goglobalData = $this->getGoglobalExternalCodesAndPrices($data);

        if (! empty($goglobalData)) {
            $data['hotel']['external_codes'] = array_keys($goglobalData);
        }

        $hotelsPagination = $this->getBuilder($data)->paginate();

        if ($hotelsPagination->isEmpty()) {
            throw new BusinessException(__('errors.app.hotel.search.not_found'), request()->all());
        }

        $this->formatBack($hotelsPagination, $goglobalData);


        return $hotelsPagination;
    }

    public function count(SearchHotelsDTO $searchHotelsDTO): int
    {
        $data = $searchHotelsDTO->toArray();

        $this->format($data);

        return Cache::remember($data['search_session'] . '_count', now()->addMinute(), function () use ($data) {

            $dbCount = $this->getBuilder($data)->count();

            if (! empty($dbCount)) {
                return $dbCount;
            }

            try {
                $searchHotelsDto = $this->getGoglobalDto($data, $goglobalSearchSession);
            } catch (Exception $e) {
                Log::error(__METHOD__ . ' | ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                return 0;
            }

            $goglobalData = $this->getGoglobalData($searchHotelsDto, $goglobalSearchSession);

            if (empty($goglobalData)) {
                return 0;
            }

            $offers = collect($goglobalData['offers'] ?? null);
            if ($offers->isEmpty()) {
                return 0;
            }

            return $this->countSpecific($data, $offers);
        });
    }

    public function minPrice(SearchHotelsDTO $searchHotelsDTO): ?int
    {
        $data = $searchHotelsDTO->toArray();

        $this->format($data);

        return Cache::remember($data['search_session'] . '_min_price', now()->addMinute(), function () use ($data) {

            $dbValue = RoomBase::whereHas('roomType', function($query) use($data) {

                $query->whereHas('hotel', function ($query) use ($data) {
                    (new HotelFilterService($data))->filter($query);
                });

            })->min('price');

            if (! empty($dbValue)) {
                return $dbValue;
            }

            try {
                $searchHotelsDto = $this->getGoglobalDto($data, $goglobalSearchSession);
            } catch (Exception $e) {
                Log::error(__METHOD__ . ' | ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                return 0;
            }

            $goglobalData = $this->getGoglobalData($searchHotelsDto, $goglobalSearchSession);

            if (empty($goglobalData)) {
                return 0;
            }

            $offers = collect($goglobalData['offers'] ?? null);
            if ($offers->isEmpty()) {
                return 0;
            }

            return $offers->min('total_price');
        });
    }

    public function maxPrice(SearchHotelsDTO $searchHotelsDTO): ?int
    {
        $data = $searchHotelsDTO->toArray();

        $this->format($data);

        return Cache::remember($data['search_session'] . '_max_price', now()->addMinute(), function () use ($data) {
            $dbValue = RoomBase::whereHas('roomType', function($query) use($data) {

                $query->whereHas('hotel', function ($query) use ($data) {
                    (new HotelFilterService($data))->filter($query);
                });

            })->max('price');

            if (! empty($dbValue)) {
                return $dbValue;
            }

            try {
                $searchHotelsDto = $this->getGoglobalDto($data, $goglobalSearchSession);
            } catch (Exception $e) {
                Log::error(__METHOD__ . ' | ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                return 0;
            }

            $goglobalData = $this->getGoglobalData($searchHotelsDto, $goglobalSearchSession);

            if (empty($goglobalData)) {
                return 0;
            }

            $offers = collect($goglobalData['offers'] ?? null);
            if ($offers->isEmpty()) {
                return 0;
            }

            return $offers->max('total_price');
        });
    }

    private function format(array &$data)
    {
        # Should be first
        $this->addSearchSession($data);
        $data['rooms_count'] = count($data['rooms'] ?? []);

        $this->formatPage($data);
        $this->formatDates($data);
        $this->formatStars($data);
        $this->formatInstancesSlugToId($data);
        $this->formatSortOrder($data);
    }

    private function formatPage(array &$data)
    {
        if (empty($data['page'])) {
            $data['page'] = 1;
        }
    }

    private function formatDates(array &$data)
    {
        $dates = $data['dates'] ?? null;

        $arrivalDate = $dates['arrival'] ?? null;
        $departureDate = $dates['departure'] ?? null;

        if (empty($arrivalDate) && empty($departureDate)) {
            return;
        }


        $arrivalDate = Carbon::createFromFormat('d.m.Y', $arrivalDate);

        $departureDate = Carbon::createFromFormat('d.m.Y', $departureDate);

        $data['dates']['arrival'] = $arrivalDate;
        $data['dates']['departure'] = $departureDate;
        $data['dates']['days'] = $arrivalDate->diffInDays($departureDate);
    }

    public function getBuilder(array $data): Builder
    {
        $hotels = Hotel::query();

        (new HotelFilterService($data))->filter($hotels);

        $hotels->with([
            'district' => DistrictQueryHelper::relationForBreadcrumbs($this->locale),
            'facilities' => function ($query) {
                $query->select(['facilities.id', 'facilities.title_l->' . $this->locale . ' as title']);
            },
            'page:instance_type,instance_id,slug'
        ]);

        $hotels->select([
            'id',
            'title_l->' . $this->locale . ' as title',
            'description_l->' . $this->locale . ' as description',
            'external_code',
            'district_id',
            'address',
            'stars',
            'geo'
        ]);

        $hotels->withMin('roomBases as min_price', 'price');
        $hotels->withMin('roomBases as min_base_price', 'base_price');
        $hotels->withCount('reviews');
        $hotels->withAvg('reviews as rating', 'rating_avg');

        return match (SortOrder::from($data['sort'])) {
            SortOrder::BY_PRICE_ASC   => $hotels->orderBy('min_price'),
            SortOrder::BY_PRICE_DESC  => $hotels->orderByDesc('min_price'),
            SortOrder::BY_STARS_ASC   => $hotels->orderBy('stars'),
            SortOrder::BY_STARS_DESC  => $hotels->orderByDesc('stars'),
            SortOrder::BY_RATING_ASC  => $hotels->orderBy('rating'),
            SortOrder::BY_RATING_DESC => $hotels->orderByDesc('rating'),
            SortOrder::BY_DISCOUNT    => $hotels->orderByDesc('discount_id'),
            SortOrder::BY_TITLE_ASC   => $hotels->orderBy('title'),
            SortOrder::BY_TITLE_DESC  => $hotels->orderByDesc('title'),
        };
    }

    private function formatStars(array &$data)
    {
        if (empty($stars = $data['stars'] ?? null)) {
            return;
        }

        $minStar = min($stars);
        $maxStar = max($stars);

        if ($minStar === $maxStar) {
            $maxStar = null;
        }

        $data['min_star'] = $minStar;
        $data['max_star'] = $maxStar;
    }

    private function formatInstancesSlugToId(array &$data)
    {
        if ( ! empty($data['city_slug'])) {
            $this->addCity($data);
        } elseif ( ! empty($data['district_slug'])) {
            $this->addDistrict($data);
        } elseif ( ! empty($data['region_slug'])) {
            $this->addRegion($data);
        }
    }

    private function addCity(array &$data)
    {
         $page = Page::where('instance_type', City::class)
             ->where('slug', $data['city_slug'])
             ->with('instance:id,external_code')
             ->first(['id', 'instance_id', 'instance_type']);

         if ( ! $page) {
             throw new Exception(__('errors.app.city.not_found'));
         }

        $data['city'] = $page->instance_id;
        $data['city_external_code'] = $page->instance->external_code ?? null;
    }

    private function addDistrict(array &$data)
    {
        $districts = District::whereHas('page', function ($query) use ($data) {
            $query->whereIn('slug', $data['district_slug']);
        })
            ->with('city:id,external_code')
            ->get(['id', 'city_id']);

        if ($districts->isEmpty()) {
            throw new Exception(__('errors.app.district.not_found'));
        }

        $districtIds = $districts->pluck('id');

        $data['district'] = $districtIds->toArray();
        $data['city_external_code'] = $districts->first()?->city?->external_code ?? null;
    }

    private function addRegion(array &$data)
    {
        $page = Page::where('instance_type', Region::class)
            ->where('slug', $data['region_slug'])
            ->first('instance_id');

        if ( ! $page) {
            throw new Exception(__('errors.app.region.not_found'));
        }

        $data['region'] = $page->instance_id;
    }

    private function formatSortOrder(array &$data)
    {
        if (empty($data['sort'])) {
            $data['sort'] = SortOrder::BY_PRICE_ASC->value;
        }
    }

    private function formatBack(LengthAwarePaginator $hotelsPagination, ?array $goglobalData)
    {
        $hotels = $hotelsPagination->getCollection();

        $result = collect();

        $userFavorites = UserHelper::favoriteHotelIds(auth()->user() ?? auth('sanctum')->user());

        /** @var Hotel $hotel */
        foreach ($hotels as $hotel) {

            $minPrice = $hotel->min_price;
            if (empty($minPrice) && ! empty($externalMinPrice = $goglobalData[$hotel->external_code ?? null] ?? null)) {
                $minPrice = $externalMinPrice;
            }

            $result[] = [
                'id' => $hotel->id,
                'title' => $hotel->title,
                'description' => $hotel->description,
                'external_code' => $hotel->external_code,
                'facilities' => $hotel->facilities->take(5)->pluck('title'),
                'stars' => $hotel->stars ?? 0,
                'location' => $hotel->location,
                'image' => $hotel->getFirstMedia()?->getUrl('original'),
                'rating' => $hotel->rating ?? null,
                'reviews_count' => $hotel->reviews_count ?? null,
                'min_price' => $minPrice,
                'price' => $hotel->min_price === $hotel->price ? null : $hotel->price,
                'is_favorite' => $userFavorites->contains($hotel->id),
                'breadcrumbs' => $hotel->breadcrumbs,
                'district' => $hotel->district
            ];
        }

        $hotelsPagination->setCollection($result);
    }

    private function getGoglobalData(GoglobalSearchHotelsDTO $dto, string $searchSession): ?array
    {
        return Cache::remember(
            $searchSession,
            now()->addMinutes(5),
            function () use ($dto, $searchSession) {

                try {
                    $result = (new GoGlobalHotelSearchService())->search($dto, $searchSession);
                } catch (Exception $e) {
                    Log::error(__METHOD__ . ' Error while getting info from goglobal -' . $e->getMessage());
                    return null;
                }

                return $result;
            }
        );
    }

    private function getGoglobalDto(array $data, string &$searchSession = null): GoglobalSearchHotelsDTO
    {
        if (empty($data['nationality'])) {
            $data['nationality'] = 'GB';
        }
        if (empty($data['rooms'])) {
            $data['rooms'] = [['adults' => 1, 'children' => 0]];
        }
        if (empty($data['dates']['arrival'])) {
            $data['dates']['arrival'] = now()->addWeek()->startOfDay();
        }
        if (empty($data['dates']['departure'])) {
            /** @var Carbon $arrivalDate */
            $arrivalDate = $data['dates']['arrival'];
            $data['dates']['departure'] = (clone $arrivalDate)->addWeek()->endOfDay();
        }
        if (empty($data['city_external_code'])) {
            $citiesInBestDeals = City::where('show_in_best_deals')
                ->limit(4)
                ->pluck('external_code');

            if ($citiesInBestDeals->isEmpty()) {
                throw new Exception('Empty required data');
            }

            $data['city_external_code'] = $citiesInBestDeals->first();
        }

        $dto = new GoglobalSearchHotelsDTO(
            nationality: $data['nationality'],
            rooms: $data['rooms'],
            arrivalDate: $data['dates']['arrival'],
            departureDate: $data['dates']['departure'],
            cityCode: $data['city_external_code'],
            currency: Str::upper($this->currency)
        );

        $searchSession = hash('md5', json_encode($dto->toArray()));

        return $dto;
    }

    private function getGoglobalExternalCodesAndPrices(array $data): ?array
    {
        try {
            $searchHotelsDto = $this->getGoglobalDto($data, $goglobalSearchSession);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' | ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return null;
        }

        return Cache::remember(
            $goglobalSearchSession . '_codes_prices',
            now()->addMinutes(5),
            function () use ($searchHotelsDto, $goglobalSearchSession) {

                $goglobalData = $this->getGoglobalData($searchHotelsDto, $goglobalSearchSession);

                if (empty($goglobalData)) {
                    return null;
                }

                $hotels = collect($goglobalData['hotels'] ?? null);
                if ($hotels->isEmpty()) {
                    return null;
                }

                return $hotels->pluck('min_price', 'external_code')->toArray();
            }
        );
    }

    private function countSpecific(array $data, Collection $offers): int
    {
        if (! empty($data['prices'])) {
            return $offers->whereBetween('total_price', [$data['prices']['min'], $data['prices']['max']])->count();
        }
        if (! empty($data['room_basis'])) {
            return $offers->whereIn('room_basis_value', $data['room_basis'])->count();
        }
        return 0;
    }
}
