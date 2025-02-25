<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Country;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition(): array
    {
        return [
            'title_l' => [
                'en' => $this->faker->unique()->city()
            ],
            'show_in_best_deals' => rand(0,1),
            'region_id' => Region::factory(),
            'external_code' => $this->faker->randomNumber()
        ];
    }
}
