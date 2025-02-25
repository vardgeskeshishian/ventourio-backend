<?php

namespace App\Http\Requests\Admin\Booking;

use App\Enums\BookingStatus;
use App\Enums\Helper;
use App\Enums\Provider;
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
            'status' => 'required|int|in:' . Helper::implode(BookingStatus::cases()),
        ];
    }
}
