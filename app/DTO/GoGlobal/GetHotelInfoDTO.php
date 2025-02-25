<?php

namespace App\DTO\GoGlobal;

use App\DTO\DTO;

final class GetHotelInfoDTO extends DTO
{
    public function __construct(
        private readonly int|string $id,
        private readonly GoGlobalLanguage $language
    ) {}

    /**
     * @return int|string
     */
    public function getId(): int|string
    {
        return $this->id;
    }

    /**
     * @return GoGlobalLanguage
     */
    public function getLanguage(): GoGlobalLanguage
    {
        return $this->language;
    }
}
