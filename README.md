# Socialment - Socialite OAuth Support for Filament

![Socialment](https://github.com/chrisreedio/socialment/assets/77644584/53dd1b45-d775-4335-a7ec-ae18456bcab4)


[![Latest Version on Packagist](https://img.shields.io/packagist/v/chrisreedio/socialment.svg?style=flat-square)](https://packagist.org/packages/chrisreedio/socialment)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/chrisreedio/socialment/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/chrisreedio/socialment/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/chrisreedio/socialment/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/chrisreedio/socialment/actions?query=branch%3A3.x+workflow%3A%22Fix+PHP+Code+Styling%22++)
[![Total Downloads](https://img.shields.io/packagist/dt/chrisreedio/socialment.svg?style=flat-square)](https://packagist.org/packages/chrisreedio/socialment)

## About

Bring up-to-date and simple Socialite support to your Filament admin panel with this plugin. Adds OAuth buttons to your login page.

Ideal for Laravel and Filament users seeking a straightforward OAuth integration.

> [!WARNING]
> Socialment is currently in beta. Please report any issues you encounter.
> Caution is advised if you choose to use this package in production.
> Azure AD support has been the only tested provider so far.

#### References

This package extends [Laravel Socialite](https://laravel.com/docs/master/socialite). Socialite currently supports authentication via Facebook, Twitter, LinkedIn, Google, GitHub, GitLab, and Bitbucket out of the box.

Refer to the [Socialite documentation](https://laravel.com/docs/master/socialite) for more information on how to configure your application to use these providers.

Many other providers are available via the [Socialite Providers](https://socialiteproviders.com/) website. Refer to the documentation for each provider for information on how to configure your application to use them.

### Demo

For an example usage of this package, see [ChrisReedIO/Socialment-Demo](https://github.com/chrisreedio/socialment-demo).

---

## Installation

You can install the package via composer:

```bash
composer require chrisreedio/socialment
```


## Usage


#### Initial Setup

After installation you should publish and run the migration(s) with:

> [!IMPORTANT]  
> This package requires that the `users` `password` field be nullable. If you have not already done so, you should update your `users` table migration to make this change.

```bash
php artisan vendor:publish --tag="socialment-migrations"
php artisan migrate
```

Then publish the config file with:

```bash
php artisan vendor:publish --tag="socialment-config"
```

#### Provider Configuration

Whether you're using the default providers or adding your own, you'll need to configure them in the `socialment.php` config file.

Configure the `socialment.php` config file to specify providers in the following format:

```php
return [
    'providers' => [
        'azure' => [
            'icon' => 'fab-microsoft', // Font Awesome Brand Icon
            'label' => 'Azure', // Display Name on the Login Page
        ]
    ],
	// ... Other Configuration Parameters
];
```

This package also uses the [Blade Font Awesome package](https://github.com/owenvoke/blade-fontawesome) by [Owen Voke](https://github.com/owenvoke). 

Search for brand icons on the [Font Awesome Website](https://fontawesome.com/search?o=r&f=brands).

### Panel Configuration

Include this plugin in your panel configuration:

```php
$panel
	->plugins([
		// ... Other Plugins
		\ChrisReedIO\Socialment\SocialmentPlugin::make(),
	])
```


### Visibility Override

By default, the plugin displays the configured providers at the bottom of the login form. 
You can additionally override the visibility of the plugin by passing a boolean or closure to the `visible` method:

```php
$panel->plugins([
    \ChrisReedIO\Socialment\SocialmentPlugin::make()
        ->visible(fn () => false)
]);
```

### Extras

You may publish and customize the views using

```bash
php artisan vendor:publish --tag="socialment-views"

```

### Config

This is the contents of the published config file:

```php
return [
	'view' => [
		// Set the text above the provider list
        'prompt' => 'Or Login Via',
		// Or change out the view completely with your own
		'providers-list' => 'socialment::providers-list',
	],
	'routes' => [
		'home' => 'filament.admin.pages.dashboard',
	],
	'models' => [
		// If you want to use a custom user model, you can specify it here.
		'user' => \App\Models\User::class,
	],
	'providers' => [
        'azure' => [
        	'icon' => 'fab-microsoft',
        	'label' => 'Azure Active Directory',
        ]
	],
];
```

## Testing

> [!NOTE] 
> Tests have yet to be written for this package. They are on my TODO list. I'm also open to PRs.

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
