<?php

namespace App\Http\Requests\Web\TwoFa;

use App\Models\UserTwoFa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LoginTwoFaRequest extends FormRequest
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
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'code' => [
                'required',
                Rule::exists(UserTwoFa::class, 'code')
            ]
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'phone' => Str::onlyNumber($this->phone),
        ]);
    }
}
