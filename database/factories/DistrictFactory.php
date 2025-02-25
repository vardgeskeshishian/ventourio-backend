<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DistrictFactory extends Factory
{
    protected $model = District::class;

    public function definition(): array
    {
        return [
            'city_id' => City::factory(),
            'title_l' => ['en' => $this->faker->unique()->city()],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
