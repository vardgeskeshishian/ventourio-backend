<?php

namespace App\Observers;

use App\Helpers\PageSlugGenerator;
use App\Models\District;
use App\Models\Page;

class DistrictObserver
{
    /**
     * Handle the District "created" event.
     *
     * @param  \App\Models\District  $district
     * @return void
     */
    public function created(District $district)
    {
        if (empty($district->page) && ! $district->page()->exists()) {
            Page::create([
                'instance_id' => $district->id,
                'instance_type' => $district->getMorphClass(),
                'heading_title_l' => [
                    'en' => $district->title . ' Hotels'
                ],
                'meta_title_l' => [
                    'en' => $district->title . ' Hotels'
                ],
                'meta_description_l' => [
                    'en' => $district->title . ' Hotels'
                ],
                'content_l' => [
                    'en' => [
                        'top_block' => [
                            'header' => $district->title . ' Hotels',
                            'paragraph' => 'The staff will arrange food and drinks delivery in a room on demand. Here you can book rooms for non-smokers. Free access to the Internet is provided. If necessary, there\'s a shuttle service from/to the airport. The front desk works round the clock. The territory is well-attended and green. Guests can pay for s'
                        ],
                        'bottom_block' => [
                            'header_1' => "Where to Stay in $district->title?",
                            'paragraph_1' => 'Answer takes 10 minutes',
                            'header_2' => 'Title 1',
                            'paragraph_2' => 'By investing in the technology that helps take the friction out of travel, Ventourio.com seamlessly connects millions of travellers with memorable experiences, a range of transport options and incredible places to stay - from homes to hotels and much more. As one of the world’s largest travel marketplaces for both established brands and entrepreneurs of all sizes, Ventourio.com enables properties all over the world to reach a global audience and grow their businesses.'
                        ]
                    ]
                ],
                'slug' => PageSlugGenerator::make($district)
            ]);
        }
    }

    /**
     * Handle the City "deleting" event.
     *
     * @param District $district
     * @return void
     */
    public function deleting(District $district): void
    {
        $district->page()->delete();
    }
}
