# Providing SocialStream style functionality for Filament.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/chrisreedio/socialment.svg?style=flat-square)](https://packagist.org/packages/chrisreedio/socialment)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/chrisreedio/socialment/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/chrisreedio/socialment/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/chrisreedio/socialment/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/chrisreedio/socialment/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/chrisreedio/socialment.svg?style=flat-square)](https://packagist.org/packages/chrisreedio/socialment)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require chrisreedio/socialment
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="socialment-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="socialment-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="socialment-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$socialment = new ChrisReedIO\Socialment();
echo $socialment->echoPhrase('Hello, ChrisReedIO!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Chris Reed](https://github.com/chrisreedio)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
