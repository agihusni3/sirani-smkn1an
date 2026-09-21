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

        // Data Kalender Pendidikan & RPE
        $kalender = $ta ? \App\Models\AkademikKalender::with('items')
            ->where('tahun_ajaran_id', $ta->id)
            ->where('semester', 1)
            ->first() : null;

        // Agenda khusus terdekat / berjalan dari Kaldik untuk Wakakur
        $todayStr = today()->format('Y-m-d');
        $agendaKaldikTerdekat = null;
        if ($kalender) {
            $specialItems = $kalender->items->where('jenis', '!=', 'efektif');
            // Prioritas: yang mencakup hari ini atau tanggal mulai >= hari ini
            $agendaKaldikTerdekat = $specialItems->first(function ($it) use ($todayStr) {
                return $it->isDateCovered($todayStr);
            }) ?: $specialItems->first(function ($it) use ($todayStr) {
                return $it->tanggal_mulai && $it->tanggal_mulai->format('Y-m-d') >= $todayStr;
            });

            if (!$agendaKaldikTerdekat) {
                $namaBulanIndo = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                ];
                $bulanIni = $namaBulanIndo[(int)today()->month] ?? 'September';
                $agendaKaldikTerdekat = $specialItems->firstWhere('bulan', $bulanIni) ?: $specialItems->first();
            }
        }

        // KPI Indikator Kurikulum & GTK
        $totalMapel = AkademikMataPelajaran::where('is_active', true)->count();
        $totalDistribusi = AkademikDistribusiMengajar::count();
        $totalGuru = \App\Models\Guru::where('status', 'aktif')->count();
        $totalGuruMengajar = AkademikDistribusiMengajar::distinct('guru_id')->count('guru_id');
        $totalRombel = \App\Models\Rombel::where('is_active', true)->count();
        $totalNilai = \App\Models\AkademikNilai::count();

        // Jurnal hari ini
        $jurnalHariIni = AkademikJurnalKbm::whereDate('tanggal', today())->count();

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

        $totalJadwalHariIni = \App\Models\AkademikJadwalPelajaran::where('hari', $hariAktif)
            ->whereNotNull('mata_pelajaran_id')
            ->count();

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
            'ta', 'kalender', 'agendaKaldikTerdekat',
            'totalMapel', 'totalDistribusi', 'totalGuru', 'totalGuruMengajar', 'totalRombel', 'totalNilai',
            'jurnalHariIni', 'totalJadwalHariIni', 'asesmenAktif', 'siswaPklAktif',
            'hariAktif', 'jadwalHariIni', 'piketHariIni', 'daftarHariKbm',
            'jurnalTerbaru', 'asesmenMendatang'
        ));
    }
}
