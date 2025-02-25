<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;

use App\Models\Sight;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\SightService;
use App\Http\Resources\Admin\SightResource;
use App\Http\Resources\Admin\CityResource;
use App\Http\Requests\Admin\Sight\StoreSightRequest;
use App\Http\Requests\Admin\Sight\UpdateSightRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SightController extends Controller
{
    /** Display a listing of the resource. */
    public function index(Request $request): JsonResponse
    {
        $result = (new SightService())->getData($request);
        return response()->json($result, 200);
    }

    /** Show the form for creating a new resource. */
    public function create(): AnonymousResourceCollection
    {
        $cities = City::all(['id', 'title_l']);
        return CityResource::collection($cities);
    }

    /** Store a newly created resource in storage. */
    public function store(StoreSightRequest $request): SightResource
    {
        $sight = Sight::create($request->validated());
        return new SightResource($sight);
    }

    /** Display the specified resource. */
    public function show(Sight $sight): SightResource
    {
        return new SightResource($sight->load('city'));
    }

    /** Show the form for editing the specified resource. */
    public function edit(Sight $sight): AnonymousResourceCollection
    {
        $cities = City::all(['id', 'title_l']);
        return CityResource::collection($cities);
    }

    /** Update the specified resource in storage. */
    public function update(UpdateSightRequest $request, Sight $sight): SightResource
    {
        $sight->update($request->validated());
        return new SightResource($sight->load('city'));
    }

    /** Remove the specified resource from storage. */
    public function destroy(Sight $sight): JsonResponse
    {
        $sight->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ]);
    }
}
