<?php

namespace ChrisReedIO\Socialment\Http\Middleware;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use function dd;

class SpaAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next): Response | RedirectResponse | JsonResponse
    {
        $spaLoggedIn = Auth::guard('spa')->check();

        // Log::info('SpaAuthentication Middleware', [
        //     'spaLoggedIn' => $spaLoggedIn,
        //     'request' => $request->all(),
        // ]);
        // return response()
        //     ->json([
        //             'spaLoggedIn' => $spaLoggedIn,
        //         ]
        //     );

        // dd("SPA LOGGED IN: " . ($spaLoggedIn ? "TRUE" : "FALSE"));
        if (! Auth::guard('spa')->check()) {
            // Log::error('Spa 401!', [
            // 	'request' => $request->all(),
            // 	'response' => $response,
            // ]);

            // Old method: Redirect to a hard 401 error page, bad UX
            abort(401, 'Not logged in');

            // Option #2: Redirect to the 'home page' - Probably the best option
            // We could optionally pass a message by 'flash'ing it to the session
            // return redirect()
            // ->to('https://nuxt-chat.local.winux.io:3000/login')
            // ->withErrors('You must be logged in to access this page.');

            // Option #3: Just display the pretty error page on the existing URL.
            // This is not a very elegant solution, but it works.
            // return inertia('Error/Landing', [
            // 	'title' => 'Not Logged In',
            // 	'message' => "We're sorry, but your session has ended due to inactivity. To ensure the security of your information, we automatically log you out after a period of time. Please log in again to continue.",
            // ]);
        }

        // If we get here, the spa user is logged in
        return $next($request);
    }
}
