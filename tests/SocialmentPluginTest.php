<?php

use ChrisReedIO\Socialment\Models\ConnectedAccount;
use ChrisReedIO\Socialment\SocialmentPlugin;
use ChrisReedIO\Socialment\Tests\Models\OrderAction;
use ChrisReedIO\Socialment\Tests\Models\User;

uses()->group('core');

test('createUser with createUserUsing callback', function (callable $callback) {
    /** @var array{user: User, expectation: ?User} $data */
    $data = $callback();

    $pluginUser = SocialmentPlugin::make()
        ->createUserUsing(static fn () => $data['expectation'])
        ->createUser($data['user']->connectedAccounts->first());

    expect($pluginUser)->toEqual($data['expectation']);
})->with([
    'empty user' => static fn () => [
        'user' => User::factory()->has(ConnectedAccount::factory())->create(),
        'expectation' => null,
    ],
    'existed user' => static fn () => [
        'user' => User::factory()->has(ConnectedAccount::factory())->create(),
        'expectation' => User::factory()->create(),
    ],
]);

test('createUser without createUserUsing callback', function (callable $callback) {
    /** @var array{account: ConnectedAccount, users_initial_count: int} $data */
    $data = $callback();

    expect(User::count())->toBe($data['users_initial_count']);

    $user = SocialmentPlugin::make()->createUser($data['account']);

    expect($user->email)
        ->toBe($data['account']->email)
        ->and($user->name)
        ->toBe($data['account']->name)
        ->and(User::count())->toBe($data['users_initial_count'] + 1);
})->with([
    'new account model' => static fn () => [
        'account' => new ConnectedAccount(['name' => 'test', 'email' => 'test@test.com']),
        'users_initial_count' => 0,
    ],
    'existing account model' => static fn () => [
        'account' => ConnectedAccount::factory()->for(User::factory())->create(),
        'users_initial_count' => 1,
    ],
]);

test('userModel', function (string $class) {
    config(['socialment.models.user' => 'App\Models\User']);

    switch ($class) {
        case 'TestUser':
            expect(static fn () => SocialmentPlugin::make()->userModel($class))
                ->toThrow(new \InvalidArgumentException("Target class [$class] does not exist"));

            break;
        case OrderAction::class:
            expect(static fn () => SocialmentPlugin::make()->userModel($class))
                ->toThrow(new \InvalidArgumentException("The object of $class parameter should be instance of Eloquent model class"));

            break;

        default:
            SocialmentPlugin::make()->userModel($class);

            expect(config('socialment.models.user'))
                ->not->toBe('App\Models\User')
                ->toExtend(\Illuminate\Database\Eloquent\Model::class);
    }
})->with([
    'not existing class' => 'TestUser',
    'existing not eloquent class' => OrderAction::class,
    'existing class' => User::class,
]);
