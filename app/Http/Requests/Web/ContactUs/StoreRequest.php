<?php

namespace App\Http\Requests\Web\ContactUs;

use App\Models\CompanyService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
            'company_service_id' => [
                'required',
                Rule::exists(CompanyService::class, 'id'),
            ],
            'email' => 'required|email',
            'body' => 'required|string'
        ];
    }
}
