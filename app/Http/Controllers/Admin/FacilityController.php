<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Facility\StoreRequest;
use App\Http\Requests\Admin\Facility\UpdateRequest;
use App\Http\Resources\Admin\FacilityCategoryResource;
use App\Http\Resources\Admin\FacilityResource;
use App\Models\Facility;
use App\Models\FacilityCategory;
use App\Services\Admin\FacilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FacilityController extends Controller
{
    /** Display a listing of the resource. */
    public function index(Request $request): JsonResponse
    {
        $result = (new FacilityService())->getData($request);

        return response()->json($result);
    }

    /** Show the form for creating a new resource. */
    public function create(): AnonymousResourceCollection
    {
        $facilitiesCategories = FacilityCategory::all();
        return FacilityCategoryResource::collection($facilitiesCategories);
    }

    /** Store a newly created resource in storage. */
    public function store(StoreRequest $request): FacilityResource
    {
        $facility = (new FacilityService())->store($request->validated());

        return FacilityResource::make($facility->load('category'));
    }

    /** Display the specified resource. */
    public function show(Facility $facility): FacilityResource
    {
        return FacilityResource::make($facility->load('media', 'category'));
    }

    /** Show the form for editing the specified resource. */
    public function edit(Facility $facility): JsonResponse
    {
        $result['facility'] = FacilityResource::make($facility);
        $result['facilityCategories'] = FacilityCategoryResource::collection(FacilityCategory::all());

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /** Update the specified resource in storage. */
    public function update(UpdateRequest $request, Facility $facility): FacilityResource
    {
        $facility = (new FacilityService())->update($request->validated(), $facility);

        return new FacilityResource($facility->load('category'));
    }

    /** Remove the specified resource from storage. */
    public function destroy(Facility $facility): JsonResponse
    {
        $facility->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ]);
    }
}
