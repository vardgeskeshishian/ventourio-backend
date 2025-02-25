<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Region;
use App\Models\Country;
use App\Services\GoGlobal\ImportService;
use Exception;
use Illuminate\Database\Seeder;
use Psy\Readline\Hoa\FileDoesNotExistException;

class CountryAndCitySeeder extends Seeder
{
    public function run()
    {
//        $importService = new ImportService();
//
//        try {
//
//            $importService->countriesAndCities(100);
//
//        } catch (FileDoesNotExistException|Exception $e) {

            $countries = Country::factory(15)
                ->create([])
                ->each( function ($country) {
                    Region::factory(5)
                        ->create(['country_id' => $country->id])
                        ->each( function ($region) {
                            City::factory(5)
                                ->create(['region_id' => $region->id]);
                        });
                });
//        }
    }
}
