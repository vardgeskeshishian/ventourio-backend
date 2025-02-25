<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CookieRequest;
use App\Http\Resources\Admin\CookieResource;
use App\Models\Cookie;
use App\Services\Admin\CookieService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CookieController extends Controller
{


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $result = (new CookieService())->getData($request->all());
        $result['data'] = CookieResource::collection($result['data']);
        return response()->json($result, 200);
    }

    /**
     * @param CookieRequest $request
     * @return CookieResource
     */
    public function store(CookieRequest $request)
    {
        $currencies = Cookie::create($request->validated());
        return new CookieResource($currencies);
    }

    /**
     * @param $id
     * @return CookieResource
     */
    public function show($id)
    {
        return new CookieResource(Cookie::findOrFail($id));
    }

    /**
     * @param CookieRequest $request
     * @param $id
     * @return CookieResource
     */
    public function update(CookieRequest $request, $id)
    {
        Cookie::where('id', $id)->update($request->validated());
        return $this->show($id);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $result = (new CookieService())->restore($id);
        return response()->json($result, 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $result = (new CookieService())->destroy($id);
        return response()->json($result, 200);
    }

}
