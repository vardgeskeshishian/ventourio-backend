<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BaseCertificateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->title,
            'amount' => $this->faker->numberBetween(100, 10000),
            'color_hex' => $this->faker->hexColor
        ];
    }
}
