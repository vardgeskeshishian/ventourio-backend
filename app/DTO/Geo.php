<?php

namespace App\DTO;

final class Geo
{
    private function __construct(private readonly float $latitude, private readonly float $longitude) {}

    public static function create(float|string $latitude, string $longitude): Geo
    {
        return new Geo((float) $latitude, (float) $longitude);
    }

    public function value(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude
        ];
    }

    public function latitude(): float
    {
        return $this->latitude;
    }

    public function longitude(): float
    {
        return $this->longitude;
    }
}
