<?php

namespace App\Mixins;

class StrOnlyNumber
{
    /**
     * @return \Closure
     */
    public function onlyNumber()
    {
        return function($phone) {
            return preg_replace('/[^0-9]/', '', $phone);
        };
    }
}
