<?php

namespace App\DTO;

final class PhoneNumber
{
    private int $phone;

    private function __construct(string|int $phone)
    {
        $this->phone = preg_replace('/[^0-9]/', '', $phone);
    }

    public static function create(string|int $phone): PhoneNumber
    {
        return new PhoneNumber($phone);
    }

    public function value(): int
    {
        return $this->phone;
    }
}
