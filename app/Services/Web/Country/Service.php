<?php

namespace App\Services\Web\Country;

use App\Exceptions\BusinessException;
use App\Models\Country;
use App\Services\Web\LocationService;
use App\Services\Web\WebService;
use Illuminate\Database\Eloquent\Collection;

final class Service extends WebService
{
    public function define(string $ip): Country
    {
        $location = LocationService::getPosition($ip);

        if (empty($location)) {
            throw new BusinessException(__('errors.app.country.can_not_define'));
        }

        $countryCode = $location->countryCode;
        $countryTitle = $location->countryName;

        $country = Country::where('iso_code', $countryCode)->orWhere('title_l->en', $countryTitle)->first();
        if (empty($country)) {
            throw new BusinessException(__('errors.app.country.can_not_define'));
        }

        return $country;
    }

    public function getNationalityListForSelect(): Collection
    {
        return Country::get(['id', 'iso_code', 'nationality_l->' . $this->locale . ' as title']);
    }
}
