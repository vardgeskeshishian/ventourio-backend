<?php

namespace App\Models\System;

interface InteractsWithUserBalance
{
    public function getUserId(): int;

    /**
     * @return string
     */
    public function getMorphClass();

    public function getId(): int;

    public function getAmount(): float;

}
