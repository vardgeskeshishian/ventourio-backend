<?php

namespace App\Enums;

enum RatingCategory: string
{
    case STAFF           = 'staff';
    case FACILITIES      = 'facilities';
    case CLEANLINESS     = 'cleanliness';
    case COMFORT         = 'comfort';
    case VALUE_FOR_MONEY = 'value_for_money';
    case LOCATION        = 'location';
    case FREE_WIFI       = 'wifi';
}
