<?php

namespace App\Http\Resources\Web;

use App\Models\InfoBlock;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin InfoBlock */
class InfoBlocksResource extends JsonResource
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
            'alias' => $this->alias,
            'content' => $this->content,
        ];
    }
}
