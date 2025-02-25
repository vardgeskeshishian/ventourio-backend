<?php

namespace App\Helpers;

use \App\Enums\BookingStatus as BookingStatusEnum;
use App\Enums\Helper;

final class BookingStatus
{
    public static function toAdmin(): array
    {
        // todo
        $keys = Helper::toArray(BookingStatusEnum::cases());
        $values = [
            'NEW',
            'REQUESTED',
            'CONFIRMED',
            'CANCEL_REQUEST',
            'CANCELLATION_REQUESTED',
            'CANCELLED',
            'CANCELLED_WITH_FEES',
            'REJECTED',
            'VOUCHER_ISSUED',
            'VOUCHER_REQUESTED',
        ];

        return array_combine($keys, $values);
    }
}
