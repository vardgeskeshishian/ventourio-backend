<?php

namespace App\Helpers;


final class CurrencyStorage
{
    private ?string $currency = null;

    public function get(): ?string
    {
        return $this->currency;
    }

    public function set(string $currency): string
    {
        return $this->currency = $currency;
    }
}
