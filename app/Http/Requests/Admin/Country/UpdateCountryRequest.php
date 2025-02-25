<?php

namespace App\Http\Requests\Admin\Country;

use App\Models\Country;
use App\Rules\JsonUniqueTitle;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCountryRequest extends FormRequest
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
        $model = $this->country;

        return [
            'title_l'       => ['required', new JsonUniqueTitle($model, $model?->id)],
            'nationality_l' => 'nullable|array',
            'geography_l'   => 'nullable|array',
            'media' => 'nullable|array|max:3',
            'media.*' => 'required_with:media|image',
            'description_l' => 'nullable|array',
            'article_l'     => 'nullable|array',
            'continent_id'  => 'nullable|integer|exists:continents,id',
            'iso_code'      => 'nullable|unique:countries,iso_code,' . $model?->id,
            'external_code' => 'nullable|unique:countries,external_code,' . $model?->id,
            'geo'           => 'nullable|array|required_array_keys:latitude,longitude',
        ];
    }
}
