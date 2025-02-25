<?php

namespace App\Http\Resources\Admin;

use App\Models\Facility;
use App\Models\FacilityCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Facility */
class FacilityResource extends JsonResource
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
            'image' => MediaResource::make($this->whenLoaded('media', $this->getFirstMedia())),
            'category' => FacilityCategoryResource::make($this->whenLoaded('category')),
        ];
    }
}
