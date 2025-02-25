<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{

    protected $model = Article::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'article_category_id' => ArticleCategory::factory(),
            'title_l' => [
                'en' => $this->faker->paragraph
            ],
            'content_l' => [
                [
                    'title' => [
                        'en' => $this->faker->sentence,
                        'ru' => $this->faker->sentence,
                    ],
                    'body' => [
                        'en' => $this->faker->sentence,
                        'ru' => $this->faker->sentence,
                    ],
                ]
            ]
        ];
    }
}
