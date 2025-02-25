<?php

namespace App\Http\Requests\Web\User;

use App\Enums\Gender;
use App\Enums\Helper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UpdateRequest extends FormRequest
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
            'avatar' => 'nullable|image',
            'first_name' => 'nullable',
            'last_name' => 'nullable',
            'email' => 'nullable|email|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|numeric|regex:/^([0-9]*)$/|unique:users,phone,' . auth()->id(),
            'country_id' => 'nullable|exists:countries,id',
            'gender' => 'nullable|string|in:' . Helper::implode(Gender::cases()),
            'current_password' => 'required_with:password|string',
            'password' => 'nullable|confirmed|string',
        ];
    }

    public function withValidator($validator){

        $validator->after(function($validator) {

            if(isset($this->password) && ! Hash::check($this->current_password, $this->user()->password)) {
                $validator->errors()->add('current_password', 'Your current password is incorrect');
            }
        });
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        unset($data['current_password'], $data['password_confirmation']);

        return $data;
    }
}
