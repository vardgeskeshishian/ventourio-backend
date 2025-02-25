<?php

namespace App\Jobs\ImportParsed;

use App\Models\Continent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportParsedContinentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public int $continentIndex, public string $filePath) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        if ( ! file_exists($this->filePath)) {
            $this->fail(new FileNotFoundException());
            return;
        }

        $continents = json_decode(file_get_contents($this->filePath), true);

        $continentData = $continents[$this->continentIndex] ?? null;
        if (empty($continentData)) {
            $this->fail(new \Exception('ContinentIndex not exists | ' . json_encode(array_keys($continents))));
            return;
        }

        $continentTitle = $continentData['name'] ?? [];

        if (empty(array_filter($continentTitle))) {
            Log::error('continent doesnt have correct format', $continentTitle);
            return;
        }

        $continent = Continent::create([
            'title_l' => $continentTitle,
            'parsing_source' => $continentData['source_id'] ?? null
        ]);

        if (empty($countries = $continentData['countries'] ?? null)) {
            return;
        }

        foreach ($countries as $countryData) {
            ImportParsedCountryJob::dispatch($countryData, $continent);
        }
    }
}
