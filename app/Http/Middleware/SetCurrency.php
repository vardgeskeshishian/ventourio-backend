<?php

namespace App\Http\Middleware;

use App\Helpers\CurrencyStorage;
use App\Models\Currency;
use Closure;
use Exception;
use Illuminate\Http\Request;

class SetCurrency
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->hasHeader('X-currency')) {
            $currency = $request->header('X-currency');
        } else {
            $currency = Currency::getMain()->code;
        }

        app(CurrencyStorage::class)->set($currency);

        return $next($request);
    }
}
