<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\City\SearchRequest;
use App\Services\Web\City\Service;

class CityController extends Controller
{
    public function search(SearchRequest $request)
    {
        $result = (new Service())->search($request->validated());

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
}
