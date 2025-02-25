<?php

namespace App\Services\Parsing;
use Goutte\Client;
use Illuminate\Support\Facades\File;

set_time_limit(0);


final class ParsingService
{

    private array $data = [];

    private array $continents = [];

    private array $countries = [];

    private array $cities = [];

    private array $regions = [];

    private array $availableLanguages = ['ru'];

    private string $scrapingUrl = 'https://planetofhotels.com';

    private string $planetHotelExportPath = 'app/public/planet-hotel-geolocations/';

    private Client $client;

    public function __construct(
    )
    {
        $this->client = new Client();
    }


    public function getUrl($lang = null, $source = null): string
    {

        $path = $lang === 'ru' ? '' : "/{$lang}";

        if($source){
            $path = $source;
        }

        return $this->scrapingUrl . $path;
    }

    public function parseAtOnceWithoutTranslations()
    {
        $continentsCrawler = $this->client->request('GET', 'https://planetofhotels.com/en/');

        $continentsCrawler->filter('.block-country-fp-list-page-title-r a')
            ->each(function ($continent, $continentIndex){
                //if($continentIndex === 5){

                    $currentContinent = $this->client->request('GET', $this->scrapingUrl.$continent->attr('href'));

                    $this->data[$continentIndex] = [
                        'name'      => [
                            'en' => $continent->text()
                        ],
                        'source_id' => $continent->attr('href'),
                    ];

                    $currentContinent->filter('.block-country-fp-list-page-content-list-item-inner span a')
                        ->each(
                            function ($country, $countryIndex) use ($continentIndex) {

                                $this->data[$continentIndex]['countries'][$countryIndex] = [
                                    'flag'      => $country->filter('img')->count() ? $this->getFlag(
                                        $country->filter('img')
                                    ):NULL,
                                    'name'      => [
                                        'en' => $country->filter('span')->first()->text()
                                    ],
                                    'source_id' => $country->attr('href'),
                                ];

                                $regionsCrawler = $this->client->request(
                                    'GET',
                                    $this->scrapingUrl.$country->attr('href')
                                );

                                $filteredRegions = $regionsCrawler->filter('.page-type-region')
                                    ->first();

                                $hasRegionBlock = $filteredRegions->filterXPath(
                                    "//span[contains(text(),'Select a region')]"
                                )->first();
                                if($filteredRegions->count() > 0 && $hasRegionBlock->count() > 0)
                                {
                                    $filteredRegions->filter('div.block-city-list-page-content-list-item a')->each(
                                        function ($region, $regionIndex) use ($continentIndex, $countryIndex) {

                                            $regionName = $this->getAttributeName($region->attr('title'));

                                            $this->data[$continentIndex]['countries'][$countryIndex]['regions'][$regionIndex] = [
                                                'name'      => [
                                                    'en' => $regionName
                                                ],
                                                'is_common' => 0,
                                                'source_id' => $region->attr('href')
                                            ];

                                            $citiesCrawler = $this->client->request(
                                                'GET',
                                                $this->scrapingUrl.$region->attr('href')
                                            );

                                            $filteredCities = $citiesCrawler
                                                ->filter('.page-type-city')
                                                ->first()
                                                ->filter('div.block-city-list-page-content-list-item a');

                                            if($filteredCities->count() > 0)
                                            {

                                                $filteredCities->each(
                                                    function ($city, $cityIndex) use (
                                                        $continentIndex,
                                                        $countryIndex,
                                                        $regionIndex
                                                    ) {
                                                        $cityName = $this->getAttributeName($city->attr('title'));
                                                        $this->data[$continentIndex]['countries'][$countryIndex]['regions'][$regionIndex]['cities'][$cityIndex] = [
                                                            'name'      => [
                                                                'en' => $cityName
                                                            ],
                                                            'source_id' => $city->attr('href')
                                                        ];
                                                    }
                                                );
                                            }
                                        }
                                    );
                                }
                                else
                                {

                                    $cityBlock = $regionsCrawler->filter('.page-type-city')->first();

                                    $cities = $cityBlock->filterXPath("//span[contains(text(),'Select destination')]")
                                        ->first();

                                    if($cityBlock->count() > 0 && $cities->count() > 0)
                                    {

                                        $cities = [];

                                        $cityBlock->filter('div.block-city-list-page-content-list-item a')->each(
                                            function ($city) use ($continentIndex, $countryIndex, &$cities) {

                                                $cityName = $this->getAttributeName($city->attr('title'));

                                                $cities[] = [
                                                    'name' => [
                                                        'en' => $cityName
                                                    ],
                                                    'source_id' => $city->attr('href')
                                                ];
                                            }
                                        );

                                        $this->data[$continentIndex]['countries'][$countryIndex]['regions'][0] = [
                                            'name'      => [
                                                'en' => "Base"
                                            ],
                                            'source_id' => NULL,
                                            'is_common' => 1,
                                            'cities' => $cities
                                        ];
                                    }
                                }
                            }
                        );
                //}
            });
        $path = storage_path($this->planetHotelExportPath .  time() . '-new-parse.json');
        File::put($path, json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        //return $this->data;
    }

    public function parseAtOnce()
    {
        $continentsCrawler = $this->client->request('GET', 'https://planetofhotels.com/en/');

        $continentsCrawler->filter('.block-country-fp-list-page-title-r a')
            ->each(function ($continent, $continentIndex){
                if($continentIndex === 0)
                {
                    $currentContinent = $this->client->request('GET', $this->scrapingUrl . $continent->attr('href'));

                    $this->data[$continentIndex] = [
                        'name' => [
                            'en' => $continent->text()
                        ],
                        'source_id' => $continent->attr('href'),
                    ];

                    //$this->pushOtherContinentTranslations($currentContinent, $continentIndex);

                    $currentContinent->filter('.block-country-fp-list-page-content-list-item-inner span a')
                        ->each(
                            function ($country, $countryIndex) use ($continentIndex) {
                                info($country->filter('span')->first()->text());
                                //                                $countryCrawler = $this->client->request(
                                //                                    'GET',
                                //                                    $this->scrapingUrl.$country->attr('href')
                                //                                );

                                $this->data[$continentIndex]['countries'][$countryIndex] = [
                                    'flag' => $country->filter('img')->count() ? $this->getFlag($country->filter('img')):NULL,
                                    'name' => [
                                        'en' => $country->filter('span')->first()->text()
                                    ],
                                    'source_id' => $country->attr('href'),
                                ];

                                //$this->pushOtherCountryTranslations($countryCrawler, $continentIndex, $countryIndex);

                                $regionsCrawler = $this->client->request(
                                    'GET',
                                    $this->scrapingUrl.$country->attr('href')
                                );

                                $filteredRegions = $regionsCrawler->filter('.page-type-region')
                                    ->first();
                                if($filteredRegions->count() > 0)
                                {
                                    $filteredRegions->filter('div.block-city-list-page-content-list-item a')->each(
                                        function ($region, $regionIndex) use ($continentIndex, $countryIndex) {

                                            //                                            $regionPage = $this->client->request(
                                            //                                                'GET',
                                            //                                                $this->scrapingUrl.$region->attr('href')
                                            //                                            );

                                            $this->data[$continentIndex]['countries'][$countryIndex]['regions'][$regionIndex] = [
                                                'name' => [
                                                    'en' => $region->text()
                                                ],
                                                'source_id' => $region->attr('href')
                                            ];
                                            //                                            $this->pushOtherRegionsTranslations(
                                            //                                                $regionPage,
                                            //                                                $continentIndex,
                                            //                                                $countryIndex,
                                            //                                                $regionIndex
                                            //                                            );


                                            $citiesCrawler = $this->client->request(
                                                'GET',
                                                $this->scrapingUrl . $region->attr('href')
                                            );

                                            $filteredCities = $citiesCrawler
                                                ->filter('.page-type-city')
                                                ->first()
                                                ->filter('div.block-city-list-page-content-list-item a');

                                            if($filteredCities->count() > 0) {

                                                $filteredCities->each(
                                                    function ($city, $cityIndex ) use ($continentIndex, $countryIndex, $regionIndex)
                                                    {

                                                        //                                                        $cityPage = $this->client->request(
                                                        //                                                            'GET',
                                                        //                                                            $this->scrapingUrl.$city->attr('href')
                                                        //                                                        );

                                                        $this->data[$continentIndex]['countries'][$countryIndex]['regions'][$regionIndex]['cities'][$cityIndex] = [
                                                            'name' => [
                                                                'en' => $city->text()
                                                            ],
                                                            'source_id' => $city->attr('href')
                                                        ];
                                                        //                                                        $this->pushOtherCitiesTranslations(
                                                        //                                                            $cityPage,
                                                        //                                                            $continentIndex,
                                                        //                                                            $countryIndex,
                                                        //                                                            $regionIndex,
                                                        //                                                            $cityIndex
                                                        //                                                        );

                                                    });
                                            }
                                        }
                                    );
                                }
                            }

                        );

                }
            });
        $path = storage_path($this->planetHotelExportPath .  time() . '-new-parse.json');
        File::put($path, json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        //return $this->data;
    }

    private function pushOtherContinentTranslations($page, $index): void
    {

        foreach($this->availableLanguages as $language){

            $link = $page->filterXPath('//head')
                ->filterXPath("//link[@hreflang='{$language}']");

            if($link->count() > 0){

                $currentContinent = $this->client->request('GET', $link->attr('href'));

                $this->data[$index]['name'][$language] = $currentContinent->filter('.breadcrumbs > ol li')
                    ->eq(2)
                    ->filter('span')
                    ->first()
                    ->text();

                $this->data[$index]['source_id'][$language] = $link->attr('href');
            }
        }
    }

    private function pushOtherCountryTranslations($page, $index, $countryIndex): void
    {

        foreach($this->availableLanguages as $language){

            $link = $page->filterXPath('//head')
                ->filterXPath("//link[@hreflang='{$language}']");

            if($link->count() > 0){

                $currentContinent = $this->client->request('GET', $link->attr('href'));

                $countryBreadcrumb = $currentContinent->filter('.breadcrumbs > ol li')
                    ->eq(4)
                    ->filter('span')
                    ->first();

                $countryName = $countryBreadcrumb->count() > 0 ? $countryBreadcrumb->text() : null;

                if($countryName){
                    $this->data[$index]['countries'][$countryIndex]['name'][$language] = $countryName;
                    $this->data[$index]['countries'][$countryIndex]['source_id'][$language] = $link->attr('href');
                }
            }
        }
    }

    private function pushOtherRegionsTranslations($page, $index, $countryIndex, $regionIndex): void
    {

        foreach($this->availableLanguages as $language){

            $link = $page->filterXPath('//head')
                ->filterXPath("//link[@hreflang='{$language}']");

            if($link->count() > 0){

                $currentContinent = $this->client->request('GET', $link->attr('href'));

                $countryBreadcrumb = $currentContinent->filter('.breadcrumbs > ol li')
                    ->eq(6)
                    ->filter('span')
                    ->first();

                $countryName = $countryBreadcrumb->count() > 0 ? $countryBreadcrumb->text() : null;

                if($countryName){
                    $this->data[$index]['countries'][$countryIndex]['regions'][$regionIndex]['name'][$language] = $countryName;
                    $this->data[$index]['countries'][$countryIndex]['regions'][$regionIndex]['source_id'][$language] = $link->attr('href');
                }
            }
        }
    }

    private function pushOtherCitiesTranslations($page, $index, $countryIndex, $regionIndex, $cityIndex): void
    {

        foreach($this->availableLanguages as $language){

            $link = $page->filterXPath('//head')
                ->filterXPath("//link[@hreflang='{$language}']");

            if($link->count() > 0){

                $currentCity = $this->client->request('GET', $link->attr('href'));

                $countryBreadcrumb = $currentCity->filter('.breadcrumbs > ol li')
                    ->eq(8)
                    ->filter('span')
                    ->first();

                $countryName = $countryBreadcrumb->count() > 0 ? $countryBreadcrumb->text() : null;

                if($countryName){
                    $this->data[$index]['countries'][$countryIndex]['regions'][$regionIndex]['cities'][$cityIndex]['name'] = $countryName;
                    $this->data[$index]['countries'][$countryIndex]['regions'][$regionIndex]['cities'][$cityIndex]['source_id'] = $link->attr('href');
                }
            }
        }
    }

    private function getFlag($country): string|null
    {
        return $country->filter('img')->count() > 0 ? $country->filter('img')->first()->attr('src') : null;
    }

    private function getAttributeName($attributeText): string|null
    {
        $name = $attributeText;

        if (strpos($attributeText, "Hotels in") !== false) {
            $name = str_replace("Hotels in", "", $attributeText);
        }

        return $name;
    }
}
