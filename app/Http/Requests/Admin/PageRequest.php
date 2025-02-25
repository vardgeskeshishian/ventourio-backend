<?php

namespace App\Http\Requests\Admin;

use App\Enums\Helper;
use App\Enums\PageType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class PageRequest extends FormRequest
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

        $rules =  [
            'content_l' => 'required',
            'heading_title_l' => 'required',
            'meta_title_l' => 'required',
            'meta_description_l' => 'required',
            'slug' => 'required|alpha_dash|unique:pages,slug',
            'type' => 'required|in:' . Helper::implode(PageType::cases()),
            'info_blocks' => 'nullable|array'
        ];

        if (! empty($this->page)) {
            $rules['slug'] = 'required|alpha_dash|unique:pages,slug,'.$this->page->id;
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'slug' => Str::slug($this->slug),
        ]);
    }

}
