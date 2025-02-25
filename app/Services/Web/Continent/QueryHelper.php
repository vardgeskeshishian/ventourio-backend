<?php

namespace App\Services\Web\Continent;

use Closure;

final class QueryHelper
{
    public static function relationForBreadcrumbs(string $locale): Closure
    {
        return function ($query) use ($locale) {
            $query->select([
                'continents.id',
                'continents.title_l->' . $locale . ' as title',
            ])
                ->with([
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
