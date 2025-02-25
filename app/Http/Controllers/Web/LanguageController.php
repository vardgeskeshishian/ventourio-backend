<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\Web\LanguageResource;
use App\Models\Language;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LanguageController extends Controller
{

    /**
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        $languages = Language::with('countries:id,iso_code')->get();

        foreach ($languages as $language) {
            $language->setRelation('countries', $language->countries->pluck('iso_code'));
        }

        return LanguageResource::collection($languages);
    }

    public function getLocalizationJson($code)
    {
        $language = Language::where('code', $code)->first();
        if ($language) {
            return response()->json([
                'data'  => $language->localization_json
            ], 200);
        } else {
            return response()->json([
                'error' => 'code note found'
            ], 404);
        }

    }
}
