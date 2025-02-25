<?php

namespace App\Http\Requests\Web\Location;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
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
            'continent' => 'nullable|string',
            'country' => 'nullable|string',
            'without_continents' => 'nullable|boolean',
            'with_page' => 'nullable|boolean'
        ];
    }
}
