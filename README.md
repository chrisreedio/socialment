# Socialment - Socialite OAuth Support for Filament

![Socialment](https://github.com/chrisreedio/socialment/assets/77644584/53dd1b45-d775-4335-a7ec-ae18456bcab4)


[![Latest Version on Packagist](https://img.shields.io/packagist/v/chrisreedio/socialment.svg?style=flat-square)](https://packagist.org/packages/chrisreedio/socialment)
![Tests Action Status](https://github.com/chrisreedio/socialment/actions/workflows/run-tests.yml/badge.svg)
![Code Style Action Status](https://github.com/chrisreedio/socialment/actions/workflows/fix-php-code-styling.yml/badge.svg)
![PHPStan Action Status](https://github.com/chrisreedio/socialment/actions/workflows/phpstan.yml/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/chrisreedio/socialment.svg?style=flat-square)](https://packagist.org/packages/chrisreedio/socialment)

## About

Bring up-to-date and simple Socialite support to your Filament admin panel with this plugin. Adds OAuth buttons to your login page.

Ideal for Laravel and Filament users seeking a straightforward OAuth integration.
    
> [!WARNING]
> Socialment is currently in beta. Please report any issues you encounter.
> 
> Caution is advised if you choose to use this package in production.
> 
> Azure AD support has been the only tested provider so far.

#### References

This package extends [Laravel Socialite](https://laravel.com/docs/master/socialite). Socialite currently supports authentication via Facebook, Twitter, LinkedIn, Google, GitHub, GitLab, and Bitbucket out of the box.

Refer to the [Socialite documentation](https://laravel.com/docs/master/socialite) for more information on how to configure your application to use these providers.

Many other providers are available via the [Socialite Providers](https://socialiteproviders.com/) website. Refer to the documentation for each provider for information on how to configure your application to use them.

### Demo

For an example usage of this package, see [ChrisReedIO/Socialment-Demo](https://github.com/chrisreedio/socialment-demo).

![image](https://github.com/chrisreedio/socialment/assets/77644584/c07c6518-df0b-4143-8826-efa3cbdaa681)

---

## Installation

You can install the package via composer:

```bash
composer require chrisreedio/socialment
```

## Usage

#### Initial Setup

You can easily perform the initial setup by running the following command:

```bash
php artisan socialment:install
```

Additionally, edit your panel's `tailwind.config.js` content section to include the last line of the following:

```js
    content: [
        "./app/Filament/**/*.php",
        "./resources/views/filament/**/*.blade.php",
        "./vendor/filament/**/*.blade.php",
        // ... Other Content Paths

		// Ensure the line below is listed!!!
        "./vendor/chrisreedio/socialment/resources/**/*.blade.php",
    ],
```

If this step is forgotten, the styling of the plugin will not be applied.

Please continue to the next sections to continue the setup process.

### Panel Configuration

Include this plugin in your panel configuration:

```php
$panel
	->plugins([
		// ... Other Plugins
        \ChrisReedIO\Socialment\SocialmentPlugin::make(),        
	])
```

#### Provider Configuration

> [!IMPORTANT]
> At this point, you'll need to configure your application to use the provider(s) you want to support. 
> 
> Either configure the needed stock socialite providers or [community maintained providers](https://socialiteproviders.com/).
>
> Refer to the [Socialite documentation](https://laravel.com/docs/master/socialite) for more information.
> 
> This will usually involve installing a package and configuring your application's `config/services.php` file.


##### Socialment Configuration

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
Providers specified in the config file are global across all panels.

##### Per-Panel Provider Configuration

You may also specify providers on a per-panel basis. To do this use the `->registerProvider` method on the plugin.

```php
$panel->plugins([
    \ChrisReedIO\Socialment\SocialmentPlugin::make()
        ->registerProvider('azure', 'fab-microsoft', 'Azure Active Directory'),
]);
```
##### Sample Provider Configuration - Azure Active Directory

> [!IMPORTANT]
> For this configured Azure provider, the redirect URI would be `https://DOMAIN/login/azure/redirect`
> 
> The callback URI would be `https://DOMAIN/login/azure/callback` 

For example, the sample provider included in the stock `socialment.php` config is Azure Active Directory. 
To start, You would refer to the documentation for the [Azure Socialite Provider](https://socialiteproviders.com/Microsoft-Azure/).

Normally, you would follow the providers documentation on the aforementioned link but to demostrate the process for Socialment, I'll include the steps here.

Per their documentation, you would install the community Azure provider via

```bash
composer require socialiteproviders/microsoft-azure
```

Then you would configure your `config/services.php` file to include the Azure provider's credentials:

```php
'azure' => [    
  'client_id' => env('AZURE_CLIENT_ID'),
  'client_secret' => env('AZURE_CLIENT_SECRET'),
  'redirect' => env('AZURE_REDIRECT_URI'),
  'tenant' => env('AZURE_TENANT_ID'),
  'proxy' => env('PROXY')  // optionally
],
```

In addition, you need to add this provider's event listener to your `app/Providers/EventServiceProvider.php` file:

```php
protected $listen = [
	// ... other listeners

    \SocialiteProviders\Manager\SocialiteWasCalled::class => [
        // ... other providers
        \SocialiteProviders\Azure\AzureExtendSocialite::class.'@handle',
    ],
];
```

Finally, don't forget to add the needed environment variables to your `.env` file:

```dotenv
AZURE_CLIENT_ID=
AZURE_CLIENT_SECRET=
AZURE_REDIRECT_URI=
AZURE_TENANT_ID=
```

The `usage` section can usually be ignored as that is the main part this package should handle for you. 

> [!NOTE]
> It is in the plans to improve the handling of the sign in process to align more with Socialstream in allowing you to specify an `action` class or closure to handle the sign in process. 
> 
> This will allow for customized handling on a per provider, per application basis.



This package also uses the [Blade Font Awesome package](https://github.com/owenvoke/blade-fontawesome) by [Owen Voke](https://github.com/owenvoke). 

Search for brand icons on the [Font Awesome Website](https://fontawesome.com/search?o=r&f=brands).


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

#### Login Callbacks

You may configure pre/post login hooks/callbacks by adding code similar to the following to the `boot` method of a service provider:

```php
use ChrisReedIO\Socialment\Models\ConnectedAccount;
use SocialiteProviders\Manager\OAuth2\User as SocialiteUser;

public function boot(): void
{
    // Post Login Hook
	Socialment::preLogin(function (ConnectedAccount $connectedAccount) {
		// Handle custom pre login logic here.
	});
	// Multiple hooks can be added
	Socialment::preLogin(function (ConnectedAccount $connectedAccount) {
		// Handle additional custom pre login logic here if you need.
	});

    // Post Login Hook
	Socialment::postLogin(function (ConnectedAccount $connectedAccount) {
		// Handle custom post login logic here.
		Log::info('User logged in with ' . $connectedAccount->provider . ' account', [
			'connectedAccount' => $connectedAccount,
		]);
	});

    // Pre User Creation Hook
    Socialment::preUserCreation(function (SocialiteUser $user) {
        // Handle custom pre user creation logic here.
    });
}
```

The user relation can be accessed via `$connectedAccount->user`. 

The `ConnectedAccount` is passed instead of the `User` so that you can easily know which social account was used for the login. 

#### Login Route for failed logins

If a login fails or encounters a InvalidStateException, the user will be redirected to the configured `loginRoute` route.

This defaults to `filament.admin.auth.login` but can be overriden on the plugin declaration in your panel provider configuration:

```php
$panel->plugins([
    \ChrisReedIO\Socialment\SocialmentPlugin::make()
        ->loginRoute('filament.staff.auth.login')
]);
```
You may also use a closure here to dynamically set the route:

```php
$panel->plugins([
    \ChrisReedIO\Socialment\SocialmentPlugin::make()
        ->loginRoute(fn () => SomeFunctionToGetTheRouteName())
]);
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
