<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes([
                'openid',
                'profile', 
                'email',
                'https://www.googleapis.com/auth/youtube.readonly',
                'https://www.googleapis.com/auth/youtube.force-ssl'
            ])
            ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Log untuk debugging
            Log::info('Google User Token Info: ', [
                'access_token' => $googleUser->token ? 'Present' : 'Missing',
                'refresh_token' => $googleUser->refreshToken ? 'Present' : 'Missing',
                'expires_in' => $googleUser->expiresIn ?? 'N/A'
            ]);
            
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'youtube_access_token' => $googleUser->token,
                    'youtube_refresh_token' => $googleUser->refreshToken,
                    'youtube_token_expires_at' => $googleUser->expiresIn ? now()->addSeconds($googleUser->expiresIn) : null,
                    'password' => bcrypt(uniqid()),
                    'email_verified_at' => now(),
                ]);
            } else {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'youtube_access_token' => $googleUser->token,
                    'youtube_refresh_token' => $googleUser->refreshToken,
                    'youtube_token_expires_at' => $googleUser->expiresIn ? now()->addSeconds($googleUser->expiresIn) : null,
                ]);
            }
            
            Auth::login($user);
            
            return redirect()->intended('/dashboard')
                ->with('success', 'Successfully connected to Google and YouTube!');
            
        } catch (\Exception $e) {
            Log::error('Google login error: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Google login failed: ' . $e->getMessage());
        }
    }
}