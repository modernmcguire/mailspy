# MailSpy

[![Latest Version on Packagist](https://img.shields.io/packagist/v/modernmcguire/mailspy.svg?style=flat-square)](https://packagist.org/packages/modernmcguire/mailspy)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/modernmcguire/mailspy/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/modernmcguire/mailspy/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/modernmcguire/mailspy/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/modernmcguire/mailspy/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/modernmcguire/mailspy.svg?style=flat-square)](https://packagist.org/packages/modernmcguire/mailspy)

MailSpy is a Laravel package that allows you to capture and inspect emails sent by your application. It was created to help with testing and debugging email sending in Laravel applications in addition to getting around low retention log limits in services like MailGun and MailerSend.

## Installation

You can install the package via composer:

```bash
composer require modernmcguire/mailspy
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="mailspy-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="mailspy-config"
```

## Usage

Nothing to do here! Simply install the package and we will start tracking outgoing email saving the results to your database.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Ben Miller](https://github.com/modernben)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
