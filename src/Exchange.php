<?php

namespace Brunoinds\SunatDolarLaravel;


use DateTime;
use Brunoinds\SunatDolarLaravel\ExchangeDate\ExchangeDate;

class Exchange{
    public static function on(DateTime $date): ExchangeDate
    {
        return new ExchangeDate($date);
    }
    public static function now():ExchangeDate{
        return new ExchangeDate(new DateTime());
    }
}