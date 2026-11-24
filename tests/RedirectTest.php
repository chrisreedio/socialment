<?php

use ChrisReedIO\Socialment\SocialmentPlugin;
use Illuminate\Testing\TestResponse;

use function Pest\Laravel\get;

beforeEach(function () {
    config()->set('services.github', [
        'client_id' => 'client-id',
        'client_secret' => 'client-secret',
        'redirect' => 'https://app.test/login/github/callback',
    ]);
});

function redirectQuery(TestResponse $response): array
{
    parse_str(parse_url($response->headers->get('Location'), PHP_URL_QUERY), $query);

    return $query;
}

it('redirects to the provider with its configured scopes', function () {
    app(SocialmentPlugin::class)->registerProvider('github', null, 'GitHub', scopes: ['read:org']);

    $response = get(route('socialment.redirect', ['provider' => 'github']));

    $response->assertRedirectContains('https://github.com/login/oauth/authorize');
    expect(redirectQuery($response))
        ->client_id->toBe('client-id')
        ->scope->toBe('user:email,read:org');
});

it('redirects with only the default scopes when none are configured', function () {
    app(SocialmentPlugin::class)->registerProvider('github', null, 'GitHub');

    expect(redirectQuery(get(route('socialment.redirect', ['provider' => 'github']))))
        ->scope->toBe('user:email');
});

it('remembers the referer as the intended url on the panel redirect', function () {
    app(SocialmentPlugin::class)->registerProvider('github', null, 'GitHub');

    $this->get(route('socialment.redirect.panel', ['provider' => 'github', 'panelId' => 'admin']), ['referer' => '/admin/login'])
        ->assertRedirectContains('github.com');

    expect(session('socialment.intended.url'))->toBe('/admin/login');
});

it('keeps an intended url that is already in the session on the panel redirect', function () {
    app(SocialmentPlugin::class)->registerProvider('github', null, 'GitHub');

    $this->withSession(['socialment.intended.url' => '/first'])
        ->get(route('socialment.redirect.panel', ['provider' => 'github', 'panelId' => 'admin']), ['referer' => '/second']);

    expect(session('socialment.intended.url'))->toBe('/first');
});
