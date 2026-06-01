<?php

use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;

it('does not flash raw generic callback exception messages', function () {
    $leakedToken = 'fake-oauth-access-token-value';

    config()->set('app.key', 'base64:' . base64_encode(random_bytes(32)));

    $exception = new Exception(
        "SQLSTATE[HY000]: General error: 1205 Lock wait timeout exceeded (SQL: insert into `connected_accounts` (`token`) values ('{$leakedToken}'))"
    );

    $provider = Mockery::mock();
    $provider->shouldReceive('user')
        ->once()
        ->andThrow($exception);

    $socialite = Mockery::mock(SocialiteFactory::class);
    $socialite->shouldReceive('driver')
        ->once()
        ->with('azure')
        ->andReturn($provider);

    app()->instance(SocialiteFactory::class, $socialite);

    Log::spy();

    $this->withSession(['socialment.intended.url' => '/admin/login'])
        ->get(route('socialment.callback', ['provider' => 'azure']))
        ->assertRedirect('/admin/login');

    Log::shouldHaveReceived('error')
        ->once()
        ->with('Socialment callback error', Mockery::on(fn (array $context): bool => $context['exception'] === $exception));

    expect(session('socialment.error'))
        ->toBe('An error occurred during sign-in. Please try again.')
        ->not->toContain($leakedToken)
        ->not->toContain('connected_accounts')
        ->not->toContain('SQLSTATE');
});
