<?php

namespace App\Observers;

use App\Enums\BookingStatus;
use App\Events\BookingConfirmed;
use App\Models\Booking;

class BookingObserver
{
    /**
     * Handle the Booking "saved" event.
     *
     * @param Booking $booking
     * @return void
     */
    public function saved(Booking $booking): void
    {
        if ($booking->wasChanged('status')) {

            if ($booking->status === BookingStatus::CONFIRMED) {
                BookingConfirmed::dispatch($booking);
            }
        }
    }
}
