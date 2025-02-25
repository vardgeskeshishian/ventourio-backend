<?php

namespace App\DTO\GoGlobal;

use App\DTO\DTO;
use App\Enums\SortOrder;
use Carbon\Carbon;

final class SearchHotelsDTO extends DTO
{
    public function __construct(
        private readonly string $nationality,
        private readonly array $rooms,
        private readonly Carbon $arrivalDate,
        private readonly Carbon $departureDate,
        private readonly ?array $hotels = null,
        private readonly ?int $cityCode = null,
        private readonly ?array $prices = null,
        private readonly string $currency = 'USD',
        private readonly ?SortOrder $sortOrder = SortOrder::BY_PRICE_ASC,
        private readonly ?GoGlobalStar $minStar = null,
        private readonly ?GoGlobalStar $maxStar = null,
        private readonly ?array $roomBasises = null
    ) {}

    /**
     * @return string
     */
    public function getNationality(): string
    {
        return $this->nationality;
    }

    /**
     * @return array
     */
    public function getRooms(): array
    {
        return $this->rooms;
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
     * @return array|null
     */
    public function getHotels(): ?array
    {
        return $this->hotels;
    }

    /**
     * @return int|null
     */
    public function getCityCode(): ?int
    {
        return $this->cityCode;
    }

    /**
     * @return array|null
     */
    public function getPrices(): ?array
    {
        return $this->prices;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return SortOrder|null
     */
    public function getSortOrder(): ?SortOrder
    {
        return $this->sortOrder;
    }

    /**
     * @return GoGlobalStar|null
     */
    public function getMinStar(): ?GoGlobalStar
    {
        return $this->minStar;
    }

    /**
     * @return GoGlobalStar|null
     */
    public function getMaxStar(): ?GoGlobalStar
    {
        return $this->maxStar;
    }

    /**
     * @return array|null
     */
    public function getRoomBasises(): ?array
    {
        return $this->roomBasises;
    }

    public function toArray(): array
    {
        return [
            'nationality' => $this->getNationality(),
            'rooms' => $this->getRooms(),
            'arrival_date' => $this->getArrivalDate(),
            'departure_date' => $this->getDepartureDate(),
            'hotels' => $this->getHotels(),
            'city_code' => $this->getCityCode(),
            'prices' => $this->getPrices(),
            'currency' => $this->getCurrency(),
            'sort_order' => $this->getSortOrder(),
            'min_star' => $this->getMinStar(),
            'max_star' => $this->getMaxStar(),
            'room_basises' => $this->getRoomBasises(),
        ];
    }
}
