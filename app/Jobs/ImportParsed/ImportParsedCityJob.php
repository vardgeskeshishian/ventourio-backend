<?php

namespace App\Jobs\ImportParsed;

use App\Models\City;
use App\Models\Region;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportParsedCityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public array $cityData, public Region $region) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $cityTitle = $this->cityData['name'] ?? [];

        if (empty(array_filter($cityTitle)) || empty($cityTitle['en'])) {
            Log::error('city doesnt have correct format', $cityTitle);
            return;
        }

        City::create([
            'region_id' => $this->region->id,
            'title_l' => $cityTitle,
            'parsing_source' => $this->cityData['source_id'] ?? null
        ]);
    }
}
