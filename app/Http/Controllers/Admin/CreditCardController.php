<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CreditCardType;
use App\Enums\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreditCard\IndexRequest;
use App\Http\Requests\Admin\CreditCard\StoreRequest;
use App\Http\Requests\Admin\CreditCard\UpdateRequest;
use App\Http\Resources\Admin\CreditCardResource;
use App\Models\CreditCard;
use App\Services\Admin\CreditCardService;
use Illuminate\Http\JsonResponse;

class CreditCardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $result = (new CreditCardService())->index($request->validated());

        return response()->json($result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        $types = Helper::toArray(CreditCardType::cases());

        return response()->json([
            'success' => true,
            'data' => [
                'types' => array_combine($types, $types)
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return CreditCardResource
     */
    public function store(StoreRequest $request): CreditCardResource
    {
        $creditCard = (new CreditCardService())->store($request->validated());

        return CreditCardResource::make($creditCard);
    }

    /**
     * Display the specified resource.
     *
     * @param CreditCard $creditCard
     * @return CreditCardResource
     */
    public function show(CreditCard $creditCard): CreditCardResource
    {
        return CreditCardResource::make($creditCard);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param CreditCard $creditCard
     * @return JsonResponse
     */
    public function edit(CreditCard $creditCard): JsonResponse
    {
        return $this->create();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param CreditCard $creditCard
     * @return CreditCardResource
     */
    public function update(UpdateRequest $request, CreditCard $creditCard): CreditCardResource
    {
        throw new \Exception('not_implemented');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CreditCard $creditCard
     * @return JsonResponse
     */
    public function destroy(CreditCard $creditCard): JsonResponse
    {
        $creditCard->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ]);
    }
}
