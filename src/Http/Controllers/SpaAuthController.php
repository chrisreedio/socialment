<?php

namespace ChrisReedIO\Socialment\Http\Controllers;

use ChrisReedIO\Socialment\Http\Requests\SpaLoginRequest;
use ChrisReedIO\Socialment\Http\Resources\UserResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function response;

class SpaAuthController extends BaseController
{
    public function login(SpaLoginRequest $request)
    {
        // Find the user by email and validate their password
        $authSuccess = Auth::attempt($request->validated());

        // If the user is not found or the password is invalid, return an error
        if (! $authSuccess) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Get the 'now logged in' user
        $user = Auth::user();

        // Cookie Auth
        return UserResponse::make($user);

        // Token Auth
        // Send the token back as a response
        // return UserResponse::make($user)
        //     ->additional([
        //         'token' => $user->createToken('auth_token')->plainTextToken,
        //     ]);
    }

    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request...
        // Auth::user()->currentAccessToken()->delete();
        // Log the user out of the application...
        Auth::guard('web')->logout();

        // Invalidate the session token to prevent reuse
        $request->session()->invalidate();

        // Regenerate the session token to prevent session fixation attacks
        $request->session()->regenerateToken();

        // Respond with the logged out message
        return response()->json(['message' => 'Logged out']);
    }

    public function me()
    {
        return UserResponse::make(Auth::user());
    }
}
