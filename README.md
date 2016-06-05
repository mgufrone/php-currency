# Currency API Wrapper for API
[![Build Status](https://travis-ci.org/mgufrone/php-currency.svg?branch=master)](https://travis-ci.org/mgufrone/php-currency)
[![Coverage Status](https://coveralls.io/repos/github/mgufrone/php-currency/badge.svg?branch=master)](https://coveralls.io/github/mgufrone/php-currency?branch=master)

## Table of contents
- [Installation](#installation)
- [Usage](#usage)


## Installation

Just run this simple command through your command line

```shell
composer require gufy/currency:~1
```

Or update manually your `composer.json`
```json
{
  "require":{
    ...
    "gufy/currency":"~1"
    ...
  }
}
```

## Usage

This package currently only support OpenExchangeRates as the main API.

### Getting latest currency rates
```php
<?php
include 'vendor/autoload.php';

use Gufy\Currency\OpenExchange;

$api = new OpenExchange("your-app-id");
$rates = $api->rates();
```

### Getting currency rates based on date
```php
<?php
include 'vendor/autoload.php';

use Gufy\Currency\OpenExchange;
$date = "2016-06-06";
$api = new OpenExchange("your-app-id");
$rates = $api->rates("USD", $date);
```

### Convert value to some currency
```php
<?php
include 'vendor/autoload.php';

use Gufy\Currency\OpenExchange;
$date = "2016-06-06";
$api = new OpenExchange("your-app-id");
$value = 10;
$base = "USD";
$target = "IDR";
$rates = $api->convert($value, $base, $target);
```
