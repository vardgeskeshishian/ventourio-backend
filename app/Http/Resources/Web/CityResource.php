<?php

namespace App\Http\Resources\Web;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin City */
class CityResource extends JsonResource
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
            'geo' => $this->geo,
            'title' => $this->title,
            'description' => $this->description,
            'geography' => $this->geography,
            'article' => $this->article,
            'page'  => new PageResource($this->whenLoaded('page')),
            'media' => MediaResource::make($this->whenLoaded('media', $this->getFirstMedia())),
        ];
    }
}
