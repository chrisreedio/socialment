<?php

declare(strict_types=1);

namespace ChrisReedIO\Socialment\Http\Controllers;

use ChrisReedIO\Socialment\Exceptions\AbortedLoginException;
use ChrisReedIO\Socialment\Facades\Socialment;
use ChrisReedIO\Socialment\Models\ConnectedAccount;
use ChrisReedIO\Socialment\SocialmentPlugin;
use Exception;
use Filament\Facades\Filament;
use JetBrains\PhpStorm\Deprecated;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\InvalidStateException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SocialmentController extends BaseController
{
    /**
     * @deprecated All panel redirects go through the redirectPanel method now
     */
    public function redirect(string $provider): RedirectResponse
    {
        return $this->getProviderRedirect($provider);
    }

    public function redirectSpa(string $provider): RedirectResponse
    {
        // Store the referring url in the session
        request()->session()->put('socialment.intended.url', request()->headers->get('referer'));

        return $this->getProviderRedirect($provider);
    }

    public function redirectPanel(string $provider, string $panelId): RedirectResponse
    {
        $referer = request()->headers->get('referer');
        if (! request()->session()->exists('socialment.intended.url')) {
            request()->session()->put('socialment.intended.url', $referer);
        }

        return $this->getProviderRedirect($provider);
    }

    private function getProviderRedirect(string $providerName): \Illuminate\Http\RedirectResponse
    {
        /** @var AbstractProvider $provider */
        $provider = Socialite::driver($providerName);
        $providerConfig = app(SocialmentPlugin::class)->getProvider($providerName);
        if (! empty($providerConfig['scopes'])) {
            $provider->scopes($providerConfig['scopes']);
        }

        return $provider->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        try {
            /** @var \SocialiteProviders\Manager\OAuth2\User $socialUser */
            $socialUser = Socialite::driver($provider)->user();

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
                $user = Socialment::createUser($connectedAccount);

                if ($user === null) {
                    throw new AbortedLoginException('This account is not authorized to log in.');
                }

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

            auth()->login($connectedAccount->user);

            Socialment::executePostLogin($connectedAccount);
        } catch (InvalidStateException) {
            session()->flash('socialment.error', 'Something went wrong. Please try again.');
        } catch (\GuzzleHttp\Exception\ClientException) {
            session()->flash('socialment.error', 'We had a problem contacting the authentication server. Please try again.');
        } catch (AbortedLoginException $e) {
            session()->flash('socialment.error', $e->getMessage());
        } catch (Exception $e) {
            session()->flash('socialment.error', 'An unknown error occurred: ' . $e->getMessage() . '. Please try again.');
        }

        return redirect()->to($this->getRedirectUrl());
    }

    private function getRedirectUrl(): string
    {
        return request()->session()->pull('socialment.intended.url') ?? Filament::getLoginUrl();
    }
}
