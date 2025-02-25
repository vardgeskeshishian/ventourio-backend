<?php

namespace Database\Factories;

use App\Models\ArticleCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ArticleCategoryFactory extends Factory
{
    protected $model = ArticleCategory::class;

    public function definition(): array
    {
        return [
            'title_l' => ['en' => $this->faker->title()],
            'color_hex' => $this->faker->hexColor()
        ];
    }
}
