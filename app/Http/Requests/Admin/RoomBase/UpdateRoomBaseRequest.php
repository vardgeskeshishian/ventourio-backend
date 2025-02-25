<?php

namespace App\Http\Requests\Admin\RoomBase;

use App\Enums\Helper;
use App\Enums\RoomBasis;
use App\Rules\JsonUniqueTitle;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomBaseRequest extends FormRequest
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
        $model = $this->room_base;

        return [
            'remark_l' => 'nullable|array',
            'basis' => 'required|integer|in:' . Helper::implode(RoomBasis::cases()),
            'refundable' => 'required|boolean',
            'cancel_range' => 'required|integer',
            'booking_range' => 'required|integer',
            'booking_max_term' => 'required|integer',
            'base_price' => 'required|numeric',
            'discount_id' => 'nullable|numeric|exists:discounts,id',
            'adults_count' => 'required|integer',
            'children_count' => 'nullable|integer',
            'room_type_id' => 'required|integer|exists:room_types,id',
            'title_l' => ['required', new JsonUniqueTitle($model, $model?->id)],
        ];
    }
}
