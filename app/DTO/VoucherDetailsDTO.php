<?php

namespace App\DTO;

use App\Enums\RoomBasis;
use Carbon\Carbon;

final class VoucherDetailsDTO extends DTO
{
    public function __construct(
        private readonly string $hotelTitle,
        private readonly string $address,
        private readonly string $phone,
        private readonly string $fax,
        private readonly RoomBasis $roomBasis,
        private readonly Carbon $checkinDate,
        private readonly Carbon $departureDate,
        private readonly string $leadPearson,
        private readonly ?string $remark = null,
        private readonly ?string $emergencyPhone = null,
    ) {}

    /**
     * @return string
     */
    public function getHotelTitle(): string
    {
        return $this->hotelTitle;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getFax(): string
    {
        return $this->fax;
    }

    /**
     * @return RoomBasis
     */
    public function getRoomBasis(): RoomBasis
    {
        return $this->roomBasis;
    }

    /**
     * @return Carbon
     */
    public function getCheckinDate(): Carbon
    {
        return $this->checkinDate;
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
    public function getLeadPearson(): string
    {
        return $this->leadPearson;
    }

    /**
     * @return string|null
     */
    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * @return string|null
     */
    public function getEmergencyPhone(): ?string
    {
        return $this->emergencyPhone;
    }
}
