<?php

namespace ChrisReedIO\Socialment\Http\Middleware;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

class VerifySpaCsrfToken extends VerifyCsrfToken
{
    /**
     * Get the CSRF token from the request.
     *
     * @param  Request  $request
     */
    protected function getTokenFromRequest($request): ?string
    {
        $headerName = config('socialment.spa.cookies.csrf.header', 'X-XSRF-TOKEN');
        $token = $request->input('_token') ?: $request->header($headerName);

        if (! $token && $header = $request->header($headerName)) {
            try {
                $token = CookieValuePrefix::remove($this->encrypter->decrypt($header, static::serialized()));
            } catch (DecryptException) {
                $token = '';
            }
        }

        return $token;
    }

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
            $config['partitioned'] ?? false
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
