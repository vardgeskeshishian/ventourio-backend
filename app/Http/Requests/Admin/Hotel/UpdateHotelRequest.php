<?php

namespace App\Http\Requests\Admin\Hotel;

use App\Models\Hotel;
use App\Rules\JsonUniqueTitle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateHotelRequest extends FormRequest
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
        $model = $this->hotel;

        return [
            'fax' => 'nullable|string',
            'geo' => 'nullable|array|required_array_keys:latitude,longitude',
            'giata_code' => 'nullable|string',
            'external_code' => 'nullable|string',
            'stars' => 'nullable|numeric|between:0,6',
            'address' => 'required|string|max:255',
            'is_apartment' => 'nullable|boolean',

            "house_rules.*.title.ru" => "required|string",
            "house_rules.*.title.en" => "required|string",
            "house_rules.*.body.ru" => "required|string",
            "house_rules.*.body.en" => "required|string",

            'phone' => 'nullable|numeric|regex:/^([0-9]*)$/',
            'district_id' => 'required|integer|exists:districts,id',
            'title_l' => ['required', new JsonUniqueTitle($model, $model?->id)],
            'media' => 'nullable|array',
                'media.*' => 'required_with:media|image',
            'facilities' => 'nullable|array',
                'facilities.*' => 'required|exists:facilities,id',
            'discount_id' => 'nullable|numeric|exists:discounts,id',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'phone' => Str::onlyNumber($this->phone),
        ]);
    }
}
