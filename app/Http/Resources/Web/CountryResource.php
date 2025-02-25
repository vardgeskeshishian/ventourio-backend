<?php

namespace App\Http\Resources\Web;

use App\Http\Resources\Admin\ContinentResource;
use App\Http\Resources\Admin\RegionResource;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Country */
class CountryResource extends JsonResource
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
            'title' => $this->title,
            'nationality' => $this->nationality,
            'iso_code' => $this->iso_code,
            'external_code' => $this->external_code,
            'continent_id' => $this->continent_id,
            'geo' => $this->geo,
            'description' => $this->description,
            'geography' => $this->geography,
            'article' => $this->article,
            'page'  => new PageResource($this->whenLoaded('page')),
            'continent' => new ContinentResource($this->whenLoaded('continent')),
            'regions' => RegionResource::collection($this->whenLoaded('regions')),
            'media' => MediaResource::make($this->whenLoaded('media', $this->getFirstMedia())),
            'flag' => $this->flag?->original_url,
        ];
    }
}
