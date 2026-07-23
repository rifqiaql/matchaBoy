<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Cek apakah user sudah login
        if (!auth()->check()) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. Cek apakah role user SESUAI dengan parameter yang diminta route
        if (auth()->user()->role !== $role) {
            // Lempar ke halaman 403 Forbidden (Akses Ditolak)
            abort(403, 'Akses Ditolak. Halaman ini hanya untuk otoritas ' . strtoupper($role) . '.');
        }

        // Jika lolos, izinkan sistem memuat halaman
        return $next($request);
    }
}
