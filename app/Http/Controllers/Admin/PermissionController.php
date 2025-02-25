<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Services\Admin\PermissionService;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\PermissionRequest;
use App\Http\Resources\Admin\PermissionResource;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $result = (new PermissionService())->getData($request);
        return response()->json($result, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return PermissionResource
     */
    public function store(PermissionRequest $request)
    {
        $permissions = Permission::create($request->validated());
        return new PermissionResource($permissions);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Permission  $permission
     * @return PermissionResource
     */
    public function show(Permission $permission)
    {
        return new PermissionResource($permission);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Permission  $permission
     * @return PermissionResource
     */
    public function update(PermissionRequest $request, Permission $permission)
    {
        $permission->update($request->validated());
        return new PermissionResource($permission);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return response()->json([
            'status' => true,
            'message' => 'Page Deleted successfully!'
        ], 200);
    }
}
