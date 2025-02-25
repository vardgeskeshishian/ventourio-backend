<?php

namespace App\Jobs;

use App\Models\Hotel;
use App\Services\GoGlobal\HotelUpdateService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateHotelByGoGlobalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public string $locale, public int $offset, public int $step = 1)
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
        $hotel = Hotel::orderBy('id')
            ->with('facilities:id', 'media')
            ->offset($this->offset)
            ->first();

        if ( ! $hotel) {
            return;
        }

        UpdateHotelByGoGlobalJob::dispatch(locale: $this->locale, offset: $this->offset + $this->step, step: $this->step);

        try {

            (new HotelUpdateService())->facilities(3)->images(1)
                ->do($hotel, $this->locale);

        } catch (Exception $e) {
            $this->fail($e);
            return;
        }
    }
}
