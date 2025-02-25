<?php

namespace App\Helpers;

use App\Models\Currency;
use Exception;

final class CurrencyConverter
{
    const FORMAT_CURRENCY_AMOUNT = 0;

    /**
     * @throws Exception
     */
    public static function convert(float $amount, Currency|int|string $from, Currency|int|string $to): float
    {
        # Если int|string и одинаковые, то не нужно конвертировать
        if (is_scalar($from) && is_scalar($to) && $from === $to) {
            return $amount;
        }

        $currencyFrom = self::getCurrency($from);
        $currencyTo   = self::getCurrency($to);

        # Если одинаковые валюты, то не нужно конвертировать
        if ($currencyFrom->id === $currencyTo->id) {
            return $amount;
        }

        if ( ! $currencyFrom->is_main) {
            $amountInMain = self::toMain($amount, $currencyFrom);
        } else {
            $amountInMain = $amount;
        }

        if ($currencyTo->is_main) {
            return $amountInMain;
        }

        return self::fromMain($amountInMain, $currencyTo);
    }

    /**
     * @throws Exception
     */
    public static function toMain(float $amount, Currency|int|string $from): float
    {
        $currency = self::getCurrency($from);

        if ($currency->is_main) {
            return $amount;
        }

        if (empty($rate = $currency->currency_rate)) {
            throw new Exception(__('errors.system.currency.empty_rate', ['value' => $currency->code]));
        }

        return round($amount * $rate, 2);
    }

    /**
     * @throws Exception
     */
    public static function fromMain(float $amount, Currency|int|string $to, int $format = null): string|float
    {
        $currency = self::getCurrency($to);

        if (empty($rate = $currency->currency_rate)) {
            throw new Exception(__('errors.system.currency.empty_rate', ['value' => $currency->code]));
        }

        if ($currency->is_main) {
            $convertedAmount = $amount;
        } else {
            $convertedAmount = round($amount / $rate, '2');
        }

        if (isset($format)) {
            $convertedAmount = self::format($convertedAmount, $currency, $format);
        }

        return $convertedAmount;
    }

    private static function format(float $amount, Currency $currency, int $format): string
    {
        return match ($format) {
            self::FORMAT_CURRENCY_AMOUNT => $currency->symbol . ' ' . number_format($amount, 0, '.', ' ')
        };
    }

    /**
     * @throws Exception
     */
    private static function getCurrency(Currency|int|string $currency): Currency
    {
        $currencies = Currency::getCached();

        if (is_int($currency)) {
            $currency = $currencies->where('id', $currency)->first();

        } elseif (is_string($currency)) {
            $currency = $currencies->where('code', str($currency)->lower())->first();
        }

        if ( ! $currency || ! $currency->exists()) {
            throw new Exception(__('errors.system.currency.not_exists', ['value' => $currency]));
        }

        return $currency;
    }
}
