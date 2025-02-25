<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ContinentResource */
class ContinentResource extends JsonResource
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
            'id'        => $this->id,
            'title'     => $this->title,
            'page'      => new PageResource($this->whenLoaded('page')),
            'cities'    => CityResource::collection($this->whenLoaded('cities')),
            'countries' => CountryResource::collection($this->whenLoaded('countries')),
        ];
    }
}
