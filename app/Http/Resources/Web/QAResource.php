<?php

namespace App\Http\Resources\Web;

use App\Models\QuestionAnswer;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin QuestionAnswer */
final class QAResource extends JsonResource
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
            'page_id' => $this->page_id ?? null,
            'question' => $this->question,
            'answer' => $this->answer,
        ];
    }
}
