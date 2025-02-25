<?php

namespace App\Http\Resources\Admin;

use App\Models\CompanyService;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin CompanyService */
class CompanyServiceResource extends JsonResource
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
            'description_l' => $this->getTranslations('description_l'),
            'page' => new PageResource($this->whenLoaded('page')),
            'icon' => $this->getFirstMediaUrl('icon'),
            'image' => $this->getFirstMediaUrl('image'),
        ];
    }
}
