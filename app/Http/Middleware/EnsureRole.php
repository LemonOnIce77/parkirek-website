<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // kalau belum login
        if (!$user) {
            // kalau request AJAX/JSON, kasih 401
            if ($request->expectsJson()) {
                abort(401);
            }
            return redirect()->route('login');
        }

        $userRole = strtolower((string) ($user->role ?? ''));

        // roles dari route middleware: role:admin atau role:admin,student
        $allowed = collect($roles)
            ->map(fn ($r) => strtolower(trim($r)))
            ->filter()
            ->contains($userRole);

        if (!$allowed) {
            // kalau request AJAX/JSON, kasih 403
            if ($request->expectsJson()) {
                abort(403, 'Akses ditolak.');
            }

            // OPTIONAL: redirect sesuai role biar gak mentok 403
            // kalau kamu mau strict 403, hapus blok redirect ini dan pakai abort saja
            if ($userRole === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            if ($userRole === 'student') {
                return redirect()->route('student.dashboard');
            }

            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
