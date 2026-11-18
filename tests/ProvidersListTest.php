<?php

use ChrisReedIO\Socialment\SocialmentPlugin;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Icons\Heroicon;

function renderProvidersList(SocialmentPlugin $plugin): string
{
    $panel = Panel::make()->id('admin')->path('admin');
    $plugin->register($panel);
    $plugin->boot($panel);
    $panel->boot();

    return FilamentView::renderHook('panels::auth.login.form.after')->toHtml();
}

it('renders providers as Filament buttons linking to the panel redirect', function () {
    $html = renderProvidersList(
        (new SocialmentPlugin)
            ->visible(true)
            ->registerProvider('github', 'fab-github', 'GitHub')
            ->registerProvider('azure', Heroicon::OutlinedKey, 'Azure', color: 'primary', size: 'lg'),
    );

    expect($html)
        ->toContain('href="' . route('socialment.redirect.panel', ['panelId' => 'admin', 'provider' => 'github']) . '"')
        ->toContain('GitHub')
        ->toContain('Azure')
        ->toContain('fi-btn')
        ->toContain('fi-color-primary')
        ->toContain('fi-size-lg')
        // Font Awesome (installed as a dev dependency) and Heroicon enum icons both render as SVGs.
        ->and(substr_count($html, '<svg'))->toBe(2);
});

it('renders the button without an icon when its icon set is not installed', function () {
    $html = renderProvidersList(
        (new SocialmentPlugin)
            ->visible(true)
            ->registerProvider('github', 'notinstalled-github', 'GitHub'),
    );

    expect($html)
        ->toContain('GitHub')
        ->toContain('fi-btn')
        ->not->toContain('<svg');
});
