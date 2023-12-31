<?php

namespace Brunoinds\SunatDolarLaravel\ExchangeTransaction;

use Brunoinds\SunatDolarLaravel\Converter\Converter;
use Brunoinds\SunatDolarLaravel\Enums\Currency;
use Brunoinds\SunatDolarLaravel\ExchangeDate\ExchangeDate;

class ExchangeTransaction
{
    private ExchangeDate $exchangeDate;
    private Currency $currency;
    private float $amount;

    public function __construct(ExchangeDate $exchangeDate, Currency $currency, float $amount)
    {
        $this->exchangeDate = $exchangeDate;
        $this->currency = $currency;
        $this->amount = $amount;
    }

    public function to(Currency $currency): float
    {
        if ($this->currency === $currency) {
            return $this->amount;
        }

        if ($this->currency === Currency::PEN){
            return Converter::convertSolesToDollar($this->exchangeDate->date, $this->amount);
        }elseif ($this->currency === Currency::USD){
            return Converter::convertDollarToSoles($this->exchangeDate->date, $this->amount);
        }

        throw new \Exception('Invalid currency');
    }
}