<?php

namespace App\Observers;

use App\Helpers\PageSlugGenerator;
use App\Models\City;
use App\Models\Page;

class CityObserver
{
    /**
     * Handle the City "created" event.
     *
     * @param City $city
     * @return void
     */
    public function created(City $city): void
    {
        if (empty($city->page) && ! $city->page()->exists()) {
            Page::create([
                'instance_id' => $city->id,
                'instance_type' => $city->getMorphClass(),
                'meta_title_l' => [
                    'en' => $city->title . ' Hotels'
                ],
                'heading_title_l' => [
                    'en' => $city->title . ' Hotels'
                ],
                'meta_description_l' => [
                    'en' => $city->title . ' Hotels'
                ],
                'content_l' => [
                    'en' => [
                        'top_block' => [
                            'header' => $city->title . ' Hotels',
                            'paragraph' => 'The staff will arrange food and drinks delivery in a room on demand. Here you can book rooms for non-smokers. Free access to the Internet is provided. If necessary, there\'s a shuttle service from/to the airport. The front desk works round the clock. The territory is well-attended and green. Guests can pay for s'
                        ],
                        'bottom_block' => [
                            'header_1' => "Where to Stay in $city->title?",
                            'paragraph_1' => 'Answer takes 10 minutes',
                            'header_2' => 'Title 1',
                            'paragraph_2' => 'By investing in the technology that helps take the friction out of travel, Ventourio.com seamlessly connects millions of travellers with memorable experiences, a range of transport options and incredible places to stay - from homes to hotels and much more. As one of the world’s largest travel marketplaces for both established brands and entrepreneurs of all sizes, Ventourio.com enables properties all over the world to reach a global audience and grow their businesses.'
                        ]
                    ]
                ],
                'slug' => PageSlugGenerator::make($city)
            ]);
        }
    }

    /**
     * Handle the City "deleting" event.
     *
     * @param City $city
     * @return void
     */
    public function deleting(City $city): void
    {
        $city->page()->delete();
    }
}
