<?php

namespace App\Observers;

use App\Enums\CacheKey;
use App\Models\Currency;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class CurrencyObserver
{
    /**
     * Handle the Currency "saved" event.
     *
     * @param Currency $currency
     * @return void
     */
    public function saved(Currency $currency): void
    {
        Cache::forget(CacheKey::CURRENCIES->value);
    }

    /**
     * Handle the Currency "deleted" event.
     *
     * @param Currency $currency
     * @return void
     */
    public function deleted(Currency $currency): void
    {
        Cache::forget(CacheKey::CURRENCIES->value);
    }
}
