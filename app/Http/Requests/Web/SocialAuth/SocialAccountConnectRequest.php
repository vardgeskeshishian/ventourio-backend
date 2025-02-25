<?php

namespace App\Http\Requests\Web\SocialAuth;

use App\Rules\StringContains;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class SocialAccountConnectRequest extends FormRequest
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
            'redirect_url' => 'required',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!Str::is(config('app.url') . "*", $this->redirect_url)) {
                $validator->errors()->add('field', __('errors.app.user.social_auth.incorrect_redirect_url'));
            }
        });
    }
}
