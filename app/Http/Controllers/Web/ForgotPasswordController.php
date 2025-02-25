<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordReset\PasswordResetRequest;
use App\Http\Requests\PasswordReset\PasswordResetLinkRequest;
use App\Services\Web\PasswordResetService;
use Illuminate\Http\JsonResponse;

class ForgotPasswordController extends Controller
{
    public function __construct(public PasswordResetService $service) {}

    public function sendToEmail(PasswordResetLinkRequest $request): JsonResponse
    {
        $this->service->sendToEmail($request->validated());

        return response()->json([
            'success' => true,
            'message' => __('Send code to email')
        ]);
    }

    public function resetPassword(PasswordResetRequest $request): JsonResponse
    {
        if ($this->service->validateToken($request->validated())) {
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
