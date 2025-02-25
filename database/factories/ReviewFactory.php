<?php

namespace Database\Factories;

use App\Enums\RatingCategory;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'body' => $this->faker->realText(),
            'rating' => [
                RatingCategory::STAFF->value           => rand(1,10),
                RatingCategory::FACILITIES->value      => rand(1,10),
                RatingCategory::CLEANLINESS->value     => rand(1,10),
                RatingCategory::COMFORT->value         => rand(1,10),
                RatingCategory::VALUE_FOR_MONEY->value => rand(1,10),
                RatingCategory::LOCATION->value        => rand(1,10),
                RatingCategory::FREE_WIFI->value       => rand(1,10),
            ],
            'rating_avg' => $this->faker->randomFloat(1, 1, 10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'booking_id' => Booking::factory(),
        ];
    }
}
