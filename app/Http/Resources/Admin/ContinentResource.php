<?php

namespace App\Http\Resources\Admin;

use App\Models\Continent;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Continent */
class ContinentResource extends JsonResource
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
            'countries' => CountryResource::collection($this->whenLoaded('countries'))
        ];
    }
}
