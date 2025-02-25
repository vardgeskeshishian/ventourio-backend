<?php

namespace App\Http\Requests\Web\Hotel;

use Illuminate\Foundation\Http\FormRequest;

class GetFiltersRequest extends FormRequest
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
            'nationality' => 'required|string',
            'region_slug' => 'nullable|string',
            'city_slug' => 'nullable|string',
            'district_slug' => 'nullable|array',
                'district_slug.*' => 'nullable|string',
            'only_discount' => 'nullable|boolean'
        ];
    }
}
