<?php

use ChrisReedIO\Socialment\Controllers\SocialmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    // Single-Panel Routes and panels with a path of empty string (root level panels)
    Route::get('/login/{provider}', [SocialmentController::class, 'redirect'])->name('socialment.redirect');
    Route::get('/login/{provider}/callback', [SocialmentController::class, 'callback'])->name('socialment.callback');

    // Multi-Panel Redirect
    Route::get('/{panelId}/login/{provider}', [SocialmentController::class, 'panelRedirect'])->name('socialment.panel.redirect');
});
