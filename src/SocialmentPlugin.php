<?php

namespace ChrisReedIO\Socialment;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\View\View;

class SocialmentPlugin implements Plugin
{
	use EvaluatesClosures;

	public bool | Closure | null $visible = null;
	
    public function getId(): string
    {
        return 'socialment';
    }

    public function register(Panel $panel): void
    {
        $panel->renderHook('panels::auth.login.form.after', function () {
            if (! $this->evaluate($this->visible)) {
                return '';
            }

            return View::make('socialment::providers-list', [
                'providers' => [
					'azure' => [
						'icon' => 'azure',
						'label' => 'Azure',
					]
				]
            ]);
        });
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
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
}
