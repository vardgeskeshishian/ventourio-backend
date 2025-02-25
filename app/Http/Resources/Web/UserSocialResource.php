<?php

namespace App\Http\Resources\Web;

use App\Models\UserSocial;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin UserSocial */
final class UserSocialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'provider' => $this->provider,
            'provider_id' => $this->provider_id,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
