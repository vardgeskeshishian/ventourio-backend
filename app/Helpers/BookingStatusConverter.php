<?php

namespace App\Helpers;

use App\Enums\Provider;

final class BookingStatusConverter
{
    public static function convert(string|int $status, string $from, string $to): int|string|null
    {
        return match ($from) {
            Provider::GOGLOBAL->value => self::convertFromGoGlobal($status, $to),
            Provider::DB->value => self::convertFromDB($status, $to),
        };
    }

    private static function convertFromGoGlobal(int|string $status, string $to): int|string|null
    {
        return match ($to) {
            Provider::DB->value => self::convertFromGoGlobalToDB($status)
        };
    }

    private static function convertFromGoGlobalToDB(int|string $status): int|string|null
    {
        $goglobalStatuses = collect(config('goglobal.booking.statuses'));
        if ($goglobalStatuses->isEmpty()) {
            return null;
        }

        return $goglobalStatuses->where('code', $status)->first()['matched'] ?? null;
    }

    private static function convertFromDB(int|string $status, string $to): int|string|null
    {
        return match ($to) {
            Provider::GOGLOBAL->value => self::convertFromDBToGoGlobal($status)
        };
    }

    private static function convertFromDBToGoGlobal(int|string $status): int|string|null
    {
        $goglobalStatuses = collect(config('goglobal.booking.statuses'));
        if ($goglobalStatuses->isEmpty()) {
            return null;
        }

        return $goglobalStatuses->where('matched', $status)->first()['code'] ?? null;
    }
}
