<?php

namespace App\Helpers;

use Illuminate\Support\Str;

final class PasswordGenerator
{
    public static function generate(): string
    {
        return Str::random(10);
    }
}
