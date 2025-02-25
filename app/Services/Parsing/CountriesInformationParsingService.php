<?php

namespace App\Services\Parsing;
use App\DTO\PlanetHotel\CountryParserDTO;
use App\Traits\InteractWithParsing;
use Goutte\Client;
use Illuminate\Support\Collection;

set_time_limit(9000);


final class CountriesInformationParsingService
{
    use InteractWithParsing;

    private array $data = [];
    private string $planetHotelExportPath = 'app/public/planet-hotel-countries-information/';

    private Client $client;

    public function __construct(
    )
    {
        $this->client = new Client();
    }

    public function parse(): void
    {

        $countries = $this->getCountries();

        foreach($countries as $country){

            $countryCrawler = $this->client->request('GET', $this->scrapingUrl . $country['source_id']);

            $page = new CountryParserDTO($countryCrawler);

            $this->data[] = [
                'parsing_source' =>  $country['source_id'],
                'gallery' => $page->getGallery(),
                'slider_images' => $this->prependParsingSource($page->getSliderImages()),
                'description' => [
                    'en' => $page->getDescription(),
                ],
                'geography' => [
                    'en' => $page->getGeography(),
                ],
                'long_description' => [
                    'en' => $page->longDescription(),
                ],
            ];
        }
        $path = storage_path($this->planetHotelExportPath . time() .'-countries-information.json');
        $this->saveInFile($path, $this->data);
    }

    private function getCountries(): Collection
    {
        $file = $this->geolocationsMainPath();

        $fileToJson = json_decode(file_get_contents($file), true);

        if (empty($fileToJson)) {
            throw new \Exception('ImportData File is empty');
        }

        return collect($fileToJson)
            ->pluck('countries')
            ->flatten(1);
    }
}
