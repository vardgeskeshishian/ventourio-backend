<?php

namespace App\Services\Admin;

use App\Http\Resources\Admin\RoleResource;
use App\Models\Role;

class RoleService
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
        $roles = Role::orderBy('id', "desc");

        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 8;
        $count = $roles->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $roles = $roles->take($take)->skip($skip);
        } else {
            $roles = $roles->take($take)->skip(0);
        }

        return [
            'data' => RoleResource::collection($roles->with('permissions')->get()),
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
