<?php

namespace App\Enums;

enum RoomBasis: int
{
    case BED_AND_BREAKFAST = 0;
    case CONTINENTAL_BREAKFAST = 1;
    case ALL_INCLUSIVE = 2;
    case FULL_BOARD = 3;
    case HALF_BOARD = 4;
    case ROOM_ONLY = 5;
    case BED_AND_DINNER = 6;
}
