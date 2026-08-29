<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuruPiketMiddleware
{
    /**
     * Hanya izinkan akses jika sesi guru piket aktif.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('guru_piket_id')) {
            if (auth()->check()) {
                if (auth()->user()->isAdmin()) {
                    session([
                        'guru_piket_id'      => 0,
                        'guru_piket_nama'    => 'Administrator',
                        'guru_piket_jabatan' => 'Super Admin',
                    ]);
                    return $next($request);
                }

                if (auth()->user()->guru_id) {
                    $guru = auth()->user()->guru;
                    if ($guru) {
                        session([
                            'guru_piket_id'      => $guru->id,
                            'guru_piket_nama'    => $guru->nama,
                            'guru_piket_jabatan' => $guru->jabatan,
                        ]);
                        return $next($request);
                    }
                }
            }
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}
