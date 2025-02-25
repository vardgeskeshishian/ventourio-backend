<?php

namespace App\Services\Web\City;

use App\Exceptions\BusinessException;
use App\Models\City;
use App\Services\Web\WebService;
use Illuminate\Database\Eloquent\Collection;

final class Service extends WebService
{
    public function search(array $data): Collection
    {
        $cities = City::where('title_l->' . $this->locale, 'LIKE', "%{$data['text']}%")
            ->orWhereHas('region', function ($query) use ($data) {
                $query->whereHas('country', function ($query) use ($data) {
                    $query->where('title_l->' . $this->locale, 'LIKE', "%{$data['text']}%");
                });
            })
            ->select(['id', 'region_id', 'title_l->' . $this->locale . ' as title'])
            ->with([
                'country' => function ($query) {
                    $query->select(['countries.id', 'countries.title_l->' . $this->locale . ' as title']);
                },
                'page:id,instance_id,instance_type,slug'
            ])
            ->get();

        if ($cities->isEmpty()) {
            throw new BusinessException(__('errors.app.city.search.not_found'));
        }

        $cities->each( function (City $city) {
            $city->setAttribute('title_full', $city->title . ', ' . $city->country->title ?? '');
            $city->makeHidden(['country', 'title', 'region_id']);
        });

        return $cities;
    }
}
