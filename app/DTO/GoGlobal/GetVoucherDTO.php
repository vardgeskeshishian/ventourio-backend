<?php

namespace App\DTO\GoGlobal;

use App\DTO\DTO;

final class GetVoucherDTO extends DTO
{
    public function __construct(
        private readonly string $bookingCode,
    ) {}

    /**
     * @return string
     */
    public function getBookingCode(): string
    {
        return $this->bookingCode;
    }
}
