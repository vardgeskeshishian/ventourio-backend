<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Admin\HotelResource;
use App\Http\Resources\Web\UserResource;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Tag */
class TagResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title_l' => $this->getTranslations('title_l'),
            'color_hex' => $this->color_hex
        ];
    }
}
