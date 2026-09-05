<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;

class SituanDashboardController extends Controller
{
    /**
     * Tampilkan Dasbor Khusus Modul SITUAN — SMKN 1 AN.
     * Pusat Administrasi Tata Usaha & Data Pokok Institusi.
     */
    public function index()
    {
        $user = auth()->user();

        // Verifikasi hak akses SITUAN
        if (!$user || !$user->canAccessSituan()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengakses Modul SITUAN (Sistem Informasi Tata Usaha).');
        }

        // 1. Data Ringkasan Peserta Didik (Siswa)
        $totalSiswaAktif = Siswa::where('status', 'aktif')->count();
        $totalSiswaLaki = Siswa::where('status', 'aktif')->where('jenis_kelamin', 'L')->count();
        $totalSiswaPerempuan = Siswa::where('status', 'aktif')->where('jenis_kelamin', 'P')->count();
        $totalSiswaPkl = Siswa::where('status', 'pkl')->count();
        $totalAlumni = Siswa::where('status', 'lulus')->count();

        // 2. Data Ringkasan Pendidik & Tenaga Kependidikan (PTK)
        $totalGuruAktif = Guru::where('status', 'aktif')->count();
        $totalAkunLoginGtk = User::whereNotNull('guru_id')->count();

        // 3. Data Kelembagaan, Rombel & Kurikulum
        $totalRombel = Rombel::count();
        $totalJurusan = Jurusan::count();
        $jurusans = Jurusan::withCount('rombels')->get();
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        // 4. Audit Kelengkapan Data Pokok Siswa Aktif
        $siswaDenganNisn = Siswa::where('status', 'aktif')->whereNotNull('nisn')->where('nisn', '!=', '')->count();
        $siswaDenganNoOrtu = Siswa::where('status', 'aktif')->whereNotNull('no_hp_ortu')->where('no_hp_ortu', '!=', '')->count();
        $siswaDenganRfid = Siswa::where('status', 'aktif')->whereHas('kartuRfid')->count();
        $siswaDenganFoto = Siswa::where('status', 'aktif')->whereNotNull('foto')->where('foto', '!=', '')->count();

        $persenNisn = $totalSiswaAktif > 0 ? round(($siswaDenganNisn / $totalSiswaAktif) * 100, 1) : 0;
        $persenNoOrtu = $totalSiswaAktif > 0 ? round(($siswaDenganNoOrtu / $totalSiswaAktif) * 100, 1) : 0;
        $persenRfid = $totalSiswaAktif > 0 ? round(($siswaDenganRfid / $totalSiswaAktif) * 100, 1) : 0;
        $persenFoto = $totalSiswaAktif > 0 ? round(($siswaDenganFoto / $totalSiswaAktif) * 100, 1) : 0;

        // 5. Data Rombel per Tingkat
        $rombelTingkat10 = Rombel::where('tingkat', 10)->count();
        $rombelTingkat11 = Rombel::where('tingkat', 11)->count();
        $rombelTingkat12 = Rombel::where('tingkat', 12)->count();

        // 6. Log Mutasi & Aktivitas Tata Usaha Terakhir
        $recentAuditLogs = AuditLog::whereIn('modul', ['siswa', 'guru', 'rombel', 'settings', 'auth'])
            ->latest()
            ->take(8)
            ->get();

        return view('situan.index', compact(
            'user',
            'totalSiswaAktif',
            'totalSiswaLaki',
            'totalSiswaPerempuan',
            'totalSiswaPkl',
            'totalAlumni',
            'totalGuruAktif',
            'totalAkunLoginGtk',
            'totalRombel',
            'totalJurusan',
            'jurusans',
            'tahunAjaranAktif',
            'siswaDenganNisn',
            'siswaDenganNoOrtu',
            'siswaDenganRfid',
            'siswaDenganFoto',
            'persenNisn',
            'persenNoOrtu',
            'persenRfid',
            'persenFoto',
            'rombelTingkat10',
            'rombelTingkat11',
            'rombelTingkat12',
            'recentAuditLogs'
        ));
    }
}
