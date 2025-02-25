<?php

namespace App\DTO;

use App\Enums\Provider;

final class CreateHotelDTO extends DTO
{
    public function __construct(
        private readonly Provider $createBy,
        private ?string $externalCode = null,
        private ?array $titleL = null,
        private ?int $cityId = null,
        private ?string $address = null,
        private ?PhoneNumber $phoneNumber = null,
        private ?string $fax = null,
        private ?int $stars = null,
        private ?Geo $geo = null,
        private ?bool $isApartment = null,
        private ?string $giataCode = null,
    ) {}

    /**
     * @return Provider
     */
    public function getCreateBy(): Provider
    {
        return $this->createBy;
    }

    /**
     * @return string|null
     */
    public function getExternalCode(): ?string
    {
        return $this->externalCode;
    }

    /**
     * @return int|null
     */
    public function getCityId(): ?int
    {
        return $this->cityId;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @return PhoneNumber|null
     */
    public function getPhoneNumber(): ?PhoneNumber
    {
        return $this->phoneNumber;
    }

    /**
     * @return string|null
     */
    public function getFax(): ?string
    {
        return $this->fax;
    }

    /**
     * @return int|null
     */
    public function getStars(): ?int
    {
        return $this->stars;
    }

    /**
     * @return Geo|null
     */
    public function getGeo(): ?Geo
    {
        return $this->geo;
    }

    /**
     * @return bool|null
     */
    public function getIsApartment(): ?bool
    {
        return $this->isApartment;
    }

    /**
     * @return string|null
     */
    public function getGiataCode(): ?string
    {
        return $this->giataCode;
    }

    /**
     * @return array|null
     */
    public function getTitleL(): ?array
    {
        return $this->titleL;
    }

    /**
     * @param string|null $address
     * @return CreateHotelDTO
     */
    public function setAddress(?string $address): CreateHotelDTO
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @param string|null $externalCode
     * @return CreateHotelDTO
     */
    public function setExternalCode(?string $externalCode): CreateHotelDTO
    {
        $this->externalCode = $externalCode;
        return $this;
    }

    /**
     * @param array|null $titleL
     * @return CreateHotelDTO
     */
    public function setTitleL(?array $titleL): CreateHotelDTO
    {
        $this->titleL = $titleL;
        return $this;
    }

    /**
     * @param int|null $cityId
     * @return CreateHotelDTO
     */
    public function setCityId(?int $cityId): CreateHotelDTO
    {
        $this->cityId = $cityId;
        return $this;
    }

    /**
     * @param PhoneNumber|null $phoneNumber
     * @return CreateHotelDTO
     */
    public function setPhoneNumber(?PhoneNumber $phoneNumber): CreateHotelDTO
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * @param string|null $fax
     * @return CreateHotelDTO
     */
    public function setFax(?string $fax): CreateHotelDTO
    {
        $this->fax = $fax;
        return $this;
    }

    /**
     * @param int|null $stars
     * @return CreateHotelDTO
     */
    public function setStars(?int $stars): CreateHotelDTO
    {
        $this->stars = $stars;
        return $this;
    }

    /**
     * @param Geo|null $geo
     * @return CreateHotelDTO
     */
    public function setGeo(?Geo $geo): CreateHotelDTO
    {
        $this->geo = $geo;
        return $this;
    }

    /**
     * @param bool|null $isApartment
     * @return CreateHotelDTO
     */
    public function setIsApartment(?bool $isApartment): CreateHotelDTO
    {
        $this->isApartment = $isApartment;
        return $this;
    }

    /**
     * @param string|null $giataCode
     * @return CreateHotelDTO
     */
    public function setGiataCode(?string $giataCode): CreateHotelDTO
    {
        $this->giataCode = $giataCode;
        return $this;
    }
}
