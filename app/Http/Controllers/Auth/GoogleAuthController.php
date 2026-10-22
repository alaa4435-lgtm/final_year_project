<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')
            ->scopes(['https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile'])
            ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->redirect();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Google authentication failed. Please try again.',
            ]);
        }

        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user) {
            $updateData = [
                'google_token' => $googleUser->token,
            ];
            if ($googleUser->refreshToken) {
                $updateData['google_refresh_token'] = $googleUser->refreshToken;
            }
            $user->update($updateData);
        } else {
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $updateData = [
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                    'email_verified_at' => $user->email_verified_at ?? now(),
                    'status' => 'active',
                ];
                if ($googleUser->refreshToken) {
                    $updateData['google_refresh_token'] = $googleUser->refreshToken;
                }
                $user->update($updateData);
            } else {
                $user = User::create([
                    'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Google User',
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                    'status' => 'active',
                    'role' => 'learner',
                ]);
            }
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }
}
