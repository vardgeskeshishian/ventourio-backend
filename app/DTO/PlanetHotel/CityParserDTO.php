<?php

namespace App\DTO\PlanetHotel;

use App\DTO\DTO;
use Symfony\Component\DomCrawler\Crawler;

final class CityParserDTO extends DTO
{
    public function __construct(public  Crawler $page
    ) {}

    /**
     * @return Crawler
     */
    public function getDescription(): string|null
    {
        $description = $this->page->filter('.c-description--gallery-height')
                ->filter('p')
                ->first();
        return $description->count() > 0 ? $description->text() : null;
    }

    public function getGallery(): array
    {
        $gallery = $this->page->filter('.c-gallery__city a')->extract(['href']);
        return count($gallery) ? $gallery : [];
    }


    public function getGeography(): string|null
    {
         $text = null;

         $geography = $this->page->filter('.l-container')->eq(3);

         $blockExists = $geography->filterXPath("//h2[contains(text(),'Geography')]")->first();

         if($blockExists->count() > 0){
             $text = $geography->filter('.c-description__text.c-description__text-title')->text();
         }

        return $text;
    }

    public function longDescription(): string|null
    {
        $longDescription =  $this->page->filter('.c-description__text-title')
            ->eq(1)
            ->first();

        return $longDescription->count() > 0 ? $longDescription->html() : null;
    }

    public function getSliderImages(): array|null
    {
        $slider =  $this->page->filter('.c-banner__picture')
            ->filter('picture')
            ->filter('img');

        return $slider->first()->count() > 0 ? $slider->extract(['data-src']) : null;
    }
}
