<?php

namespace ChrisReedIO\Socialment;

use ChrisReedIO\Socialment\Models\ConnectedAccount;
use Closure;
use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

use function array_merge;
use function config;

class SocialmentPlugin implements Plugin
{
    use EvaluatesClosures;

    public bool | Closure | null $visible = null;

    /** @var array<Closure> */
    public array $preLoginCallbacks = [];

    public static array $globalPreLoginCallbacks = [];

    /** @var array<Closure> */
    public array $postLoginCallbacks = [];

    public static array $globalPostLoginCallbacks = [];

    protected ?string $loginRoute = null;

    protected ?string $homeRoute = null;

    protected array $providers = [];

    protected ?bool $multiPanel = null;

    public Panel $panel;

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

            $providers = array_merge(config('socialment.providers'), $this->providers);

            return View::make(
                config('socialment.view.providers-list', 'socialment::providers-list'),
                [
                    'providers' => $providers,
                    'multiPanel' => $this->isMultiPanel(),
                    'panel' => $this->panel,
                ]
            );
        });
    }

    public function boot(Panel $panel): void
    {
        $this->panel = $panel;
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

    public function getLoginRoute(): ?string
    {
        // dd($this->panel->getId());
        if ($this->loginRoute === null) {
            return null;
            // return $this->panel->getLoginUrl();
        }

        return (string) $this->evaluate($this->loginRoute);
    }

    public function homeRoute(string | Closure $route): static
    {
        $this->homeRoute = $route;

        return $this;
    }

    public function getHomeRoute(): ?string
    {
        if ($this->homeRoute === null) {
            return null;
            // return $this->panel->getHomeUrl();
            // return config('app.url') . '/' . Filament::getDefaultPanel()->getPath();
        }

        return (string) $this->evaluate($this->homeRoute);
    }

    public static function globalPreLogin(Closure $callback): void
    {
        self::$globalPreLoginCallbacks[] = $callback;
    }

    public static function globalPostLogin(Closure $callback): void
    {
        self::$globalPostLoginCallbacks[] = $callback;
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
        // dump('plugin ID: ' . $this->getId());
        // dump('panel ID: ' . $this->panel->getId());
        // dump('executePreLogin');
        // dump('Count of hooks: ' . count($this->preLoginCallbacks));
        // dd('Count of global hooks: ' . count(self::$loginHooks))

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

    public function registerProvider(string $provider, string $icon, string $label): static
    {
        $this->providers[$provider] = [
            'icon' => $icon,
            'label' => $label,
        ];

        return $this;
    }

    public function multiPanel(bool $multiPanel = true): static
    {
        $this->multiPanel = $multiPanel;

        return $this;
    }

    public function isMultiPanel(): bool
    {
        // 'Guess' what setting this should be if it's not explicitly set.
        if ($this->multiPanel === null) {
            return count(Filament::getPanels()) > 1;
        }

        return $this->multiPanel;
    }
}
