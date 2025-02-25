<?php

namespace App\DTO;

use App\Enums\SortOrder;

final class SearchHotelsDTO extends DTO
{
    public function __construct(
        private readonly string $nationality,
        private readonly ?string $regionSlug = null,
        private readonly ?string $citySlug = null,
        private readonly ?array $districtSlug = null,
        private readonly ?array $rooms = null,
        private readonly ?array $prices = null,
        private readonly ?array $dates = null,
        private readonly ?array $stars = null,
        private readonly ?array $roomBasis = null,
        private readonly ?SortOrder $sortOrder = null,
        private readonly ?int $page = null,
        private readonly ?bool $onlyDiscount = false,
    ) {}

    /**
     * @return string
     */
    public function getNationality(): string
    {
        return $this->nationality;
    }

    /**
     * @return string|null
     */
    public function getRegionSlug(): ?string
    {
        return $this->regionSlug;
    }

    /**
     * @return string|null
     */
    public function getCitySlug(): ?string
    {
        return $this->citySlug;
    }

    /**
     * @return array|null
     */
    public function getDistrictSlug(): ?array
    {
        return $this->districtSlug;
    }

    /**
     * @return array|null
     */
    public function getRooms(): ?array
    {
        return $this->rooms;
    }

    /**
     * @return array|null
     */
    public function getPrices(): ?array
    {
        return $this->prices;
    }

    /**
     * @return array|null
     */
    public function getDates(): ?array
    {
        return $this->dates;
    }

    /**
     * @return array|null
     */
    public function getStars(): ?array
    {
        return $this->stars;
    }

    /**
     * @return array|null
     */
    public function getRoomBasis(): ?array
    {
        return $this->roomBasis;
    }

    /**
     * @return SortOrder|null
     */
    public function getSortOrder(): ?SortOrder
    {
        return $this->sortOrder;
    }

    /**
     * @return int|null
     */
    public function getPage(): ?int
    {
        return $this->page;
    }

    /**
     * @return bool|null
     */
    public function getOnlyDiscount(): ?bool
    {
        return $this->onlyDiscount;
    }

    public function toArray(): array
    {
        return [
            'nationality' => $this->getNationality(),
            'region_slug' => $this->getRegionSlug(),
            'city_slug' => $this->getCitySlug(),
            'district_slug' => $this->getDistrictSlug(),
            'rooms' => $this->getRooms(),
            'prices' => $this->getPrices(),
            'dates' => $this->getDates(),
            'stars' => $this->getStars(),
            'room_basis' => $this->getRoomBasis(),
            'sort' => $this->getSortOrder()?->value,
            'page' => $this->getPage(),
            'only_discount' => $this->getOnlyDiscount(),
        ];
    }
}
