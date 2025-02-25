<?php

namespace App\Http\Requests\Admin\Region;

use App\Models\Region;
use App\Rules\JsonUniqueTitle;
use Illuminate\Foundation\Http\FormRequest;

class StoreRegionRequest extends FormRequest
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
            'title_l' => ['required', new JsonUniqueTitle(new Region)],
            'country_id' => 'nullable|integer|exists:countries,id',
        ];
    }
}
