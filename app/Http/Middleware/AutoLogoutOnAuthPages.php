<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutoLogoutOnAuthPages
{
    public function handle(Request $request, Closure $next)
    {
        // Kalau masih login lalu buka halaman login/register -> logout otomatis
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $response = $next($request);

        // Anti cache supaya browser tidak pakai CSRF lama
        return $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
