<?php

namespace ChrisReedIO\Socialment\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use ChrisReedIO\Socialment\SocialmentPlugin;
use ChrisReedIO\Socialment\SocialmentServiceProvider;
use ChrisReedIO\Socialment\Tests\Fixtures\User;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Socialite\SocialiteServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use OwenVoke\BladeFontAwesome\BladeFontAwesomeServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        SocialmentPlugin::$globalPreLoginCallbacks = [];
        SocialmentPlugin::$globalPostLoginCallbacks = [];

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'ChrisReedIO\\Socialment\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            ActionsServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeFontAwesomeServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            LivewireServiceProvider::class,
            NotificationsServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            SocialiteServiceProvider::class,
            SocialmentServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
        config()->set('socialment.models.user', User::class);
        config()->set('auth.providers.users.model', User::class);

        // Laravel Sanctum is not a dependency; session guards stand in for the guards the SPA routes name.
        config()->set('auth.guards.sanctum', ['driver' => 'session', 'provider' => 'users']);
        config()->set('auth.guards.spa', ['driver' => 'session', 'provider' => 'users']);
    }

    protected function defineDatabaseMigrations()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        (include __DIR__ . '/../database/migrations/create_connected_accounts_table.php.stub')->up();
    }
}
