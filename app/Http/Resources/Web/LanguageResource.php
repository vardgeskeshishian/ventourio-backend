<?php

namespace App\Http\Resources\Web;

use App\Http\Resources\Admin\MediaResource;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Language */
class LanguageResource extends JsonResource
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
            'type' => $this->type,
            'code' => $this->code,
            'flag' => $this->flag ? $this->getFirstMedia('flag')?->getFullUrl() : null,
            'countries' => $this->whenLoaded('countries'),
            'localization_json' => $this->localization_json,
        ];
    }
}
