<?php

namespace App\Http\Resources\Admin;

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
            'title_l' => $this->getTranslations('title_l'),
            'type' => $this->type,
            'code' => $this->code,
            'flag' => new MediaResource($this->whenLoaded('media', $this->getFirstMedia('flag'))),
            'countries' => CountryResource::collection($this->whenLoaded('countries')),
            'localization_json' => $this->localization_json ,
        ];
    }
}
