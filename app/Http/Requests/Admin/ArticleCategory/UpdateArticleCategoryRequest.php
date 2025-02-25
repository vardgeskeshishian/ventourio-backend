<?php

namespace App\Http\Requests\Admin\ArticleCategory;

use App\Models\ArticleCategory;
use App\Models\Tag;
use App\Rules\JsonUniqueTitle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateArticleCategoryRequest extends FormRequest
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
        $model = $this->article_category;

        return [
            'title_l' => ['required', new JsonUniqueTitle($model, $model?->id)],
            'color_hex' => 'nullable|string|between:7,7'
        ];
    }
}
