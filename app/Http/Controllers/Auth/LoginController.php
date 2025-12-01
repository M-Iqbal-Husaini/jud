<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    /**
     * Jangan pakai redirectTo statis lagi,
     * kita ganti dengan fungsi redirectAfterLogin()
     */
    // protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Form login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Login manual (email + password)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = $request->filled('remember');

        // Cek apakah email ini memang khusus login Google
        $user = User::where('email', $request->email)->first();

        if ($user && $user->google_id) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'Email ini terdaftar dengan Google login. Silakan login menggunakan Google.',
                ]);
        }

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return $this->redirectAfterLogin($request, Auth::user());
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'Email atau password tidak sesuai.',
            ]);
    }

    /**
     * Redirect ke Google
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->redirectUrl(config('services.google.redirect'))
            ->redirect();
    }

    /**
     * Callback dari Google
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl(config('services.google.redirect'))
                ->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Kalau user sudah ada tapi belum punya google_id -> update
                if (empty($user->google_id)) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                    ]);
                }
            } else {
                // Buat user baru
                $user = User::create([
                    'name'              => $googleUser->getName(),
                    'email'             => $googleUser->getEmail(),
                    'google_id'         => $googleUser->getId(),
                    'password'          => bcrypt(uniqid()),
                    'email_verified_at' => now(),
                ]);
            }

            Auth::login($user, true);

            // gunakan redirectAfterLogin supaya admin / user langsung tepat
            return $this->redirectAfterLogin(request(), $user);

        } catch (\Exception $e) {
            \Log::error('Google login error: ' . $e->getMessage());

            return redirect('/login')
                ->with('error', 'Gagal login dengan Google. Coba lagi.');
        }
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Fungsi pusat redirect setelah login (manual / Google)
     * - Admin  -> /admin/dashboard
     * - User   -> /dashboard (atau /videos, kalau mau)
     */
    protected function redirectAfterLogin(Request $request, User $user)
    {
        if ($user->is_admin) {
            return redirect()->intended(route('admin.dashboard'));
        }

        // kalau mau user langsung ke video:
        // return redirect()->intended(route('videos.index'));

        return redirect()->intended(route('dashboard'));
    }
}
