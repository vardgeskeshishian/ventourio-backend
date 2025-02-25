<?php

namespace App\Services\Web\District;

use App\Services\Web\City\QueryHelper as CityQueryHelper;
use Closure;

final class QueryHelper
{
    public static function relationForBreadcrumbs(string $locale): Closure
    {
        return function ($query) use ($locale) {

            $query->select([
                'districts.id',
                'districts.city_id',
                'districts.title_l->' . $locale . ' as title',
                'districts.is_common'
            ])
                ->with([
                    'city' => CityQueryHelper::relationForBreadcrumbs($locale),
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
