<?php

// config for ChrisReedIO/Socialment

return [
    'view' => [
        // Set the text above the provider list
        'prompt' => 'Or Login Via',
        // Or change out the view completely with your own
        'providers-list' => 'socialment::providers-list',
    ],

    'spa' => [
        // The URL to redirect to after a successful login
        'home' => env('SPA_URL', 'http://localhost:3000'),
        'responses' => [
            // Replace with your own JsonResource class if you want to customize the response
            // 'me' => \ChrisReedIO\Socialment\Http\Resources\UserResponse::class,
        ],
    ],

    'models' => [
        // If you want to use a custom user model, you can specify it here.
        'user' => '\App\Models\User',
    ],

];
