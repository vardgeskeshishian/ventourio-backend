<?php

namespace App\Http\Resources\Admin;

use App\Models\Sight;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Sight */
class SightResource  extends JsonResource
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
            'city' => new CityResource($this->whenLoaded('city')),
            'title_l' => $this->getTranslations('title_l'),
        ];
    }
}
