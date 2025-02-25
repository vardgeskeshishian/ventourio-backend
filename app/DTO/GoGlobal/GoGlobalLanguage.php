<?php

namespace App\DTO\GoGlobal;

final class GoGlobalLanguage
{
    private string $language;

    private function __construct(string $language)
    {
        $availableLanguages = collect(config('goglobal.languages'));

        if ( ! $availableLanguages->where('code', $language)->first()) {
            $language = 'us';
        }

        $this->language = $language;
    }

    public static function create(string $language): GoGlobalLanguage
    {
        return new GoGlobalLanguage($language);
    }

    public function value(): string
    {
        return $this->language;
    }
}
