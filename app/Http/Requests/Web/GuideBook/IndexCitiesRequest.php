<?php

namespace App\Http\Requests\Web\GuideBook;

use Illuminate\Foundation\Http\FormRequest;

class IndexCitiesRequest extends FormRequest
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
            'search' => 'nullable|string|min:1'
        ];
    }
}
