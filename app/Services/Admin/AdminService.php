<?php

namespace App\Services\Admin;

use App\Http\Resources\Admin\AdminResource;
use App\Models\Admin;

class AdminService
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
        $admin = Admin::orderBy('id', "desc");

        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 8;
        $count = $admin->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $admin = $admin->take($take)->skip($skip);
        } else {
            $admin = $admin->take($take)->skip(0);
        }

        return [
            'data' => AdminResource::collection($admin->with('roles')->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }

    public function store($request)
    {
        $userData = $request->only('name', 'email', 'password');

        $roles = $request->only('roles');

        $admin = Admin::create($userData);

        $admin->syncRoles($roles);

        return $admin;

    }

    public function update($request, $admin)
    {
        $userData = $request->only('name', 'email', 'password');

        $roles = $request->only('roles');

        $admin->update($userData);

        $admin->syncRoles($roles);

        return $admin;

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
