<?php

use Illuminate\Support\Facades\Route;
use ChrisReedIO\Socialment\Controllers\OAuthController;

// Route::get('/socialment/azure', function () {
// 	return 'Azure';
// })->name('socialment.azure');

Route::get('/login/{provider}', [OAuthController::class, 'redirect'])->name('socialment.redirect');
Route::get('/login/{provider}/callback', [OAuthController::class, 'callback'])->name('socialment.callback');