<?php

namespace App\Enums;

enum ExternalPaymentMethodType: int
{
    case CREDIT = 0;
    case CREDIT_CARD = 1;
}
