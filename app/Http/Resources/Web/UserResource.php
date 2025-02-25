<?php

namespace App\Http\Resources\Web;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone,
            'balance' => $this->balance,
            'gender' => $this->gender,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'country_id' => $this->country_id,
            'avatar' => $this->avatar,
            'password_updated_at' => $this->password_updated_at,
            'country' => new CountryResource($this->whenLoaded('country')),
            'bookings' => BookingResource::collection($this->whenLoaded('bookings')),
            'bought_gift_certificates' => CertificateResource::collection($this->whenLoaded('boughtCertificates')),
            'used_gift_certificates' => CertificateResource::collection($this->whenLoaded('usedCertificates')),
            'favorites' => HotelResource::collection($this->whenLoaded('favorites')),
            'social_accounts' => UserSocialResource::collection($this->whenLoaded('socialAccounts'))
        ];
    }

}
