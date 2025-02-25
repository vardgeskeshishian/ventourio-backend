<?php

namespace App\Helpers;

use App\Models\Certificate;

final class CertificateCodeGenerator
{
    public static function make(int $length = 10): string
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        if (Certificate::where('code', $randomString)->exists()) {
            return self::make($length);
        }

        return $randomString;
    }
}
