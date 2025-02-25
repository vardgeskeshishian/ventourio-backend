<?php

namespace App\Services\Admin;

use App\Http\Resources\Admin\QuestionAnswerResource;
use App\Models\QuestionAnswer;

class QuestionAnswerService
{
    public function getData($request)
    {
        $questionAnswers = QuestionAnswer::orderBy('id', "desc");

        $page  = $request->input('page') ? : 1;
        $take  = $request->input('count') ? : 8;
        $count = $questionAnswers->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $questionAnswers = $questionAnswers->take($take)->skip($skip);
        } else {
            $questionAnswers = $questionAnswers->take($take)->skip(0);
        }

        return [
            'data' => QuestionAnswerResource::collection($questionAnswers->with('page')->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }
}
