<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Continent;
use App\Models\Country;
use App\Models\City;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class PageFactory extends Factory
{

    protected $model = Page::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        $randomTable = $this->randomTable();

        return [
            'instance_type' => $randomTable,
            'instance_id' => $randomTable::factory(),
            'slug' => function (array $attributes) use($randomTable){
                $model = $randomTable::find($attributes['instance_id']);
                return Str::slug($model->id . ' ' . $model->title, '-');
            },
            'content_l' => function (array $attributes) use($randomTable){
                    return $randomTable::find($attributes['instance_id'])->title_l;
            },
        ];
    }

    public function randomTable()
    {
        return $this->faker->randomElement([
            ArticleCategory::class,
            Article::class,
            Continent::class,
            Country::class,
            City::class,
        ]);
    }
}
