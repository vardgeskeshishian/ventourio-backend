<?php

namespace App\Services\Web;

use App\Http\Resources\Web\ArticleCategoryResource;
use App\Http\Resources\Web\ArticleResource;
use App\Http\Resources\Web\RecommendedArticleResource;
use App\Http\Resources\Web\TagResource;
use App\Models\Article;
use App\Models\ArticleCategory;

class ArticleCategoryService extends WebService
{
    public function show($request, $slug): array
    {
        $articleCategories = ArticleCategory::select([
            'id',
            'title_l->' . $this->locale . ' as title',
            'color_hex'
        ])
        ->with([
            'articles.tags' => function ($query) {
                $query->select([
                    'tags.id',
                    'tags.title_l->'.$this->locale.' as title',
                    'tags.color_hex'
                ]);
            }
        ])
        ->get();

        $tags = collect();

        $articleCategories->each(function (ArticleCategory $articleCategory) use($tags){
            $tags->push($articleCategory->articles->pluck('tags'));
        });

        $articleCategory = ArticleCategory::whereHas('page', function ($query) use ($slug) {
          $query->where('slug', $slug);
        })->first();

        return [
            'article_categories' => ArticleCategoryResource::collection($articleCategories),
            'recommended' => RecommendedArticleResource::collection((new ArticleService)->recommendations()),
            'articles' => $this->articles($request, $articleCategory),
            'latestArticles' => (new GuideBookService)->latestArticles($request),
            'tags' => TagResource::collection($tags->flatten(2)->unique('title')),
        ];
    }


    public function articles($request, ArticleCategory $articleCategory): array
    {
        $articles = Article::select([
                'articles.id',
                'articles.article_category_id',
                'articles.title_l->'.$this->locale.' as title',
                'articles.created_at',
            ])
            ->with([
                'tags' => function ($query) {
                    $query->select([
                        'tags.id',
                        'tags.title_l->'.$this->locale.' as title',
                        'tags.color_hex'
                    ]);
                }
            ])
            ->with([
                'category' => function ($query) {
                    $query->select([
                        'id',
                        'title_l->' . $this->locale . ' as title',
                        'color_hex'
                    ]);
                }
            ])
            ->with([
                'page' => function ($query) {
                    $query->select([
                        'pages.id',
                        'pages.slug',
                        'pages.instance_id',
                        'pages.instance_type',
                    ]);
                }
            ])
            ->with('media')
            ->where('article_category_id', $articleCategory->id);

        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 8;
        $count = $articles->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $articles = $articles->take($take)->skip($skip);
        } else {
            $articles = $articles->take($take)->skip(0);
        }

        $request->merge([
            'locale' => $this->locale,
        ]);

        return [
            'data' => ArticleResource::collection($articles->get()),
            'pagination'=> [
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }
}
