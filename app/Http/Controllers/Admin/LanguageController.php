<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Language\StoreRequest;
use App\Http\Resources\Admin\CountryResource;
use App\Http\Resources\Admin\LanguageResource;
use App\Models\Country;
use App\Models\Language;
use App\Services\Admin\LanguageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LanguageController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return LanguageResource::collection(Language::all());
    }

    public function getJson(): JsonResponse
    {
        $json = Language::pluck('localization_json')->first();

        return response()->json([
            'data' => $json
        ], 200);
    }

    public function create(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'countries' => CountryResource::collection(Country::all(['id', 'title_l']))
            ]
        ]);
    }

    public function store(StoreRequest $request): LanguageResource
    {
        $language = (new LanguageService())->store($request->validated());
        return new LanguageResource($language->load('countries'));
    }

    public function show(Language $language): LanguageResource
    {
        return new LanguageResource($language->load('countries'));
    }

    public function edit(Language $language): JsonResponse
    {
        return $this->create();
    }

    public function update(Request $request, $id): LanguageResource
    {
        $language = (new LanguageService())->updateOrCreateLanguage($request->all(), $id);

        return new LanguageResource($language->load('countries'));
    }

    public function destroy($id): JsonResponse
    {
        $result = (new LanguageService())->destroy($id);
        return response()->json([
            'status' => $result,
            'message'  => 'Language has been deleted successfully!'
        ], 200);
    }
}
