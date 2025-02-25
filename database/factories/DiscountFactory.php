<?php

namespace Database\Factories;

use App\Enums\DiscountType;
use App\Models\Discount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DiscountFactory extends Factory
{
    protected $model = Discount::class;

    public function definition(): array
    {
        return [
            'type' => DiscountType::PERCENT,
            'amount' => $this->faker->numberBetween(5, 50),
            'expired_at' => $this->faker->dateTimeBetween(now()->subMonth(), now()->addMonth()),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
