<?php

namespace App\Services\Web\Hotel;

use App\Helpers\CurrencyConverter;
use App\Models\Hotel;
use App\Models\User;
use App\Services\Web\District\QueryHelper as DistrictQueryHelper;
use App\Services\Web\WebService;
use Illuminate\Database\Eloquent\Collection;

final class FavoritesService extends WebService
{
    public function get(User $user): Collection
    {
        $hotels = $user->favorites()
            ->join('pages', 'hotels.id', '=', 'pages.instance_id')
            ->where('pages.instance_type', '=', Hotel::class)
            ->select([
                'hotels.id',
                'district_id',
                'address',
                'view_count',
                'title_l->' . $this->locale . ' as title'
            ])
            ->with([
                'media',
                'district' => DistrictQueryHelper::relationForBreadcrumbs($this->locale),
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
            ->get();

        /** @var Hotel $hotel */
        foreach ($hotels as $hotel) {

            if (empty($minPrice = $hotel->min_price ?? null)) {
                $minPrice = null;
            } else {
                $minPrice = CurrencyConverter::fromMain($minPrice, $this->currency, CurrencyConverter::FORMAT_CURRENCY_AMOUNT);
            }

            $hotel->setAttribute('min_price', $minPrice);
            $hotel->makeHidden(['media','page','pivot']);
            $hotel->append('breadcrumbs');

            Helper::addImages($hotel);

            $hotel->setAttribute('is_favorite', true);
        }

        return $hotels;
    }
}
