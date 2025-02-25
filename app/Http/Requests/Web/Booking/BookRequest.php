<?php

namespace App\Http\Requests\Web\Booking;

use App\Enums\Helper;
use App\Enums\Provider;
use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
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
        $rules = [
            'lead_person' => 'required|array',
                'lead_person.email' => 'required|email',
                'lead_person.first_name' => 'required|string|between:2,100',
                'lead_person.last_name' => 'required|string|between:2,100',

            'search_code' => 'required|string',
            'provider' => 'required|in:' . Helper::implode(Provider::cases()),
            'total_price' => 'required|numeric',

            'dates' => 'required|array',
                'dates.arrival' => 'required|date',
                'dates.departure' => 'required|date',

            'certificate_code' => 'nullable|exists:certificates,code',

            'rooms' => 'required|array',

                'rooms.*.people' => ['required_if:provider,' . Provider::DB->value, 'array'],
                    'rooms.*.people.*.full_name' => 'required|string',
                    'rooms.*.people.*.email' => 'nullable|string',

                'rooms.*.adults' => ['required_if:provider,' . Provider::GOGLOBAL->value, 'array'],
                    'rooms.*.adults.*.first_name' => 'required|string',
                    'rooms.*.adults.*.last_name' => 'required|string',
                    'rooms.*.adults.*.sex' => 'required|string|in:male,female',

                'rooms.*.children' => 'nullable|array',
                    'rooms.*.children.*.first_name' => 'required_with:rooms.*.children|string',
                    'rooms.*.children.*.last_name' => 'required_with:rooms.*.children|string',
                    'rooms.*.children.*.age' => 'required_with:rooms.*.children|numeric|between:1,18',
        ];

        if ( ! auth()->check() && ! auth('sanctum')->check()) {
            $rules['lead_person.email'] = $rules['lead_person.email'] . '|confirmed';
            $rules['lead_person.email_confirmation'] = 'required';
        }

        return $rules;
    }
}
