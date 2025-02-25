<?php

namespace App\Services\GoGlobal;

use App\Helpers\Parser;
use App\Jobs\GoGlobal\AttachCountryWithCitiesToDBJob;
use App\Jobs\GoGlobal\ImportCitiesJob;
use App\Jobs\GoGlobal\ImportHotelsJob;
use Exception;
use Illuminate\Support\Collection;
use Psy\Readline\Hoa\FileDoesNotExistException;

final class ImportService
{
    /**
     * @throws FileDoesNotExistException
     * @throws Exception
     */
    public function hotels(?int $limit = null): array
    {
        $path = storage_path('goglobal/StaticDataHotel_134596.csv');

        $hotels = Parser::csv($path);
        if ( ! $hotels) {
            throw new Exception('Csv parsing error');
        }

        // Убираем заглавную строку
        unset($hotels[0]);

        $hotels = collect($hotels);

        if ($limit) {
            $hotels = $hotels->take($limit);
        }

        $hotelChunks = $hotels->chunk(1000);

        foreach ($hotelChunks as $hotelChunk) {
            ImportHotelsJob::dispatch($hotelChunk);
        }

        return [
            'success' => true,
            'message' => 'Import started',
        ];
    }

    /**
     * @throws Exception
     */
    public function countriesAndCities(?int $limit = null): array
    {
        $cities = $this->getCities();

        if ($limit) {
            $cities = $cities->take($limit);
        }

        $citiesChunks = $cities->chunk(1000);

        foreach ($citiesChunks as $citiesChunk) {
            ImportCitiesJob::dispatch($citiesChunk);
        }

        return [
            'success' => true,
            'message' => 'Import started',
        ];
    }

    public function attachCountriesWithCitiesToDb(): array
    {
        $cities = $this->getCities();

        $countryKey = 3;

        $citiesByCountry = $cities->groupBy($countryKey);

        foreach ($citiesByCountry as $country => $data) {

            if (empty($data)) {
                continue;
            }

            AttachCountryWithCitiesToDBJob::dispatch($data);
        }

        return [
            'success' => true,
            'message' => 'Import started',
        ];
    }

    private function getCities(): Collection
    {
        $path = storage_path('goglobal/Destinations.csv');

        $cities = Parser::csv($path);
        if ( ! $cities) {
            throw new Exception('Csv parsing error');
        }

        // Убираем заглавную строку
        unset($cities[0]);

        return collect($cities);
    }
}
