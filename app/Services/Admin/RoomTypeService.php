<?php

namespace App\Services\Admin;

use App\Exceptions\BusinessException;
use App\Http\Resources\Admin\RoomTypeResource;
use App\Models\RoomType;
use Illuminate\Support\Facades\DB;

final class RoomTypeService
{
    public function getData($request)
    {
        $roomTypes = RoomType::orderBy('id', "desc");

        $page  = $request->input('page') ? : 1;
        $take  = $request->input('count') ? : 8;
        $count = $roomTypes->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $roomTypes = $roomTypes->take($take)->skip($skip);
        } else {
            $roomTypes = $roomTypes->take($take)->skip(0);
        }

        return [
            'data' => RoomTypeResource::collection($roomTypes->with('hotel')->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }

    public function store(array $data): RoomType
    {
        DB::beginTransaction();
        try {

            $roomType = RoomType::create([
                'title_l' => $data['title_l'],
                'hotel_id' => $data['hotel_id'],
            ]);

            if ( ! empty($data['image'])) {
                $roomType->addMedia($data['image'])
                    ->toMediaCollection();
            }

            $roomType->facilities()->sync($data['facilities'] ?? []);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new BusinessException($e->getMessage());
        }

        return $roomType;
    }

    public function update(array $data, RoomType $roomType): RoomType
    {
        DB::beginTransaction();
        try {

            $roomType->update([
                'title_l' => $data['title_l'],
                'hotel_id' => $data['hotel_id'],
            ]);

            if ( ! empty($data['image'])) {
                $roomType->clearMediaCollection();
                $roomType->addMedia($data['image'])
                    ->toMediaCollection();
            }

            $roomType->facilities()->sync($data['facilities'] ?? []);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new BusinessException($e->getMessage());
        }

        return $roomType;
    }
}
