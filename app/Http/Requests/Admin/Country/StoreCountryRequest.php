<?php

namespace App\Http\Requests\Admin\Country;

use App\Models\Country;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\JsonUniqueTitle;

class StoreCountryRequest extends FormRequest
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
            'title_l' => ['required', new JsonUniqueTitle(new Country)],
            'geography_l' => 'nullable|array',
            'media' => 'nullable|array|max:3',
            'media.*' => 'required_with:media|image',
            'description_l' => 'nullable|array',
            'article_l' => 'nullable|array',
            'nationality_l' => 'nullable|array',
            'continent_id' => 'nullable|integer|exists:continents,id',
            'iso_code' => 'nullable|unique:countries,iso_code',
            'external_code' => 'nullable|unique:countries,external_code',
            'geo' => 'nullable|array|required_array_keys:latitude,longitude',
        ];
    }
}
