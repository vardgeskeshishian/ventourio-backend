<?php

namespace App\Helpers;

final class UX
{
    public static function generateHexColor(): string
    {
        $colorPart = fn() => str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);

        return '#' . $colorPart() . $colorPart() . $colorPart();
    }
}
