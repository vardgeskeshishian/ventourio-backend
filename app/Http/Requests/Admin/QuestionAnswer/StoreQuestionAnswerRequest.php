<?php

namespace App\Http\Requests\Admin\QuestionAnswer;

use App\Rules\JsonUniqueTitle;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionAnswerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'answer_l' => 'required',
            'question_l' => 'required',
            'page_id' => 'required|integer|exists:App\Models\Page,id',
        ];
    }
}
