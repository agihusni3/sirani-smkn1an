<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request with role-based access control.
     * Mendukung multi-role seperti: role:admin,kepala_sekolah,waka_kesiswaan
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Administrator selalu memiliki izin akses ke seluruh rute jika role admin diizinkan atau bypass
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Cek kecocokan peran dengan parameter middleware
        foreach ($roles as $role) {
            $role = trim($role);
            $matched = match($role) {
                'admin'          => $user->isAdmin(),
                'kepala_sekolah' => $user->isKepalaSekolah(),
                'waka_kesiswaan' => $user->isWakaKesiswaan(),
                'waka_kurikulum' => $user->isWakaKurikulum(),
                'guru_bk'        => $user->isGuruBk(),
                'wali_kelas'     => $user->isWaliKelas(),
                'guru_piket'     => $user->isGuruPiket(),
                'staf_tu'        => $user->isStafTu(),
                'guru'           => $user->isGuru(),
                default          => false,
            };

            if ($matched) {
                return $next($request);
            }
        }

        return abort(403, 'Akses Ditolak: Anda tidak memiliki hak akses untuk membuka halaman tersebut.');
    }
}
