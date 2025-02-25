<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Article\StoreArticleRequest;
use App\Http\Requests\Admin\Article\UpdateArticleRequest;
use App\Http\Requests\Admin\EditorUploadRequest;
use App\Http\Resources\Admin\ArticleCategoryResource;
use App\Http\Resources\Admin\ArticleResource;
use App\Http\Resources\Admin\TagResource;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Tag;
use App\Services\Admin\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $result = (new ArticleService())->getData($request);
        return response()->json($result, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        $result['articleCategories'] = ArticleCategoryResource::collection(ArticleCategory::all());
        $result['tags'] = TagResource::collection(Tag::all());

        return response()->json($result, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request): ArticleResource
    {
        $article = (new ArticleService())->store($request->validated());
        return new ArticleResource($article->load('page', 'category', 'tags', 'media'));
    }

    /**
     * Display the specified resource.
     *
     * @param Article $article
     * @return ArticleResource
     */
    public function show(Article $article)
    {
        return new ArticleResource($article->load('page', 'category', 'tags', 'media'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Article $article
     * @return JsonResponse
     */
    public function edit(Article $article)
    {
        $result['articleCategories'] = ArticleCategoryResource::collection(ArticleCategory::all());
        $result['tags'] = TagResource::collection(Tag::all());

        return response()->json($result, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateArticleRequest $request
     * @param Article $article
     * @return ArticleResource
     */
    public function update(UpdateArticleRequest $request, Article $article)
    {
        $article = (new ArticleService())->update($request->validated(), $article);
        return new ArticleResource($article->load('page', 'category', 'tags', 'media'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Article $article
     * @return JsonResponse
     */
    public function destroy(Article $article)
    {
        $article->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ], 200);
    }
}
