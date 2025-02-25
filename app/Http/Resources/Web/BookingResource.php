<?php

namespace App\Http\Resources\Web;

use App\Http\Resources\Admin\HotelResource;
use App\Http\Resources\Web\UserResource;
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
            'extra' => $this->extra,
            'status' => $this->status,
            'provider' => $this->provider,
            'search_code' => $this->search_code,
            'arrival_date' => $this->arrival_date,
            'external_code' => $this->external_code,
            'departure_date' => $this->departure_date,
            'cancel_deadline' => $this->cancel_deadline,
            'hotel' => new HotelResource($this->whenLoaded('hotel')),
            'user_id' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
