<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UpdatePassportRequest extends FormRequest
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
            'current_password' => 'required',
            'password' => 'required|confirmed',
        ];
    }

    public function withValidator($validator){

        $validator->after(function($validator){

            if(!Hash::check($this->current_password, $this->user()->password)){

                $validator->errors()->add('current_password', 'Your current password is incorrect');

            }

            $this->merge([
                'password' => Hash::make($this->password),
            ]);

        });
    }
}
