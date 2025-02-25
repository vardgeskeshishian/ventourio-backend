<?php

namespace App\Jobs\ImportParsed;

use App\Jobs\InteractsWithParsed;
use App\Models\Continent;
use App\Models\Country;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class ImportParsedCountryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, InteractsWithParsed;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public array $countryData, public Continent $continent) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $countryTitle = $this->countryData['name'] ?? [];

        if (empty(array_filter($countryTitle)) || empty($countryTitle['en'])) {
            Log::error('country doesnt have correct format', $countryTitle);
            return;
        }

        # убираем рф из импорта
        if ($countryTitle['en'] === 'Russia') {
            return;
        }

        $country = Country::create([
            'continent_id' => $this->continent->id,
            'title_l' => $countryTitle,
            'nationality_l' => $countryTitle,
            'parsing_source' => $this->countryData['source_id'] ?? null
        ]);

        try {
            $country->addMediaFromUrl($this->parsedDomain . $this->countryData['flag'])
                ->toMediaCollection('flag');
        } catch (FileDoesNotExist|FileIsTooBig|FileCannotBeAdded $e) {
            Log::error('country can not attach image', $countryTitle);
        }

        if (empty($regions = $this->countryData['regions'] ?? null)) {
            return;
        }

        foreach ($regions as $regionData) {
            ImportParsedRegionJob::dispatch($regionData, $country);
        }
    }
}
