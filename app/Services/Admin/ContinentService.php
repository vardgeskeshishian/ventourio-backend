<?php

namespace App\Services\Admin;

use App\Http\Resources\Admin\ContinentResource;
use App\Models\Continent;

class ContinentService
{

    public function __construct()
    {

    }

    public function getData($request)
    {
        $continents = Continent::orderBy('id', "desc");

        $page  = $request->input('page') ? : 1;
        $take  = $request->input('count') ? : 8;
        $count = $continents->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $continents = $continents->take($take)->skip($skip);
        } else {
            $continents = $continents->take($take)->skip(0);
        }

        return [
            'data' => ContinentResource::collection($continents->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }


    /**
     * @param $id
     * @return array
     */
    public function destroy($id)
    {
        return [];
    }

}
