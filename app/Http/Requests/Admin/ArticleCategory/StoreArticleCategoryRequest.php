<?php

namespace App\Http\Requests\Admin\ArticleCategory;

use App\Models\ArticleCategory;
use App\Rules\JsonUniqueTitle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreArticleCategoryRequest extends FormRequest
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
            'title_l' => ['required', new JsonUniqueTitle(new ArticleCategory)],
            'color_hex' => 'nullable|string|between:7,7',
            'page' => 'nullable',
              'page.slug' => 'nullable|string',
        ];
    }
}
