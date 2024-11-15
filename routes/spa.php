<?php

use ChrisReedIO\Socialment\Http\Controllers\CsrfCookieController;
use ChrisReedIO\Socialment\Http\Controllers\SocialmentController;
use ChrisReedIO\Socialment\Http\Controllers\SpaAuthController;
use ChrisReedIO\Socialment\Http\Middleware\VerifySpaCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

// Custom SPA specific route for getting a CSRF cookie
Route::get('sanctum/csrf-cookie', [CsrfCookieController::class, 'show'])->name('csrf-cookie');
// Non-Social User Login
Route::post('login', [SpaAuthController::class, 'login'])->name('login');
// Redirect out to OAuth Provider
Route::get('login/{provider}', [SocialmentController::class, 'redirectSpa'])
    ->name('redirect');
// Authenticated Routes
Route::middleware(['auth:sanctum'])->group(function () {
// Route::middleware(['auth'])->group(function () {
    Route::post('logout', [SpaAuthController::class, 'logout'])->name('logout');
    Route::get('me', [SpaAuthController::class, 'me'])->name('me');
});
// });
