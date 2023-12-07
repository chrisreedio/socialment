<?php

namespace ChrisReedIO\Socialment\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class SpaAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next, ?string $guard = 'spa'): Response | RedirectResponse | JsonResponse
    {
        if (! Auth::guard($guard)->check()) {
            return response()->json(['message' => 'Not logged in'], 401);
        }

        // If we get here, the user is logged in
        return $next($request);
    }
}
