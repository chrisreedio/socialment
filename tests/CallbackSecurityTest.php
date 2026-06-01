<?php

use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

it('does not flash raw generic callback exception messages', function () {
    $exception = new Exception(
        "SQLSTATE[HY000]: General error: 1205 Lock wait timeout exceeded (SQL: insert into `connected_accounts` (`token`) values ('eyJ0eXAiOiJKV1Qi'))"
    );

    $provider = Mockery::mock();
    $provider->shouldReceive('user')
        ->once()
        ->andThrow($exception);

    Socialite::shouldReceive('driver')
        ->once()
        ->with('azure')
        ->andReturn($provider);

    Log::shouldReceive('error')
        ->once()
        ->with('Socialment callback error', Mockery::on(fn (array $context): bool => $context['exception'] === $exception));

    $this->withSession(['socialment.intended.url' => '/admin/login'])
        ->get(route('socialment.callback', ['provider' => 'azure']))
        ->assertRedirect('/admin/login');

    expect(session('socialment.error'))
        ->toBe('An error occurred during sign-in. Please try again.')
        ->not->toContain('eyJ0eXAiOiJKV1Qi')
        ->not->toContain('connected_accounts')
        ->not->toContain('SQLSTATE');
});
