<?php

namespace App\Enums;

enum DiscountType:int
{
    case PERCENT = 0;
    case SUBTRACT = 1;
}
