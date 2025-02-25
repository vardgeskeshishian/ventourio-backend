<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Web\User\EmailCheckRequest;
use App\Services\Web\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(public AuthService $service) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $token = $this->service->registerAndGetToken($request->validated());

        return response()->json([
            'success' => 'true',
            'data' => [
                'token' => $token
            ],
            'message' => __('auth.register')
        ]);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->service->login($request->validated());

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token
            ],
            'message' => __('auth.login')
        ]);
    }

    public function logout(): JsonResponse
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => __('auth.logout'),
        ]);
    }

    public function checkEmail(EmailCheckRequest $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => __('success.common.success'),
        ]);
    }
}
