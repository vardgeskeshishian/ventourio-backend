<?php

namespace App\Http\Requests\Admin\Tag;

use App\Models\Tag;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\JsonUniqueTitle;

class StoreTagRequest extends FormRequest
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
            'title_l' => ['required', new JsonUniqueTitle(new Tag())],
            'color_hex' => 'nullable|string',
        ];
    }
}
