<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Favorite\StoreFavoriteRequest;
use App\Services\Web\FavoriteService;
use Illuminate\Http\JsonResponse;

class UserFavoriteController extends Controller
{
    public function store(StoreFavoriteRequest $request): JsonResponse
    {

        $result = (new FavoriteService())->toggle($request->validated());

        return response()->json([
            'success' => true,
            'message' => __('success.favorite.stored')
        ]);
    }
}
