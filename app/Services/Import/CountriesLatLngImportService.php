<?php

namespace App\Services\Import;
use App\Models\Country;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

set_time_limit(0);


final class CountriesLatLngImportService
{
    public function run(): void
    {

        $countries = Country::query()->whereNotNull('parsing_source')->get();

        foreach($countries as $country){

            $countryName = $country->title_l;

            $result = $this->getLatLng($countryName);

            if(!empty($result)){
                $country->update([
                    'geo' => $result
                ]);
            }

        }
    }

    private function getLatLng($cityName):array|null
    {
        // replace YOUR_API_KEY with your actual API key
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $cityName . "&key=" . config('google.google_map_api_key');
        $response = Http::get($url);

        if($response->successful() && $response->status() === 200)
        {
            $result = $response->json();

            if(Arr::has($result, 'results.0.geometry.location.lat')){
                $lat = $result['results'][0]['geometry']['location']['lat'];
                $lng = $result['results'][0]['geometry']['location']['lng'];
                return [
                    "latitude" => $lat,
                    "longitude" => $lng
                ];
            }
        }

        return null;
    }
}
