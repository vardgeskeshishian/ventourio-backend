<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Admin\ContinentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Continent;
use App\Http\Controllers\Controller;
use App\Services\Admin\CountryService;
use App\Http\Resources\Admin\CountryResource;
use App\Http\Requests\Admin\Country\StoreCountryRequest;
use App\Http\Requests\Admin\Country\UpdateCountryRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CountryController extends Controller
{
    /** Display a listing of the resource */
    public function index(Request $request): JsonResponse
    {
        $result = (new CountryService())->getData($request);
        return response()->json($result, 200);
    }

    /** Show the form for creating a new resource. */
    public function create(): AnonymousResourceCollection
    {
        $continents = Continent::all();
        return ContinentResource::collection($continents);
    }

    /** Store a newly created resource in storage. */
    public function store(StoreCountryRequest $request): CountryResource
    {
        $country = (new CountryService())->store($request->validated());
        return new CountryResource($country);
    }

    /** Display the specified resource. */
    public function show(Country $country): CountryResource
    {
        return new CountryResource($country->load('continent'));
    }

    /** Show the form for editing the specified resource. */
    public function edit(Country $country): JsonResponse
    {
        $result['continents'] = Continent::all();
        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /** Update the specified resource in storage. */
    public function update(UpdateCountryRequest $request, Country $country): CountryResource
    {
        $country = (new CountryService())->update($request->validated(), $country);
        return new CountryResource($country);
    }

    /** Remove the specified resource from storage. */
    public function destroy(Country $country): JsonResponse
    {
        $country->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ]);
    }
}
