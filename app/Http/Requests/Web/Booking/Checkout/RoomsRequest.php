<?php

namespace App\Http\Requests\Web\Booking\Checkout;

use Illuminate\Foundation\Http\FormRequest;

class RoomsRequest extends FormRequest
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
            'room_bases' => 'required|array',
                'room_bases.*.id' => 'required|int',
                'room_bases.*.count' => 'required|int',

            'dates' => 'required|array',
                'dates.arrival' => 'required_with:dates|date',
                'dates.departure' => 'required_with:dates|date',
        ];
    }
}
