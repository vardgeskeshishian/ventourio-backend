<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Mail\VoucherDetailsMail;
use App\Services\Web\Hotel\BookingVoucherService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendBookingVoucher implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(public readonly BookingVoucherService $service) {}

    /**
     * Handle the event.
     *
     * @param BookingConfirmed $event
     * @return void
     * @throws Exception
     */
    public function handle(BookingConfirmed $event): void
    {
        $booking = $event->booking;

        $voucherDetailsDto = $this->service->getDetails($booking);

        $user = $booking->user;
        if ( ! $booking->user) {
            return;
        }

        Mail::to($user)
            ->queue(new VoucherDetailsMail($voucherDetailsDto));
    }
}
