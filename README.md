# PHP SUNAT Peru Currency Exchange

A simple PHP library for exchanging currencies based on Peruvian SUNAT exchange rates.

<p align="center">
<a href="https://packagist.org/packages/brunoinds/sunat-dolar-laravel"><img src="https://img.shields.io/packagist/dt/brunoinds/sunat-dolar-laravel" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/brunoinds/sunat-dolar-laravel"><img src="https://img.shields.io/packagist/v/brunoinds/sunat-dolar-laravel" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/brunoinds/sunat-dolar-laravel"><img src="https://img.shields.io/packagist/l/brunoinds/sunat-dolar-laravel" alt="License"></a>
</p>


## Installation

Install via Composer:

```bash
composer require brunoinds/sunat-dolar-laravel
```

## Usage

The `Exchange` class provides methods for exchanging between PEN and USD:

```php
use Brunoinds\SunatDolarLaravel\Exchange;
use Brunoinds\SunatDolarLaravel\Enums\Currency;

// Get current exchange rate
$result = Exchange::now()->convert(Currency::USD, 1)->to(Currency::PEN);

// Get historical exchange rate 
$date = new DateTime('2023-12-10');
$result = Exchange::on($date)
                ->convert(Currency::USD, 1)
                ->to(Currency::PEN);
echo $result // 0.27

// Get buy/sell prices
$dollarBuy = Exchange::on($date)->getDollarBuyPrice(); //3.749;
$solesSell = Exchange::on($date)->getSolesSellPrice(); //0.266
```

The `Currency` enum provides constants for the supported currencies:

```php
use Brunoinds\SunatDolarLaravel\Enums\Currency;

Currency::USD; // 'USD'
Currency::PEN; // 'PEN' 
```

## Testing

Unit tests are located in the `tests` directory. Run tests with:

```
composer test
```

## Contributing

Pull requests welcome!

## License

MIT License

## Powered by:
- [API Tipo de cambio SUNAT](https://apis.net.pe/api-tipo-cambio.html)

Let me know if you would like any sections expanded or have any other feedback!
