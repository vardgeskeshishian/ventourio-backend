<?php

namespace App\Http\Resources\Admin;

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
            'title_l' => $this->getTranslations('title_l'),
            'page' => new PageResource($this->whenLoaded('page')),
            'color_hex' => $this->color_hex
        ];
    }
}
