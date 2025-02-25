<?php

namespace App\Services\Web\Region;

use App\Models\Region;
use App\Services\Web\Country\Helper as CountryHelper;

final class Helper
{
    public static function breadcrumbs(Region $region): array
    {
        $breadCrumbs = CountryHelper::breadcrumbs($region->country);

        if ( ! $region->is_common) {
            $breadCrumbs[$region->page->slug] = $region->title;
        }

        return $breadCrumbs;
    }
}
