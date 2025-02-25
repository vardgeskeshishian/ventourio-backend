<?php

namespace App\DTO\GoGlobal;

use App\DTO\DTO;
use Carbon\Carbon;

final class ValuateGoGlobalBookingDTO extends DTO
{
    public function __construct(
        private readonly string $searchCode,
        private readonly Carbon $arrivalDate
    ) {}

    /**
     * @return string
     */
    public function getSearchCode(): string
    {
        return $this->searchCode;
    }

    /**
     * @return Carbon
     */
    public function getArrivalDate(): Carbon
    {
        return $this->arrivalDate;
    }
}
