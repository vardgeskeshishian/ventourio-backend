<?php

namespace App\DTO\GoGlobal;

use App\DTO\DTO;
use Carbon\Carbon;

final class BookHotelDTO extends DTO
{
    public function __construct(
        private readonly string $searchCode,
        private readonly Carbon $arrivalDate,
        private readonly Carbon $departureDate,
        private readonly array $rooms,
        private readonly array $paymentInfo,
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

    /**
     * @return Carbon
     */
    public function getDepartureDate(): Carbon
    {
        return $this->departureDate;
    }

    /**
     * @return array
     */
    public function getRooms(): array
    {
        return $this->rooms;
    }

    /**
     * @return array
     */
    public function getPaymentInfo(): array
    {
        return $this->paymentInfo;
    }
}
