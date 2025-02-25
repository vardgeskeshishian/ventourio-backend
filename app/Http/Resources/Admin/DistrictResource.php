<?php

namespace App\Http\Resources\Admin;

use App\Models\District;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin District */
final class DistrictResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'city_id' => $this->city_id,
            'is_common' => $this->is_common,
            'city' => new CityResource($this->whenLoaded('city')),
            'title_l' => $this->getTranslations('title_l'),
        ];
    }
}
