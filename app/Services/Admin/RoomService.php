<?php

namespace App\Services\Admin;

use App\Http\Resources\Admin\RoomResource;
use App\Models\Room;

class RoomService
{
    public function getData($request)
    {
        $rooms = Room::orderBy('id', "desc");

        $page  = $request->input('page') ? : 1;
        $take  = $request->input('count') ? : 8;
        $count = $rooms->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $rooms = $rooms->take($take)->skip($skip);
        } else {
            $rooms = $rooms->take($take)->skip(0);
        }

        return [
            'data' => RoomResource::collection($rooms->with('roomBase')->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }
}
