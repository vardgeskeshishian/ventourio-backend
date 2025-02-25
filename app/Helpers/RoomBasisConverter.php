<?php

namespace App\Helpers;

use App\Enums\Provider;
use App\Enums\RoomBasis;

final class RoomBasisConverter
{
    public static function convert(string|int $roomBasis, Provider $from, Provider $to): string|RoomBasis|null
    {
        return match ($from) {
            Provider::GOGLOBAL => self::convertFromGoGlobal($roomBasis, $to),
            Provider::DB => self::convertFromDB($roomBasis, $to),
        };
    }

    public static function convertFromGoGlobal(int|string $roomBasis, Provider $to): RoomBasis|null
    {
        return match ($to) {
            Provider::DB => self::convertFromGoGlobalToDB($roomBasis)
        };
    }

    public static function convertFromGoGlobalToDB(int|string $roomBasis): RoomBasis|null
    {
        $goglobalBasises = collect(config('goglobal.booking.room_basis'));
        if ($goglobalBasises->isEmpty()) {
            return null;
        }

        return $goglobalBasises->where('code', $roomBasis)->first()['matched'] ?? null;
    }

    public static function convertFromDB(int|string $roomBasis, Provider $to): string|null
    {
        return match ($to) {
            Provider::GOGLOBAL => self::convertFromDBToGoGlobal($roomBasis)
        };
    }

    public static function convertFromDBToGoGlobal(int|string $roomBasis): string|null
    {
        $goglobalBasises = collect(config('goglobal.booking.room_basis'));
        if ($goglobalBasises->isEmpty()) {
            return null;
        }

        return $goglobalBasises->where('matched', $roomBasis)->first()['code'] ?? null;
    }
}
