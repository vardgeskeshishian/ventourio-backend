<?php

namespace App\Jobs\GoGlobal;

use App\Models\City;
use App\Models\Country;
use App\Models\Region;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportCitiesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public readonly Collection $cities) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $countryIdKey = 2;
        $cityIdKey = 0;

        $countries = Country::whereIn('external_code', $this->cities->pluck($countryIdKey))
            ->get(['id', 'external_code']);

        $regions = Region::all(['id', 'country_id']);

        $cities = City::whereIn('external_code', $this->cities->pluck($cityIdKey))
            ->get(['id', 'external_code']);

        DB::beginTransaction();
        try {

            $citiesForCreation = collect();

            foreach ($this->cities as $city) {

                if (empty($city)) {
                    continue;
                }

                $cityId = $city[$cityIdKey];
                $cityName = $city[1];
                $countryId = $city[$countryIdKey];
                $countryName = $city[3];
                $countryIsoCode = $city[4];

                $suitableCity = $cities->where('external_code', $cityId)->first();
                if ($suitableCity) {
                    continue;
                }

                $country = $this->addCountry($countries, $countryId, $countryName, $countryIsoCode);
                $region  = $this->addRegion($regions, 'Base', $country->id);

                $citiesForCreation->add([
                    'title_l' => json_encode([
                        'en' => str($cityName)->lower()->ucfirst(),
                    ]),
                    'external_code' => $cityId,
                    'region_id' => $region->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            City::insert($citiesForCreation->toArray());

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $this->fail($e);
        }
    }

    private function addCountry(EloquentCollection $countries, string $externalCode, string $title, string $isoCode): Country
    {
        $country = $countries->where('external_code', $externalCode)->first();

        if ( ! $country) {
            $country = Country::create([
                'external_code' => $externalCode,
                'title_l' => [
                    'en' => str($title)->lower()->ucfirst(),
                ],
                'iso_code' => $isoCode
            ]);
            $countries->add($country);
        }

        return $country;
    }

    private function addRegion(EloquentCollection $regions, string $title, int $countryId): Region
    {
        $region = $regions->where('country_id', $countryId)->first();

        if ( ! $region) {
            $region = Region::create([
                'country_id' => $countryId,
                'title_l' => ['en' => $title]
            ]);
            $regions->add($region);
        }

        return $region;
    }
}
