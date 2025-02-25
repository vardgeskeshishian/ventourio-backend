<?php

namespace App\Http\Resources\Admin;

use App\Models\Admin;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Role;

/** @mixin Admin */
class AdminResource extends JsonResource
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
            'email' => $this->email,
            'created_at' => $this->created_at,
            'admin_roles' => $this->roles,
            'roles' => !empty($request->admin) ? Role::all(['id','name']) : [],
        ];
    }

}
