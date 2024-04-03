<?php

namespace Brunoinds\SunatDolarLaravel;


use DateTime;
use Brunoinds\SunatDolarLaravel\ExchangeDate\ExchangeDate;
use Brunoinds\SunatDolarLaravel\Store\Store;
use Brunoinds\SunatDolarLaravel\Converter\Converter;

class Exchange{
    public static function on(DateTime $date): ExchangeDate
    {
        return new ExchangeDate($date);
    }
    public static function now():ExchangeDate{
        return new ExchangeDate(new DateTime());
    }
    public static function useStore(Store $store)
    {
        Converter::$store = $store;
    }
}