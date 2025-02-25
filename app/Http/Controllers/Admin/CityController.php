<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;

use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\CityService;
use App\Http\Resources\Admin\CityResource;
use App\Http\Resources\Admin\RegionResource;
use App\Http\Requests\Admin\City\StoreCityRequest;
use App\Http\Requests\Admin\City\UpdateCityRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CityController extends Controller
{
    /** Display a listing of the resource. */
    public function index(Request $request): JsonResponse
    {
        $result = (new CityService())->getData($request);
        return response()->json($result, 200);
    }

    /** Show the form for creating a new resource. */
    public function create(): AnonymousResourceCollection
    {
        $regions = Region::all(['id', 'title_l']);
        return RegionResource::collection($regions);
    }

    /** Store a newly created resource in storage. */
    public function store(StoreCityRequest $request): CityResource
    {
        $city =  (new CityService())->store($request->validated());
        return new CityResource($city);
    }

    /** Display the specified resource. */
    public function show(City $city): CityResource
    {
        return new CityResource($city->load('region'));
    }

    /** Show the form for editing the specified resource. */
    public function edit(City $city): AnonymousResourceCollection
    {
        $regions = Region::all(['id', 'title_l']);
        return RegionResource::collection($regions);
    }

    /** Update the specified resource in storage. */
    public function update(UpdateCityRequest $request, City $city): CityResource
    {
        $city = (new CityService())->update($request->validated(), $city);
        return new CityResource($city->load('region'));
    }

    /** Remove the specified resource from storage. */
    public function destroy(City $city): JsonResponse
    {
        $city->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ]);
    }
}
