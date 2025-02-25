<?php

namespace App\Http\Requests\Web\Booking\Checkout;

use App\Enums\Helper;
use App\Enums\Provider;
use Illuminate\Foundation\Http\FormRequest;

class OfferRequest extends FormRequest
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
            'hotel_id' => 'required|numeric',
            'offer' => 'required|array',

            'dates' => 'required|array',
                'dates.arrival' => 'required_with:dates|date',
                'dates.departure' => 'required_with:dates|date',

            'rooms' => 'nullable|array',
                'rooms.*.adults' => 'required_with:rooms|numeric',
                'rooms.*.children' => 'nullable|numeric',
        ];
    }
}
