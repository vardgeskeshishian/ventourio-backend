<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\District\StoreRequest;
use App\Http\Requests\Admin\District\UpdateRequest;
use App\Http\Resources\Admin\CityResource;
use App\Http\Resources\Admin\DistrictResource;
use App\Models\City;
use App\Models\District;
use App\Services\Admin\DistrictService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DistrictController extends Controller
{
    /** Display a listing of the resource. */
    public function index(Request $request): JsonResponse
    {
        $result = (new DistrictService())->getData($request);
        return response()->json($result);
    }

    /** Show the form for creating a new resource. */
    public function create(): AnonymousResourceCollection
    {
        $cities = City::all(['id', 'title_l']);
        return CityResource::collection($cities);
    }

    /** Store a newly created resource in storage. */
    public function store(StoreRequest $request): DistrictResource
    {
        $district = District::create($request->validated());
        return new DistrictResource($district);
    }

    /** Display the specified resource. */
    public function show(District $district): DistrictResource
    {
        return new DistrictResource($district->load('city'));
    }

    /** Show the form for editing the specified resource. */
    public function edit(District $district): AnonymousResourceCollection
    {
        $cities = City::all(['id', 'title_l']);
        return CityResource::collection($cities);
    }

    /** Update the specified resource in storage. */
    public function update(UpdateRequest $request, District $district): DistrictResource
    {
        $district->update($request->validated());
        return new DistrictResource($district->load('city'));
    }

    /** Remove the specified resource from storage. */
    public function destroy(District $district): JsonResponse
    {
        $district->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ]);
    }
}
