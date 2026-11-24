<?php

use ChrisReedIO\Socialment\Http\Middleware\SpaAuthentication;
use ChrisReedIO\Socialment\Http\Resources\UserResponse;
use ChrisReedIO\Socialment\SocialmentPlugin;
use ChrisReedIO\Socialment\Tests\Fixtures\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

beforeEach(function () {
    Route::spaAuth();
    Route::spaAuth('portal');

    $this->user = User::create([
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'password' => Hash::make('secret-password'),
    ]);
});

it('registers the SPA routes under the given prefix and allows credentialed CORS on it', function () {
    app('router')->getRoutes()->refreshNameLookups();

    expect(Route::has('socialment.spa.default.login'))->toBeTrue()
        ->and(Route::has('socialment.spa.portal.me'))->toBeTrue()
        ->and(config('cors.paths'))->toContain('spa/*', 'portal/*')
        ->and(config('cors.supports_credentials'))->toBeTrue();
});

it('logs in with valid credentials and returns the user without sensitive fields', function () {
    postJson('/spa/login', ['email' => 'ada@example.com', 'password' => 'secret-password'])
        ->assertOk()
        ->assertExactJson(['name' => 'Ada Lovelace', 'email' => 'ada@example.com']);

    expect(auth()->id())->toBe($this->user->id);
});

it('rejects an invalid password', function () {
    postJson('/spa/login', ['email' => 'ada@example.com', 'password' => 'wrong'])
        ->assertUnauthorized()
        ->assertExactJson(['message' => 'Invalid credentials']);

    expect(auth()->check())->toBeFalse();
});

it('validates the login request', function () {
    postJson('/spa/login', ['email' => 'not-an-email'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'password']);
});

it('returns the current user from me', function () {
    actingAs($this->user, 'sanctum');

    getJson('/spa/me')->assertExactJson(['name' => 'Ada Lovelace', 'email' => 'ada@example.com']);
});

it('returns the current user through a configured resource class', function () {
    config()->set('socialment.spa.responses.me', CustomMeResponse::class);
    actingAs($this->user, 'sanctum');

    getJson('/spa/me')->assertExactJson(['email' => 'ada@example.com', 'custom' => true]);
});

it('requires authentication for me and logout', function () {
    getJson('/spa/me')->assertUnauthorized();
    postJson('/spa/logout')->assertUnauthorized();
});

it('logs the user out and invalidates the session', function () {
    actingAs($this->user, 'sanctum');
    $this->withSession(['something' => 'stored']);

    postJson('/spa/logout')
        ->assertOk()
        ->assertExactJson(['message' => 'Logged out']);

    expect(auth()->guard('web')->check())->toBeFalse()
        ->and(session('something'))->toBeNull();
});

it('returns an empty 204 from the csrf cookie endpoint', function () {
    get('/spa/sanctum/csrf-cookie')->assertNoContent()->assertCookie('XSRF-TOKEN');
    getJson('/spa/sanctum/csrf-cookie')->assertNoContent();
});

it('stores the referer as the intended url on the SPA provider redirect', function () {
    config()->set('services.github', ['client_id' => 'id', 'client_secret' => 'secret', 'redirect' => '/callback']);
    app(SocialmentPlugin::class)->registerProvider('github', null, 'GitHub');

    $this->get('/spa/login/github', ['referer' => 'https://spa.test/login'])
        ->assertRedirectContains('github.com');

    expect(session('socialment.intended.url'))->toBe('https://spa.test/login');
});

describe('SpaAuthentication middleware', function () {
    beforeEach(function () {
        Route::middleware(['web', SpaAuthentication::class])->get('/spa-protected', fn () => 'ok');
    });

    it('rejects guests with a JSON 401', function () {
        get('/spa-protected')->assertUnauthorized()->assertExactJson(['message' => 'Not logged in']);
    });

    it('lets users signed in on the spa guard through', function () {
        actingAs($this->user, 'spa');

        get('/spa-protected')->assertOk()->assertSee('ok');
    });
});

class CustomMeResponse extends UserResponse
{
    public function toArray($request): array
    {
        return ['email' => $this->email, 'custom' => true];
    }
}
