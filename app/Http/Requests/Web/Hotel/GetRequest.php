<?php

namespace App\Http\Requests\Web\Hotel;

use Illuminate\Foundation\Http\FormRequest;

class GetRequest extends FormRequest
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
            'id' => 'nullable|numeric',
            'dates' => 'nullable|array',
                'dates.arrival' => 'required_with:dates|date',
                'dates.departure' => 'required_with:dates|date',

            'rooms' => 'nullable|array',
                'rooms.*.adults' => 'required_with:rooms|numeric',
                'rooms.*.children' => 'nullable|numeric',

            'nationality' => 'nullable|string',
        ];
    }
}
