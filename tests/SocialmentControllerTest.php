<?php

use ChrisReedIO\Socialment\Models\ConnectedAccount;
use ChrisReedIO\Socialment\Tests\Models\User;
use Laravel\Socialite\Contracts\Factory;
use Laravel\Socialite\SocialiteManager;
use Laravel\Socialite\Two\GithubProvider;
use Mockery\MockInterface;

use function Pest\Laravel\getJson;
use function Pest\Laravel\instance;

uses()->group('controllers');

test('user redirect callback', function () {
    $userResponse = new class implements \Laravel\Socialite\Contracts\User
    {
        public int $expiresIn;

        public string $token;

        public string $refreshToken;

        public function __construct()
        {
            $this->expiresIn = now()->addMonth()->getTimestamp();
            $this->token = str()->random(32);
            $this->refreshToken = str()->random(32);
        }

        public function getId(): string
        {
            return '123';
        }

        public function getNickname(): string
        {
            return 'best coder';
        }

        public function getName(): string
        {
            return 'ever';
        }

        public function getEmail(): string
        {
            return 'in universe';
        }

        public function getAvatar(): ?string
        {
            return null;
        }
    };

    session(['socialment.intended.url' => 'https://github.com/qwerty']);

    $provider = instance(
        GithubProvider::class,
        Mockery::mock(GithubProvider::class, static function (MockInterface $mock) use ($userResponse) {
            $mock->shouldReceive('user')->andReturn($userResponse);
        })
    );
    instance(
        Factory::class,
        Mockery::mock(SocialiteManager::class, static function (MockInterface $mock) use ($provider) {
            $mock->shouldReceive('driver')->andReturn($provider);
        })
    );

    expect(User::count())->toBe(0)
        ->and(ConnectedAccount::count())->toBe(0);

    $response = getJson(route('socialment.callback', ['provider' => 'github']));
    $response->assertRedirect('https://github.com/qwerty');

    $account = ConnectedAccount::first();
    $user = User::first();

    expect(User::count())->toBe(1)
        ->and(ConnectedAccount::count())->toBe(1)
        ->and($account->user)->toEqual($user)
        ->and($account->provider_user_id)->toBe($userResponse->getId())
        ->and($account->name)->toBe($userResponse->getName())
        ->and($account->nickname)->toBe($userResponse->getNickname())
        ->and($account->email)->toBe($userResponse->getEmail())
        ->and($account->avatar)->toBe($userResponse->getAvatar())
        ->and($user->name)->toBe($userResponse->getName())
        ->and($user->email)->toBe($userResponse->getEmail());
});
