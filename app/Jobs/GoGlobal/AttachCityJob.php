<?php

namespace App\Jobs\GoGlobal;

use App\Models\City;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class AttachCityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public array $cityData, public int $baseRegionId, public int $countryId) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $cityExternalId = $this->cityData[AttachCountryWithCitiesToDBJob::CITY_EXTERNAL_ID_KEY];
        $cityName = $this->cityData[AttachCountryWithCitiesToDBJob::CITY_NAME_KEY];
        $countryId = $this->countryId;

        $suitableCity = City::query()
            ->where(function ($query) use ($cityName) {
                $query->whereHas('page', function ($query) use ($cityName) {
                    $query->where('slug', Str::slug($cityName));
                })->orWhere('title_l->en', Str::ucfirst(Str::lower($cityName)));
            })
            ->whereHas('region', function (Builder $query) use ($countryId) {
                $query->where('country_id', $countryId);
            })
            ->first();

        if ($suitableCity) {
            # Если найден город

            $suitableCity->update([
                'external_code' => $cityExternalId,
            ]);
        } else {
            # Если город не найден

            City::create([
                'title_l' => ['en' => $cityName],
                'region_id' => $this->baseRegionId,
                'external_code' => $cityExternalId,
                'show_in_best_deals' => 0,
            ]);
        }
    }
}
