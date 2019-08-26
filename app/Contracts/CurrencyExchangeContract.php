<?php

namespace App\Contracts;

interface CurrencyExchangeContract
{
    /**
     * fetch list of currencies from external API 
     */
    public function listCurrencies() : array;

    /**
     * fetch exchange rate (against USD) for a currency using its code
     */
    public function rate(string $code) : float;
}
