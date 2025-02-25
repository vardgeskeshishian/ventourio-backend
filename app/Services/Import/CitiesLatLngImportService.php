<?php

namespace App\Services\Import;
use App\Models\City;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

set_time_limit(0);


final class CitiesLatLngImportService
{
    public function run(): void
    {

        $cities = City::query()->whereNotNull('parsing_source')->get();

        foreach($cities as $key =>$city){
            if($key > 50){
                break;
            }
            $cityName = $city->title_l;

            $result = $this->getLatLng($cityName);

            if(!empty($result)){
                $city->update([
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
