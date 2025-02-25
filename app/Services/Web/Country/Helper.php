<?php

namespace App\Services\Web\Country;

use App\Models\Country;
use App\Services\Web\Continent\Helper as ContinentHelper;

final class Helper
{
    public static function breadcrumbs(Country $country): array
    {
        $breadcrumbs = ContinentHelper::breadcrumbs($country->continent);

        $breadcrumbs[$country->page->slug] = $country->title;

        return $breadcrumbs;
    }
}
