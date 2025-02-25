<?php

namespace App\Services\System;

use App\Enums\BookingStatus;
use App\Enums\Provider;
use App\Jobs\UpdateExternalBookingStatus;
use App\Models\Booking;

final class DispatchExternalBookingsStatusUpdate
{
    public function __invoke(): void
    {
        # Получаем бронирования, у которых переходящий статус (запрошено бронирование или запрошена отмена)
        $externalBookings = Booking::whereNot('provider', Provider::DB)
            ->where(function ($query) {
                $query->where('status', BookingStatus::CANCELLATION_REQUESTED)
                    ->orWhere('status', BookingStatus::REQUESTED);
            })
            ->whereNotNull('external_code')
            ->pluck('id');

        if ($externalBookings->isEmpty()) {
            return;
        }

        foreach ($externalBookings as $bookingId) {
            UpdateExternalBookingStatus::dispatch($bookingId);
        }
    }
}
