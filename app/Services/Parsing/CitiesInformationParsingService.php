<?php

namespace App\Services\Parsing;
use App\DTO\PlanetHotel\CityParserDTO;
use Goutte\Client;
use Illuminate\Support\Collection;
use App\Traits\InteractWithParsing;

set_time_limit(0);


final class CitiesInformationParsingService
{
    use InteractWithParsing;

    private array $data = [];
    private string $planetHotelCitiesExportPath = 'app/public/planet-hotel-cities-information/';

    private Client $client;

    public function __construct(
    )
    {
        $this->client = new Client();
    }

    /**
     * @throws \Exception
     */
    public function parse(): void
    {

        $cities = $this->getCities();

        foreach($cities as $city){

            $crawler = $this->client->request('GET', $this->scrapingUrl . $city['parsing_source']);

            $statusCode = $this->client->getInternalResponse()->getStatusCode();

            if ($statusCode === 200) {

                $page = new CityParserDTO($crawler);

                $this->data[] = [
                    'parsing_source' =>  $city['parsing_source'],
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
        }
        $path = storage_path($this->planetHotelCitiesExportPath . time().'-cities-information.json');
        $this->saveInFile($path, $this->data);
    }

    private function getCities(): Collection
    {
        $fileToJson = json_decode(file_get_contents($this->citiesInformationMainPath()), true);

        if (empty($fileToJson)) {
            throw new \Exception('ImportData File is empty');
        }

        return collect($fileToJson);
    }

    private function getCitiesFormGeolocation(): Collection
    {
        $fileToJson = json_decode(file_get_contents($this->citiesInformationMainPath()), true);

        if (empty($fileToJson)) {
            throw new \Exception('ImportData File is empty');
        }

        return collect($fileToJson)
            ->pluck('countries')
            ->flatten(1)
            ->pluck('regions')
            ->flatten(1)
            ->pluck('cities')
            ->flatten(1)
            ->whereNotNull('source_id');
    }
}
