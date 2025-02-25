<?php

namespace App\Services\Web\City;

use App\Services\Web\Region\QueryHelper as RegionQueryHelper;
use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;

final class QueryHelper
{
    public static function relationForBreadcrumbs(string $locale): Closure
    {
        return function (Builder $query) use ($locale) {

            $query->select([
                'cities.id',
                'cities.region_id',
                'cities.title_l->' . $locale . ' as title',
            ])
                ->with([
                    'region' => RegionQueryHelper::relationForBreadcrumbs($locale),
                    'page' => function ($query) {
                        $query->select([
                            'pages.id',
                            'pages.slug',
                            'pages.instance_type',
                            'pages.instance_id',
                        ]);
                    }
                ]);
        };
    }
}
