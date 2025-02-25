<?php

namespace App\Http\Resources\Admin;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Page */
class PageResource extends JsonResource
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
            'id' =>$this->id,
            'slug' => $this->slug,
            'type' => $this->type,
            'heading_title_l' => (object) $this->getTranslations('heading_title_l'),
            'meta_title_l' => $this->getTranslations('meta_title_l'),
            'meta_description_l' => $this->getTranslations('meta_description_l'),
            'content_l' => $this->getTranslations('content_l'),
            'infoBlocks' => InfoBlocksResource::collection($this->whenLoaded('infoBlocks')),
        ];
    }
}
