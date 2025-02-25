<?php

namespace App\Http\Controllers\Admin;

use App\Models\Country;
use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\RegionService;
use App\Http\Resources\Admin\RegionResource;
use App\Http\Resources\Admin\CountryResource;
use App\Http\Requests\Admin\Region\StoreRegionRequest;
use App\Http\Requests\Admin\Region\UpdateRegionRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RegionController extends Controller
{
    /** Display a listing of the resource. */
    public function index(Request $request): JsonResponse
    {
        $result = (new RegionService())->getData($request);
        return response()->json($result);
    }

    /** Show the form for creating a new resource. */
    public function create(): AnonymousResourceCollection
    {
        $countries = Country::all();
        return CountryResource::collection($countries);
    }

    /** Store a newly created resource in storage. */
    public function store(StoreRegionRequest $request): RegionResource
    {
        $region = Region::create($request->validated());
        return new RegionResource($region);
    }

    /** Display the specified resource. */
    public function show(Region $region): RegionResource
    {
        return new RegionResource($region->load('country'));
    }

    /** Show the form for editing the specified resource. */
    public function edit(Region $region): AnonymousResourceCollection
    {
        $countries = Country::all();
        return CountryResource::collection($countries);
    }

    /** Update the specified resource in storage. */
    public function update(UpdateRegionRequest $request, Region $region): RegionResource
    {
        $region->update($request->validated());
        return new RegionResource($region->load('country'));
    }

    /** Remove the specified resource from storage. */
    public function destroy(Region $region): JsonResponse
    {
        $region->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ]);
    }
}
