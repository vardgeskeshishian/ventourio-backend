<?php

namespace App\Http\Resources\Web;

use App\Models\Article;
use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Article */
class ArticleResource  extends JsonResource
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
            'quote' => $this->quote,
            'content' => $this->makeContentOrReturnNull($this->content_l, $request['locale']),
            'created_at' => now()->parse($this->created_at)->format('d.m.Y'),
            'media' => MediaResource::make($this->whenLoaded('media', $this->getFirstMedia())),
            'avatar' => MediaResource::make($this->whenLoaded('media', $this->getFirstMedia('avatar'))),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'page' => new PageResource($this->whenLoaded('page')),
            'category' => new ArticleCategoryResource($this->whenLoaded('category')),
        ];
    }

    //TODO make some other logic
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
