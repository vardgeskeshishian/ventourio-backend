<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use App\Models\Page;
use App\Services\Web\ArticleCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PHPUnit\Util\Json;

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
        //TODO
    }


    /**
     * Display the specified resource.
     *
     * @param ArticleCategory $articleCategory
     * @return JsonResponse
     */
    public function show(Request $request, $slug): JsonResponse
    {
        $result = (new ArticleCategoryService())->show($request, $slug);
        return response()->json($result, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Page $page
     * @return JsonResponse
     */
    public function articles(Request $request, Page $page): JsonResponse
    {
        $result = (new ArticleCategoryService())->articles($request, $page->instance);
        return response()->json($result, 200);
    }
}
