<?php

use ChrisReedIO\Socialment\Controllers\SocialmentController;
use Illuminate\Support\Facades\Route;

// Route::get('/socialment/azure', function () {
// 	return 'Azure';
// })->name('socialment.azure');

Route::get('/login/{provider}', [SocialmentController::class, 'redirect'])->name('socialment.redirect');
Route::get('/login/{provider}/callback', [SocialmentController::class, 'callback'])->name('socialment.callback');
