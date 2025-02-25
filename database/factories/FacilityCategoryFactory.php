<?php

namespace Database\Factories;

use App\Models\FacilityCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class FacilityCategoryFactory extends Factory
{
    protected $model = FacilityCategory::class;
    public function definition(): array
    {
        return [
            'title_l' => [
                'en' => $this->faker->word()
            ],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
