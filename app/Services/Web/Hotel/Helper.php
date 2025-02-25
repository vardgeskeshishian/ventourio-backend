<?php

namespace App\Services\Web\Hotel;

use App\Models\Hotel;
use App\Services\Web\District\Helper as DistrictHelper;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class Helper
{
    public static function breadcrumbs(Hotel $hotel): array
    {
        $breadCrumbs = [];

        $breadCrumbs[$hotel->page->slug] = $hotel->title;

        $city = $hotel->district->city;
        $breadCrumbs[$city->page->slug] = $city->title;

        $country = $city->region->country;
        $breadCrumbs[$country->page->slug] = $country->title;

        $continent = $country->continent;
        $breadCrumbs[$continent->page->slug] = $continent->title;

        return array_reverse($breadCrumbs);
    }

    public static function addImages(Hotel $hotel): void
    {
        $images = [];

        $media = $hotel->getMedia();
        if ($media->isNotEmpty()) {

            /** @var Media $image */
            foreach ($media as $image) {
                $images[] = $image->getUrl('original');
            }
        }

        $media = $hotel->getMedia('goglobal');
        if ($media->isNotEmpty()) {

            /** @var Media $image */
            foreach ($media as $image) {
                $images[] = $image->getUrl('original');
            }
        }

        $hotel->setAttribute('images', $images);
        $hotel->makeHidden(['media']);
    }
}
