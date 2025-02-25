<?php

namespace App\Services\Admin;

use App\Http\Resources\Admin\SightResource;
use App\Models\Sight;

class SightService
{
    public function getData($request)
    {
        $sights = Sight::orderBy('id', "desc");

        $page  = $request->input('page') ? : 1;
        $take  = $request->input('count') ? : 8;
        $count = $sights->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $sights = $sights->take($take)->skip($skip);
        } else {
            $sights = $sights->take($take)->skip(0);
        }

        return [
            'data' => SightResource::collection($sights->with('city')->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }
}
