<?php
namespace Brunoinds\SunatDolarLaravel;

//Require composer autoload
require __DIR__ . '/../vendor/autoload.php';

use Brunoinds\SunatDolarLaravel\Enums\Currency;
use Brunoinds\SunatDolarLaravel\Exchange;
use DateTime;

$result = Exchange::on(DateTime::createFromFormat('Y-m-d', '2023-12-10'))->convert(Currency::USD, 1)->to(Currency::PEN);
var_dump($result);


$date = DateTime::createFromFormat('Y-m-d', '2023-12-10');

$result = Exchange::on($date)
                    ->convert(Currency::USD, 1)
                    ->to(Currency::PEN);

echo $result; // 0.27


Exchange::now()->convert(Currency::USD, 1)->to(Currency::PEN); // 0.32


Exchange::on($date)->getDollarBuyPrice(); //3.749;
Exchange::on($date)->getDollarSellPrice(); // 3.757
Exchange::on($date)->getSolesBuyPrice(); // 0.266
Exchange::on($date)->getSolesSellPrice(); // 0.27