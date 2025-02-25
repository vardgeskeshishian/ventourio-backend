<?php

namespace App\Http\Resources\Web;

use App\Models\Cookie;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Cookie */
class CookieResource extends JsonResource
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
            'id' =>$this->id,
            'description' => $this->description,
            'title' => $this->title,
            'key' => $this->key,
            'status' => $this->deleted_at ? 'inactive' : 'active',
            'created_at' => $this->created_at,
        ];
    }
}
