<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PaymentSystemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title_l' => ['en' => $this->faker->word()],
            'payment_system' => $this->faker->word(),
            'enabled' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
