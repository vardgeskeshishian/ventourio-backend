<?php

namespace App\DTO\GoGlobal;

use App\DTO\DTO;

final class MakeOffersDTO extends DTO
{
    public function __construct(
        private readonly array $offersByHotel,
        private readonly array $hotel,
        private readonly string $searchSession,
        private readonly string $currency,
        private readonly int $nights,
    ) {}

    /**
     * @return array
     */
    public function getOffersByHotel(): array
    {
        return $this->offersByHotel;
    }

    /**
     * @return array
     */
    public function getHotel(): array
    {
        return $this->hotel;
    }

    /**
     * @return string
     */
    public function getSearchSession(): string
    {
        return $this->searchSession;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return int
     */
    public function getNights(): int
    {
        return $this->nights;
    }
}
