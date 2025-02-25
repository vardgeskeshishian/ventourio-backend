<?php

namespace App\Observers;

use App\Helpers\PageSlugGenerator;
use App\Models\Page;
use App\Models\Region;

class RegionObserver
{
    /**
     * Handle the Region "created" event.
     *
     * @param Region $region
     * @return void
     */
    public function created(Region $region): void
    {
        if (empty($region->page) && ! $region->page()->exists()) {
            Page::create([
                'instance_id' => $region->id,
                'instance_type' => $region->getMorphClass(),
                'content_l' => $region->title_l,
                'slug' => PageSlugGenerator::make($region)
            ]);
        }
    }

    /**
     * Handle the Region "deleting" event.
     *
     * @param Region $region
     * @return void
     */
    public function deleting(Region $region): void
    {
        $region->page()->delete();
    }
}
