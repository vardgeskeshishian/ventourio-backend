<?php

namespace App\Http\Requests\Admin\CreditCard;

use App\Enums\CreditCardType;
use App\Enums\Helper;
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
            'holder_name' => 'required|string|max:100',
            'type' => 'required|string|in:' . Helper::implode(CreditCardType::cases()),
            'number' => 'required|string',
            'exp_month' => 'required|string',
            'exp_year' => 'required|string',
            'svc' => 'required|string'
        ];
    }
}
