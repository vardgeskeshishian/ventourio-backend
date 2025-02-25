<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoomType\StoreRequest;
use App\Http\Requests\Admin\RoomType\UpdateRequest;
use App\Http\Resources\Admin\FacilityResource;
use App\Http\Resources\Admin\HotelResource;
use App\Http\Resources\Admin\RoomTypeResource;
use App\Models\Facility;
use App\Models\Hotel;
use App\Models\RoomType;
use App\Services\Admin\RoomTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoomTypeController extends Controller
{
    /** Display a listing of the resource. */
    public function index(Request $request): JsonResponse
    {
        $result = (new RoomTypeService())->getData($request);
        return response()->json($result);
    }

    /** Show the form for creating a new resource. */
    public function create(): JsonResponse
    {
        $data = [
            'hotels' => HotelResource::collection(Hotel::all(['id', 'title_l'])),
            'facilities' => FacilityResource::collection(Facility::all('id','title_l')),
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /** Store a newly created resource in storage. */
    public function store(StoreRequest $request): RoomTypeResource
    {
        $roomType = (new RoomTypeService())->store($request->validated());

        return new RoomTypeResource($roomType->load('hotel', 'facilities'));
    }

    /** Display the specified resource. */
    public function show(RoomType $roomType): RoomTypeResource
    {
        return new RoomTypeResource($roomType->load('hotel', 'facilities'));
    }

    /** Show the form for editing the specified resource. */
    public function edit(RoomType $roomType): JsonResponse
    {
        return $this->create();
    }

    /** Update the specified resource in storage. */
    public function update(UpdateRequest $request, RoomType $roomType): RoomTypeResource
    {
        $roomType = (new RoomTypeService())->update($request->validated(), $roomType);

        return new RoomTypeResource($roomType->load('hotel', 'facilities'));
    }

    /** Remove the specified resource from storage. */
    public function destroy(RoomType $roomType): JsonResponse
    {
        $roomType->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ]);
    }
}
