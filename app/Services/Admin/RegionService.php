<?php

namespace App\Services\Admin;

use App\Http\Resources\Admin\RegionResource;
use App\Models\Region;

class RegionService
{
    public function getData($request)
    {
        $regions = Region::orderBy('id', "desc");

        $page  = $request->input('page') ? : 1;
        $take  = $request->input('count') ? : 8;
        $count = $regions->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $regions = $regions->take($take)->skip($skip);
        } else {
            $regions = $regions->take($take)->skip(0);
        }

        return [
            'data' => RegionResource::collection($regions->with('country')->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }
}
