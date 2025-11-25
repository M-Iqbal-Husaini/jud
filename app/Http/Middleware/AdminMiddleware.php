<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan user sudah login DAN user adalah admin
        if (Auth::check() && Auth::user()->is_admin) {
            return $next($request);
        }

        // Jika bukan admin, redirect ke halaman login atau tampilkan 403 Forbidden
        // Contoh: redirect ke home atau login dengan pesan error
        return redirect('/login')->with('error', 'Anda tidak memiliki akses admin.');
        // Atau untuk 403 Forbidden:
        // abort(403, 'Unauthorized access.');
    }
}