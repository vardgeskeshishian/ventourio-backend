<?php

namespace App\Http\Resources\Web;

use App\Models\Article;
use App\Models\Page;
use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Page */
class DestinationResource  extends JsonResource
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
            'slug' => $this->slug,
            'heading_title' => $this->heading_title,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'breadcrumbs' => $this->breadcrumbs,
            'instance' => InstanceResource::make($this->whenLoaded('instance')),
        ];
    }

}
