<?php

namespace Database\Factories;

use App\Models\Continent;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition(): array
    {
        $country = $this->faker->unique()->country();
        return [
            'continent_id' => Continent::factory(),
            'title_l' => [
                'en' => $country,
            ],
            'nationality_l' => [
                'en' => $country
            ],
            'iso_code' => $this->faker->countryCode(),
            'external_code' => $this->faker->uuid()
        ];
    }
}
