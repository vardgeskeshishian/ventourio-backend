<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\Web\ArticleService;
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

    /** Display the specified resource. */
    public function show(Request $request, $slug): JsonResponse
    {
        $result = (new ArticleService())->show($request, $slug);
        return response()->json($result, 200);
    }
}
