<?php

namespace ChrisReedIO\Socialment\Controllers;

use ChrisReedIO\Socialment\Models\ConnectedAccount;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Laravel\Socialite\Facades\Socialite;

class SocialmentController extends Controller
{
    use AuthorizesRequests;
    use ValidatesRequests;

    public function redirect(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        $socialUser = Socialite::driver($provider)->user();

        $tokenExpiration = match ($provider) {
            'azure' => now()->addSeconds($socialUser->expiresIn),
            default => null,
        };

		// Create a user or log them in...
        /** @var ConnectedAccount */
        $connectedAccount = ConnectedAccount::firstOrNew([
            'provider' => $provider,
            'provider_user_id' => $socialUser->getId(),
        ], [
            'name' => $socialUser->getName(),
            'nickname' => $socialUser->getNickname(),
            'email' => $socialUser->getEmail(),
            'avatar' => $socialUser->getAvatar(),
            'token' => $socialUser->token,
            'refresh_token' => $socialUser->refreshToken,
            'expires_at' => $tokenExpiration,
        ]);

        if (!$connectedAccount->exists) {
            // Create the user and save this connected account
            $connectedAccount->user()->associate(config('socialment.models.user')::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
            ]))->save();
        }

        auth()->login($connectedAccount->user);

        return redirect()->route(config('socialment.routes.home'));
    }
}
