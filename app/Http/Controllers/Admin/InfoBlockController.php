<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InfoBlockController extends Controller
{
    public function getFormat()
    {
        return response()->json(config('info_blocks.format'));
    }
}
