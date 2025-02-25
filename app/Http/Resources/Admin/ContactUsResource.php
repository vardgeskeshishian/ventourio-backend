<?php

namespace App\Http\Resources\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class ContactUsResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'email' => $this->email,
            'body'  => $this->body,
            'created_at' => $this->created_at,
            'company_service' => new CompanyServiceResource($this->whenLoaded('company_service')),
        ];
    }
}
