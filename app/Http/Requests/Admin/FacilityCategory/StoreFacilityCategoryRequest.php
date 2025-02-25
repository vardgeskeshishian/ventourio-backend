<?php

namespace App\Http\Requests\Admin\FacilityCategory;

use App\Models\FacilityCategory;
use App\Rules\JsonUniqueTitle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFacilityCategoryRequest extends FormRequest
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
            'title_l' => [
                'required',
                new JsonUniqueTitle(new FacilityCategory)
            ]
        ];
    }
}
