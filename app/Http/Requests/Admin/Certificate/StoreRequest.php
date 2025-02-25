<?php

namespace App\Http\Requests\Admin\Certificate;

use Illuminate\Foundation\Http\FormRequest;

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
            'bought_by_user_id' => 'required|exists:users,id',
            'currency_id' => 'required|exists:currencies,id',
            'base_certificate_id' => 'required|exists:base_certificates,id',
            'paid_at' => 'nullable|date',
        ];

    }
}
