<?php

namespace App\Services\Web\Hotel;

use App\Helpers\CurrencyConverter;
use App\Models\Hotel;
use App\Services\Web\District\QueryHelper as DistrictQueryHelper;
use App\Services\Web\UserHelper;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class HotelsBlockService
{
    public static function byCity(int $id, string $locale, string $currency, int $star = null): Collection
    {
        $hotels = Hotel::whereHas('district', function (Builder $query) use ($id) {
            $query->where('city_id', $id);
        });

		if($star){
			$hotels->where('stars', $star);
		}

        return self::index($hotels, $locale, $currency);
    }

    public static function byDistrict(int $id, string $locale, string $currency): Collection
    {
        $hotels = Hotel::query()->where('district_id', $id);

        return self::index($hotels, $locale, $currency);
    }

    public static function byCountry(int $id, string $locale, string $currency, int $star = null): Collection
    {
        $hotels = Hotel::whereHas('district', function (Builder $query) use ($id) {
            $query->whereHas('city', function (Builder $query) use ($id) {
                $query->whereHas('region', function (Builder $query) use ($id) {
                    $query->where('country_id', $id);
                });
            });
        });

		if($star){
			$hotels->where('stars', $star);
		}

        return self::index($hotels, $locale, $currency);
    }

    public static function index(Builder $hotelsQuery, string $locale, string $currency): Collection
    {
        $hotelsQuery->join('pages', 'hotels.id', '=', 'pages.instance_id')
            ->where('pages.instance_type', '=', Hotel::class)
            ->select([
                'hotels.id',
                'district_id',
                'address',
                'view_count',
                'title_l->' . $locale . ' as title'
            ])
            ->with([
                'media',
                'district' => DistrictQueryHelper::relationForBreadcrumbs($locale),
                'page' => function ($query) {
                    $query->select([
                        'pages.id',
                        'pages.slug',
                        'pages.instance_id',
                        'pages.instance_type',
                    ]);
                },
            ])
            ->withMin('roomBases as min_price', 'price')
            ->orderByDesc('view_count')
            ->limit(8);

        /** @var Collection $hotels */
        $hotels = $hotelsQuery->get();

        $userFavorites = UserHelper::favoriteHotelIds(auth()->user() ?? auth('sanctum')->user());

        /** @var Hotel $hotel */
        foreach ($hotels as $hotel) {

            if (empty($minPrice = $hotel->min_price ?? null)) {
                $minPrice = null;
            } else {
                $minPrice = CurrencyConverter::fromMain($minPrice, $currency, CurrencyConverter::FORMAT_CURRENCY_AMOUNT);
            }

            Helper::addImages($hotel);

            $hotel->setAttribute('min_price', $minPrice);
            $hotel->makeHidden(['media','page']);
            $hotel->append('breadcrumbs');

            $hotel->setAttribute('is_favorite', $userFavorites->contains($hotel->id));
        }

        return $hotels;
    }
}
