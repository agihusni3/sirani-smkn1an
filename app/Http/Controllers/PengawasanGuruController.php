<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Guru;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PengawasanGuruController extends Controller
{
    /**
     * Halaman Utama Pengawasan Kinerja & Monitoring Keaktifan Guru / Wali Kelas.
     * Khusus untuk Kepala Sekolah dan Manajemen (Admin, Waka).
     */
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $isHariIni = ($tanggal === Carbon::today()->toDateString());

        // 1. Ambil Seluruh Rombel Aktif beserta Wali Kelas
        $rombels = Rombel::with(['waliKelas'])
            ->whereNotNull('wali_kelas_id')
            ->orderBy('tingkat')
            ->orderBy('nama_rombel')
            ->get();

        $rekapWaliKelas = collect();
        $waliKelasUserIds = [];

        foreach ($rombels as $rombel) {
            $guru = $rombel->waliKelas;
            if (!$guru) continue;

            // Cari Akun User dari Guru ini
            $user = User::where('guru_id', $guru->id)->first();
            if ($user) {
                $waliKelasUserIds[] = $user->id;
            }

            // Hitung Kehadiran Siswa di Kelas Binaan pada tanggal terpilih
            $siswaIds = $rombel->siswaRombels()
                ->where('status_keanggotaan', 'aktif')
                ->pluck('siswa_id')
                ->toArray();

            $totalSiswaKelas = count($siswaIds);

            $absensiKelas = Absensi::where('pemilik_type', 'siswa')
                ->where('tanggal', $tanggal)
                ->whereIn('pemilik_id', $siswaIds)
                ->get();

            $hadirCount     = $absensiKelas->where('status', 'hadir')->count();
            $terlambatCount = $absensiKelas->where('status', 'terlambat')->count();
            $izinCount      = $absensiKelas->whereIn('status', ['sakit', 'izin', 'dispen'])->count();
            $alphaCount     = max(0, $totalSiswaKelas - ($hadirCount + $terlambatCount + $izinCount));
            $persenHadir    = $totalSiswaKelas > 0 ? round((($hadirCount + $terlambatCount) / $totalSiswaKelas) * 100, 1) : 0;

            // Cek Jejak Aktivitas Wali Kelas pada tanggal terpilih
            $logAktivitasHariIni = null;
            $totalAktivitasHariIni = 0;
            $loginTerakhir = null;

            if ($user) {
                $logHariIniQuery = AuditLog::where('user_id', $user->id)
                    ->whereDate('created_at', $tanggal)
                    ->orderBy('created_at', 'desc');

                $totalAktivitasHariIni = (clone $logHariIniQuery)->count();
                $logAktivitasHariIni   = (clone $logHariIniQuery)->first();

                // Cari waktu login terakhir (kapanpun)
                $loginTerakhirRecord = AuditLog::where('user_id', $user->id)
                    ->where('modul', 'auth')
                    ->where('aksi', 'login')
                    ->orderBy('created_at', 'desc')
                    ->first();

                $loginTerakhir = $loginTerakhirRecord ? $loginTerakhirRecord->created_at : null;
            }

            // Tentukan Status Keaktifan Monitoring Wali Kelas
            $isAktifHariIni = ($totalAktivitasHariIni > 0);
            $statusBadge = 'belum';
            $statusLabel = 'Belum Login';
            $statusDesc  = 'Belum mengakses SIRANI pada tanggal ini';

            if ($isAktifHariIni) {
                $statusBadge = 'aktif';
                $statusLabel = 'Aktif Memantau';
                $statusDesc  = "Telah melakukan {$totalAktivitasHariIni} aktivitas di sistem hari ini";
            } elseif ($alphaCount > 0) {
                $statusBadge = 'bahaya';
                $statusLabel = 'Belum Memantau';
                $statusDesc  = "Terdapat {$alphaCount} siswa belum hadir/alpha namun belum dipantau wali kelas";
            } else {
                $statusBadge = 'belum';
                $statusLabel = 'Belum Login';
                $statusDesc  = 'Seluruh siswa kelas terpantau hadir/izin, wali kelas belum login hari ini';
            }

            // Nomor HP WhatsApp Wali Kelas Bersih
            $hpClean = preg_replace('/[^0-9]/', '', $guru->no_hp ?? '');
            if (str_starts_with($hpClean, '0')) {
                $hpClean = '62' . substr($hpClean, 1);
            }

            // Format Pesan WhatsApp dari Kepala Sekolah ke Wali Kelas
            $tglFormatted = Carbon::parse($tanggal)->translatedFormat('l, d F Y');
            $pesanWa = "Assalamu'alaikum Wr. Wb. Yth. Bapak/Ibu {$guru->nama}.\n\n"
                     . "Berdasarkan pantauan sistem SIRANI hari ini ({$tglFormatted}), di kelas binaan Anda (*{$rombel->nama_rombel}*) tercatat:\n"
                     . "• Hadir: {$hadirCount} siswa\n"
                     . "• Terlambat: {$terlambatCount} siswa\n"
                     . "• Izin/Sakit: {$izinCount} siswa\n"
                     . "• Belum Hadir / Alpha: *{$alphaCount} siswa*\n\n"
                     . ($alphaCount > 0
                         ? "Mohon kesediaannya untuk segera login ke sistem SIRANI guna memantau dan melakukan tindak lanjut/koordinasi dengan orang tua siswa bersangkutan.\n\n"
                         : "Terima kasih atas pemantauan dan kedisiplinan Bapak/Ibu dalam mengawal kehadiran siswa binaan.\n\n")
                     . "Terima kasih.\nWassalamu'alaikum Wr. Wb.\n— *Kepala SMKN 1 Air Naningan*";

            $waLink = !empty($hpClean) ? 'https://wa.me/' . $hpClean . '?text=' . urlencode($pesanWa) : null;

            $rekapWaliKelas->push([
                'rombel_id'         => $rombel->id,
                'nama_rombel'       => $rombel->nama_rombel,
                'tingkat'           => $rombel->tingkat,
                'guru_id'           => $guru->id,
                'nama_guru'         => $guru->nama,
                'nip'               => $guru->nip ?: 'Non-NIP',
                'foto'              => $guru->foto_url ?? '/img/user-default.png',
                'user_id'           => $user?->id,
                'total_siswa'       => $totalSiswaKelas,
                'hadir'             => $hadirCount,
                'terlambat'         => $terlambatCount,
                'izin'              => $izinCount,
                'alpha'             => $alphaCount,
                'persen_hadir'      => $persenHadir,
                'is_aktif_hari_ini' => $isAktifHariIni,
                'status_badge'      => $statusBadge,
                'status_label'      => $statusLabel,
                'status_desc'       => $statusDesc,
                'total_aktivitas'   => $totalAktivitasHariIni,
                'aktivitas_terakhir'=> $logAktivitasHariIni ? $logAktivitasHariIni->created_at->format('H:i') : null,
                'login_terakhir'    => $loginTerakhir ? $loginTerakhir->diffForHumans() : 'Belum pernah login',
                'no_hp'             => $guru->no_hp,
                'hp_clean'          => $hpClean,
                'wa_link'           => $waLink,
            ]);
        }

        // 2. Statistik Eksekutif untuk Kepala Sekolah
        $totalWaliKelas       = $rekapWaliKelas->count();
        $waliKelasAktif       = $rekapWaliKelas->where('is_aktif_hari_ini', true)->count();
        $waliKelasBelumAktif  = $totalWaliKelas - $waliKelasAktif;
        $totalSiswaSekolah    = $rekapWaliKelas->sum('total_siswa');
        $totalHadirSekolah    = $rekapWaliKelas->sum('hadir') + $rekapWaliKelas->sum('terlambat');
        $totalAlphaSekolah    = $rekapWaliKelas->sum('alpha');
        $persenSekolah        = $totalSiswaSekolah > 0 ? round(($totalHadirSekolah / $totalSiswaSekolah) * 100, 1) : 0;

        // 3. Kronologi Log Aktivitas Seluruh Guru Hari Ini (Audit Trail Terkini)
        $guruUserIds = User::whereNotNull('guru_id')->pluck('id')->toArray();
        $aktivitasGuruList = AuditLog::with(['user.guru'])
            ->whereIn('user_id', $guruUserIds)
            ->whereDate('created_at', $tanggal)
            ->orderBy('created_at', 'desc')
            ->take(60)
            ->get();

        return view('sirani.pengawasan.index', compact(
            'tanggal',
            'isHariIni',
            'rekapWaliKelas',
            'totalWaliKelas',
            'waliKelasAktif',
            'waliKelasBelumAktif',
            'totalSiswaSekolah',
            'totalHadirSekolah',
            'totalAlphaSekolah',
            'persenSekolah',
            'aktivitasGuruList'
        ));
    }

    /**
     * Detail Riwayat Log Aktivitas untuk 1 Guru (AJAX Modal).
     */
    public function detailAktivitas(int $guruId, Request $request): JsonResponse
    {
        $guru = Guru::findOrFail($guruId);
        $user = User::where('guru_id', $guru->id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => "Guru {$guru->nama} belum memiliki akun login terdaftar.",
                'logs'    => [],
            ]);
        }

        $logs = AuditLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get()
            ->map(function ($log) {
                return [
                    'id'         => $log->id,
                    'modul'      => strtoupper($log->modul),
                    'aksi'       => strtoupper($log->aksi),
                    'deskripsi'  => $log->deskripsi,
                    'waktu'      => $log->created_at->format('H:i:s'),
                    'tanggal'    => $log->created_at->format('d/m/Y'),
                    'relatif'    => $log->created_at->diffForHumans(),
                    'ip_address' => $log->ip_address,
                ];
            });

        return response()->json([
            'success'   => true,
            'guru'      => [
                'nama'    => $guru->nama,
                'nip'     => $guru->nip ?: '-',
                'jabatan' => $guru->jabatan ?: 'Guru',
                'foto'    => $guru->foto_url ?? '/img/user-default.png',
            ],
            'total_log' => count($logs),
            'logs'      => $logs,
        ]);
    }
}
