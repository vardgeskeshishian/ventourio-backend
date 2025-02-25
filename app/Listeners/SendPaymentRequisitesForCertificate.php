<?php

namespace App\Listeners;

use App\Events\CertificateCreated;
use App\Mail\PaymentRequisitesForCertificateMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendPaymentRequisitesForCertificate
{
    /**
     * Handle the event.
     *
     * @param CertificateCreated $event
     * @return void
     */
    public function handle(CertificateCreated $event): void
    {
        $certificate = $event->certificate;

        if ( ! $certificate->relationLoaded('boughtByUser')) {
            $certificate->load('boughtByUser');
        }

        if ( ! $certificate->boughtByUser) {
            return;
        }

        Mail::to($certificate->boughtByUser)
            ->queue(new PaymentRequisitesForCertificateMail($certificate));
    }
}
