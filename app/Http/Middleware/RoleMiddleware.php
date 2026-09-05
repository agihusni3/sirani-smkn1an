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

        // Administrator selalu memiliki izin akses ke seluruh rute
        if ($user->hasAvailableRole('admin')) {
            return $next($request);
        }

        // 1. Cek kecocokan dengan peran aktif saat ini
        foreach ($roles as $role) {
            $role = trim($role);
            $matched = match($role) {
                'admin'          => $user->isAdmin(),
                'kepala_sekolah' => $user->isKepalaSekolah(),
                'waka_kesiswaan' => $user->isWakaKesiswaan(),
                'waka_kurikulum' => $user->isWakaKurikulum(),
                'waka_sarpras'   => $user->isWakaSarpras(),
                'waka_hubin'     => $user->isWakaHubin(),
                'kaprog'         => $user->isKaprog(),
                'kepala_bengkel' => $user->isKepalaBengkel(),
                'pustakawan'     => $user->isPustakawan(),
                'guru_bk'        => $user->isGuruBk(),
                'wali_kelas'     => $user->isWaliKelas(),
                'guru_piket'     => $user->isGuruPiket(),
                'staf_tu'        => $user->isStafTu(),
                'guru'           => $user->isGuru(),
                'humas'          => $user->isHumas(),
                'panitia_ppdb'   => $user->isPanitiaPpdb(),
                default          => false,
            };

            if ($matched) {
                return $next($request);
            }
        }

        // 2. Jika peran aktif tidak cocok, periksa apakah pengguna memiliki peran sah tersebut di daftar multi-role
        foreach ($roles as $role) {
            $role = trim($role);
            if ($user->hasAvailableRole($role)) {
                return $next($request);
            }
        }

        return abort(403, 'Akses Ditolak: Anda tidak memiliki hak akses untuk membuka halaman tersebut.');
    }
}
