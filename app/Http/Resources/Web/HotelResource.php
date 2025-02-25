<?php

namespace App\Http\Resources\Web;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Hotel */
class HotelResource extends JsonResource
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
            'title' => $this->title,
            'address' => $this->address,
            'page'  => new PageResource($this->whenLoaded('page')),
            'media' => MediaResource::make($this->whenLoaded('media', $this->getFirstMedia())),
        ];
    }
}
