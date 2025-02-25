<?php

namespace App\Http\Requests\Admin\User;

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
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'phone' => 'required|numeric|regex:/^([0-9]*)$/|unique:users,phone',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'country_id' => 'nullable|exists:countries,id',
            'gender' => 'nullable|string',
        ];
    }
}
