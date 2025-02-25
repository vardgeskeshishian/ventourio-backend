<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Admin\DiscountResource;
use App\Http\Resources\Admin\RoomTypeResource;

use App\Models\Discount;
use App\Models\RoomBase;
use App\Models\RoomType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\RoomBaseService;
use App\Http\Resources\Admin\RoomBaseResource;
use App\Http\Requests\Admin\RoomBase\StoreRoomBaseRequest;
use App\Http\Requests\Admin\RoomBase\UpdateRoomBaseRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoomBaseController extends Controller
{
    /** Display a listing of the resource. */
    public function index(Request $request): JsonResponse
    {
        $result = (new RoomBaseService())->getData($request);
        return response()->json($result);
    }

    /** Show the form for creating a new resource. */
    public function create(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'room_types' => RoomTypeResource::collection(RoomType::all()),
                'discounts' => DiscountResource::collection(Discount::active()->get())
            ]
        ]);
    }

    /** Store a newly created resource in storage. */
    public function store(StoreRoomBaseRequest $request): RoomBaseResource
    {
        $roomBase = (new RoomBaseService())->store($request->validated());
        return new RoomBaseResource($roomBase->load('roomType', 'discount'));
    }

    /** Display the specified resource. */
    public function show(RoomBase $roomBase): RoomBaseResource
    {
        return new RoomBaseResource($roomBase->load('roomType', 'discount'));
    }

    /** Show the form for editing the specified resource. */
    public function edit(RoomBase $roomBase): AnonymousResourceCollection
    {
        $roomTypes = RoomType::all();
        return RoomTypeResource::collection($roomTypes);
    }

    /** Update the specified resource in storage. */
    public function update(UpdateRoomBaseRequest $request, RoomBase $roomBase): RoomBaseResource
    {
        (new RoomBaseService())->update($request->validated(), $roomBase);
        return new RoomBaseResource($roomBase->load('roomType', 'discount'));
    }

    /** Remove the specified resource from storage. */
    public function destroy(RoomBase $roomBase): JsonResponse
    {
        $roomBase->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ]);
    }
}
