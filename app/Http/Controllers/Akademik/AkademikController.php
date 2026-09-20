<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\AkademikAsesmenHasil;
use App\Models\AkademikAsesmenOnline;
use App\Models\AkademikDistribusiMengajar;
use App\Models\AkademikJurnalKbm;
use App\Models\AkademikMataPelajaran;
use App\Models\AkademikPklSiswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AkademikController extends Controller
{
    public function dashboard(Request $request)
    {
        $ta = TahunAjaran::where('is_active', true)->first();
        $user = auth()->user();

        // KPI Cards
        $totalMapel      = AkademikMataPelajaran::where('is_active', true)->count();
        $totalDistribusi = AkademikDistribusiMengajar::count();
        $totalGuru       = \App\Models\Guru::where('status', 'aktif')->count();
        $totalRombel     = \App\Models\Rombel::where('is_active', true)->count();
        $totalNilai      = \App\Models\AkademikNilai::count();

        // Jurnal hari ini
        $jurnalHariIni = AkademikJurnalKbm::whereDate('tanggal', today())->count();

        // Guru belum isi jurnal hari ini
        $distribusiHariIni = AkademikDistribusiMengajar::whereHas('mataPelajaran', fn($q) => $q->where('is_active', true))->count();
        $belumIsiJurnal = max(0, $distribusiHariIni - $jurnalHariIni);

        // Asesmen aktif
        $asesmenAktif = AkademikAsesmenOnline::where('is_active', true)
            ->where(fn($q) => $q->whereNull('ditutup_pada')->orWhere('ditutup_pada', '>=', now()))
            ->count();

        // Siswa PKL aktif
        $siswaPklAktif = AkademikPklSiswa::where('status', 'aktif')->count();

        // Menentukan Hari Aktif untuk Jadwal Hari Ini
        $namaHariIndo = strtoupper(Carbon::now()->locale('id')->isoFormat('dddd'));
        $daftarHariKbm = ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];
        
        // Bisa di-override dengan filter ?hari=SELASA
        if ($request->filled('hari') && in_array(strtoupper($request->hari), $daftarHariKbm)) {
            $hariAktif = strtoupper($request->hari);
        } elseif (in_array($namaHariIndo, $daftarHariKbm)) {
            $hariAktif = $namaHariIndo;
        } else {
            $hariAktif = 'SENIN'; // default saat hari libur (Minggu)
        }

        // Jadwal KBM Hari Ini (dari matriks roster Wakakur)
        $jadwalHariIni = \App\Models\AkademikJadwalPelajaran::with(['guru', 'mataPelajaran', 'rombel'])
            ->where('hari', $hariAktif)
            ->whereNotNull('mata_pelajaran_id')
            ->orderBy('jam_ke')
            ->orderBy('rombel_id')
            ->limit(12)
            ->get();

        // Petugas Piket Hari Ini
        $piketHariIni = \App\Models\AkademikGuruPiket::with('wakaPiket')
            ->where('hari', $hariAktif)
            ->first();

        // Jurnal terbaru (feed)
        $jurnalTerbaru = AkademikJurnalKbm::with(['distribusi.guru', 'distribusi.mataPelajaran', 'distribusi.rombel'])
            ->latest()->limit(5)->get();

        // Asesmen mendatang (belum dibuka / sedang buka)
        $asesmenMendatang = AkademikAsesmenOnline::with(['distribusi.mataPelajaran', 'distribusi.rombel'])
            ->where('is_active', true)
            ->where(fn($q) => $q->whereNull('ditutup_pada')->orWhere('ditutup_pada', '>=', now()))
            ->latest()->limit(4)->get();

        return view('dcc.akademik.dashboard', compact(
            'ta', 'totalMapel', 'totalDistribusi', 'totalGuru', 'totalRombel', 'totalNilai',
            'jurnalHariIni', 'belumIsiJurnal', 'asesmenAktif', 'siswaPklAktif',
            'hariAktif', 'jadwalHariIni', 'piketHariIni', 'daftarHariKbm',
            'jurnalTerbaru', 'asesmenMendatang'
        ));
    }
}
