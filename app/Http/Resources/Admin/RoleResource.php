<?php

namespace App\Http\Resources\Admin;

use App\Models\Role;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Permission;

/** @mixin Role */
class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' =>$this->id,
            'name' => $this->name,
            'role_permissions' => $this->permissions->pluck('id'),
            'permissions' => Permission::all(['id', 'name']),
        ];
    }
}
