<?php

namespace ChrisReedIO\Socialment;

use BackedEnum;
use BladeUI\Icons\Exceptions\SvgNotFound;
use BladeUI\Icons\Factory as IconFactory;
use ChrisReedIO\Socialment\Models\ConnectedAccount;
use Closure;
use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Support\Enums\Size;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

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
        return $this->providers;
    }

    public function getProvider(string $provider): array
    {
        return $this->getProviders()[$provider];
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
                    'providers' => array_map(
                        fn (array $provider): array => [...$provider, 'icon' => static::resolveIcon($provider['icon'] ?? null)],
                        $this->providers,
                    ),
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

    public function visible(bool | Closure | null $visible = null): static
    {
        $this->visible = $visible;

        return $this;
    }

    public function userModel(string | Closure $model): static
    {
        config()->set('socialment.models.user', (($model instanceof Closure) ? $model() : $model));

        return $this;
    }

    public function loginRoute(null | string | Closure $route = null): static
    {
        $this->loginRoute = $route;

        return $this;
    }

    public function getLoginRoute(): ?string
    {
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

    // New Standard trying to match Filament proper
    public function createUserUsing(Closure $closure): static
    {
        $this->createUserClosure = $closure;

        return $this;
    }

    public function createUser(ConnectedAccount $account)
    {
        // If the closure is set, use it to create the user
        if ($this->createUserClosure !== null) {
            return ($this->createUserClosure)($account);
        }

        // Otherwise, use the default method - Get the user model from the config
        $userModel = config('socialment.models.user');

        // Check for an existing user with this email
        // Create a new user if one doesn't exist
        return $userModel::where('email', $account->email)->first()
            ?? $userModel::create([
                'name' => $account->name,
                'email' => $account->email,
            ]);
    }

    /**
     * @param  string | BackedEnum | Htmlable | null  $icon  Any icon Filament can render: a Blade icon name (e.g. `heroicon-o-key`, or `fab-github` with blade-fontawesome installed), a `Heroicon` enum case, or HTML.
     * @param  string | array<int | string, string | int> | null  $color  A Filament color name such as `primary` or `danger`. Defaults to `gray`.
     * @param  string | Size | null  $size  A Filament button size. Defaults to `md`.
     */
    public function registerProvider(
        string $provider,
        string | BackedEnum | Htmlable | null $icon,
        string $label,
        array $scopes = [],
        string | array | null $color = null,
        string | Size | null $size = null,
    ): static {
        $this->providers[$provider] = [
            'icon' => $icon,
            'label' => $label,
            'scopes' => $scopes,
            'color' => $color,
            'size' => $size,
        ];

        return $this;
    }

    /**
     * Returns the icon if it can be rendered, or null when it names an icon from a Blade icon set that is not installed,
     * such as a Font Awesome icon without owenvoke/blade-fontawesome. Other icons are returned unchanged.
     */
    protected static function resolveIcon(string | BackedEnum | Htmlable | null $icon): string | BackedEnum | Htmlable | null
    {
        if (! is_string($icon) || blank($icon) || str_contains($icon, '/')) {
            return $icon;
        }

        try {
            app(IconFactory::class)->svg($icon);
        } catch (SvgNotFound) {
            return null;
        }

        return $icon;
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
