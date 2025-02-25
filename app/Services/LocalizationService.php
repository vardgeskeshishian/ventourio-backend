<?php

namespace App\Services;

use App\Enums\Locale;
use App\Models\Language;
use Illuminate\Support\Facades\App;

class LocalizationService extends MainService
{
    public static function setLocale()
    {
        try {

            $locale = request()->segment(Locale::LOCALE_SEGMENT->value, '');

            $languages = Language::active();

            $codeLanguages = $languages->pluck('code')->toArray();

            $defaultLanguage = $languages->select('code')
                ->where('is_default', true)
                ->first();

            if( $locale && in_array($locale, $codeLanguages) )
            {

                App::setLocale($locale);

                return $locale;
            }

            App::setLocale($defaultLanguage->code);

        } catch (\Exception $e) {

        }

        return "";
    }
}
