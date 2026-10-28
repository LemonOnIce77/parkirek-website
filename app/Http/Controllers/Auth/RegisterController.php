<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function show()
    {
        return view('student.auth.register'); // sesuaikan nama blade kamu
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'area' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'terms' => ['accepted'],
        ], [
            'terms.accepted' => 'Kamu harus menyetujui terms & conditions.',
        ]);

        $user = User::create([
            'nama' => $validated['nama'],        // ✅ kolom db = nama
            'email' => $validated['email'],
            // 'area' => $validated['area'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',                 // ✅ otomatis student
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('student.dashboard')->with('success', 'Registrasi berhasil.');
    }
}
