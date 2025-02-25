<?php

namespace App\Services\Web\City;

use App\Models\City;
use App\Services\Web\Region\Helper as RegionHelper;

final class Helper
{
    public static function breadcrumbs(City $city): array
    {
        $breadCrumbs = RegionHelper::breadcrumbs($city->region);

        $breadCrumbs[$city->page->slug] = $city->title;

        return $breadCrumbs;
    }
}
