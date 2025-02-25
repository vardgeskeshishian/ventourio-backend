<?php

namespace App\Listeners;

use App\Events\CertificatePaid;
use App\Helpers\CertificatePdfGenerator;
use App\Mail\PaidCertificateMail;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCertificate
{
    /**
     * Handle the event.
     *
     * @param CertificatePaid $event
     * @return void
     * @throws Exception
     */
    public function handle(CertificatePaid $event): void
    {
        $certificate = $event->certificate;

        if ( ! $certificate->relationLoaded('boughtByUser') || ! $certificate->relationLoaded('baseCertificate')) {
            $certificate->load('boughtByUser', 'baseCertificate');
        }

        $pdfPath = CertificatePdfGenerator::make($certificate);

        if ( ! $certificate->boughtByUser) {
            return;
        }

        Mail::to($certificate->boughtByUser)
            ->queue(new PaidCertificateMail($certificate, $pdfPath));

        unlink($pdfPath);
    }
}
