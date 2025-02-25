<?php

namespace App\Services\Admin;

use App\Http\Resources\Admin\TagResource;
use App\Models\Tag;

class TagService
{
    public function getData($request)
    {
        $tags = Tag::orderBy('id', "desc");

        $page  = $request->input('page') ? : 1;
        $take  = $request->input('count') ? : 8;
        $count = $tags->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $tags = $tags->take($take)->skip($skip);
        } else {
            $tags = $tags->take($take)->skip(0);
        }

        return [
            'data' => TagResource::collection($tags->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }
}
