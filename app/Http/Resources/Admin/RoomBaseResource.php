<?php

namespace App\Http\Resources\Admin;

use App\Models\RoomBase;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin RoomBase */
class RoomBaseResource  extends JsonResource
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
            'title_l' => $this->getTranslations('title_l'),
            'basis' => $this->basis,
            'room_type_id' => $this->room_type_id,
            'refundable' => $this->refundable,
            'cancel_range' => $this->cancel_range,
            'booking_range' => $this->booking_range,
            'booking_max_term' => $this->booking_max_term,
            'price' => $this->price,
            'base_price' => $this->base_price,
            'adults_count' => $this->adults_count,
            'children_count' => $this->children_count,
            'remark_l' => $this->getTranslations('remark_l'),
            'discount_id' => $this->discount_id,

            'discount' => new DiscountResource($this->whenLoaded('discount')),
            'roomType' => new RoomTypeResource($this->whenLoaded('roomType')),
        ];
    }
}
