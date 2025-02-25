<?php

namespace App\DTO;

use App\DTO\GoGlobal\ValuateGoGlobalBookingDTO;
use App\Enums\Provider;
use App\Models\Currency;
use Carbon\Carbon;

final class ValuateBookingDTO extends DTO
{
    public function __construct(
        private readonly string $searchCode,
        private readonly Provider $provider,
        private readonly Carbon $arrivalDate,
        private readonly Carbon $departureDate,
        private readonly string $currency,
        private readonly float $amount
    ) {}

    /**
     * @return string
     */
    public function getSearchCode(): string
    {
        return $this->searchCode;
    }

    /**
     * @return Provider
     */
    public function getProvider(): Provider
    {
        return $this->provider;
    }

    /**
     * @return Carbon
     */
    public function getArrivalDate(): Carbon
    {
        return $this->arrivalDate;
    }

    /**
     * @return Carbon
     */
    public function getDepartureDate(): Carbon
    {
        return $this->departureDate;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }
}
