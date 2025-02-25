<?php

namespace App\Exceptions;

use RuntimeException;

class RoomAlreadyBookedException extends BusinessException
{
    public function __construct()
    {
        parent::__construct(__('errors.app.booking.already_booked'));
    }
}
