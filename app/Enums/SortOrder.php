<?php

namespace App\Enums;

enum SortOrder:int
{
    case BY_PRICE_ASC      = 1;
    case BY_PRICE_DESC     = 2;
//    case BY_DEADLINE_ASC   = 3;
//    case BY_DEADLINE_DESC  = 4;
    case BY_STARS_ASC      = 5;
    case BY_STARS_DESC     = 6;
    case BY_RATING_ASC     = 7;
    case BY_RATING_DESC    = 8;
    case BY_DISCOUNT       = 9;
    case BY_TITLE_ASC      = 10;
    case BY_TITLE_DESC     = 11;
}
