<?php

namespace App\Http\Resources\Web;

use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ArticleCategory */
class ArticleCategoryResource  extends JsonResource
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
            'page' => new PageResource($this->whenLoaded('page')),
            'title' => $this->title,
            'color_hex' => $this->color_hex,
            'articles' => ArticleResource::collection($this->whenLoaded('articles')),
        ];
    }
}
