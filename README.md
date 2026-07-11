# Socialment - Socialite OAuth Support for Filament

![Socialment](https://github.com/chrisreedio/socialment/assets/77644584/53dd1b45-d775-4335-a7ec-ae18456bcab4)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/chrisreedio/socialment.svg?style=flat-square)](https://packagist.org/packages/chrisreedio/socialment)
![Tests Action Status](https://github.com/chrisreedio/socialment/actions/workflows/run-tests.yml/badge.svg)
![Code Style Action Status](https://github.com/chrisreedio/socialment/actions/workflows/fix-php-code-styling.yml/badge.svg)
![PHPStan Action Status](https://github.com/chrisreedio/socialment/actions/workflows/phpstan.yml/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/chrisreedio/socialment.svg?style=flat-square)](https://packagist.org/packages/chrisreedio/socialment)

## About

Bring up-to-date and simple Socialite support to your Filament admin panel with this plugin. Adds OAuth buttons to your
login page.

Ideal for Laravel and Filament users seeking a straightforward OAuth integration.

> [!WARNING]
> Socialment is currently in beta. Please report any issues you encounter.
>
> Caution is advised if you choose to use this package in production.
>
> Azure AD support has been the only tested provider so far.

#### References

This package extends [Laravel Socialite](https://laravel.com/docs/master/socialite). Socialite currently supports
authentication via Facebook, Twitter, LinkedIn, Google, GitHub, GitLab, and Bitbucket out of the box.

Refer to the [Socialite documentation](https://laravel.com/docs/master/socialite) for more information on how to
configure your application to use these providers.

Many other providers are available via the [Socialite Providers](https://socialiteproviders.com/) website. Refer to the
documentation for each provider for information on how to configure your application to use them.

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
{
    content: [
        "./app/Filament/**/*.php",
        "./resources/views/filament/**/*.blade.php",
        "./vendor/filament/**/*.blade.php",
        // ... Other Content Paths

        // Ensure the line below is listed!!!
        "./vendor/chrisreedio/socialment/resources/**/*.blade.php",
    ]
}
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
> Either configure the needed stock socialite providers
> or [community maintained providers](https://socialiteproviders.com/).
>
> Refer to the [Socialite documentation](https://laravel.com/docs/master/socialite) for more information.
>
> This will usually involve installing a package and configuring your application's `config/services.php` file.

##### Socialment Configuration

###### Provider Configuration

> [!WARNING]
> This method of provider configuration is now deprecated and will be removed in a future release.
> 
> Configuring providers in your [panel configuration](#per-panel-provider-configuration) has many advantages and is the recommended method.

Whether you're using the default providers or adding your own, you'll need to configure them in the `socialment.php`
config file.

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

You should specify providers on a per-panel basis. To do this use the `->registerProvider` method on the plugin.

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
To start, You would refer to the documentation for
the [Azure Socialite Provider](https://socialiteproviders.com/Microsoft-Azure/).

Normally, you would follow the providers documentation on the aforementioned link but to demostrate the process for
Socialment, I'll include the steps here.

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
> It is in the plans to improve the handling of the sign in process to align more with Socialstream in allowing you to
> specify an `action` class or closure to handle the sign in process.
>
> This will allow for customized handling on a per provider, per application basis.

This package also uses the [Blade Font Awesome package](https://github.com/owenvoke/blade-fontawesome)
by [Owen Voke](https://github.com/owenvoke).

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

You may configure pre/post login hooks/callbacks by adding code similar to the following to the `boot` method of a
service provider:

```php
use ChrisReedIO\Socialment\Models\ConnectedAccount;

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
}
```

The user relation can be accessed via `$connectedAccount->user`.

The `ConnectedAccount` is passed instead of the `User` so that you can easily know which social account was used for the
login.

#### Login Route for failed logins

If a login fails or encounters a InvalidStateException, the user will be redirected to the configured `loginRoute`
route.

This defaults to `filament.admin.auth.login` but can be overriden on the plugin declaration in your panel provider
configuration:

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
    
    // DEPRECATED: This will be removed in a future version.
    // Configure routes via the panel provider.
    'routes' => [
        'home' => 'filament.admin.pages.dashboard',
    ],
    
    // DEPRECATED: This will be removed in a future version.
    // Configure providers via the panel provider.
    'providers' => [
        'azure' => [
            'icon' => 'fab-microsoft',
            'label' => 'Azure Active Directory',
        ],
    ],
    
    'spa' => [
        // The URL to redirect to after a successful login
        'home' => env('SPA_URL', 'http://localhost:3000'),
        'responses' => [
            // Replace with your own JsonResource class if you want to customize the response
            // 'me' => \ChrisReedIO\Socialment\Http\Resources\UserResponse::class,
        ],
    ],

    'models' => [
        // If you want to use a custom user model, you can specify it here.
        'user' => env('SOCIALMENT_USER_MODEL', \App\Models\User::class),
    ],
];
```

## Frontend SPA Authentication

> [!CAUTION]
> This feature is still in development and thus highly experimental.
> 
> Expect breaking changes and bugs. Use at your own risk.
> 
> The documentation will be updated as the feature is finalized.
> 
> This feature may be spun off into a separate package in the future.

This package includes support for authenticating with a Single Page Application (SPA) frontend. Both the Filament backend and SPA frontend must be hosted on the same domain. 

The login session is shared so logging into either the SPA or the backend will log you into both. 

Special CORS and session settings are required to make this work and caution must be taken to ensure that proper access controls (Policies / Panel Access / Etc) are in place.

### Setup

You'll need to add the new `spaAuth` routes to your `routes/web` file.

```php
// In this example, we pass 'dashboard' as the SPA route name.
// We'll want to make sure the 'prefix' our custom routes match.
// If no prefix is set/passed to spaAuth, the default is 'spa'.

Route::spaAuth('dashboard');

Route::middleware('auth:sanctum')
    ->prefix('dashboard')
    ->as('dashboard.')
    ->group(function () {
        // Custom Routes
    });
```

### Configuration Changes

You'll need to modify the `config/cors.php` file. 

You'll need to add the following to the `paths` array:

```php
'paths' => [
    // ... Other Paths
    'spa/*', // OR use the custom prefix you set in the routes/web file.
],
```

Also ensure that the `supports_credentials` is set to `true`:

```php
'supports_credentials' => true,
```

### Environment Variables

We need to set a few ENV variables to ensure that the SPA authentication works properly.

```dotenv
SANCTUM_STATEFUL_DOMAINS="https://frontend.localhost:3000,https://backend.localhost"
SESSION_DOMAIN=".localhost"
SESSION_SECURE_COOKIE=true
SPA_URL="https://frontend.localhost:3000"
```

The `SESSION_DOMAIN` should be the shared domain between your SPA and your backend. It should begin with a period.

The `SPA_URL` is the URL of your SPA application.

> [!NOTE]
> Ths SPA functionality is a work in progress and is subject to change.
>
> This documentation section will be updated as the feature is finalized.

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
