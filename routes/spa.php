<?php

use ChrisReedIO\Socialment\Http\Controllers\CsrfCookieController;
use ChrisReedIO\Socialment\Http\Controllers\SocialmentController;
use ChrisReedIO\Socialment\Http\Controllers\SpaAuthController;
use Illuminate\Support\Facades\Route;

// Custom SPA specific route for getting a CSRF cookie
Route::get('sanctum/csrf-cookie', [CsrfCookieController::class, 'show'])->name('csrf-cookie');
// Non-Social User Login
Route::post('login', [SpaAuthController::class, 'login'])->name('login');
// Redirect out to OAuth Provider
Route::get('login/{provider}', [SocialmentController::class, 'redirectSpa'])
    ->name('redirect');
// Authenticated Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [SpaAuthController::class, 'logout'])->name('logout');
    Route::get('me', [SpaAuthController::class, 'me'])->name('me');
});
