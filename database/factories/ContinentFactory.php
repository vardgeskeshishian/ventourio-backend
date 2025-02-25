<?php

namespace Database\Factories;

use App\Models\Continent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ContinentFactory extends Factory
{
    protected $model = Continent::class;

    public function definition(): array
    {
        return [
            'title_l' => ['en' => $this->faker->country()],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
