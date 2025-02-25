<?php

namespace App\Http\Resources\Web;

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
            'title' => $this->title,
            'description' => $this->description,
            'icon' => $this->getFirstMediaUrl('icon'),
            'image' => $this->getFirstMediaUrl('image'),
            'page' => new PageResource($this->whenLoaded('page')),
        ];
    }
}
