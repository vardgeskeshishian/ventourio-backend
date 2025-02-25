<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Web\PopularDestinationsService;
use Illuminate\Http\JsonResponse;

class PopularDestinationsController extends Controller
{
    public function index(): JsonResponse
    {
        $result = (new PopularDestinationsService())->index();

        return response()->json([
            'success' => true,
            'data' => $result
        ], 200);
    }
}
