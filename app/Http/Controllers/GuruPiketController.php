<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\IzinSiswa;
use App\Models\JadwalHariIni;
use App\Models\JadwalPiket;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class GuruPiketController extends Controller
{
    /**
     * Dashboard Guru Piket — tampilkan rekap kehadiran hari ini
     * beserta tools operasional (presensi manual, catatan izin).
     */
    public function index()
    {
        $today      = Carbon::today()->toDateString();
        $now        = Carbon::now();
        $taAktif    = TahunAjaran::where('is_active', true)->first();
        $jadwal     = JadwalHariIni::getJadwalAktif($today);
        $hariHariIni = JadwalPiket::getHariIndonesia();
        $isLibur    = \App\Models\HariLibur::isLibur($today);
        $liburDetail = \App\Models\HariLibur::getLiburHariIni($today);

        // Guru piket yang bertugas hari ini
        $guruPiketHariIni = JadwalPiket::where('hari', $hariHariIni)
            ->with('guru')
            ->get();

        // Rekap absensi siswa hari ini
        $absensiHariIni = Absensi::with(['siswa', 'siswaRombel.rombel'])
            ->where('pemilik_type', 'siswa')
            ->where('tanggal', $today)
            ->orderBy('jam_masuk', 'desc')
            ->get();

        // Rekap absensi guru hari ini
        $absensiGuruHariIni = Absensi::with('guru')
            ->where('pemilik_type', 'guru')
            ->where('tanggal', $today)
            ->orderBy('jam_masuk', 'desc')
            ->get();

        $totalSiswaAktif = Siswa::where('status', 'aktif')->count();
        $totalGuruAktif  = Guru::where('status', 'aktif')->count();
        $hadirTepat      = $absensiHariIni->where('status', 'hadir')->count();
        $terlambat       = $absensiHariIni->where('status', 'terlambat')->count();
        $hadirTotal      = $hadirTepat + $terlambat;
        $sudahPulang     = $absensiHariIni->whereNotNull('jam_pulang')->count();
        $persenKehadiran = $totalSiswaAktif > 0 ? round(($hadirTotal / $totalSiswaAktif) * 100, 1) : 0;

        // Izin hari ini
        $izinHariIni = IzinSiswa::with(['siswa.siswaRombels' => function ($q) use ($taAktif) {
                if ($taAktif) {
                    $q->where('tahun_ajaran_id', $taAktif->id)->where('status_keanggotaan', 'aktif')->with('rombel');
                }
            }])
            ->where('tanggal', $today)
            ->orderBy('created_at', 'desc')
            ->get();

        $izinCount = $izinHariIni->count();
        $belumAbsen = max(0, $totalSiswaAktif - $absensiHariIni->count());

        // Siswa yang sudah hadir / memiliki record absensi
        $hadirSiswaIds = $absensiHariIni->pluck('pemilik_id')->toArray();
        $izinSiswaIds  = $izinHariIni->pluck('siswa_id')->toArray();

        // Data untuk form presensi manual
        $semuaSiswa = Siswa::where('status', 'aktif')
            ->with([
                'siswaRombels' => function ($q) use ($taAktif) {
                    if ($taAktif) {
                        $q->where('tahun_ajaran_id', $taAktif->id)
                          ->where('status_keanggotaan', 'aktif')
                          ->with('rombel');
                    }
                }
            ])
            ->orderBy('nama')
            ->get();

        // Siswa belum hadir dan belum izin (potensi alpha / bolos)
        $siswaBelumHadirList = $semuaSiswa->filter(function ($s) use ($hadirSiswaIds, $izinSiswaIds) {
            return !in_array($s->id, $hadirSiswaIds) && !in_array($s->id, $izinSiswaIds);
        });

        // Siswa terlambat hari ini
        $siswaTerlambatList = $absensiHariIni->where('status', 'terlambat');

        $semuaGuru = Guru::where('status', 'aktif')->orderBy('nama')->get();

        // Jadwal piket seminggu
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jadwalPiketSeminggu = JadwalPiket::with('guru')->get()->groupBy('hari');

        return view('piket.index', compact(
            'today',
            'now',
            'jadwal',
            'hariHariIni',
            'isLibur',
            'liburDetail',
            'guruPiketHariIni',
            'absensiHariIni',
            'absensiGuruHariIni',
            'totalSiswaAktif',
            'totalGuruAktif',
            'hadirTepat',
            'hadirTotal',
            'terlambat',
            'sudahPulang',
            'persenKehadiran',
            'belumAbsen',
            'izinCount',
            'izinHariIni',
            'semuaSiswa',
            'siswaBelumHadirList',
            'siswaTerlambatList',
            'semuaGuru',
            'hariList',
            'jadwalPiketSeminggu'
        ));
    }

    /**
     * Catat presensi manual dari meja piket (lupa kartu / tidak ada kartu).
     * Mendelegasikan logika ke PresensiManualController::prosesPresensiManual().
     */
    public function storePresensiManual(Request $request)
    {
        $pencatat = auth()->user()?->name ?? 'Guru Piket';
        $result   = PresensiManualController::prosesPresensiManual($request, $pencatat);

        return redirect()
            ->route('piket.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Koreksi presensi siswa atau guru langsung dari Meja Piket.
     */
    public function updateAbsensi(Request $request, $id)
    {
        $absensi = Absensi::findOrFail($id);
        $request->validate([
            'status'     => 'required|in:hadir,terlambat,alpha,sakit,izin,dispen,bolos',
            'jam_masuk'  => 'nullable',
            'jam_pulang' => 'nullable',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $status     = $request->input('status');
        $keterangan = $request->input('keterangan');
        $jamMasuk   = $request->input('jam_masuk');
        $jamPulang  = $request->input('jam_pulang');

        if (in_array($status, ['alpha', 'sakit', 'izin', 'dispen'])) {
            if (empty($jamMasuk)) $jamMasuk = null;
            if (empty($jamPulang)) $jamPulang = null;
        } elseif ($status === 'hadir') {
            if (empty($jamMasuk)) $jamMasuk = $absensi->jam_masuk ?: '07:10:00';
        } elseif ($status === 'terlambat') {
            if (empty($jamMasuk)) $jamMasuk = $absensi->jam_masuk ?: '07:25:00';
        } elseif ($status === 'bolos') {
            if (empty($jamMasuk)) $jamMasuk = $absensi->jam_masuk ?: '07:10:00';
            $jamPulang = null;
        }

        $pencatat = auth()->user()?->name ?? 'Guru Piket';
        $ketFinal = $keterangan ?: "Dikoreksi oleh {$pencatat}";

        $absensi->update([
            'status'       => $status,
            'jam_masuk'    => $jamMasuk,
            'jam_pulang'   => $jamPulang,
            'sumber_absen' => 'koreksi_piket_manual',
            'keterangan'   => $ketFinal,
        ]);

        // Jika siswa dan status perizinan, sinkronkan ke IzinSiswa
        if ($absensi->pemilik_type === 'siswa') {
            $siswaId = $absensi->pemilik_id ?: ($absensi->siswaRombel?->siswa_id);
            if ($siswaId && in_array($status, ['izin', 'sakit', 'dispen'])) {
                IzinSiswa::updateOrCreate(
                    [
                        'siswa_id' => $siswaId,
                        'tanggal'  => $absensi->tanggal,
                    ],
                    [
                        'jenis'          => $status,
                        'status'         => 'disetujui',
                        'keterangan'     => $ketFinal,
                        'disetujui_oleh' => $pencatat,
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Data presensi berhasil dikoreksi oleh Guru Piket.');
    }

    /**
     * Buka atau Tutup Sesi Gerbang Presensi oleh Guru Piket / Admin
     */
    public function toggleSesiGerbang(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $jadwal = JadwalHariIni::getJadwalAktif($today);
        $user = auth()->user();
        $petugasNama = $user->name ?? 'Petugas Piket';

        $targetStatus = $request->input('status');

        if ($targetStatus === 'buka' || ($targetStatus === null && !$jadwal->is_sesi_buka)) {
            $jadwal->bukaSesi($petugasNama);
            $msg = "Sesi Smart Gate BERHASIL DIBUKA oleh {$petugasNama}. Pemindaian Face ID di gerbang kini aktif.";
            \App\Models\AuditLog::catat('buka_sesi_gerbang', 'jadwal_hari_ini', $msg);
        } else {
            $jadwal->tutupSesi($petugasNama);
            $msg = "Sesi Smart Gate BERHASIL DITUTUP oleh {$petugasNama}. Pemindaian Face ID di gerbang dinonaktifkan sementara.";
            \App\Models\AuditLog::catat('tutup_sesi_gerbang', 'jadwal_hari_ini', $msg);
        }

        return redirect()->back()->with('success', $msg);
    }
}
