<?php

namespace App\Services\Web\Country;

use App\Services\Web\Continent\QueryHelper as ContinentQueryHelper;
use Closure;

final class QueryHelper
{
    public static function relationForBreadcrumbs(string $locale): Closure
    {
        return function ($query) use ($locale) {

            $query->select([
                'countries.id',
                'countries.continent_id',
                'countries.title_l->' . $locale . ' as title'
            ])
                ->with([
                    'continent' => ContinentQueryHelper::relationForBreadcrumbs($locale),
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
