<?php

namespace App\Services;

use App\Models\Currency;

class CurrencyService
{
    /**
     * Get current exchange rate for a currency relative to base currency (IDR)
     */
    public function getExchangeRate($currencyId, $companyId)
    {
        $currency = Currency::where('id', $currencyId)
            ->where('company_id', $companyId)
            ->first();

        if (!$currency) {
            return 1.0;
        }

        // In a real app, this might fetch from an external API or a rates table
        // For now, we use the rate stored in the currencies table
        return $currency->exchange_rate ?? 1.0;
    }

    /**
     * Convert amount from given currency to base currency
     */
    public function convertToBase($amount, $rate)
    {
        return $amount * $rate;
    }

    /**
     * Convert amount from base currency to target currency
     */
    public function convertFromBase($amount, $rate)
    {
        return $rate != 0 ? $amount / $rate : $amount;
    }
}
