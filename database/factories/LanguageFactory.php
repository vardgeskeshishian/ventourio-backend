<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class LanguageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title_l' => [
                'en' => $this->faker->word()
            ],
            'code' => $this->faker->word(),
            'type' => $this->faker->word(),
            'flag' => $this->faker->word(),
            'is_rtl' => $this->faker->boolean(),
            'is_active' => $this->faker->boolean(),
            'is_default' => $this->faker->boolean(),
            'localization_json' => json_encode([
                'en' => $this->faker->text()
            ]),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
