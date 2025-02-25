<?php

namespace App\Services\Admin;

use App\Enums\RoomBasis;
use App\Http\Resources\Admin\RoomBaseResource;
use App\Models\Discount;
use App\Models\RoomBase;

class RoomBaseService
{
    public function getData($request)
    {
        $roomBases = RoomBase::query();

        $page  = $request->input('page') ? : 1;
        $take  = $request->input('count') ? : 8;
        $count = $roomBases->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $roomBases = $roomBases->take($take)->skip($skip);
        } else {
            $roomBases = $roomBases->take($take)->skip(0);
        }

        return [
            'data' => RoomBaseResource::collection($roomBases->orderBy('id', "desc")->with('roomType')->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }

    public function store(array $data): RoomBase
    {
        if ( ! empty($data['discount_id'])) {
            $discount = Discount::find($data['discount_id']);
            $data['price'] = $discount->apply($data['base_price']);
        } else {
            $data['price'] = $data['base_price'];
        }

        return RoomBase::create([
            'remark_l' => $data['remark_l'] ?? null,
            'basis' => RoomBasis::from($data['basis']),
            'refundable' => $data['refundable'],
            'cancel_range' => $data['cancel_range'],
            'booking_range' => $data['booking_range'],
            'booking_max_term' => $data['booking_max_term'],
            'base_price' => $data['base_price'],
            'price' => $data['price'],
            'discount_id' => $data['discount_id'] ?? null,
            'adults_count' => $data['adults_count'],
            'children_count' => $data['children_count'] ?? null,
            'room_type_id' => $data['room_type_id'],
            'title_l' => $data['title_l'],
        ]);
    }

    public function update(array $data, RoomBase $roomBase): void
    {
        if ( ! empty($data['discount_id'])) {

            if ((int) $data['discount_id'] === $roomBase->discount_id) {
                $data['price'] = $roomBase->price;
            } else {
                $discount = Discount::find($data['discount_id']);
                $data['price'] = $discount->apply($data['base_price']);
            }

        } else {
            $data['price'] = $data['base_price'];
        }

        $roomBase->update([
            'remark_l' => $data['remark_l'] ?? null,
            'basis' => RoomBasis::from($data['basis']),
            'refundable' => $data['refundable'],
            'cancel_range' => $data['cancel_range'],
            'booking_range' => $data['booking_range'],
            'booking_max_term' => $data['booking_max_term'],
            'base_price' => $data['base_price'],
            'price' => $data['price'],
            'discount_id' => $data['discount_id'] ?? null,
            'adults_count' => $data['adults_count'],
            'children_count' => $data['children_count'] ?? null,
            'room_type_id' => $data['room_type_id'],
            'title_l' => $data['title_l'],
        ]);
    }
}
