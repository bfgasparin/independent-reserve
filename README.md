# Independent Reserve Client Library for PHP

**THIS LIBRARY IS IN BETA RELEASE, SOME API ENDPOINTS ARE NOT SUPPORTED YET.**

*This library requires a minimum PHP version of 7.2*

This is a PHP client library to interact with Independent Reserve's API. To use this, you'll need an Independent Reserve account and an API Key.
[Create an account][signup] and generate an API Key in the Settings menu. This is currently a beta release.

 * [Installation](#installation)
 * [Usage](#usage)
 * [Examples](#examples)
 * [Contributing](#contributing)

Installation
------------

To install the PHP client library to your project, I recommend using [Composer](https://getcomposer.org/).

```bash
composer require bfgasparin/independent-reserve
```

> You don't need to clone this repository to use this library in your own projects. Use Composer to install it from Packagist.

If you're new to Composer, here are some resources that you may find useful:

* [Composer's Getting Started page](https://getcomposer.org/doc/00-intro.md) from Composer project's documentation.
* [A Beginner's Guide to Composer](https://scotch.io/tutorials/a-beginners-guide-to-composer) from the good people at ScotchBox.

TO use the [private methods][privatemethods] of Independent Reserve API,  you'll need to have [created an Independent Reserve account][signup].

Usage
-----

If you're using Composer, make sure the autoloader is included in your project's bootstrap file:

```php
require_once "vendor/autoload.php";
```

To interact with only [public methods][publicmethods], you do need any API KEY, so you just need to create a client:

```php
$client = IndependentReserve\IndependentReserve::instance();
```

If you need to interact with [private methods][privatemethods], you must create the client with the API KEY and API SECRET:

Create a client with your API key and secret:

```php
$client = IndependentReserve\IndependentReserve::instance(API_KEY, API_SECRET);
```

For testing purposes you may want to change the URL that `nexmo-php` makes requests to from `api.independentreserve.com` to something else. You can do this by providing a base uri as the third parameter when creating a `IndependentReserve\IndependentReserve` instance.

```php
$client = IndependentReserve\IndependentReserve::instance(API_KEY, API_KEY, 'https://example.com');
```

Examples
--------

To call the API methods, you usually just need to call the method as in documentation.
If the method requires parameters, you must pass the parameter in the same order of the documentation.

If the API response is an object in the documentation, the response will be a \StdClass instance, otherwise,
the response will be a primitive type response.

See the available public and private functions in the [docs][docs]

### Some Public Methods Examples

```php
$codes = $client->getValidPrimaryCurrencyCodes();
```

```php
$codes = $client->getValidSecondaryCurrencyCodes();
```

```php
$codes = $client->getValidSecondaryCurrencyCodes();
```

```php
$summary = $client->getMarketSummary(
    'Xbt' // primary currency code
    'Usd' // secondary currency code
);
```

### Some Private Methods Examples

```php
$fees = $client->getBrokerageFees();
```

```php
$order = $client->PlaceMarketOrder(
    'Xbt' // primary currency code
    'Usd' // secondary currency code
    'MarketBid' // order type,
    '0.012' // volume
)
```

Contributing
------------

To contribute to the library or docs, [create an issue][issues] or [a pull request][pulls].

License
-------

This library is released under the [MIT License][license]

[signup]: https://www.independentreserve.com
[docs]: https://www.independentreserve.com/api
[privatemethods]: https://www.independentreserve.com/api#private
[publicmethods]: https://www.independentreserve.com/api#public
[issues]: https://github.com/bfgasparin/independent-reserve/issues
[pulls]: https://github.com/bfgasparin/independent-reserve/pulls
[license]: LICENSE.txt
