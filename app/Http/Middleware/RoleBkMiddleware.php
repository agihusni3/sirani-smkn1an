<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleBkMiddleware
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

        // Akses diberikan kepada Guru BK, Admin, Kepala Sekolah, dan Waka Kesiswaan
        if (!$user->isGuruBk() && !$user->isAdmin() && !$user->isKepalaSekolah() && !$user->isWakaKesiswaan()) {
            if ($user->isWaliKelas()) {
                return redirect('/wali-kelas/dashboard')
                    ->with('error', 'Akses Ditolak: Halaman khusus Bimbingan & Konseling (BK).');
            }

            if ($user->isGuru()) {
                return redirect('/guru-piket/dashboard')
                    ->with('error', 'Akses Ditolak: Akun Anda bukan Guru BK.');
            }

            return redirect('/login')
                ->with('error', 'Akses tidak diizinkan.');
        }

        return $next($request);
    }
}
