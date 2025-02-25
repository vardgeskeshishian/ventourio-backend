<?php

namespace App\Http\Requests\Admin\City;

use App\Models\City;
use App\Rules\JsonUniqueTitle;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCityRequest extends FormRequest
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
        $model = $this->city;

        return [
            'title_l' => ['required', new JsonUniqueTitle($model, $model?->id)],
            'media' => 'nullable|array|max:3',
            'media.*' => 'required_with:media|image',
            'region_id' => 'nullable|integer|exists:regions,id',
            'external_code' => 'nullable|numeric|unique:cities,external_code,' . $model?->id,
            'show_in_best_deals' => 'boolean',
            'geo' => 'nullable|array|required_array_keys:latitude,longitude',
            'geography_l' => 'nullable|array',
            'description_l' => 'nullable|array',
            'article_l' => 'nullable|array',
        ];
    }
}
