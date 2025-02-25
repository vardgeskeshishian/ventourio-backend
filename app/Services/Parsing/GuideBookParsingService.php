<?php

namespace App\Services\Parsing;
use App\Models\Continent;
use Goutte\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Pure;
use App\DTO\PlanetHotel\BlogDTO;

set_time_limit(9000);


final class GuideBookParsingService
{
    private array $data = [];

    private array $latestArticles = [];

    private array $availableLanguages = ['ru'];

    private string $scrapingUrl = 'https://planetofhotels.com/guide/en';

    private string $scrapingUrlPure = 'https://planetofhotels.com';

    private string $planetHotelExportPath = 'app/public/planet-hotel-guide-book-data/';

    private Client $client;

    public function __construct(
    )
    {
        $this->client = new Client();
    }

    public function latestArticles()
    {
        $continentsCrawler = $this->client->request('GET', $this->scrapingUrl);

        $continentsCrawler->filter('.c-block__latest-publications')
            ->first()
            ->filter('.c-box__top')
            ->each(function ($item, $index){

                $nextPage = $this->client->request('GET', $this->scrapingUrlPure . $item->attr('href'));

                $page = new BlogDTO($nextPage);

                $this->data[$index] = [
                    'title_l' => [
                        'en' => $page->getTitle()
                    ],
                    'content_l' => [
                        [
                            'title' => [
                                'en' => $page->getTitle(),
                            ],
                            'body' => [
                                'en' => $page->getDescription(),
                            ],
                        ]
                    ],
                    'quote_l' => [
                        'en' => $page->getQuote(),
                    ],
                    'tags' => $page->getTags(),
                    'author_l' => [
                        'en' => $page->getAuthor(),
                    ],
                    'category' => [
                        'title_l' =>[
                            'en' => $page->getCategoryName()
                        ],
                        'parsing_source' => $page->getCategorySourceId()
                    ],
                    'created_at' => $page->getPublishDate(),
                    'image' => $page->getImage(),
                    'parsing_source' => $item->attr('href'),
                ];

                //$this->pushOtherTranslations($index, $nextPage);
            });
        $path = storage_path($this->planetHotelExportPath .  time() . '-latest-articles.json');
        File::put($path, json_encode($this->data));
        //return $this->data;
    }

    private function pushOtherTranslations($index, $nextPage): void
    {

        foreach($this->availableLanguages as $language){

            $link = $nextPage->filterXPath('//head')
                ->filterXPath("//link[@hreflang='{$language}']");

            if($link->count() > 0){

                $currentSlider = $this->client->request('GET', $link->attr('href'));

                $page = new BlogDTO($currentSlider);

                $this->data[$index]['title_l'][$language] = $page->getTitle();
                $this->data[$index]['content_l'][0]['title'][$language] = $page->getTitle();
                $this->data[$index]['content_l'][0]['body'][$language] = $page->getDescription();
                $this->data[$index]['quote_l'][$language] = $page->getQuote();
                $this->data[$index]['author_l'][$language] = $page->getAuthor();
                $this->pushOtherTags($index, $page->getTags());
            }
        }
    }

    private function pushOtherTags(int $index, array $tags): void
    {
        foreach($tags as $tag)
        {
            $this->data[$index]['tags'][] = $tag;
        }
    }
}
