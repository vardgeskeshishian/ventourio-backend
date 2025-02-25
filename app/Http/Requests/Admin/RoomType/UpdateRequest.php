<?php

namespace App\Http\Requests\Admin\RoomType;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /** Determine if the user is authorized to make this request. */
    public function authorize(): bool
    {
        return true;
    }

    /** Get the validation rules that apply to the request. */
    public function rules(): array
    {
        return [
            'title_l' => 'required|array',
            'hotel_id' => 'required|exists:hotels,id',
            'image' => 'nullable|image',
            'facilities' => 'nullable|array',
                'facilities.*' => 'required|exists:facilities,id'
        ];
    }
}
