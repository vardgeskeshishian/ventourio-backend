<?php

namespace App\Http\Resources\Admin;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Room */
class RoomResource  extends JsonResource
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
            'room_base_id' => $this->room_base_id,
            'roomBase' => new RoomBaseResource($this->whenLoaded('roomBase')),
            'bookings' => new BookingResource($this->whenLoaded('bookings'))
        ];
    }
}
