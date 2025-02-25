<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Gender;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\IndexRequest;
use App\Http\Requests\Admin\User\StoreRequest;
use App\Http\Requests\Admin\User\UpdateRequest;
use App\Http\Resources\Admin\CountryResource;
use App\Http\Resources\Admin\UserResource;
use App\Models\Country;
use App\Models\User;
use App\Services\Admin\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /** Display a listing of the resource. */
    public function index(IndexRequest $request): JsonResponse
    {
        $result = (new UserService())->index($request->validated());

        return response()->json($result);
    }

    /** Show the form for creating a new resource. */
    public function create(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'genders' => array_combine(Gender::types(), Gender::types()),
                'countries' => CountryResource::collection(Country::all()),
            ]
        ]);
    }

    /** Store a newly created resource in storage. */
    public function store(StoreRequest $request): JsonResponse
    {
        $user = (new UserService())->store($request->validated());

        return response()->json([
            'success' => true,
            'data' => new UserResource($user->load('country'))
        ]);
    }

    /** Display the specified resource. */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($user->load(['bookings', 'usedCertificates', 'country']))
        ]);
    }

    /** Show the form for editing the specified resource. */
    public function edit(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'genders' => array_combine(Gender::types(), Gender::types()),
                'countries' => CountryResource::collection(Country::all()),
            ]
        ]);
    }

    /** Update the specified resource in storage. */
    public function update(UpdateRequest $request, User $user): JsonResponse
    {
        (new UserService())->update($request->validated(), $user);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user->load('country'))
        ]);
    }

    /** Remove the specified resource from storage. */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ]);
    }
}
