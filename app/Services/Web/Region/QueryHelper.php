<?php

namespace App\Services\Web\Region;

use App\Services\Web\Country\QueryHelper as CountryQueryHelper;
use Closure;

final class QueryHelper
{
    public static function relationForBreadcrumbs(string $locale): Closure
    {
        return function ($query) use ($locale) {

            $query->select([
                'regions.id',
                'regions.title_l->' . $locale . ' as title',
                'regions.country_id',
                'regions.is_common',
            ])
                ->with([
                    'country' => CountryQueryHelper::relationForBreadcrumbs($locale),
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
