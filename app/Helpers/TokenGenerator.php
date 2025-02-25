<?php

namespace App\Helpers;

use Illuminate\Support\Str;

final class TokenGenerator
{
    public static function generate(): string
    {
        return md5(Str::random(10) . microtime());
    }
}
