<?php

namespace App\Http\Resources\Admin;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Booking */
class BookingResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' =>$this->id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'provider' => $this->provider,
            'external_code' => $this->external_code,
            'search_code' => $this->search_code,
            'arrival_date' => $this->arrival_date,
            'departure_date' => $this->departure_date,
            'cancel_deadline' => $this->cancel_deadline,
            'paid_at' => $this->paid_at,
            'is_paid' => $this->is_paid,
            'extra' => $this->extra,
            'hotel' => new HotelResource($this->whenLoaded('hotel')),
            'user' => new UserResource($this->whenLoaded('user')),
            'externalPaymentMethod' => new ExternalPaymentMethodResource($this->whenLoaded('externalPaymentMethod')),
        ];
    }
}
