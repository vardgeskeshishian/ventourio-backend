<?php

namespace App\Services\Web\Hotel;

use App\Exceptions\BusinessException;
use App\Models\City;
use App\Services\Web\WebService;
use Illuminate\Database\Eloquent\Collection;

final class BestDealsService extends WebService
{
    public function getInfoForBestDeals(array $data): array
    {
        $countrySlug = $data['country'] ?? null;
        $cityId = $data['city_id'] ?? null;

        if ( ! isset($cityId)) {
            $cities = $this->getCities($countrySlug);
            $cityId = $cities->first()->id;

            $cities->loadCount('hotels');
        }

        return [
            'cities' => $cities ?? collect(),
            'hotels' => [
                HotelsBlockService::byCity($cityId, $this->locale, $this->currency)
            ],
        ];
    }

    public function getCities(?string $countrySlug): Collection
    {
        $cities = City::shownInBestDeals()
            ->select(['id', 'title_l->' . $this->locale . ' as title'])
            ->whereHas('districts', function ($query) {
                $query->whereHas('hotels', function ($query) {
                    $query->has('page');
                });
            });

        if ($countrySlug) {
            $cities->whereHas('region', function ($query) use ($countrySlug) {
                $query->whereHas('country', function ($query) use ($countrySlug) {
                    $query->whereHas('page', function ($query) use ($countrySlug) {
                        $query->where('slug', $countrySlug);
                    });
                });
            });
        }

        $cities = $cities->get();

        if ($cities->isEmpty()) {
            throw new BusinessException('Empty cities for show in best deals');
        }

        return $cities;
    }
}
