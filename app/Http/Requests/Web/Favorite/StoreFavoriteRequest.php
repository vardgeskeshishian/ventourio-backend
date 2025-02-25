<?php

namespace App\Http\Requests\Web\Favorite;

use App\Models\Hotel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFavoriteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->hasUser();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'hotel_id' => [
                'required',
                Rule::exists(Hotel::class, 'id'),
            ]
        ];
    }
}
