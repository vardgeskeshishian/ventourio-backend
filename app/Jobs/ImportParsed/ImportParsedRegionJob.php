<?php

namespace App\Jobs\ImportParsed;

use App\Models\Country;
use App\Models\Region;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportParsedRegionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public array $regionData, public Country $country) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $regionTitle = $this->regionData['name'] ?? [];

        if (empty(array_filter($regionTitle)) || empty($regionTitle['en'])) {
            Log::error('region doesnt have correct format', $regionTitle);
            return;
        }

        $region = Region::create([
            'country_id' => $this->country->id,
            'title_l' => $regionTitle,
            'parsing_source' => $this->regionData['source_id'] ?? null
        ]);

        if (empty($cities = $this->regionData['cities'] ?? null)) {
            return;
        }

        foreach ($cities as $cityData) {
            ImportParsedCityJob::dispatch($cityData, $region);
        }
    }
}
