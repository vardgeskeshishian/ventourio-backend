<?php

namespace Database\Factories;

use App\Models\Discount;
use App\Models\District;
use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class HotelFactory extends Factory
{
    protected $model = Hotel::class;

    public function definition(): array
    {
        return [
            'external_code' => $this->faker->word(),
            'title_l' => ['en' => $this->faker->unique()->words(3, true)],
            'description_l' => ['en' => $this->faker->realTextBetween('100', '200')],
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'fax' => $this->faker->phoneNumber(),
            'stars' => $this->faker->numberBetween(1, 6),
            'geo' => [
                'longitude' => $this->faker->longitude(),
                'latitude' => $this->faker->latitude()
            ],
            'is_apartment' => $this->faker->boolean(),
            'giata_code' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'discount_id' => rand(0, 4) === 0 ? Discount::inRandomOrder()->first()->id  : null,
            'district_id' => District::factory(),
        ];
    }
}
