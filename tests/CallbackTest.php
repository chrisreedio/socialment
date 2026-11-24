<?php

use ChrisReedIO\Socialment\Exceptions\AbortedLoginException;
use ChrisReedIO\Socialment\Models\ConnectedAccount;
use ChrisReedIO\Socialment\SocialmentPlugin;
use ChrisReedIO\Socialment\Tests\Fixtures\User;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request as PsrRequest;
use GuzzleHttp\Psr7\Response as PsrResponse;
use Illuminate\Support\Carbon;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as SocialiteUser;

function socialiteUser(array $overrides = []): SocialiteUser
{
    $attributes = [
        'id' => 'provider-123',
        'name' => 'Ada Lovelace',
        'nickname' => 'ada',
        'email' => 'ada@example.com',
        'avatar' => 'https://example.com/ada.png',
        ...$overrides,
    ];

    return (new SocialiteUser)
        ->map($attributes)
        ->setToken($overrides['token'] ?? 'access-token')
        ->setRefreshToken($overrides['refreshToken'] ?? 'refresh-token')
        ->setExpiresIn(3600);
}

/**
 * Makes Socialite return the given user, or throw the given exception, for the provider's callback.
 */
function fakeSocialiteCallback(string $provider, SocialiteUser | Throwable $result): void
{
    $driver = Mockery::mock();
    $expectation = $driver->shouldReceive('user')->once();
    $result instanceof Throwable ? $expectation->andThrow($result) : $expectation->andReturn($result);

    $socialite = Mockery::mock(SocialiteFactory::class);
    $socialite->shouldReceive('driver')->with($provider)->andReturn($driver);

    Socialite::swap($socialite);
}

function callback(string $provider = 'github')
{
    return test()
        ->withSession(['socialment.intended.url' => '/admin/login'])
        ->get(route('socialment.callback', ['provider' => $provider]));
}

it('creates a user and connected account on first login', function () {
    fakeSocialiteCallback('github', socialiteUser());

    callback()->assertRedirect('/admin/login');

    $user = User::sole();
    expect($user->only('name', 'email'))->toBe(['name' => 'Ada Lovelace', 'email' => 'ada@example.com'])
        ->and(auth()->id())->toBe($user->id)
        ->and($user->connectedAccounts)->toHaveCount(1)
        ->and($user->connectedAccounts->first()->only('provider', 'provider_user_id', 'nickname', 'token', 'refresh_token', 'expires_at'))
        ->toBe([
            'provider' => 'github',
            'provider_user_id' => 'provider-123',
            'nickname' => 'ada',
            'token' => 'access-token',
            'refresh_token' => 'refresh-token',
            'expires_at' => null,
        ])
        ->and(session('socialment.error'))->toBeNull();
});

it('links a first login to an existing user with the same email', function () {
    $existing = User::create(['name' => 'Existing', 'email' => 'ada@example.com']);
    fakeSocialiteCallback('github', socialiteUser());

    callback();

    expect(User::count())->toBe(1)
        ->and(auth()->id())->toBe($existing->id)
        ->and(ConnectedAccount::sole()->user_id)->toBe($existing->id);
});

it('updates the connected account of a returning user instead of creating another', function () {
    $user = User::create(['name' => 'Ada', 'email' => 'ada@example.com']);
    $account = new ConnectedAccount(['provider' => 'github', 'provider_user_id' => 'provider-123', 'token' => 'old-token']);
    $account->user()->associate($user)->save();

    fakeSocialiteCallback('github', socialiteUser(['name' => 'Ada King', 'token' => 'new-token']));

    callback();

    expect(ConnectedAccount::count())->toBe(1)
        ->and($account->fresh()->only('name', 'token'))->toBe(['name' => 'Ada King', 'token' => 'new-token'])
        ->and(auth()->id())->toBe($user->id);
});

