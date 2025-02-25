<?php

namespace App\Http\Resources\Web;

use App\Models\Article;
use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

class InstanceResource  extends JsonResource
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
            'geo' => $this->geo,
            'title' => $this->title,
            'description' => $this->description,
            'geography' => $this->geography,
            'article' => $this->article,
            'media' => MediaResource::collection($this->whenLoaded('media', $this->getMedia('default')->take(3))),
            'slider' => MediaResource::collection($this->whenLoaded('media', $this->getMedia('slider'))),
        ];
    }

}
