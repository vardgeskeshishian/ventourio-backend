<?php

namespace App\Http\Resources\Web;

use App\Models\Article;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/** @mixin Article */
class RecommendedArticleResource  extends JsonResource
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
            'slug' => $this->slug,
            'created_at' => now()->parse($this->created_at)->format('d.m.Y'),
            'content' => $this->makeContentOrReturnNull($this->content_l, $request['locale']),
            'media' => MediaResource::make($this->whenLoaded('media', $this->getFirstMedia())),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'category' => new ArticleCategoryResource($this->whenLoaded('category')),
            'page' => new PageResource($this->whenLoaded('page'))
        ];
    }

    private function makeContentOrReturnNull(array|null $content, string $locale):Collection|null
    {
        if(empty($content)){
            return null;
        }

        return collect($content)->map(function ($block) use($locale){
            return [
                'title' => $block['title'][$locale],
                'body' => $block['body'][$locale]
            ];
        });
    }
}
