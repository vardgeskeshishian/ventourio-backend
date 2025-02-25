<?php

namespace App\Http\Resources\Web;

use App\Models\Article;
use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Article */
class ArticleSlidesResource  extends JsonResource
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
            'author' => $this->author,
            'category' => $this->category,
            'created_at' => now()->parse($this->created_at)->format('d.m.Y'),
            'media' => MediaResource::make($this->whenLoaded('media', $this->getFirstMedia())),
            'page' => new PageResource($this->whenLoaded('page')),
        ];
    }
}