it('keeps one connected account per provider for the same user', function () {
    fakeSocialiteCallback('github', socialiteUser());
    callback();
    fakeSocialiteCallback('azure', socialiteUser(['id' => 'azure-456']));
    callback('azure');

    $user = User::sole();
    expect($user->connectedAccounts)->toHaveCount(2)
        ->and($user->connectedAccounts()->provider('azure')->sole()->provider_user_id)->toBe('azure-456');
});

it('stores the token expiry for azure logins', function () {
    Carbon::setTestNow('2026-01-01 12:00:00');
    fakeSocialiteCallback('azure', socialiteUser());

    callback('azure');

    expect(ConnectedAccount::sole()->expires_at)->toBe('2026-01-01 13:00:00');
});

it('uses the createUserUsing closure and aborts the login when it returns null', function () {
    app(SocialmentPlugin::class)->createUserUsing(fn () => null);
    fakeSocialiteCallback('github', socialiteUser());

    callback()->assertRedirect('/admin/login');

    expect(auth()->check())->toBeFalse()
        ->and(User::count())->toBe(0)
        ->and(ConnectedAccount::count())->toBe(0)
        ->and(session('socialment.error'))->toBe('This account is not authorized to log in.');
});

it('runs pre and post login callbacks around the login', function () {
    $calls = [];
    app(SocialmentPlugin::class)
        ->preLogin(function (ConnectedAccount $account) use (&$calls) {
            $calls[] = ['pre', $account->provider_user_id, auth()->check()];
        })
        ->postLogin(function (ConnectedAccount $account) use (&$calls) {
            $calls[] = ['post', $account->provider_user_id, auth()->check()];
        });
    fakeSocialiteCallback('github', socialiteUser());

    callback();

    expect($calls)->toBe([['pre', 'provider-123', false], ['post', 'provider-123', true]]);
});

it('runs global login callbacks before the plugin callbacks', function () {
    $calls = [];
    SocialmentPlugin::globalPreLogin(function (ConnectedAccount $account) use (&$calls) {
        $calls[] = ['global pre', $account->provider_user_id, auth()->check()];
    });
    SocialmentPlugin::globalPostLogin(function (ConnectedAccount $account) use (&$calls) {
        $calls[] = ['global post', $account->provider_user_id, auth()->check()];
    });
    app(SocialmentPlugin::class)
        ->preLogin(function () use (&$calls) {
            $calls[] = ['pre'];
        })
        ->postLogin(function () use (&$calls) {
            $calls[] = ['post'];
        });
    fakeSocialiteCallback('github', socialiteUser());

    callback();

    expect($calls)->toBe([
        ['global pre', 'provider-123', false],
        ['pre'],
        ['global post', 'provider-123', true],
        ['post'],
    ]);
});

it('lets a global pre login callback abort the login', function () {
    SocialmentPlugin::globalPreLogin(function () {
        throw new AbortedLoginException('Blocked globally.');
    });
    fakeSocialiteCallback('github', socialiteUser());

    callback();

    expect(auth()->check())->toBeFalse()
        ->and(session('socialment.error'))->toBe('Blocked globally.');
});

it('lets a pre login callback abort the login with its own message', function () {
    app(SocialmentPlugin::class)->preLogin(function () {
        throw new AbortedLoginException('Admins only.');
    });
    fakeSocialiteCallback('github', socialiteUser());

    callback();

    expect(auth()->check())->toBeFalse()
        ->and(session('socialment.error'))->toBe('Admins only.');
});

it('flashes a friendly error for known callback failures', function (Throwable $exception, string $message) {
    fakeSocialiteCallback('github', $exception);

    callback()->assertRedirect('/admin/login');

    expect(session('socialment.error'))->toBe($message);
})->with([
    'invalid state' => [new InvalidStateException, 'Something went wrong. Please try again.'],
    'provider http error' => [
        fn () => new ClientException('Bad request', new PsrRequest('POST', 'https://example.com/token'), new PsrResponse(400)),
        'We had a problem contacting the authentication server. Please try again.',
    ],
]);

it('consumes the intended url so it is used only once', function () {
    fakeSocialiteCallback('github', socialiteUser());

    callback();

    expect(session()->has('socialment.intended.url'))->toBeFalse();
});
