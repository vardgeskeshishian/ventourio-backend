<?php

namespace App\Http\Resources\Admin;

use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin RoomType */
final class RoomTypeResource extends JsonResource
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
            'hotel_id' => $this->hotel_id,
            'image' => new MediaResource($this->getFirstMedia()),
            'hotel' => new HotelResource($this->whenLoaded('hotel')),
            'facilities' => FacilityResource::collection($this->whenLoaded('facilities'))
        ];
    }
}
