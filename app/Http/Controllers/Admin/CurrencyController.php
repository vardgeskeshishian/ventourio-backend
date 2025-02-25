<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CurrencyRequest;
use App\Http\Resources\Admin\CurrencyResource;
use App\Models\Currency;
use App\Services\Admin\CurrencyService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CurrencyController extends Controller
{


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $result = (new CurrencyService())->getData($request);
        return response()->json($result, 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getAll()
    {
        return CurrencyResource::collection(Currency::all());
    }

    /**
     * @param CurrencyRequest $request
     * @return CurrencyResource
     */
    public function store(CurrencyRequest $request)
    {
        $currencies = Currency::create($request->validated());
        return new CurrencyResource($currencies);
    }

    /**
     * @param $id
     * @return CurrencyResource
     */
    public function show($id)
    {
        return new CurrencyResource(Currency::findOrFail($id));
    }

    /**
     * @param CurrencyRequest $request
     * @param $id
     * @return CurrencyResource
     */
    public function update(CurrencyRequest $request, $id)
    {
        Currency::where('id', $id)->update($request->validated());
        return $this->show($id);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $result = (new CurrencyService())->restore($id);
        return response()->json($result, 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $result = (new CurrencyService())->destroy($id);
        return response()->json($result, 200);
    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function setIsMain($id)
    {
        $result = (new CurrencyService())->setIsMain($id);
        return response()->json($result, 200);
    }

}
