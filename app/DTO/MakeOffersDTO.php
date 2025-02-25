<?php

namespace App\DTO;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

final class MakeOffersDTO extends DTO
{
    public function __construct(
        private readonly Collection|EloquentCollection $roomBases,
        private readonly int $stars,
        private readonly string|int $hotelExternalCode,
        private readonly int $hotelId,
        private readonly array $rooms,
        private readonly Carbon $arrivalDate,
        private readonly Carbon $departureDate,
        private readonly ?string $searchSession = null,
        private readonly ?array $prices = null,
    ) {}

    /**
     * @return int
     */
    public function getRoomsCount(): int
    {
        return count($this->rooms);
    }

    /**
     * @return EloquentCollection|Collection
     */
    public function getRoomBases(): EloquentCollection|Collection
    {
        return $this->roomBases;
    }

    /**
     * @return int
     */
    public function getStars(): int
    {
        return $this->stars;
    }

    /**
     * @return int|string
     */
    public function getHotelExternalCode(): int|string
    {
        return $this->hotelExternalCode;
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
    public function getPrices(): ?array
    {
        return $this->prices;
    }

    public function getDaysCount(): int
    {
        return $this->arrivalDate->diffInDays($this->departureDate);
    }

    /**
     * @return int
     */
    public function getHotelId(): int
    {
        return $this->hotelId;
    }

    /**
     * @return string
     */
    public function getSearchSession(): string
    {
        return $this->searchSession;
    }

    public function getNights(): int
    {
        return $this->departureDate->diffInDays($this->arrivalDate);
    }
}
