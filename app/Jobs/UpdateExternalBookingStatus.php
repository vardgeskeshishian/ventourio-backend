<?php

namespace App\Jobs;

use App\Enums\Provider;
use App\Models\Booking;
use App\Services\GoGlobal\BookingStatusService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateExternalBookingStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(readonly public int $bookingId) {}

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        $booking = Booking::findOrFail($this->bookingId);

        if (empty($booking->external_code)) {
            $this->fail(new Exception('External code is empty'));
            return;
        }

        try {

            $result = match ($booking->provider) {
                Provider::GOGLOBAL => $this->handleGoGlobal($booking),
                Provider::DB => throw new Exception('Not external booking'),
            };

        } catch (Exception $e) {
            $this->fail($e);
            return;
        }

        $booking->update([
            'status' => $result['status']
        ]);
    }

    /**
     * @throws Exception
     */
    private function handleGoGlobal(Booking $booking): array
    {
        $service = new BookingStatusService();

        return $service->get(['external_code' => $booking->external_code]);
    }
}
