<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /** Display a listing of the resource. */
    public function index(): JsonResponse
    {
        $unseenCertificateCount = Certificate::query()->unseen()->count();

        return response()->json([
            'unseenCertificateCount' => $unseenCertificateCount,
        ]);
    }
}
