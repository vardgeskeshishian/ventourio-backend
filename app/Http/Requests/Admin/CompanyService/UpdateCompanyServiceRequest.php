<?php

namespace App\Http\Requests\Admin\CompanyService;

use App\Models\CompanyService;
use App\Rules\JsonUniqueTitle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateCompanyServiceRequest extends FormRequest
{
    public function __construct( public CompanyService $location ) { }

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

        $model = $this->company_service;

        return [
            'title_l' => ['required', new JsonUniqueTitle($this->location, $model?->id)],
            'description_l' => 'nullable',
            'icon' => 'nullable|file',
            'image' => 'nullable|file',
            'slug' => 'required|alpha_dash|unique:pages,slug,' . $model?->page?->id,
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'slug' => Str::slug($this->slug),
        ]);
    }
}
