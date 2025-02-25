<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\PermissionResource;
use App\Models\Role;
use App\Models\Permission;
use App\Http\Requests\Admin\RoleRequest;
use App\Http\Resources\Admin\RoleResource;
use App\Services\Admin\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $result = (new RoleService())->getData($request);
        return response()->json($result, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function create()
    {
        $permissions = Permission::all(['id', 'name']);
        return PermissionResource::collection($permissions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return RoleResource
     */
    public function store(RoleRequest $request)
    {

        $roleData = $request->only('name');

        $permissions = $request->only('permission');

        $role = Role::create($roleData);

        $role->syncPermissions($permissions);

        return new RoleResource($role);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return RoleResource
     */
    public function show(Role $role)
    {
        return new RoleResource($role);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return RoleResource
     */
    public function update(RoleRequest $request, Role $role)
    {

        $roleData = $request->only('name');

        $permissions = $request->only('permission');

        $role->update($roleData);

        $role->syncPermissions($permissions);

        return new RoleResource($role);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json([
            'status' => true,
            'message' => 'Page Deleted successfully!'
        ], 200);
    }
}
