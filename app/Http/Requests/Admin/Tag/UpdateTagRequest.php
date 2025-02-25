<?php

namespace App\Http\Requests\Admin\Tag;

use App\Models\Tag;
use App\Rules\JsonUniqueTitle;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTagRequest extends FormRequest
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
        $model = $this->tag;

        return [
            'title_l' => ['required', new JsonUniqueTitle($model, $model?->id)],
            'color_hex' => 'nullable|string'
        ];
    }
}
