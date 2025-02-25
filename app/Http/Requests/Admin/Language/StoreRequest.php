<?php

namespace App\Http\Requests\Admin\Language;

use App\Enums\Helper;
use App\Enums\LanguageType;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'type' => 'required|string|in:' . Helper::implode(LanguageType::cases()),
            'code' => 'required|string',
            'title_l' => 'required|array',
            'flag' => 'nullable',
            'is_rtl' => 'nullable',
            'is_active' => 'nullable',
            'is_default' => 'nullable',
            'localization_json' => 'required',
            'countries' => 'nullable|array',
                'countries.*' => 'required|exists:countries,id',
        ];
    }
}
