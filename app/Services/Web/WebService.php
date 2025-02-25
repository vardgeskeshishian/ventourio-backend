<?php

namespace App\Services\Web;

use App\Helpers\CurrencyStorage;
use App\Models\Currency;
use App\Services\MainService;

class WebService extends MainService
{
    protected string $locale;
    protected string $currency;

    public function __construct()
    {
        $this->locale = app()->getLocale();

        $currency = app(CurrencyStorage::class)->get();
        if (empty($currency)) {
            $currency = Currency::getMain()->code;
        }

        $this->currency = $currency;
    }
}
