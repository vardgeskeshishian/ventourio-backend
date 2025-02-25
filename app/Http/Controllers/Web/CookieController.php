<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\Web\CookieResource;
use App\Services\Admin\CookieService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CookieController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $result = (new CookieService())->getData($request->all());

        $result['data'] = CookieResource::collection($result['data']);

        return response()->json($result);
    }
}
