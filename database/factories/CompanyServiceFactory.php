<?php

namespace Database\Factories;

use App\Models\CompanyService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CompanyServiceFactory extends Factory
{
    protected $model = CompanyService::class;

    public function definition(): array
    {
        return [
            'title_l' => ['en' => $this->faker->unique()->word()],
            'description_l' => ['en' => $this->faker->text()],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
