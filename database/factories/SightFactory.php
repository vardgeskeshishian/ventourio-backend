<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SightFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title_l' => [
                'en' => $this->faker->words(),
                'ru' => $this->faker->words(),
            ],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'city_id' => City::factory(),
        ];
    }
}
