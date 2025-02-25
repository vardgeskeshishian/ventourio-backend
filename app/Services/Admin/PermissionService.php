<?php

namespace App\Services\Admin;

use App\Http\Resources\Admin\PermissionResource;
use App\Models\Permission;

class PermissionService
{

    public function __construct()
    {

    }

    /**
     * @param $request
     * @return array
     */
    public function getData($request)
    {
        $permissions = Permission::orderBy('id', "desc");

        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 8;
        $count = $permissions->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $permissions = $permissions->take($take)->skip($skip);
        } else {
            $permissions = $permissions->take($take)->skip(0);
        }

        return [
            'data' => PermissionResource::collection($permissions->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function destroy($id)
    {
        return [];
    }

}
