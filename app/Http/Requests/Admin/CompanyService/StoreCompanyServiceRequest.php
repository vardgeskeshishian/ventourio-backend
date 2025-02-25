<?php

namespace App\Http\Requests\Admin\CompanyService;

use App\Models\CompanyService;
use App\Rules\JsonUniqueTitle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreCompanyServiceRequest extends FormRequest
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
            'title_l' => ['required', new JsonUniqueTitle(new CompanyService)],
            'description_l' => 'nullable',
            'icon' => 'required|file',
            'image' => 'required|file',
            'slug' => 'required|alpha_dash|unique:pages,slug',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'slug' => Str::slug($this->slug),
        ]);
    }
}
