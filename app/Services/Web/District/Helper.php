<?php

namespace App\Services\Web\District;

use App\Models\District;
use App\Services\Web\City\Helper as CityHelper;

final class Helper
{
    public static function breadcrumbs(District $district): array
    {
        $breadCrumbs = CityHelper::breadcrumbs($district->city);

        if ( ! $district->is_common) {
            $breadCrumbs[$district->page->slug] = $district->title;
        }

        return $breadCrumbs;
    }
}
