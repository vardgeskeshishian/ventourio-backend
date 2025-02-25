<?php

namespace App\Services\Admin;

use App\Http\Resources\Admin\DistrictResource;
use App\Models\District;

final class DistrictService
{
    public function getData($request)
    {
        $districts = District::orderByDesc('id');

        $page  = $request->input('page') ? : 1;
        $take  = $request->input('count') ? : 8;
        $count = $districts->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $districts = $districts->take($take)->skip($skip);
        } else {
            $districts = $districts->take($take)->skip(0);
        }

        return [
            'data' => DistrictResource::collection($districts->with('city')->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }
}
