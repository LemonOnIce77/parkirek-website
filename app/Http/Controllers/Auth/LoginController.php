<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {

        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();
        $role = strtolower((string) ($user->role ?? ''));

        // ✅ Wajib ada konteks login_as
        $loginAs = strtolower((string) $request->input('login_as', ''));

        if ($loginAs === '') {
            $this->forceLogout($request);
            return back()->withErrors([
                'email' => 'Login context tidak valid. Silakan login lewat halaman Admin/Student.',
            ])->onlyInput('email');
        }

        // ✅ Block jika role tidak cocok dengan halaman login
        if ($loginAs === 'admin' && $role !== 'admin') {
            $this->forceLogout($request);
            return back()->withErrors(['email' => 'Akun ini bukan admin.'])->onlyInput('email');
        }

        if ($loginAs === 'student' && $role !== 'student') {
            $this->forceLogout($request);
            return back()->withErrors(['email' => 'Akun ini bukan student.'])->onlyInput('email');
        }

        // ✅ Redirect yang aman
        return redirect()->intended(
            $role === 'admin'
                ? route('admin.dashboard')
                : route('student.dashboard')
        );
    }

    public function logout(Request $request)
    {
        $role = strtolower((string) (Auth::user()->role ?? ''));

        $this->forceLogout($request);

        return $role === 'admin'
            ? redirect()->route('admin.auth.login')
            : redirect()->route('student.auth.login');
    }

    private function forceLogout(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
