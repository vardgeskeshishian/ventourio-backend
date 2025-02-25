<?php

namespace App\Jobs\GoGlobal;

use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\Hotel;
use App\Models\Region;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportHotelsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Collection $hotels) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->hotels as $hotel) {

            if (empty($hotel)) {
                continue;
            }

            ImportHotelJob::dispatch($hotel);
        }
    }
}
