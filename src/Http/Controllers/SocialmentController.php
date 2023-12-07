<?php

namespace ChrisReedIO\Socialment\Http\Controllers;

use ChrisReedIO\Socialment\Exceptions\AbortedLoginException;
use ChrisReedIO\Socialment\Facades\Socialment;
use ChrisReedIO\Socialment\Models\ConnectedAccount;
use Exception;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use JetBrains\PhpStorm\Deprecated;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Symfony\Component\HttpFoundation\RedirectResponse;

use function config;
use function redirect;
use function request;

class SocialmentController extends BaseController
{
    /**
     * @deprecated All panel redirects go through the redirectPanel method now
     */
    public function redirect(string $provider): RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    public function redirectSpa(string $provider): RedirectResponse
    {
        // Store the referring url in the session
        request()->session()->put('socialment.intended.url', request()->headers->get('referer'));

        return Socialite::driver($provider)->redirect();
    }

    public function redirectPanel(string $provider, string $panelId): RedirectResponse
    {
        $referer = request()->headers->get('referer');
        if (! request()->session()->exists('socialment.intended.url')) {
            request()->session()->put('socialment.intended.url', $referer);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        // If we have a redirect URL use that before a redirect route
        $intendedUrl = request()->session()->pull('socialment.intended.url');

        try {
            /** @var \SocialiteProviders\Manager\OAuth2\User $socialUser */
            $socialUser = Socialite::driver($provider)->user();

            $userModel = config('socialment.models.user');

            $tokenExpiration = match ($provider) {
                'azure' => now()->addSeconds($socialUser->expiresIn),
                default => null,
            };

            // Create a user or log them in...
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

            if (! $connectedAccount->exists) {
                // Check for an existing user with this email
                // Create a new user if one doesn't exist
                $user = $userModel::where('email', $socialUser->getEmail())->first()
                    ?? $userModel::create([
                        'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                        'email' => $socialUser->getEmail(),
                    ]);

                // Associate the user and save this connected account
                $connectedAccount->user()->associate($user)->save();
            } else {
                // Update the connected account with the latest data
                $connectedAccount->update([
                    'name' => $socialUser->getName(),
                    'nickname' => $socialUser->getNickname(),
                    'email' => $socialUser->getEmail(),
                    'avatar' => $socialUser->getAvatar(),
                    'token' => $socialUser->token,
                    'refresh_token' => $socialUser->refreshToken,
                    'expires_at' => $tokenExpiration,
                ]);
            }

            Socialment::executePreLogin($connectedAccount);

            Auth::login($connectedAccount->user);

            Socialment::executePostLogin($connectedAccount);

            // Redirect to the intended URL if it exists
            if ($intendedUrl) {
                return redirect()->to($intendedUrl);
            }

            // Fallback to the default login url (which will take us to the right place since we're logged in)
            return redirect()->to(Filament::getLoginUrl());
        } catch (InvalidStateException $e) {
            Session::flash('socialment.error', 'Something went wrong. Please try again.');

            return redirect()->to($intendedUrl);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            Session::flash('socialment.error', 'We had a problem contacting the authentication server. Please try again.');

            return redirect()->to($intendedUrl);
        } catch (AbortedLoginException $e) {
            Session::flash('socialment.error', $e->getMessage());

            return redirect()->to($intendedUrl);
        } catch (Exception $e) {
            Session::flash('socialment.error', 'An unknown error occurred: ' . $e->getMessage() . '. Please try again.');

            return redirect()->to($intendedUrl);
        }
    }
}
