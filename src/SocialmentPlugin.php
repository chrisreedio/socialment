<?php

namespace ChrisReedIO\Socialment;

use ChrisReedIO\Socialment\Models\ConnectedAccount;
use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class SocialmentPlugin implements Plugin
{
    use EvaluatesClosures;

    public bool | Closure | null $visible = null;

    /** @var array<Closure>  */
    public array $preLoginCallbacks = [ ];

    /** @var array<Closure>  */
    public array $postLoginCallbacks = [ ];

    protected string $loginRoute = 'filament.admin.auth.login';

    public function getId(): string
    {
        return 'socialment';
    }

    public function register(Panel $panel): void
    {
        $panel->renderHook('panels::auth.login.form.before', function () {
            $errorMessage = Session::get('socialment.error');

            if (! $this->evaluate($this->visible) || ! $errorMessage) {
                return '';
            }

            return View::make(
                config('socialment.view.login-error', 'socialment::login-error'),
                [
                    'message' => $errorMessage,
                ]
            );
        });

        $panel->renderHook('panels::auth.login.form.after', function () {
            if (! $this->evaluate($this->visible)) {
                return '';
            }

            return View::make(
                config('socialment.view.providers-list', 'socialment::providers-list'),
                [
                    'providers' => config('socialment.providers'),
                ]
            );
        });
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        $plugin = app(static::class);

        $plugin->visible = fn () => true;

        return $plugin;
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function visible(bool | Closure $visible): static
    {
        $this->visible = $visible;

        return $this;
    }

    public function userModel(string | Closure $model): static
    {
        config()->set('socialment.models.user', (($model instanceof Closure) ? $model() : $model));

        return $this;
    }

    public function loginRoute(string | Closure $route): static
    {
        $this->loginRoute = $route;

        return $this;
    }

    public function getLoginRoute(): string
    {
        return (string) $this->evaluate($this->loginRoute);
    }

    /**
     * Sets up a callback to be called before a user is logged in.
     * This is useful if you wish to check a user's roles before allowing them to login.
     * Throw a Socialment\Exceptions\AbortedLoginException to abort the login.
     */
    public function preLogin(Closure $callback): static
    {
        // config()->set('socialment.post_login', $callback);
        $this->preLoginCallbacks[] = $callback;

        return $this;
    }

    /**
     * Executes the pre login callback. Set up closure to execute via the preLogin method.
     */
    public function executePreLogin(ConnectedAccount $account): void
    {
        foreach ($this->preLoginCallbacks as $callback) {
            ($callback)($account);
        }
    }

    /**
     * Sets up a callback to be called after a user logs in.
     */
    public function postLogin(Closure $callback): static
    {
        // config()->set('socialment.post_login', $callback);
        $this->postLoginCallbacks[] = $callback;

        return $this;
    }

    /**
     * Executes the post login callback. Set up closure to execute via the postLogin method.
     */
    public function executePostLogin(ConnectedAccount $account): void
    {
        foreach ($this->postLoginCallbacks as $callback) {
            ($callback)($account);
        }
    }
}
