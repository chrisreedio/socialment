<?php

use ChrisReedIO\Socialment\SocialmentPlugin;
use Filament\Panel;
use Filament\PanelRegistry;
use Filament\Support\Facades\FilamentView;

function renderLoginError(SocialmentPlugin $plugin): string
{
    $panel = Panel::make()->id('admin')->path('admin');
    $plugin->register($panel);
    $plugin->boot($panel);
    $panel->boot();

    return FilamentView::renderHook('panels::auth.login.form.before')->toHtml();
}

it('shows the flashed login error above the login form', function () {
    session()->flash('socialment.error', 'Admins only.');

    expect(renderLoginError(SocialmentPlugin::make()))
        ->toContain('Login Failed')
        ->toContain('Admins only.');
});

it('shows nothing above the login form without an error or when hidden', function (bool $visible, ?string $error) {
    session()->flash('socialment.error', $error);

    expect(trim(renderLoginError((new SocialmentPlugin)->visible(fn () => $visible))))->toBe('');
})->with([
    'no error' => [true, null],
    'hidden' => [false, 'Admins only.'],
]);

it('hides the provider list when not visible', function () {
    $panel = Panel::make()->id('admin')->path('admin');
    $plugin = (new SocialmentPlugin)->visible(false)->registerProvider('github', null, 'GitHub');
    $plugin->register($panel);
    $plugin->boot($panel);
    $panel->boot();

    expect(trim(FilamentView::renderHook('panels::auth.login.form.after')->toHtml()))->toBe('');
});

it('evaluates login and home routes, returning null when unset', function () {
    $plugin = new SocialmentPlugin;

    expect($plugin->getLoginRoute())->toBeNull()
        ->and($plugin->getHomeRoute())->toBeNull()
        ->and($plugin->loginRoute('/login')->getLoginRoute())->toBe('/login')
        ->and($plugin->homeRoute(fn () => '/home')->getHomeRoute())->toBe('/home');
});

it('guesses multi-panel mode from the registered panels unless set explicitly', function () {
    $plugin = new SocialmentPlugin;
    app(PanelRegistry::class)->register(Panel::make()->id('admin')->path('admin'));

    expect($plugin->isMultiPanel())->toBeFalse();

    app(PanelRegistry::class)->register(Panel::make()->id('app')->path('app'));

    expect($plugin->isMultiPanel())->toBeTrue()
        ->and($plugin->multiPanel(false)->isMultiPanel())->toBeFalse();
});

it('sets the user model from a string or closure', function () {
    (new SocialmentPlugin)->userModel(fn () => 'App\Models\Member');

    expect(config('socialment.models.user'))->toBe('App\Models\Member');
});

it('looks up a registered provider by name', function () {
    $plugin = (new SocialmentPlugin)->registerProvider('github', 'fab-github', 'GitHub', ['read:org']);

    expect($plugin->getProvider('github'))->toMatchArray(['label' => 'GitHub', 'scopes' => ['read:org']])
        ->and($plugin->getId())->toBe('socialment');
});
