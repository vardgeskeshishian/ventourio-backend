<?php

namespace App\Http\Requests\Admin\Sight;

use App\Models\Sight;
use App\Rules\JsonUniqueTitle;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSightRequest extends FormRequest
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

        $model = $this->sight;

        return [
            'title_l' => ['required', new JsonUniqueTitle($model, $model?->id)],
            'city_id' => 'nullable|integer|exists:cities,id',
        ];
    }
}
