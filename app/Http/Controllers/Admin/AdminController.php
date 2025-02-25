<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\RoleResource;
use App\Models\Admin;
use App\Models\Role;
use App\Services\Admin\AdminService;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\StoreAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Http\Resources\Admin\AdminResource;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $result = (new AdminService())->getData($request);
        return response()->json($result, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function create()
    {
        $roles = Role::with('permissions')->get(['id', 'name']);
        return RoleResource::collection($roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return AdminResource
     */
    public function store(StoreAdminRequest $request)
    {

        $result = (new AdminService())->store($request);

        return new AdminResource($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return AdminResource
     */
    public function show(Admin $admin)
    {
        return new AdminResource($admin);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return AdminResource
     */
    public function edit(Admin $admin)
    {
        return new AdminResource($admin);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return AdminResource
     */
    public function update(UpdateAdminRequest $request, Admin $admin)
    {

        $result = (new AdminService())->update($request, $admin);

        return new AdminResource($result);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Admin $admin)
    {
        $admin->delete();

        return response()->json([
            'status' => true,
            'message' => 'Page Deleted successfully!'
        ], 200);
    }
}
