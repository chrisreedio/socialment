<?php

// config for ChrisReedIO/Socialment
return [
	// The list of providers to display on the login page
	// You must install the appropriate Socialite provider package for each provider you want to use
	// if it isn't one supported by the core Laravel Socialite package.
	'providers' => [
		// Use the key based on the provider's documentation
		// 'azure' => [
			// Font Awesome icon class
			// 	'icon' => 'fab-microsoft',

			// The label to display for the provider
			// 	'label' => 'Azure Active Directory',
		// ]
	],

	'view' => [
		// Set the text above the provider list
		'prompt' => 'Or Login Via',
		// Or change out the view completely with your own
		'providers-list' => 'socialment::providers-list',
	],

	'routes' => [
		// The route to redirect to after a successful login
		'home' => 'filament.admin.pages.dashboard',
	],

	'models' => [
		// If you want to use a custom user model, you can specify it here.
		'user' => \App\Models\User::class,
	],

];
