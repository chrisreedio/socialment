<?php

use ChrisReedIO\Socialment\Http\Controllers\SocialmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    // OAuth Callback from Provider
    Route::get('/login/{provider}/callback', [SocialmentController::class, 'callback'])
        ->name('socialment.callback');

    // Multi-Panel Redirect
    Route::get('/login/{provider}/panel/{panelId}', [SocialmentController::class, 'redirectPanel'])
        ->name('socialment.redirect.panel');

    // Single-Panel Routes and panels with a path of empty string (root level panels)
    Route::get('/login/{provider}', [SocialmentController::class, 'redirect'])
        ->name('socialment.redirect');
});
