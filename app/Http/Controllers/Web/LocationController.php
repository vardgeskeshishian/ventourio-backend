<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Location\SearchRequest;
use App\Services\Web\LocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function search(SearchRequest $request): JsonResponse
    {
        $result = (new LocationService())->search($request->validated());

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
}
