<?php

namespace App\Enums;

final class Helper
{
    public static function toArray($cases): array
    {
        $result = [];

        foreach ($cases as $case) {
            $result[] = $case->value;
        }

        return $result;
    }

    public static function implode($cases, string $delimiter = ','): string
    {
        return implode($delimiter, self::toArray($cases));
    }
}
