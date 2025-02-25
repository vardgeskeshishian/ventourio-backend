<?php

namespace App\Services\Web\Continent;

use App\Models\Continent;

final class Helper
{
    public static function breadcrumbs(Continent $continent): array
    {
        return [
            $continent->page->slug => $continent->title,
        ];
    }
}
