<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentRequisite\IndexRequest;
use App\Http\Requests\Admin\PaymentRequisite\StoreRequest;
use App\Http\Requests\Admin\PaymentRequisite\UpdateRequest;
use App\Http\Resources\Admin\PaymentRequisiteResource;
use App\Models\PaymentRequisite;
use App\Services\Admin\PaymentRequisiteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentRequisiteController extends Controller
{
    public function index(IndexRequest $request): JsonResponse
    {
        $result = (new PaymentRequisiteService())->index($request->validated());

        return response()->json($result);
    }

    public function create(): JsonResponse
    {
        throw new \Exception('Not implemented', 501);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new PaymentRequisiteResource(
                (new PaymentRequisiteService())->store($request->validated())
            )
        ]);
    }

    public function show(PaymentRequisite $paymentRequisite): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new PaymentRequisiteResource($paymentRequisite)
        ]);
    }

    public function edit(PaymentRequisite $paymentRequisite): JsonResponse
    {
        return $this->create();
    }

    public function update(UpdateRequest $request, PaymentRequisite $paymentRequisite): JsonResponse
    {
        (new PaymentRequisiteService())->update($request->validated(), $paymentRequisite);

        return response()->json([
            'success' => true,
            'data' => new PaymentRequisiteResource($paymentRequisite)
        ]);
    }

    public function destroy(PaymentRequisite $paymentRequisite): JsonResponse
    {
        $paymentRequisite->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ]);
    }
}
