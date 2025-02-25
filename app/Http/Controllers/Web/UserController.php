<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\User\MeRequest;
use App\Http\Requests\Web\User\UpdateRequest;
use App\Http\Resources\Web\UserResource;
use App\Services\Web\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function me(MeRequest $request): JsonResponse
    {
        $user = (new UserService)->data();

        return response()->json([
            'success' => true,
            'data' => UserResource::make($user)
        ]);
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        $user = (new UserService())->update($request->validated(), auth()->user());

        return response()->json([
            'success' => true,
            'data' => UserResource::make($user)
        ]);
    }
}
