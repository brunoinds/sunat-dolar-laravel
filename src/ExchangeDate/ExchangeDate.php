<?php

namespace Brunoinds\SunatDolarLaravel\ExchangeDate;

use Brunoinds\SunatDolarLaravel\Converter\Converter;
use Brunoinds\SunatDolarLaravel\Enums\Currency;
use Brunoinds\SunatDolarLaravel\ExchangeTransaction\ExchangeTransaction;
use DateTime;
use Brunoinds\SunatDolarLaravel\Store\Store;


class ExchangeDate{
    public DateTime $date;

    public function __construct(DateTime $date){
        if (!Converter::$store){
            Converter::$store = Store::newFromLaravelCache();
        }
        
        $this->date = $date;
    }

    public function getDollarSellPrice(): float{
        return Converter::getDailyDollarExchangeRate($this->date)->price->sell;
    }
    public function getDollarBuyPrice(): float{
        return Converter::getDailyDollarExchangeRate($this->date)->price->buy;
    }
    public function getSolesSellPrice(): float{
        return Converter::getDailySolesExchangeRate($this->date)->price->sell;
    }
    public function getSolesBuyPrice(): float{
        return Converter::getDailySolesExchangeRate($this->date)->price->buy;
    }

    public function convert(Currency $currency, float $amount): ExchangeTransaction{
        return new ExchangeTransaction($this, $currency, $amount);
    }
}