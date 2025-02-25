<?php

namespace App\Http\Resources\Admin;

use App\Models\Region;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Region */
class RegionResource  extends JsonResource
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
            'title_l' => $this->getTranslations('title_l'),
            'country_id' => $this->country_id,
            'is_common' => $this->is_common,
            'country' => new CountryResource($this->whenLoaded('country')),
            'cities' => CityResource::collection($this->whenLoaded('cities'))
        ];
    }
}
