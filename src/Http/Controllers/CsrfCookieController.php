<?php

declare(strict_types=1);

namespace ChrisReedIO\Socialment\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CsrfCookieController
{
    /*
     * Return an empty response simply to trigger the storage of the CSRF cookie in the browser.
     */
    public function show(Request $request): Response | JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json(null, 204);
        }

        return response()->noContent();
    }
}
