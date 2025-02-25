<?php

namespace App\Services\Admin;

use App\Exceptions\BusinessException;
use App\Http\Resources\Admin\ArticleResource;
use App\Models\Article;
use App\Models\Page;
use App\Services\Web\WebService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArticleService extends WebService
{
    public function getData($request)
    {
        $articles = Article::orderBy('id', "desc");

        if ( ! empty($request->search)) {
            $articles->whereRaw("LOWER(title_l->'$.{$this->locale}') like ?", '%'.strtolower($request->search).'%');
            $articles->orWhereRaw("LOWER(id) like ?", '%'.strtolower($request->search).'%');
        }

        $page  = $request->input('page') ? : 1;
        $take  = $request->input('count') ? : 8;
        $count = $articles->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $articles = $articles->take($take)->skip($skip);
        } else {
            $articles = $articles->take($take)->skip(0);
        }

        return [
            'data' => ArticleResource::collection($articles->with('page', 'category', 'tags', 'media')->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }

    public function store(array $data): Article
    {
        DB::beginTransaction();
        try {

            $article = Article::create([
                'title_l' => $data['title_l'],
                'content_l' => $data['content_l'],
                'author_l' => $data['author_l'],
                'quote_l' => $data['quote_l'],
                'article_category_id' => $data['article_category_id'] ?? null
            ]);

            $article->tags()->sync($data['tags'] ?? []);

            if ( ! empty($data['media'])) {
                $article->addMediaFromRequest('media')
                    ->toMediaCollection();
            }

            if ( ! empty($data['avatar'])) {
                $article->addMediaFromRequest('avatar')
                    ->toMediaCollection('avatar');
            }

            $this->updateOrCreatePage($data, $article);

            $article->notifySubscribers();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            throw new BusinessException($e->getMessage());
        }

        return $article;
    }

    public function update(array $data, Article $article): Article
    {
        DB::beginTransaction();
        try {

            $article->update([
                'title_l' => $data['title_l'],
                'content_l' => $data['content_l'],
                'author_l' => $data['author_l'],
                'quote_l' => $data['quote_l'],
                'article_category_id' => $data['article_category_id'] ?? null
            ]);

            $article->tags()->sync($data['tags'] ?? []);

            if( ! empty($data['media'])){
                $article->clearMediaCollection();
                $article->addMediaFromRequest('media')
                    ->toMediaCollection();
            }

            if ( ! empty($data['avatar'])) {
                $article->clearMediaCollection('avatar');
                $article->addMediaFromRequest('avatar')
                    ->toMediaCollection('avatar');
            }

            $this->updateOrCreatePage($data, $article);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            throw new BusinessException(__('errors.article.create'));
        }

        return $article;
    }

    /**
     * @throws Exception
     */
    private function updateOrCreatePage(array $data, Article $article): void
    {
        $pageData = $data['page'] ?? [];
        if (empty($pageData['slug'])) {
            $pageData['slug'] = $article->title;
        }

        $pageData['slug'] = str($pageData['slug'])->slug();

        if (Page::where('instance_type', $article->getMorphClass())->whereNot('instance_id', $article->id)->where('slug', $pageData['slug'])->exists()) {
            throw new Exception("slug '{$pageData['slug']}' already used");
        }

        Page::updateOrCreate([
            'instance_id' => $article->id,
            'instance_type' => $article->getMorphClass()
        ],
        [
            'slug' => $pageData['slug'],
            'heading_title_l' => $pageData['heading_title_l'] ?? null,
            'meta_title_l' => $pageData['meta_title_l'] ?? null,
            'meta_description_l' => $pageData['meta_description_l'] ?? null,
            'content_l' => $pageData['content_l'] ?? null
        ]);
    }
}
