<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Continent;
use App\Models\Country;
use App\Models\District;
use App\Models\Hotel;
use App\Models\Region;
use App\Models\Room;
use App\Models\RoomBase;
use App\Models\RoomType;
use App\Models\Sight;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class LocationAndObjectsSeeder extends Seeder
{
    public array $hotelMedia;

    public array $cityMedia;

    public array $countryMedia;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $continents = [
            'Europe',
            'Asia',
            'North America',
            'Ocean',
            'South America',
            'Africa'
        ];

        $this->hotelMedia = Storage::disk('public')->allFiles('hotel_fake_media');
        $this->cityMedia = Storage::disk('public')->allFiles('city_fake_media');
        $this->countryMedia = Storage::disk('public')->allFiles('country_fake_media');

        foreach ($continents as $continent) {

            Continent::factory(1)
                ->create(['title_l' => ['en' => $continent]])
                ->each( function ($continent) {

                    Country::factory(3)
                        ->create(['continent_id' => $continent->id])
                        ->each( function ($country) {

                            if (count($this->countryMedia)) {
                                $randomImagePath = Storage::disk('public')->path(Arr::random($this->countryMedia));
                                $country->copyMedia($randomImagePath)->toMediaCollection('default');
                            }

                            Region::factory(2)
                                ->create(['country_id' => $country->id])
                                ->each( function ($region) {

                                    City::factory(2)
                                        ->create(['region_id' => $region->id])
                                        ->each(function ($city) {

                                            if (count($this->cityMedia)) {
                                                $randomImagePath = Storage::disk('public')->path(Arr::random($this->cityMedia));
                                                $city->copyMedia($randomImagePath)->toMediaCollection('default');
                                            }

                                            Sight::factory(1)
                                                ->create(['city_id' => $city->id]);

                                                District::factory(2)
                                                    ->create(['city_id' => $city->id])
                                                    ->each(function ($district) {

                                                        Hotel::factory(2)
                                                            ->create(['district_id' => $district->id])
                                                            ->each( function ($hotel) {

                                                                if (count($this->hotelMedia)) {
                                                                    $randomImagePath = Storage::disk('public')->path(Arr::random($this->hotelMedia));
                                                                    $hotel->copyMedia($randomImagePath)->toMediaCollection('default');
                                                                }

                                                                $hotel->facilities()->attach(rand(0,1) ? [1,3,5,6] : [2,4,7,9]);

                                                                RoomType::factory(2)
                                                                    ->create(['hotel_id' => $hotel->id])
                                                                    ->each( function ($roomType) {

                                                                        RoomBase::factory(2)
                                                                            ->create(['room_type_id' => $roomType->id])
                                                                            ->each( function ($roomBase) {

                                                                                Room::factory(2)
                                                                                    ->create(['room_base_id' => $roomBase->id]);
                                                                            });
                                                                    });
                                                            });
                                                    });
                                        });
                                });
                        });
                });
        }
    }
}
