<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ContactUsResource;
use App\Models\ContactUs;
use App\Services\Admin\ContactUsService;
use Illuminate\Http\JsonResponse;

class ContactUsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $result = (new ContactUsService())->index();

        return response()->json($result);
    }

    /**
     * Display the specified resource.
     *
     * @param ContactUs $contact_us
     * @return JsonResponse
     */
    public function show(ContactUs $contact_u): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new ContactUsResource($contact_u),
        ]);
    }
}
