<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Mail\PaymentRequisitesForBooking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendPaymentRequisitesForBooking
{
    /**
     * Handle the event.
     *
     * @param BookingCreated $event
     * @return void
     */
    public function handle(BookingCreated $event): void
    {
        $leadPerson = $event->booking->lead_person;

        $email = $leadPerson['email'] ?? null;
        if (empty($email)) {
            // todo send error msg to admin
            return;
        }

        Mail::to($email)
            ->queue(new PaymentRequisitesForBooking($event->booking));
    }
}
