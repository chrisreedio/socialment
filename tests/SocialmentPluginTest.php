<?php

use ChrisReedIO\Socialment\Models\ConnectedAccount;
use ChrisReedIO\Socialment\SocialmentPlugin;
use ChrisReedIO\Socialment\Tests\Models\User;

test('createUserUsing callback', function (callable $callback) {
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
