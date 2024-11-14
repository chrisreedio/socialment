<?php

namespace ChrisReedIO\Socialment;

use ChrisReedIO\Socialment\Http\Middleware\VerifySpaCsrfToken;
use ChrisReedIO\Socialment\Testing\TestsSocialment;
use Filament\Support\Assets\Asset;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

use function array_merge;
use function config;

class SocialmentServiceProvider extends PackageServiceProvider
{
    public static string $name = 'socialment';

    public static string $viewNamespace = 'socialment';

    public static string $middlewareGroupName = 'web_spa';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            // ->hasCommands($this->getCommands())
            ->hasRoute('web')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('chrisreedio/socialment');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(SocialmentPlugin::class, fn () => new SocialmentPlugin);

        Route::macro('spaInit', function (string $prefix = 'spa') {
            $namePrefix = 'socialment.spa.';
            $namePrefix .= ($prefix === 'spa') ? 'default.' : "{$prefix}.";

            $useCustomCsrf = config('socialment.spa.cookies.csrf.custom');

            $dashboardSpaMiddleware = [
                \Illuminate\Cookie\Middleware\EncryptCookies::class,
                \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                \Illuminate\Session\Middleware\StartSession::class,
                \Illuminate\View\Middleware\ShareErrorsFromSession::class,
                $useCustomCsrf
                    ? \ChrisReedIO\Socialment\Http\Middleware\VerifySpaCsrfToken::class
                    : \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
            ];

            Route::middlewareGroup(SocialmentServiceProvider::$middlewareGroupName, $dashboardSpaMiddleware);

            // Route::middleware([SocialmentServiceProvider::$middlewareGroupName])
            //     ->group(__DIR__.'/../routes/spa.php');

            // Now add this to the cors paths
            config([
                'cors.paths' => array_merge(config('cors.paths'), [
                    "{$prefix}/*",
                ]),
            ]);

            // Set the supports_credentials flag or the frontend can't send the goodies
            config([
                'cors.supports_credentials' => true,
            ]);
            // collect(Route::getRoutes())
            //     ->filter(fn ($route) => str_starts_with($route->uri(), $prefix))
            //     ->each(function (\Illuminate\Routing\Route $route) use ($dashboardSpaMiddleware) {
            //         $route->action['middleware'] = array_values(array_diff($route->action['middleware'], ['web']));
            //         $route->middleware($dashboardSpaMiddleware);
            //     });
        });

        Route::macro('spaAuth', function (string $prefix = 'spa') {
            $namePrefix = 'socialment.spa.';
            $namePrefix .= ($prefix === 'spa') ? 'default.' : "{$prefix}.";

            // $useCustomCsrf = config('socialment.spa.cookies.csrf.custom');
            // dd($useCustomCsrf);

            // $dashboardSpaMiddleware = [
            //     \Illuminate\Cookie\Middleware\EncryptCookies::class,
            //     \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            //     \Illuminate\Session\Middleware\StartSession::class,
            //     \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            //     $useCustomCsrf
            //         ? \ChrisReedIO\Socialment\Http\Middleware\VerifySpaCsrfToken::class
            //         : \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            //     \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // ];
            //
            // // dd($dashboardSpaMiddleware);
            //
            // Route::middlewareGroup(SocialmentServiceProvider::$middlewareGroupName, $dashboardSpaMiddleware);

            Route::middleware([SocialmentServiceProvider::$middlewareGroupName])
                // ->prefix($prefix)
                // ->as($namePrefix)
                ->group(__DIR__ . '/../routes/spa.php');

            // Now add this to the cors paths
            // config([
            //     'cors.paths' => array_merge(config('cors.paths'), [
            //         "{$prefix}/*",
            //     ]),
            // ]);

            // Set the supports_credentials flag or the frontend can't send the goodies
            // config([
            //     'cors.supports_credentials' => true,
            // ]);

            // Apply middleware conditionally to all routes with the '/dashboard' prefix
            // Route::middlewareGroup('spa', [
            //     \Illuminate\Cookie\Middleware\EncryptCookies::class,
            //     \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            //     \Illuminate\Session\Middleware\StartSession::class,
            //     \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            //     config('socialment.spa.cookies.csrf.custom')
            //         ? VerifySpaCsrfToken::class
            //         : VerifyCsrfToken::class,
            //     \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // ]);

            // Apply 'dashboard_spa' group to any routes prefixed with '/dashboard'
            // Route::middleware('spa')
            //     ->prefix('dashboard')
            //     ->group(function () {
            //         //
            //     });
            // Use the `Route` facade to get all routes and modify them if they start with `/dashboard`
            // collect(Route::getRoutes())
            //     ->filter(fn ($route) => str_starts_with($route->uri(), $prefix))
            //     ->each(function (\Illuminate\Routing\Route $route) use ($dashboardSpaMiddleware) {
            //         // if ($route->uri() === 'dashboard/me') {
            //         //     dump($route);
            //         // }
            //         // dump($route);
            //         // Replace the middleware stack for this route
            //         // Unset any 'values' of 'web' in the action's middleware
            //         $route->action['middleware'] = array_values(array_diff($route->action['middleware'], ['web']));
            //         // Insert our middleware group name at the start of the array
            //         // array_unshift($route->action['middleware'], SocialmentServiceProvider::$middlewareGroupName);
            //         // $route->middleware(SocialmentServiceProvider::$middlewareGroupName);
            //         $route->middleware($dashboardSpaMiddleware);
            //         // if ($route->uri() === 'dashboard/me') {
            //         // dump($route);
            //         // $middleware = $route->gatherMiddleware();
            //         // dd($middleware);
            //         // dd('done');
            //         // }
            //         // }
            //     });
            // dd('done');
        });

    }

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        // FilamentAsset::registerScriptData(
        //     $this->getScriptData(),
        //     $this->getAssetPackageName()
        // );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/socialment/{$file->getFilename()}"),
                ], 'socialment-stubs');
            }
        }

        // Testing
        Testable::mixin(new TestsSocialment);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'chrisreedio/socialment';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('socialment', __DIR__ . '/../resources/dist/components/socialment.js'),
            // Css::make('socialment-styles', __DIR__ . '/../resources/dist/socialment.css'),
            // Js::make('socialment-scripts', __DIR__ . '/../resources/dist/socialment.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    // protected function getRoutes(): array
    // {
    //     return [];
    // }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_connected_accounts_table',
            'modify_users_table_nullable_password',
        ];
    }
}
