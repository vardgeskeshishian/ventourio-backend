<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\EditorUploadRequest;
use App\Services\Admin\EditorService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class EditorController extends Controller
{
    /** Upload resource. */
    public function upload(EditorUploadRequest $request): JsonResponse
    {
        $url = (new EditorService())->uploadAndReturnPath($request->validated());
        return response()->json([
            'status' => true,
            "uploaded" => 1,
            "url" => $url
        ]);
    }
}
