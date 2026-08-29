<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleKepalaSekolahMiddleware
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

        if (!$user->isKepalaSekolah() && !$user->isWakaKesiswaan() && !$user->isAdmin()) {
            if ($user->isGuruBk()) {
                return redirect('/bk/dashboard')
                    ->with('error', 'Akses Ditolak: Halaman khusus Kepala Sekolah & Manajemen Kesiswaan.');
            }

            if ($user->isWaliKelas()) {
                return redirect('/wali-kelas/dashboard')
                    ->with('error', 'Akses Ditolak: Halaman khusus Kepala Sekolah & Manajemen Kesiswaan.');
            }

            if ($user->isGuru()) {
                return redirect('/guru-piket/dashboard')
                    ->with('error', 'Akses Ditolak: Halaman khusus Kepala Sekolah & Manajemen Kesiswaan.');
            }

            return redirect('/login')
                ->with('error', 'Akses tidak diizinkan.');
        }

        return $next($request);
    }
}
