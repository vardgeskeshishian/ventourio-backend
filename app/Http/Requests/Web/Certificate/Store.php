<?php

namespace App\Http\Requests\Web\Certificate;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $data = [
            'currency_id' => 'required|numeric|exists:currencies,id',
            'base_certificate_id' => 'required|exists:base_certificates,id'
        ];

        if ( ! auth()->check() && ! auth('sanctum')->check()) {
            $data['email'] = 'required|email|unique:users,email';
        }

        return $data;
    }
}
