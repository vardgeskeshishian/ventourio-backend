<?php

namespace App\Http\Requests\Web\Review;

use App\Enums\Helper;
use App\Enums\RatingCategory;
use Illuminate\Foundation\Http\FormRequest;

class StoreByHotelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check() ?? auth('sanctum')->check();
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
