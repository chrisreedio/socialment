<?php

namespace ChrisReedIO\Socialment\Http\Controllers;

use ChrisReedIO\Socialment\Exceptions\AbortedLoginException;
use ChrisReedIO\Socialment\Facades\Socialment;
use ChrisReedIO\Socialment\Models\ConnectedAccount;
use ChrisReedIO\Socialment\SocialmentPlugin;
use Exception;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Symfony\Component\HttpFoundation\RedirectResponse;

use function config;
use function redirect;
use function request;

class SocialmentController extends BaseController
{
    public function redirect(string $provider): RedirectResponse
    {
        dd('normal redirect');
        $defaultPanelId = Filament::getDefaultPanel()->getId();
        request()->session()->put('socialment.auth.panel', $defaultPanelId);

        return Socialite::driver($provider)->redirect();
    }

    public function redirectSpa(string $provider): RedirectResponse
    {
        // Store the referring url in the session
        // dd('referer: ' . request()->headers->get('referer'));
        request()->session()->put('socialment.intended.url', request()->headers->get('referer'));
        // dd(request()->path());

        return Socialite::driver($provider)->redirect();
    }

    public function redirectPanel(string $panelId, string $provider): RedirectResponse
    {
        dd('panel redirect');
        request()->session()->put('socialment.auth.panel', $panelId);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        $globalPlugin = app(SocialmentPlugin::class);
        $spaUrl = config('socialment.spa.home');
        $panelId = request()->session()->pull('socialment.auth.panel');
        // If we have a redirect URL use that before a redirect route
        $intendedUrl = request()->session()->pull('socialment.intended.url');

        $panel = Filament::getPanel($panelId);

        $fallbackLoginRoute = 'filament.' . ($panelId ?? 'admin') . '.auth.login';
        $fallbackHomeRoute = 'filament.' . ($panelId ?? 'admin') . '.pages.dashboard';

        try {
            /** @var SocialmentPlugin $plugin */
            $plugin = $panel->getPlugin('socialment');
            // $plugin = app(SocialmentPlugin::class);
        } catch (Exception $e) {
            Session::flash('socialment.error', $e->getMessage());

            // return redirect()->route($fallbackLoginRoute);
            return redirect()->to(Filament::getDefaultPanel()->getLoginUrl());
        }

        $loginRoute = $plugin->getLoginRoute() ?: $fallbackLoginRoute;
        $homeRoute = $plugin->getHomeRoute() ?: $fallbackHomeRoute;

        try {
            /** @var \SocialiteProviders\Manager\OAuth2\User $socialUser */
            $socialUser = Socialite::driver($provider)->user();

            $userModel = config('socialment.models.user');

            $tokenExpiration = match ($provider) {
                'azure' => now()->addSeconds($socialUser->expiresIn),
                default => null,
            };

            // Create a user or log them in...
            /** @var ConnectedAccount $connectedAccount */
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
                        'name' => $socialUser->getName(),
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

            // dd('prelogin in socialment')
            // dd($plugin);
            // $plugin->executePreLogin($connectedAccount);
            Socialment::executePreLogin($connectedAccount);

            Auth::login($connectedAccount->user);

            $plugin->executePostLogin($connectedAccount);

            // Redirect to the intended URL if it exists
            if ($intendedUrl) {
                return redirect()->to($intendedUrl);
            }

            // Fallback to the configured home route
            return redirect()->route($homeRoute);
        } catch (InvalidStateException $e) {
            Session::flash('socialment.error', 'Something went wrong. Please try again.');

            return redirect()->route($loginRoute);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            Session::flash('socialment.error', 'We had a problem contacting the authentication server. Please try again.');

            return redirect()->route($loginRoute);
        } catch (AbortedLoginException $e) {
            Session::flash('socialment.error', $e->getMessage());

            return redirect()->route($loginRoute);
        } catch (Exception $e) {
            throw $e;
            Session::flash('socialment.error', 'An unknown error occurred: ' . $e->getMessage() . '. Please try again.');

            return redirect()->route($loginRoute);
        }
    }
}
