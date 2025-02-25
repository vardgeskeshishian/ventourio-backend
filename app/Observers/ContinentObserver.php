<?php

namespace App\Observers;

use App\Helpers\PageSlugGenerator;
use App\Models\Continent;
use App\Models\Page;

class ContinentObserver
{
    /**
     * Handle the Continent "created" event.
     *
     * @param Continent $continent
     * @return void
     */
    public function created(Continent $continent): void
    {
        if (empty($continent->page) && ! $continent->page()->exists()) {
            Page::create([
                'instance_id' => $continent->id,
                'instance_type' => $continent->getMorphClass(),
                'heading_title_l' => [
                    'en' => $continent->title,
                ],
                'meta_title_l' => [
                    'en' => $continent->title,
                ],
                'meta_description_l' => [
                    'en' => $continent->title,
                ],
                'content_l' => [
                    'en' => [
                        'header' => 'Recreation features in ' . $continent->title,
                        'paragraph_1' => 'Восточная Азия является крупнейшей частью огромного континента, на ее территории располагается множество интереснейших стран (Япония, Южная и Северная Кореи, Китай, Тайвань и многие другие). Эта часть континента располагается в умеренных, субтропических и тропических климатических зонах, поэтому туристам обеспечен самый разнообразный отдых.',
                        'paragraph_2' => 'Для любителей городского отдыха открывают свои двери отели Пекина, Шанхая, Гонконга и других бурно развивающихся и растущих городов, которые предоставляют своим гостям возможность окунуться в незабываемый мир местной экзотики, ознакомиться с памятниками культуры, гармонично окруженными современнейшими небоскребами из стекла и металла.',
                        'paragraph_3' => 'Для любителей городского отдыха открывают свои двери отели Пекина, Шанхая, Гонконга и других бурно развивающихся и растущих городов, которые предоставляют своим гостям возможность окунуться в незабываемый мир местной экзотики, ознакомиться с памятниками культуры, гармонично окруженными современнейшими небоскребами из стекла и металла.'
                    ]
                ],
                'slug' => PageSlugGenerator::make($continent)
            ]);
        }
    }

    /**
     * Handle the Continent "deleting" event.
     *
     * @param Continent $continent
     * @return void
     */
    public function deleting(Continent $continent): void
    {
        $continent->page()->delete();
    }
}
