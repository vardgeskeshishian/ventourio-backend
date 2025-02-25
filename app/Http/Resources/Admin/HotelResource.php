<?php

namespace App\Http\Resources\Admin;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Hotel */
class HotelResource extends JsonResource
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
            'district_id' => $this->district_id,
            'address' => $this->address,
            'fax' => $this->fax,
            'geo' => $this->geo,
            'phone' => $this->phone,
            'stars' => $this->stars,
            'giata_code' => $this->giata_code,
            'is_apartment' => $this->is_apartment,
            'external_code' => $this->external_code,
            'discount_id' => $this->discount_id,

            'house_rules' => $this->house_rules,
            'discount' => new DiscountResource($this->whenLoaded('discount')),
            'district' => new DistrictResource($this->whenLoaded('district')),
            'media' =>  MediaResource::collection($this->whenLoaded('media')),
            'facilities' => FacilityResource::collection($this->whenLoaded('facilities'))
        ];
    }
}
