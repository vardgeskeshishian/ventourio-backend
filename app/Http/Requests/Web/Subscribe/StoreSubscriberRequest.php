<?php

namespace App\Http\Requests\Web\Subscribe;

use App\Models\Subscriber;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriberRequest extends FormRequest
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
            'verify_token' => [
                'required'
            ],
            'email' => [
                'required',
                'email',
                Rule::unique(Subscriber::class, 'email'),
            ]
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'verify_token' => Str::random(64),
        ]);
    }
}
