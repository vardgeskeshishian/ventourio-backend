<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Page\GetRequest;
use App\Http\Resources\Web\PageResource;
use App\Services\Web\PageService;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function get(GetRequest $request)
    {
        $page = (new PageService())->get($request->validated('slug'));

        return response()->json([
            'success' => true,
            'data' => $page
        ]);
    }
}
