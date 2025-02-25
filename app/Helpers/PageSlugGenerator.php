<?php

namespace App\Helpers;

use App\Models\City;
use App\Models\Continent;
use App\Models\Country;
use App\Models\District;
use App\Models\Hotel;
use App\Models\Page;
use App\Models\Region;
use Exception;
use Illuminate\Database\Eloquent\Model;

final class PageSlugGenerator
{
    private string $locale = 'en';

    /**
     * @throws Exception
     */
    public static function make(Model $model): string
    {
        return (new self)->unique($model);
    }

    /**
     * @throws Exception
     */
    private function unique(Model $model): string
    {
        $slug = $this->default($model);

        return match (get_class($model)) {
            Continent::class,
            Country::class   => $slug,
            Region::class    => $this->region($model, $slug),
            City::class      => $this->city($model, $slug),
            District::class  => $this->district($model, $slug),
            Hotel::class     => $this->hotel($model, $slug),
            default          => throw new Exception('Not implemented')
        };
    }

    /**
     * @throws Exception
     */
    private function default(Model $model): string
    {
        if ( ! empty($source = $model->parsing_source ?? null)) {
            return str($source)->afterLast('/')->slug();
        }

        if (empty($title = $model->title ?? null)) {
            throw new Exception('Empty title for slug generation');
        }

        return str($title)->slug();
    }

    private function region(Region $region, string $slug, int $iteration = 1): string
    {
        $exists = Page::region()
            ->where('slug', $slug)
            ->exists();

        if (! $exists) {
            return $slug;
        }

        $region->load([
            'country' => function ($query) {
                $query->select([
                    'countries.id',
                    'countries.title_l->' . $this->locale . ' as title'
                ]);
            }
        ]);

        if ($iteration === 1) {
            $part = str($region->country?->title)->slug()->limit(20, '');
        } else {
            $part = $region->id;
        }

        $slug .= '-' . $part;

        return $this->region(region: $region, slug: $slug, iteration: $iteration + 1);
    }

    private function city(City $city, string $slug, int $iteration = 1): string
    {
        $exists = Page::city()
            ->where('slug', $slug)
            ->exists();

        if (! $exists) {
            return $slug;
        }

        $city->load([
            'region' => function ($query) {
                $query->select([
                    'regions.id',
                    'regions.title_l->' . $this->locale . ' as title'
                ]);
            }
        ]);

        if ($iteration === 1) {
            $part = str($city->region?->title)->slug()->limit(20, '');
        } else {
            $part = $city->id;
        }

        $slug .= '-' . $part;

        return $this->city(city: $city, slug: $slug, iteration: $iteration + 1);
    }

    private function district(District $district, string $slug, int $iteration = 1): string
    {
        $exists = Page::district()
            ->where('slug', $slug)
            ->exists();

        if (! $exists) {
            return $slug;
        }

        $district->load([
            'city' => function ($query) {
                $query->select([
                    'cities.id',
                    'cities.title_l->' . $this->locale . ' as title'
                ]);
            }
        ]);

        if ($iteration === 1) {
            $part = str($district->city?->title)->slug()->limit(20, '');
        } else {
            $part = $district->id;
        }

        $slug .= '-' . $part;

        return $this->district(district: $district, slug: $slug, iteration: $iteration + 1);
    }

    private function hotel(Hotel $hotel, string $slug, int $iteration = 1): string
    {
        $exists = Page::hotel()
            ->where('slug', $slug)
            ->exists();

        if (! $exists) {
            return $slug;
        }

        $hotel->load([
            'district' => function ($query) {

                $query
                    ->select([
                        'districts.id',
                        'districts.city_id'
                    ])
                    ->with([
                        'city' => function ($query) {
                            $query->select([
                                'cities.id',
                                'cities.title_l->' . $this->locale . ' as title',
                            ]);
                        }
                    ]);
            }
        ]);

        if ($iteration === 1) {
            $part = str($hotel->district?->city?->title)->slug()->limit(20, '');
        } else {
            $part = $hotel->id;
        }

        $slug .= '-' . $part;

        return $this->hotel(hotel: $hotel, slug: $slug, iteration: $iteration + 1);
    }
}
