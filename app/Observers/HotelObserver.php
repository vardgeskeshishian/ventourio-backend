<?php

namespace App\Observers;

use App\Events\HotelDiscountWasChanged;
use App\Helpers\PageSlugGenerator;
use App\Models\Hotel;
use App\Models\Page;

class HotelObserver
{
    /**
     * Handle the Hotel "created" event.
     *
     * @param Hotel $hotel
     * @return void
     */
    public function created(Hotel $hotel): void
    {
        if (empty($hotel->page) && ! $hotel->page()->exists()) {

            Page::create([
                'instance_id' => $hotel->id,
                'instance_type' => $hotel->getMorphClass(),
                'slug' => PageSlugGenerator::make($hotel),
                'heading_title_l' => [
                    'en' => $hotel->title
                ],
                'meta_title_l' => [
                    'en' => $hotel->title
                ],
                'meta_description_l' => [
                    'en' => $hotel->title
                ],
                'content_l' => [
                    'en' => [
                        'about' => '',
                        'rooms_info' => '',
                        'facilities' => '',
                        'rules' => '',
                    ]
                ],
            ]);
        }
    }

    public function saved(Hotel $hotel):void
    {
        if ($hotel->wasChanged('discount_id') && ! empty($hotel->discount_id)) {
            HotelDiscountWasChanged::dispatch($hotel);
        }
    }

    /**
     * Handle the Hotel "deleted" event.
     *
     * @param Hotel $hotel
     * @return void
     */
    public function deleted(Hotel $hotel): void
    {
        $hotel->page()->delete();
    }
}
