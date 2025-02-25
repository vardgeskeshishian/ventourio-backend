<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Admin\DiscountResource;
use App\Http\Resources\Admin\DistrictResource;
use App\Http\Resources\Admin\FacilityResource;

use App\Models\Discount;
use App\Models\District;
use App\Models\Facility;
use App\Models\Hotel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\HotelService;
use App\Http\Resources\Admin\HotelResource;
use App\Http\Requests\Admin\Hotel\StoreHotelRequest;
use App\Http\Requests\Admin\Hotel\UpdateHotelRequest;

class HotelController extends Controller
{
    /** Display a listing of the resource. */
    public function index(Request $request): JsonResponse
    {
        $result = (new HotelService())->getData($request);
        return response()->json($result, 200);
    }

    /** Show the form for creating a new resource. */
    public function create(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'districts' => DistrictResource::collection(District::all(['id', 'title_l'])),
                'facilities' => FacilityResource::collection(Facility::all('id', 'title_l')),
                'discounts' => DiscountResource::collection(Discount::active()->get()),
            ]
        ]);
    }

    /** Store a newly created resource in storage. */
    public function store(StoreHotelRequest $request): HotelResource
    {
        $hotel = (new HotelService())->store($request->validated());

        return new HotelResource($hotel->load('district','media','facilities'));
    }

    /** Display the specified resource. */
    public function show(Hotel $hotel): HotelResource
    {
        return new HotelResource($hotel->load('district','media','facilities'));
    }

    /** Show the form for editing the specified resource. */
    public function edit(Hotel $hotel): JsonResponse
    {
        return $this->create();
    }

    /** Update the specified resource in storage. */
    public function update(UpdateHotelRequest $request, Hotel $hotel): HotelResource
    {
        $hotel = (new HotelService())->update($request->validated(), $hotel);

        return new HotelResource($hotel->load('district', 'media','facilities'));
    }

    /** Remove the specified resource from storage. */
    public function destroy(Hotel $hotel): JsonResponse
    {
        $hotel->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ]);
    }
}
