<?php

namespace App\Http\Requests\Admin\Facility;

use App\Models\FacilityCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title_l' => 'required|array',
            'image' => 'nullable|image',
            'category_id' => [
                'required',
                Rule::exists(FacilityCategory::class, 'id'),
            ]
        ];
    }
}
