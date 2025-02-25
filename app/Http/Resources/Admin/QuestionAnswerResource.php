<?php

namespace App\Http\Resources\Admin;

use App\Models\QuestionAnswer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin QuestionAnswer */
class QuestionAnswerResource  extends JsonResource
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
            'page' => new PageResource($this->whenLoaded('page')),
            'answer_l' => $this->getTranslations('answer_l'),
            'question_l' => $this->getTranslations('question_l'),
        ];
    }
}
