<?php

namespace App\Enums;

enum TransactionStatus: int
{
    case WAITING = 1;
    case COMPLETED = 2;
}
