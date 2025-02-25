<?php

namespace App\Http\Resources\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'country_id' => $this->country_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'gender' => $this->gender,
            'balance' => $this->balance,

            'bookings' => BookingResource::collection($this->whenLoaded('bookings')),
            'bought_gift_certificates' => CertificateResource::collection($this->whenLoaded('boughtCertificates')),
            'used_gift_certificates' => CertificateResource::collection($this->whenLoaded('usedCertificates')),
            'country' => new CountryResource($this->whenLoaded('country')),
        ];
    }
}
