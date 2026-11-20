<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => __('auth.google_failed'),
            ]);
        }

        // Only trust emails that Google has verified
        if (! ($googleUser->user['email_verified'] ?? false)) {
            return redirect()->route('login')->withErrors([
                'email' => __('auth.google_unverified'),
            ]);
        }

        $isNewUser = false;

        $user = User::where('google_id', $googleUser->getId())->first()
            ?? User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // Link Google to an existing account without changing role or status
            $user->forceFill([
                'google_id' => $googleUser->getId(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();
        } else {
            $user = User::create([
                'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Google User',
                'email' => $googleUser->getEmail(),
                'password' => Str::random(32), // hashed by the model cast
            ]);

            $user->forceFill([
                'google_id' => $googleUser->getId(),
                'email_verified_at' => now(),
            ])->save();

            $isNewUser = true;
        }

        // Block suspended accounts before logging in
        if ($user->isSuspended()) {
            return redirect()->route('login')->withErrors([
                'email' => __('auth.account_suspended'),
            ]);
        }
        if ($isNewUser) {
            event(new Registered($user));
        }
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
