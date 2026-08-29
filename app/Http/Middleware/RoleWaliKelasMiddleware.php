<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleWaliKelasMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->isWaliKelas() && !$user->isAdmin()) {
            if ($user->isKepalaSekolah() || $user->isWakaKesiswaan()) {
                return redirect('/kepala-sekolah/dashboard')
                    ->with('error', 'Akses Ditolak: Halaman khusus Wali Kelas.');
            }

            if ($user->isGuruBk()) {
                return redirect('/bk/dashboard')
                    ->with('error', 'Akses Ditolak: Halaman khusus Wali Kelas.');
            }

            if ($user->isGuru()) {
                return redirect('/guru-piket/dashboard')
                    ->with('error', 'Akses Ditolak: Akun Anda belum terdaftar sebagai Wali Kelas.');
            }

            return redirect('/login')
                ->with('error', 'Akses tidak diizinkan.');
        }

        return $next($request);
    }
}
