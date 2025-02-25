<?php

namespace App\Jobs\GoGlobal;

use App\Models\Country;
use App\Models\Region;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AttachCountryWithCitiesToDBJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const CITY_EXTERNAL_ID_KEY = 0;
    const CITY_NAME_KEY = 1;
    const COUNTRY_EXTERNAL_ID_KEY = 2;
    const COUNTRY_NAME_KEY = 3;
    const COUNTRY_ISO_CODE_KEY = 4;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Collection $data) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $countryId = $this->getCountryId();
        if ( ! isset($countryId)) {
            return;
        }

        $baseRegion = Region::where('is_common', true)
            ->where('country_id', $countryId)
            ->first();

        if (empty($baseRegion)) {
            $baseRegion = Region::create([
                'country_id' => $countryId,
                'title_l' => ['en' => 'Base'],
                'is_common' => true
            ]);
        }

        foreach ($this->data as $city) {

            if (empty($city)) {
                continue;
            }

            AttachCityJob::dispatch($city, $baseRegion->id, $countryId);
        }
    }

    private function getCountryId(): ?int
    {
        $firstInstance = $this->data->first();

        $countryExternalId = $firstInstance[self::COUNTRY_EXTERNAL_ID_KEY];
        $countryName = $firstInstance[self::COUNTRY_NAME_KEY];
        $countryIsoCode = $firstInstance[self::COUNTRY_ISO_CODE_KEY];

        $country = Country::query()
            ->whereHas('page', function ($query) use ($countryName) {
                $query->where('slug', Str::slug($countryName));
            })
            ->orWhereRaw("LOWER(title_l->'$.en') like ?", '%'.strtolower($countryName).'%')
            ->first();


        if ( ! $country) {
            // todo create Country
            return null;
        }

        $country->update([
            'external_code' => $countryExternalId,
            'iso_code' => $countryIsoCode
        ]);

        return $country->id;
    }
}
