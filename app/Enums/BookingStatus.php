<?php

namespace App\Enums;

enum BookingStatus: int
{
    case NEW = 0;
    case REQUESTED = 1;
    case CONFIRMED = 2;
    case CANCEL_REQUEST = 3;
    case CANCELLATION_REQUESTED = 4;
    case CANCELLED = 5;
    case CANCELLED_WITH_FEES = 6;
    case REJECTED = 7;
    case VOUCHER_ISSUED = 8;
    case VOUCHER_REQUESTED = 9;

    public static function closed(bool $stringFormat = true): array
    {
        $closed = [
            self::CANCELLED, self::CANCELLED_WITH_FEES, self::REJECTED,
            self::VOUCHER_ISSUED, self::VOUCHER_REQUESTED
        ];

        if ($stringFormat) {
            foreach ($closed as &$status) {
                $status = $status->value;
            }
        }

        return $closed;
    }
}
