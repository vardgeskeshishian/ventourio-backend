<?php

namespace Database\Factories;

use App\Models\PaymentSystem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PaymentWayFactory extends Factory
{
    public function definition(): array
    {
        return [
            'payment_system_way' => null,
            'enabled' => $this->faker->boolean(),
            'settings' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'payment_system_id' => PaymentSystem::factory(),
        ];
    }
}
