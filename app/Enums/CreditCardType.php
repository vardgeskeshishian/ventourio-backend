<?php

namespace App\Enums;

enum CreditCardType: string
{
    case MASTERCARD = 'master';
    case VISA = 'visa';
    case AMEX = 'amex';
    case DINERS = 'diners';
}
