<?php

namespace App\Http\Resources\Admin;

use App\Models\Country;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Country */
class CountryResource extends JsonResource
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
            'continent_id' => $this->continent_id,
            'title_l' => count($this->getTranslations('title_l')) ? $this->getTranslations('title_l') : ['en' => ''],
            'description_l' => count($this->getTranslations('description_l')) ? $this->getTranslations('description_l') : ['en' => ''],
            'geography_l' => count($this->getTranslations('geography_l')) ? $this->getTranslations('geography_l') : ['en' => ''] ,
            'article_l' => count($this->getTranslations('article_l')) ? $this->getTranslations('article_l') : ['en' => ''],
            'nationality_l' => count($this->getTranslations('nationality_l')) ? $this->getTranslations('nationality_l') : ['en' => ''],
            'iso_code' => $this->iso_code,
            'external_code' => $this->external_code,
            'geo' => $this->geo,
            'continent' => new ContinentResource($this->whenLoaded('continent')),
            'regions' => RegionResource::collection($this->whenLoaded('regions')),
            'media' => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
