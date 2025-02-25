<?php

namespace App\Enums;

enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';

    public static function types(bool $implode = false): array|string
    {
        $providers = [];

        foreach (self::cases() as $case) {
            $providers[] = $case->value;
        }

        if ($implode) {
            $providers = implode(',', $providers);
        }

        return $providers;
    }
}
