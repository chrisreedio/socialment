<?php

use ChrisReedIO\Socialment\Controllers\SocialmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    // Single-Panel Routes and panels with a path of empty string (root level panels)
    Route::get('/login/{provider}', [SocialmentController::class, 'redirect'])
        ->name('socialment.redirect');
    // SPA Route
    Route::get('/spa/login/{provider}', [SocialmentController::class, 'redirectSpa'])
        ->name('socialment.redirect.spa');
    // Multi-Panel Redirect
    Route::get('/{panelId}/login/{provider}', [SocialmentController::class, 'redirectPanel'])
        ->name('socialment.redirect.panel');

    // OAuth Callback from Provider
    Route::get('/login/{provider}/callback', [SocialmentController::class, 'callback'])
        ->name('socialment.callback');


});
