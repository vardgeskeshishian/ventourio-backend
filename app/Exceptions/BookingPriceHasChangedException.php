<?php

namespace App\Exceptions;

use Exception;

class BookingPriceHasChangedException extends BusinessException
{
    public function __construct(private float $newPrice)
    {
        parent::__construct(__('errors.app.booking.different_price', ['value' => $newPrice]));
    }

    public function getNewPrice(): float
    {
        return $this->newPrice;
    }
}
