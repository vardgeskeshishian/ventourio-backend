<?php

namespace App\Services\Web;

use App\Http\Resources\Web\ArticleCategoryResource;
use App\Http\Resources\Web\ArticleResource;
use App\Http\Resources\Web\RecommendedArticleResource;
use App\Http\Resources\Web\TagResource;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

class ArticleService extends WebService
{
    const RECOMMENDED_ARTICLE_LIMIT = 3;

    public function getData($request): array
    {

        $articleCategories = ArticleCategory::select([
                'id',
                'title_l->' . $this->locale . ' as title',
                'color_hex'
            ])->with([
               'page' => function ($query) {
                   $query->select([
                       'pages.id',
                       'pages.slug',
                       'pages.instance_id',
                       'pages.instance_type',
                   ]);
               }
             ])
             ->with([
                'articles' => function ($query) {
                    $query->select([
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
                        'page' => function ($query) {
                            $query->select([
                                'pages.id',
                                'pages.slug',
                                'pages.instance_id',
                                'pages.instance_type',
                            ]);
                        }
                    ])
                    ->with('media');
                }
            ])
            ->get()
            ->each(function (ArticleCategory $category) {
                $category->setRelation('articles', $category->articles->take(3));
            });


        $tags = Tag::select([
                    'tags.id as id',
                    'tags.title_l->'.$this->locale.' as title',
                    'tags.color_hex'
                ])->get();

        $request->merge([
            'locale' => $this->locale,
        ]);

        return [
            'data' => [
                'tags'        => TagResource::collection($tags),
                'recommended' => RecommendedArticleResource::collection($this->recommendations()),
                'articleCategories'  => ArticleCategoryResource::collection($articleCategories),
                'articlesSides' => (new GuideBookService)->latestArticlesSlides()
            ],
        ];
    }

    public function show($request, $slug): array
    {

        $currentArticle = Article::select([
                'articles.id',
                'articles.article_category_id',
                'articles.content_l',
                'articles.title_l->'.$this->locale.' as title',
                'articles.author_l->'.$this->locale.' as author',
                'articles.quote_l->'.$this->locale.' as quote',
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
                'tags' => function ($query) {
                    $query->select([
                        'tags.id',
                        'tags.title_l->'.$this->locale.' as title',
                    ]);
                }
            ])
            ->with([
                'page' => function ($query) {
                    $query->select([
                        'pages.id',
                        'pages.slug',
                        'pages.type',
                        'pages.instance_id',
                        'pages.instance_type',
                        'pages.content_l->' . $this->locale . ' as content',
                        'pages.heading_title_l->' . $this->locale . ' as heading_title',
                        'pages.meta_title_l->' . $this->locale . ' as meta_title',
                        'pages.meta_description_l->' . $this->locale . ' as meta_description',
                    ]);
                }
            ])
            ->with('media')
            ->whereHas('page', function ($query) use ($slug) {
                $query->where('slug', $slug);
            })
            ->first();


        $request->merge([
            'locale' => $this->locale,
        ]);

        return [
            'data' => [
                'article'  => new ArticleResource($currentArticle),
                'recommended' => RecommendedArticleResource::collection($this->recommendations($currentArticle)),
                'latestArticles' => (new GuideBookService)->latestArticles($request, $currentArticle, 4),
            ],
        ];
    }

    public function recommendations(Article|null $article = null): Collection
    {

        return Article::select([
                'articles.id as id',
                'articles.created_at',
                'articles.title_l->'.$this->locale.' as title',
                'articles.content_l->'.$this->locale.' as content',
                'pages.slug as slug',
                'pages.id as page_id',
                'pages.type as page_type',
                'pages.heading_title_l as heading_title',
                'pages.meta_title_l as page_meta_title',
                'pages.meta_description_l as page_meta_description',
                'pages.content_l as page_content',
                'pages.view_count as page_view_count'
            ])
            ->join('pages', function($join) {
                $join->on('pages.instance_id', '=', 'articles.id');
                $join->where('pages.instance_type', '=', Article::class);
            })
            ->where(function($query) use ($article){
                if($article){
                    $query->where('articles.id', '!=', $article->id);
                }
            })
            ->with('media')
            ->orderByDesc('pages.view_count')
            ->take(self::RECOMMENDED_ARTICLE_LIMIT)
            ->get();
    }
}
