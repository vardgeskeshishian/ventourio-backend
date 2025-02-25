<?php

namespace App\Http\Requests\Admin\Article;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Tag;
use App\Rules\JsonUniqueTitle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreArticleRequest extends FormRequest
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
            'title_l' => ['required', new JsonUniqueTitle(new Article)],
            'content_l' => 'required',

            'author_l' => 'nullable|array',

            'quote_l' => 'nullable|array',

            'media' => 'nullable|image',

            'avatar' => 'nullable|image',

            'page' => 'nullable',
                'page.slug' => 'nullable|string',

            'tags' => 'nullable|array',
                'tags.*' => 'required_with:tags|exists:tags,id',

            'article_category_id' => 'nullable|exists:article_categories,id'
        ];
    }
}
