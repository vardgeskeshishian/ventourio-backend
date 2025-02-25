<?php

namespace App\Http\Requests\Admin\Discount;

use App\Enums\DiscountType;
use App\Enums\Helper;
use Illuminate\Foundation\Http\FormRequest;

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
            'type' => 'required|in:' . Helper::implode(DiscountType::cases()),
            'amount' => 'required|numeric|min:1',
            'expired_at' => 'nullable|date',
        ];
    }
}
