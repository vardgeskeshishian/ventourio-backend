<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Application\StoreRequest;
use App\Services\Web\ApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function __construct(private readonly ApplicationService $service) {}

    public function store(StoreRequest $request): JsonResponse
    {
        $this->service->store($request->validated());

        return response()->json([
            'success' => true,
            'message' => __('success.application.stored')
        ]);
    }
}
