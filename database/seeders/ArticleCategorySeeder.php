<?php

namespace Database\Seeders;

use App\Models\ArticleCategory;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Article;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class ArticleCategorySeeder extends Seeder
{
    private static array $categories = [
        'Adventure',
        'Activities',
        'Beaches',
        'Shopping',
        'Food',
        'Family'
    ];

    public array $articleMedia;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->articleMedia = Storage::disk('public')->allFiles('article_fake_media');

        foreach(static::$categories as $category){
            ArticleCategory::factory(1)
                ->create(['title_l' => ['en' => $category]])
                ->each( function ($category) {

                    Article::factory(5)
                        ->create(['article_category_id' => $category->id])
                        ->each( function ($article) {

                            if (count($this->articleMedia)) {
                                $randomImagePath = Storage::disk('public')->path(Arr::random($this->articleMedia));
                                $article->copyMedia($randomImagePath)->toMediaCollection('default');
                            }

                            Tag::factory(1)
                                ->create()
                                ->each( function ($tag) use ($article){
                                    $article->tags()->attach($tag->id);
                                });
                        });
                });
        }
    }
}
