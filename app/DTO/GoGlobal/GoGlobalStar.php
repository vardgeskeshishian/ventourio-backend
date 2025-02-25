<?php

namespace App\DTO\GoGlobal;

use Exception;

final class GoGlobalStar
{
    private int $code;

    /**
     * @throws Exception
     */
    private function __construct(int $stars)
    {
        $availableStars = collect(config('goglobal.stars'));

        if ($stars === 0) {
            $stars = 1;
        }

        $code = $availableStars->where('matched', $stars)->first()['code'] ?? null;
        if (empty($code)) {
            throw new Exception("Star code for '$stars' stars not found in config");
        }

        $this->code = $code;
    }

    /**
     * @throws Exception
     */
    public static function create(int $stars): GoGlobalStar
    {
        return new GoGlobalStar($stars);
    }

    public function value(): int
    {
        return $this->code;
    }
}
