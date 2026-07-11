<?php

declare(strict_types=1);

namespace ChrisReedIO\Socialment\Http\Controllers;

use ChrisReedIO\Socialment\Http\Requests\SpaLoginRequest;
use ChrisReedIO\Socialment\Http\Resources\UserResource;
use Illuminate\Http\Request;

class SpaAuthController extends BaseController
{
    public function login(SpaLoginRequest $request): \Illuminate\Http\JsonResponse | UserResource
    {
        // Find the user by email and validate their password
        $authSuccess = auth()->attempt($request->validated());

        // If the user is not found or the password is invalid, return an error
        if (! $authSuccess) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Get the 'now logged in' user
        $user = auth()->user();

        /** @var class-string<UserResource> $resourceClass */
        $resourceClass = config('socialment.spa.responses.me', UserResource::class);

        // Cookie Auth
        return $resourceClass::make($user);

        // Token Auth
        // Send the token back as a response
        // return UserResponse::make($user)
        //     ->additional([
        //         'token' => $user->createToken('auth_token')->plainTextToken,
        //     ]);
    }

    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        // Revoke the token that was used to authenticate the current request...
        // Auth::user()->currentAccessToken()->delete();
        // Log the user out of the application...
        auth('web')->logout();

        // Invalidate the session token to prevent reuse
        $request->session()->invalidate();

        // Regenerate the session token to prevent session fixation attacks
        $request->session()->regenerateToken();

        // Respond with the logged out message
        return response()->json(['message' => 'Logged out']);
    }

    public function me(): UserResource
    {
        /** @var class-string<UserResource> $resourceClass */
        $resourceClass = config('socialment.spa.responses.me', UserResource::class);

        return $resourceClass::make(auth()->user());
    }
}
