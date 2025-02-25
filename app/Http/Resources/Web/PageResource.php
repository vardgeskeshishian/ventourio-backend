<?php

namespace App\Http\Resources\Web;

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
            'heading_title' => $this->heading_title,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'content' => $this->content,
            'view_count' => $this->view_count,
            'infoBlocks' => InfoBlocksResource::collection($this->whenLoaded('infoBlocks')),
            'qa' => QAResource::collection($this->whenLoaded('qa'))
        ];
    }
}
