<?php

namespace App\Http\Requests\Admin\Review;

use App\Enums\Helper;
use App\Enums\RatingCategory;
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
            'rating'     => 'required|array|required_array_keys:' . Helper::implode(RatingCategory::cases()),
            'body'       => 'required|string|min:5',
        ];
    }
}
