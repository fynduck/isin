# Isin
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/fynduck/isin.svg?style=flat-square)](https://packagist.org/packages/fynduck/isin)
[![Total Downloads](https://img.shields.io/packagist/dt/fynduck/isin.svg?style=flat-square)](https://packagist.org/packages/fynduck/isin)

A Laravel package for storing a validating an ISIN (International Securities Identification Number / ISO 6166).
ISINs will be checked against the checksum as detailed at [https://en.wikipedia.org/wiki/International_Securities_Identification_Number]

## Installation
The library can be installed via composer
```
composer require fynduck/isin
```

## Usage
You can instantiate an ISIN object by passing in a string
```php
use fynduck\ISIN;

$number = 'GB00B3W23161';
$isin = new ISIN($number);
```

If the value passed in was not a valid ISIN it will throw a ```fynduck\ISIN\Exception\InvalidISINException```
To get the value back out you can do

```
$isin->getValue();
return GB00B3W23161
```

If you want to get hold of just the check digit you can use

```
$isin->getCheckDigit();
return 1
```

The object has a ```__toString``` so usage in views/routes etc will work:

```php
echo 'The ISIN is ' . $isin;
// The ISIN is GB00B3W23161
```

### Generate ISINs
```
ISIN::generateDigit('GB00B3W2316');
return: 1
```

### Validating ISINs
There are some helper static functions for simple validation.

```
ISIN::isValid('GB00B3W23161');
return true
```

This will return true if the value was a valid ISIN, false otherwise.

```
ISIN::validate('gb00b3w23161');
return GB00B3W23161
```
```
$number = ISIN::validate('ABC');
return InvalidISINException
```

This will return the properly formatted ISIN (whitespace trimmed and converted to uppercase).
It will throw a ```fynduck\ISIN\Exception\InvalidISINException``` if the input was not valid.

## Development
This project is open source. Feedback and pull requests are welcome. To develop the code:

Checkout the project. Run
```composer install```

### Running Tests
PHPUnit

```
vendor/bin/phpunit
```

Code Sniffer

```
vendor/bin/phpcs
```

Both must be run successfully before code can be submitted. Code coverage must also be 100%.