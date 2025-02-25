<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Admin\ArticleCategoryResource;
use App\Http\Resources\Admin\PageResource;
use App\Models\Article;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Article */
class ArticleResource extends JsonResource
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
            'author_l' => $this->getTranslations('author_l'),
            'quote_l' => $this->getTranslations('quote_l'),
            'content_l' => $this->content_l,
            'article_category_id' => $this->article_category_id,
            'media' => new MediaResource($this->whenLoaded('media', $this->getFirstMedia())),
            'avatar' => new MediaResource($this->whenLoaded('media', $this->getFirstMedia('avatar'))),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'page' => new PageResource($this->whenLoaded('page')),
            'article_category' => ArticleCategoryResource::make($this->whenLoaded('category'))
        ];
    }
}
