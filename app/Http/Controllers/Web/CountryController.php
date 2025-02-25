<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Country\DefineRequest;
use App\Http\Resources\Web\CountryResource;
use App\Services\Web\Country\Service;
use Exception;
use Illuminate\Http\JsonResponse;

class CountryController extends Controller
{
    public function indexNationalities()
    {
        $nationalities = (new Service())->getNationalityListForSelect();

        return response()->json([
            'success' => true,
            'data' => CountryResource::collection($nationalities)
        ]);
    }

    /**
     * @throws Exception
     */
    public function define(DefineRequest $request): JsonResponse
    {
        $ip = $request->ip();
        if (empty($ip)) {
            throw new Exception(__('errors.app.common.no_ip'));
        }

        $country = (new Service())->define($ip);

        return response()->json([
            'success' => true,
            'data' => CountryResource::make($country)
        ]);
    }
}
