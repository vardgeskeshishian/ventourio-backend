<?php

namespace App\Jobs\GoGlobal;

use App\Models\City;
use App\Models\District;
use App\Models\Hotel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportHotelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public array $hotelData, public string $locale = 'en') {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $hotel = $this->hotelData;

        $cityId = $hotel[3];
        $hotelId = $hotel[5];
        $name = $hotel[6];
        $address = $hotel[7];
        $phone = $hotel[8] ?? null;
        $fax = $hotel[9] ?? null;
        $stars = $hotel[10] ?? null;
        $longitude = $hotel[12] ?? null;
        $latitude = $hotel[13] ?? null;
        $isApartment = $hotel[14] ?? null;
        $giataCode = $hotel[15] ?? null;

        $hotelAlreadyExists = Hotel::where('external_code', $hotelId)->exists();
        if ($hotelAlreadyExists) {
            return;
        }

        $districtId = $this->getDistrictId($cityId);
        if (empty($districtId)) {
            return;
        }

        if ( ! empty($longitude) && ! empty($latitude)) {
            $geo = [
                'longitude' => $longitude,
                'latitude' => $latitude
            ];
        }

        if ( ! empty($isApartment)) {
            $isApartment = $isApartment == 'True';
        } else {
            $isApartment = null;
        }

        Hotel::create([
            'external_code' => $hotelId,
            'district_id' => $districtId,
            'title_l' => ['en' => $name],
            'address' => $address,
            'phone' => $phone,
            'fax' => $fax,
            'stars' => $stars,
            'geo' => $geo ?? null,
            'is_apartment' => $isApartment,
            'giata_code' => $giataCode,
        ]);
    }

    private function getDistrictId(string $cityId)
    {
        $suitableCity = City::where('external_code', $cityId)->first();
        if ( ! $suitableCity) {
            return null;
        }

        $suitableDistrict = District::query()
            ->where('city_id', $suitableCity->id)
            ->where('is_common', true)
            ->first();

        if ( ! $suitableDistrict) {
            $suitableDistrict = District::create([
                'is_common' => true,
                'city_id' => $suitableCity->id,
                'title_l' => ['en' => 'Base']
            ]);
        }

        return $suitableDistrict->id;
    }
}
