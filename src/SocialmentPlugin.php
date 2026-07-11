<?php

declare(strict_types=1);

namespace ChrisReedIO\Socialment;

use ChrisReedIO\Socialment\Models\ConnectedAccount;
use Closure;
use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;

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

    public ?Closure $createUserClosure = null;

    protected string | Closure | null $loginRoute = null;

    protected string | Closure | null $homeRoute = null;

    protected array $providers = [];

    protected ?bool $multiPanel = null;

    public Panel $panel;

    public function getId(): string
    {
        return 'socialment';
    }

    public function getProviders(): array
    {
        return array_merge(config('socialment.providers'), $this->providers);
    }

    public function getProvider(string $provider): array
    {
        return $this->getProviders()[$provider];
    }

    public function register(Panel $panel): void
    {
        $panel->renderHook('panels::auth.login.form.before', function () {
            $errorMessage = session()->get('socialment.error');

            if (! $errorMessage || ! $this->evaluate($this->visible)) {
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

        $plugin->visible = static fn () => true;

        return $plugin;
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function visible(bool | Closure | null $visible = null): static
    {
        $this->visible = $visible;

        return $this;
    }

    public function userModel(string | Closure $model): static
    {
        config()->set('socialment.models.user', value($model));

        return $this;
    }

    public function loginRoute(null | string | Closure $route = null): static
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

    public function homeRoute(null | string | Closure $route = null): static
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
            value($callback, $account);
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
            value($callback, $account);
        }
    }

    // New Standard trying to match Filament proper
    public function createUserUsing(?Closure $closure): static
    {
        $this->createUserClosure = $closure;

        return $this;
    }

    public function createUser(ConnectedAccount $account): null | Closure | Model
    {
        // If the closure is set, use it to create the user
        if ($this->createUserClosure !== null) {
            return value($this->createUserClosure, $account);
        }

        // Otherwise, use the default method - Get the user model from the config
        /** @var class-string<Model> $userModel */
        $userModel = config('socialment.models.user');

        // Check for an existing user with this email
        // Create a new user if one doesn't exist
        return $userModel::firstOrCreate([
            'email' => $account->email,
        ], [
            'name' => $account->name,
        ]);
    }

    public function registerProvider(string $provider, string $icon, string $label, array $scopes = []): static
    {
        $this->providers[$provider] = [
            'icon' => $icon,
            'label' => $label,
            'scopes' => $scopes,
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
        return $this->multiPanel ?? (count(Filament::getPanels()) > 1);
    }
}
