<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function __invoke(Request $request)
    {
        $role = strtolower((string) (Auth::user()->role ?? ''));

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect sesuai role terakhir
        return $role === 'admin'
            ? redirect()->route('admin.auth.login')
            : redirect()->route('student.auth.login');
    }
}
