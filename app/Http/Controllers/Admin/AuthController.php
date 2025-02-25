<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RegisterRequest;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Requests\Admin\UpdatePassportRequest;
use App\Http\Requests\Admin\PasswordCodeCheckRequest;
use App\Http\Requests\Admin\PasswordResetRequest;
use App\Services\Admin\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(public AuthService $service) {}

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

    public function logout(Request $request): JsonResponse
    {

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'data' => [

            ],
        ]);
    }

    public function passwordUpdate(UpdatePassportRequest $request): JsonResponse
    {
        $request->user()->update($request->only(['password']));

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $request->user()
            ],
        ]);
    }

     public function sendToEmail(PasswordResetRequest $request): JsonResponse
     {
         $this->service->sendToEmail($request->validated());

         return response()->json([
             'success' => true,
             'message' => __('Send code to email')
         ]);
     }

     public function passwordCodeCheck(PasswordCodeCheckRequest $request): JsonResponse
     {
         if ($this->service->passwordCodeCheck($request->validated())) {
             $result = [
                 'success' => true,
                 'message' => __('passwords.reset')
             ];
         } else {
             $result = [
                 'success' => false,
                 'message' => __('passwords.token')
             ];
         }

         return response()->json($result);
     }
}
