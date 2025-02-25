<?php

namespace App\Http\Controllers\Admin;

use App\Models\Roombase;

use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\RoomService;
use App\Http\Resources\Admin\RoomResource;
use App\Http\Resources\Admin\RoombaseResource;
use App\Http\Requests\Admin\Room\StoreRoomRequest;
use App\Http\Requests\Admin\Room\UpdateRoomRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoomController extends Controller
{
    /** Display a listing of the resource. */
    public function index(Request $request): JsonResponse
    {
        $result = (new RoomService())->getData($request);
        return response()->json($result);
    }

    /** Show the form for creating a new resource. */
    public function create(): AnonymousResourceCollection
    {
        $roomBases = Roombase::all();
        return RoombaseResource::collection($roomBases);
    }

    /** Store a newly created resource in storage. */
    public function store(StoreRoomRequest $request): RoomResource
    {
        $room = Room::create($request->validated());
        return new RoomResource($room);
    }

    /** Display the specified resource. */
    public function show(Room $room): RoomResource
    {
        return new RoomResource($room->load('roomBase'));
    }

    /** Show the form for editing the specified resource. */
    public function edit(Room $room)
    {
        $roomBases = Roombase::all();
        return RoombaseResource::collection($roomBases);
    }

    /** Update the specified resource in storage. */
    public function update(UpdateRoomRequest $request, Room $room)
    {
        $room->update($request->validated());
        return new RoomResource($room->load('roomBase'));
    }

    /** Remove the specified resource from storage. */
    public function destroy(Room $room): JsonResponse
    {
        $room->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ]);
    }
}
