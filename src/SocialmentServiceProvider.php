<?php

declare(strict_types=1);

namespace ChrisReedIO\Socialment;

use ChrisReedIO\Socialment\Testing\TestsSocialment;
use Filament\Support\Assets\Asset;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Route;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SocialmentServiceProvider extends PackageServiceProvider
{
    public static string $name = 'socialment';

    public static string $viewNamespace = 'socialment';

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

        if (file_exists($package->basePath("/../config/$configFileName.php"))) {
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
        $this->app->singleton(SocialmentPlugin::class, fn () => new SocialmentPlugin());
    }

    public function packageBooted(): void
    {
        Route::macro('spaAuth', static function (string $prefix = 'spa'): void {
            $namePrefix = 'socialment.spa.';
            $namePrefix .= ($prefix === 'spa') ? 'default.' : "$prefix.";

            Route::middleware('web')
                ->prefix($prefix)
                ->as($namePrefix)
                ->group(__DIR__ . '/../routes/spa.php');

            // Now add this to the cors paths
            config([
                'cors.paths' => array_merge(config('cors.paths'), [
                    "$prefix/*",
                ]),
            ]);

            // Set the supports_credentials flag or the frontend can't send the goodies
            config([
                'cors.supports_credentials' => true,
            ]);
        });

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
        Testable::mixin(new TestsSocialment());
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
