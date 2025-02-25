<?php

namespace App\Observers;

use App\Helpers\PageSlugGenerator;
use App\Models\Country;
use App\Models\Page;

class CountryObserver
{
    /**
     * Handle the Country "created" event.
     *
     * @param Country $country
     * @return void
     */
    public function created(Country $country): void
    {
        if (empty($country->page) && ! $country->page()->exists()) {
            Page::create([
                'instance_id' => $country->id,
                'instance_type' => $country->getMorphClass(),
                'heading_title_l' => [
                    'en' => $country->title
                ],
                'meta_title_l' => [
                    'en' => $country->title
                ],
                'meta_description_l' => [
                    'en' => $country->title
                ],
                'content_l' => [
                    'en' => [
                        'header' => 'Recreation features in ' . $country->title,
                        'paragraph_1' => 'Восточная Азия является крупнейшей частью огромного континента, на ее территории располагается множество интереснейших стран (Япония, Южная и Северная Кореи, Китай, Тайвань и многие другие). Эта часть континента располагается в умеренных, субтропических и тропических климатических зонах, поэтому туристам обеспечен самый разнообразный отдых.',
                        'paragraph_2' => 'Для любителей городского отдыха открывают свои двери отели Пекина, Шанхая, Гонконга и других бурно развивающихся и растущих городов, которые предоставляют своим гостям возможность окунуться в незабываемый мир местной экзотики, ознакомиться с памятниками культуры, гармонично окруженными современнейшими небоскребами из стекла и металла.',
                        'paragraph_3' => 'Для любителей городского отдыха открывают свои двери отели Пекина, Шанхая, Гонконга и других бурно развивающихся и растущих городов, которые предоставляют своим гостям возможность окунуться в незабываемый мир местной экзотики, ознакомиться с памятниками культуры, гармонично окруженными современнейшими небоскребами из стекла и металла.'
                    ]
                ],
                'slug' => PageSlugGenerator::make($country)
            ]);
        }
    }

    /**
     * Handle the Country "deleting" event.
     *
     * @param Country $country
     * @return void
     */
    public function deleting(Country $country): void
    {
        $country->page()->delete();
    }
}
