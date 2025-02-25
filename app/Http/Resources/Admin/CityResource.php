<?php

namespace App\Http\Resources\Admin;

use App\Models\City;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin City */
class CityResource  extends JsonResource
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
            'geo' => $this->geo,
            'region' => new RegionResource($this->whenLoaded('region')),
            'title_l' => count($this->getTranslations('title_l')) ? $this->getTranslations('title_l') : ['en' => ''],
            'external_code' => $this->external_code,
            'show_in_best_deals' => $this->show_in_best_deals,
            'description_l' => count($this->getTranslations('description_l')) ? $this->getTranslations('description_l') : ['en' => ''],
            'geography_l' => count($this->getTranslations('geography_l')) ? $this->getTranslations('geography_l') : ['en' => ''],
            'article_l' => count($this->getTranslations('article_l')) ? $this->getTranslations('article_l') : ['en' => ''],
            'media' => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
