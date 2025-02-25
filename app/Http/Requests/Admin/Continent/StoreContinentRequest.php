<?php

namespace App\Http\Requests\Admin\Continent;

use App\Models\Continent;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\JsonUniqueTitle;

class StoreContinentRequest extends FormRequest
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
            'title_l' => ['required', new JsonUniqueTitle(new Continent)]
        ];
    }
}
