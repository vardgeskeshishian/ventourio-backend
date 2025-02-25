<?php

namespace App\Models\System;

trait CanChangeUserBalance
{
    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}
