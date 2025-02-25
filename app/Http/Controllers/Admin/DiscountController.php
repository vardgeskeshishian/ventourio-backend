<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DiscountType;
use App\Enums\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Discount\IndexRequest;
use App\Http\Requests\Admin\Discount\StoreRequest;
use App\Http\Requests\Admin\Discount\UpdateRequest;
use App\Http\Resources\Admin\DiscountResource;
use App\Models\Discount;
use App\Services\Admin\DiscountService;
use Illuminate\Http\JsonResponse;

class DiscountController extends Controller
{
    public function index(IndexRequest $request): JsonResponse
    {
        $result = (new DiscountService())->index($request->validated());

        return response()->json($result);
    }

    public function create(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                 'types' => array_combine(Helper::toArray(DiscountType::cases()), ['Percent', 'Subtract']),
            ]
        ]);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new DiscountResource(
                (new DiscountService())->store($request->validated())
            )
        ]);
    }

    public function show(Discount $discount): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new DiscountResource($discount)
        ]);
    }

    public function edit(Discount $discount): JsonResponse
    {
        return $this->create();
    }

    public function update(UpdateRequest $request, Discount $discount): JsonResponse
    {
        (new DiscountService())->update($request->validated(), $discount);

        return response()->json([
            'success' => true,
            'data' => new DiscountResource($discount)
        ]);
    }

    public function destroy(Discount $discount): JsonResponse
    {
        $discount->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ]);
    }
}
