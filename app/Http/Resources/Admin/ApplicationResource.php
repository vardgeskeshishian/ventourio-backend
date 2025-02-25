<?php

namespace App\Http\Resources\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class ApplicationResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'type'  => $this->type,
            'name'  => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'body'  => $this->body,
            'created_at' => $this->created_at,
        ];
    }
}
