<?php

namespace App\Jobs;

use App\Models\Hotel;
use App\Services\GoGlobal\HotelUpdateService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateSpecificHotelByGoGlobalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public string $locale, public int $hotelId)
    {
        $this->onQueue('goglobal-hotel-update');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $hotel = Hotel::with('facilities:id', 'media')->find($this->hotelId);
        if ( ! $hotel) {
            $this->fail(new ModelNotFoundException('Hotel with id ' . $this->hotelId . ' not found.'));
            return;
        }

        try {

            (new HotelUpdateService())->facilities(3)->images(1)
                ->do($hotel, $this->locale);

        } catch (Exception $e) {
            $this->fail($e);
            return;
        }
    }
}
