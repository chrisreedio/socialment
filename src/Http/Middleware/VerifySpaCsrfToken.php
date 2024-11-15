<?php

namespace ChrisReedIO\Socialment\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

use function config;

class VerifySpaCsrfToken extends VerifyCsrfToken
{
    /**
     * Create a new "XSRF-TOKEN" cookie that contains the CSRF token.
     *
     * @param  Request  $request
     * @param  array  $config
     */
    protected function newCookie($request, $config): Cookie
    {
        return new Cookie(
            config('socialment.spa.cookies.csrf.name', 'XSRF-TOKEN'),
            $request->session()->token(),
            $this->availableAt(60 * $config['lifetime']),
            $config['path'],
            config('socialment.spa.cookies.csrf.domain') ?? $config['domain'],
            $config['secure'],
            false,
            false,
            $config['same_site'] ?? null,
            $config['partitioned'] ?? false,
        );
    }

    /**
     * Determine if the cookie contents should be serialized.
     */
    public static function serialized(): bool
    {
        return EncryptCookies::serialized(config('socialment.spa.cookies.csrf.name', 'XSRF-TOKEN'));
    }
}
