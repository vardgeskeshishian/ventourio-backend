<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FacilityCategory\StoreFacilityCategoryRequest;
use App\Http\Requests\Admin\FacilityCategory\UpdateFacilityCategoryRequest;
use App\Http\Resources\Admin\FacilityCategoryResource;
use App\Http\Resources\Admin\FacilityResource;
use App\Models\Facility;
use App\Models\FacilityCategory;
use App\Services\Admin\FacilityCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FacilityCategoryController extends Controller
{
    /** Display a listing of the resource. */
    public function index(Request $request): JsonResponse
    {
        $result = (new FacilityCategoryService())->getData($request);

        return response()->json($result);
    }

    /** Show the form for creating a new resource. */
    public function create(): AnonymousResourceCollection
    {
        throw new \Exception('Not implemented');
    }

    /** Store a newly created resource in storage. */
    public function store(StoreFacilityCategoryRequest $request): FacilityCategoryResource
    {
        $facilityCategory = (new FacilityCategoryService())->store($request->validated());

        return FacilityCategoryResource::make($facilityCategory);
    }

    /** Display the specified resource. */
    public function show(FacilityCategory $facilityCategory): FacilityCategoryResource
    {
        return FacilityCategoryResource::make($facilityCategory);
    }

    /** Show the form for editing the specified resource. */
    public function edit(FacilityCategory $facilityCategory): JsonResponse
    {
        $result['facilityCategory'] = FacilityCategoryResource::make($facilityCategory);
        $result['facilities'] = FacilityResource::collection(Facility::all());

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /** Update the specified resource in storage. */
    public function update(UpdateFacilityCategoryRequest $request, FacilityCategory $facilityCategory): FacilityCategoryResource
    {
        $facilityCategory = (new FacilityCategoryService())->update($request->validated(), $facilityCategory);

        return new FacilityCategoryResource($facilityCategory);
    }

    /** Remove the specified resource from storage. */
    public function destroy(FacilityCategory $facilityCategory): JsonResponse
    {
        $facilityCategory->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ]);
    }
}
