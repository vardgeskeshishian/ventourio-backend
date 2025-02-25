<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArticleCategory\StoreArticleCategoryRequest;
use App\Http\Requests\Admin\ArticleCategory\UpdateArticleCategoryRequest;
use App\Http\Resources\Admin\ArticleCategoryResource;
use App\Models\ArticleCategory;
use App\Services\Admin\ArticleCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArticleCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $result = (new ArticleCategoryService())->getData($request);
        return response()->json($result, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return AnonymousResourceCollection
     */
    public function create(): AnonymousResourceCollection
    {
        throw new \Exception('not implemented');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreArticleCategoryRequest $request
     * @return ArticleCategoryResource
     */
    public function store(StoreArticleCategoryRequest $request): ArticleCategoryResource
    {
        $articleCategory = (new ArticleCategoryService())->store($request->validated());
        return new ArticleCategoryResource($articleCategory);
    }

    /**
     * Display the specified resource.
     *
     * @param ArticleCategory $articleCategory
     * @return ArticleCategoryResource
     */
    public function show(ArticleCategory $articleCategory): ArticleCategoryResource
    {
        return new ArticleCategoryResource($articleCategory);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ArticleCategory $articleCategory
     * @return void
     */
    public function edit(ArticleCategory $articleCategory)
    {
        throw new \Exception('not implemented');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateArticleCategoryRequest $request
     * @param ArticleCategory $articleCategory
     * @return ArticleCategoryResource
     */
    public function update(UpdateArticleCategoryRequest $request, ArticleCategory $articleCategory): ArticleCategoryResource
    {
        $articleCategory = (new ArticleCategoryService())->update($request->validated(), $articleCategory);
        return new ArticleCategoryResource($articleCategory);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ArticleCategory $articleCategory
     * @return JsonResponse
     */
    public function destroy(ArticleCategory $articleCategory): JsonResponse
    {
        $articleCategory->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ], 200);
    }
}
