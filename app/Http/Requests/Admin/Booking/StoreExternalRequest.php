<?php

namespace App\Http\Requests\Admin\Booking;

use App\Enums\ExternalPaymentMethodType;
use App\Enums\Helper;
use Illuminate\Foundation\Http\FormRequest;

class StoreExternalRequest extends FormRequest
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
            'booking_id' => 'required|exists:bookings,id',
            'type' => 'required|in:' . Helper::implode(ExternalPaymentMethodType::cases()),
            'credit_card_id' => 'required_if:type,' . ExternalPaymentMethodType::CREDIT_CARD->value
        ];
    }
}
