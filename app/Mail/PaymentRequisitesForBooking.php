<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\PaymentRequisite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentRequisitesForBooking extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public readonly Booking $booking) {}

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Requisites For Booking',
        );
    }

    public function build(): Mailable
    {
        $paymentRequisite = PaymentRequisite::active()->first();
        $supportHref = config('front.web_url') . "/contacts"; // todo make proper link when it will be info
        return $this->markdown('emails.booking.payment_requisites', ['support_href' => $supportHref, 'payment_requisite' => $paymentRequisite]);
    }
}
