<?php

use ChrisReedIO\Socialment\Models\ConnectedAccount;
use ChrisReedIO\Socialment\SocialmentPlugin;
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
])->skip();

test('createUser without createUserUsing callback', function (callable $callback) {
    /** @var array{account: ConnectedAccount, is_user_exists: bool} $data */
    $data = $callback();

    expect(User::count())->toBe(0);

    $user = SocialmentPlugin::make()->createUser($data['account']);

    expect($user->email === $data['account']->email)
        ->toBe($data['is_user_exists'])
        ->and(User::count())->toBe(1);

})->with([
    'new account model' => static fn () => [
        'account' => new ConnectedAccount,
        'is_user_exists' => false,
    ],
    'existing account model' => static fn () => [
        'account' => ConnectedAccount::factory()->for(User::factory())->create(),
        'is_user_exists' => true,
    ],
]);

test('userModel', function (string $class) {
    expect(config('socialment.models.user'))
        ->toBe('App\Models\User');

    if ($class === 'TestUser') {
        expect(static fn () => SocialmentPlugin::make()->userModel($class))
            ->toThrow(new \InvalidArgumentException("Target class [$class] does not exist"));
    } else {
        SocialmentPlugin::make()->userModel($class);

        expect(config('socialment.models.user'))
            ->not->toBe('App\Models\User')
            ->toExtend(\Illuminate\Database\Eloquent\Model::class);
    }
})->with([
    'not existing class' => 'TestUser',
    'existing class' => User::class,
]);
