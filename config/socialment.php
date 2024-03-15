<?php

// config for ChrisReedIO/Socialment

return [
    // The list of providers to display on the login page
    // You must install the appropriate Socialite provider package for each provider you want to use
    // if it isn't one supported by the core Laravel Socialite package.

    // DEPRECATED: This will be removed in a future version.
    // Configure providers via the panel provider.
    'providers' => [
        // Use the key based on the provider's documentation
        // 'azure' => [
        // 	'icon' => 'fab-microsoft', // Font Awesome icon class
        // 	'label' => 'Azure Active Directory', // The label to display for the provider
        // ]
    ],

    'view' => [
        // Set the text above the provider list
        'prompt' => 'Or Login Via',
        // Or change out the view completely with your own
        'providers-list' => 'socialment::providers-list',
    ],

    // DEPRECATED: This will be removed in a future version.
    // Configure routes via the panel provider.
    'routes' => [
        'home' => 'filament.admin.pages.dashboard',
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
