<?php

namespace App\Http\Controllers\Sirani;

use App\Http\Controllers\Controller;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\IzinGuru;
use App\Models\IzinSiswa;
use App\Models\JadwalHariIni;
use App\Models\JadwalPiket;
use App\Models\KasusDisiplin;
use App\Models\NotifikasiOrtu;
use App\Models\PengaturanNotifikasi;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use App\Services\WhatsAppNotificationService;
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

        $modeUjian = \App\Models\ModeUjian::getModeAktif($today);

        // Jika Mode Ujian aktif dan menonaktifkan piket reguler, tampilkan Panitia Sumatif sebagai petugas meja
        if ($modeUjian && $modeUjian->nonaktifkan_piket_reguler) {
            $panitiaGurus = $modeUjian->daftar_panitia;
            $guruPiketHariIni = $panitiaGurus->map(function ($g) use ($today) {
                $obj = new \stdClass();
                $obj->id = 'panitia_' . $g->id;
                $obj->guru = $g;
                $obj->guru_id = $g->id;
                $obj->keterangan = 'Panitia Pelaksana Sumatif';
                $obj->status_piket = JadwalPiket::getStatusKehadiranPiket($g->id, $today);
                return $obj;
            });
        } else {
            // Guru piket yang bertugas hari ini beserta status pelaksanaan tugas
            $guruPiketHariIni = JadwalPiket::where('hari', $hariHariIni)
                ->with('guru')
                ->get()
                ->each(function ($gp) use ($today) {
                    $gp->status_piket = JadwalPiket::getStatusKehadiranPiket($gp->guru_id, $today);
                });
        }

        // Rekap absensi siswa hari ini
        $absensiHariIni = Absensi::with(['siswa', 'siswaRombel.rombel'])
            ->where('pemilik_type', 'siswa')
            ->where('tanggal', $today)
            ->whereHas('siswa', fn($q) => $q->where('status', 'aktif'))
            ->orderBy('jam_masuk', 'desc')
            ->get();

        // Rekap absensi guru hari ini
        $absensiGuruHariIni = Absensi::with('guru')
            ->where('pemilik_type', 'guru')
            ->where('tanggal', $today)
            ->whereHas('guru', fn($q) => $q->where('status', 'aktif'))
            ->orderBy('jam_masuk', 'desc')
            ->get();

        $totalSiswaAktif = Siswa::where('status', 'aktif')->count();
        $totalGuruAktif  = Guru::where('status', 'aktif')->count();
        $hadirTepat      = $absensiHariIni->where('status', 'hadir')->count();
        $terlambat       = $absensiHariIni->where('status', 'terlambat')->count();
        $hadirTotal      = $hadirTepat + $terlambat;
        $sudahPulang     = $absensiHariIni->whereNotNull('jam_pulang')->count();
        $persenKehadiran = ($totalSiswaAktif > 0 && !$isLibur) ? round(($hadirTotal / $totalSiswaAktif) * 100, 1) : ($isLibur && $hadirTotal > 0 ? round(($hadirTotal / $totalSiswaAktif) * 100, 1) : 0);

        // Izin hari ini
        $izinHariIni = IzinSiswa::with(['siswa.siswaRombels' => function ($q) use ($taAktif) {
                if ($taAktif) {
                    $q->where('tahun_ajaran_id', $taAktif->id)->where('status_keanggotaan', 'aktif')->with('rombel');
                }
            }])
            ->where('tanggal', $today)
            ->whereHas('siswa', fn($q) => $q->where('status', 'aktif'))
            ->orderBy('created_at', 'desc')
            ->get();

        // Siswa yang sudah hadir / memiliki record absensi dan izin
        $hadirSiswaIds = $absensiHariIni->pluck('pemilik_id')->toArray();
        $izinSiswaIds  = array_values(array_unique(array_merge(
            $izinHariIni->pluck('siswa_id')->toArray(),
            $absensiHariIni->whereIn('status', ['sakit', 'izin', 'dispen', 'dispensasi', 'cuti', 'dinas_luar'])->pluck('pemilik_id')->toArray()
        )));
        $izinCount = count($izinSiswaIds);
        $belumAbsen = $isLibur ? 0 : max(0, $totalSiswaAktif - $absensiHariIni->count());

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
        // Pada hari libur atau akhir pekan, siswa bebas presensi sehingga list belum hadir dikosongkan
        if ($isLibur) {
            $siswaBelumHadirList = collect();
        } else {
            $siswaBelumHadirList = $semuaSiswa->filter(function ($s) use ($hadirSiswaIds, $izinSiswaIds) {
                return !in_array($s->id, $hadirSiswaIds) && !in_array($s->id, $izinSiswaIds);
            });
        }

        // Siswa terlambat hari ini
        $siswaTerlambatList = $absensiHariIni->where('status', 'terlambat');

        $semuaGuru = Guru::where('status', 'aktif')->orderBy('nama')->get();

        // ── REKAP STATISTIK KEHADIRAN DEWAN GURU & PEGAWAI HARI INI ──
        $guruHadirTepat      = $absensiGuruHariIni->where('status', 'hadir')->count();
        $guruTerlambat       = $absensiGuruHariIni->where('status', 'terlambat')->count();
        $guruIzinSakit       = $absensiGuruHariIni->whereIn('status', ['izin', 'sakit', 'cuti', 'dispen'])->count();
        $guruHadirTotal      = $guruHadirTepat + $guruTerlambat;
        $guruSudahPulang     = $absensiGuruHariIni->whereNotNull('jam_pulang')->count();
        $guruBelumHadirCount = $isLibur ? 0 : max(0, $totalGuruAktif - $absensiGuruHariIni->count());
        $guruPersenKehadiran = ($totalGuruAktif > 0 && !$isLibur) ? round(($guruHadirTotal / $totalGuruAktif) * 100, 1) : ($isLibur && $guruHadirTotal > 0 ? round(($guruHadirTotal / $totalGuruAktif) * 100, 1) : 0);

        $hadirGuruIds = $absensiGuruHariIni->pluck('pemilik_id')->toArray();
        if ($isLibur) {
            $guruBelumHadirList = collect();
        } else {
            $guruBelumHadirList = $semuaGuru->filter(function ($g) use ($hadirGuruIds) {
                return !in_array($g->id, $hadirGuruIds);
            });
        }

        // Jadwal piket seminggu
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jadwalPiketSeminggu = JadwalPiket::with('guru')->get()->groupBy('hari');

        // Otorisasi: hanya guru piket hari ini / Panitia Sumatif (atau admin) yang berhak koreksi
        $currentUser = auth()->user();
        $canKoreksi = $currentUser && (
            $currentUser->isAdmin() || 
            $currentUser->isPiketHariIni()
        );

        // Daftar Siswa Ujian Susulan (Siswa izin/sakit selama rentang pekan ujian Sumatif)
        $siswaSusulan = collect();
        if ($modeUjian) {
            $siswaSusulan = IzinSiswa::with(['siswa.siswaRombels' => function ($q) use ($taAktif) {
                    if ($taAktif) {
                        $q->where('tahun_ajaran_id', $taAktif->id)->where('status_keanggotaan', 'aktif')->with('rombel');
                    }
                }])
                ->whereBetween('tanggal', [$modeUjian->tanggal_mulai, $modeUjian->tanggal_selesai])
                ->whereHas('siswa', fn($q) => $q->where('status', 'aktif'))
                ->orderBy('tanggal', 'desc')
                ->get();
        }

        // ── REKAP SISWA BELUM SCAN PULANG ──
        // Siswa yang sudah absen masuk pagi tapi jam_pulang masih NULL
        $siswaBelumScanPulang = $absensiHariIni
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_pulang')
            ->whereIn('status', ['hadir', 'terlambat', 'bolos'])
            ->count();

        // Flag: apakah sudah melewati jam tutup gerbang (17:00:00)
        $jamTutupGerbang   = $jadwal->jam_tutup_gerbang ?? '17:00:00';
        $sudahLewatJamTutup = $now->format('H:i:s') >= $jamTutupGerbang;


        return view('sirani.piket.index', compact(
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
            'guruHadirTepat',
            'guruTerlambat',
            'guruIzinSakit',
            'guruHadirTotal',
            'guruSudahPulang',
            'guruBelumHadirCount',
            'guruPersenKehadiran',
            'guruBelumHadirList',
            'hariList',
            'jadwalPiketSeminggu',
            'canKoreksi',
            'siswaBelumScanPulang',
            'sudahLewatJamTutup',
            'modeUjian',
            'siswaSusulan'
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
        $user    = auth()->user();
        $today   = Carbon::today()->toDateString();

        // 1. Hak Akses: Guru Piket hari ini, Panitia STS, atau Admin
        $isAuthorized = $user && (
            $user->isAdmin() || 
            $user->isPiketHariIni()
        );

        if (!$isAuthorized) {
            return redirect()->back()->with('error', 'Akses ditolak: Hanya Guru Piket yang terjadwal bertugas pada hari ini atau Administrator yang berwenang melakukan koreksi presensi.');
        }

        // 2. Batasan Waktu: Guru Piket hanya untuk hari ini (Admin memiliki wewenang penuh atas rekaman lampau)
        if (!$user->isAdmin() && $absensi->tanggal !== $today) {
            return redirect()->back()->with('error', 'Koreksi presensi oleh Guru Piket hanya diizinkan untuk data absensi pada hari ini (' . Carbon::today()->translatedFormat('d F Y') . '). Catatan hari sebelumnya hanya dapat diubah oleh Administrator.');
        }

        $request->validate([
            'status'     => 'required|in:hadir,terlambat,alpha,sakit,izin,dispen,bolos,titip_kartu,reset',
            'jam_masuk'  => 'nullable',
            'jam_pulang' => 'nullable',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $status     = $request->input('status');
        $keterangan = $request->input('keterangan');
        $jamMasuk   = $request->input('jam_masuk');
        $jamPulang  = $request->input('jam_pulang');
        $pencatat   = auth()->user()?->name ?? 'Guru Piket';

        $isInterfensiTitipKartu = false;

        if ($status === 'titip_kartu') {
            $status = 'alpha';
            $jamMasuk = null;
            $jamPulang = null;
            $isInterfensiTitipKartu = true;
            $ketFinal = $keterangan ?: "Dibatalkan oleh Guru Piket — Terindikasi Titip Kartu Presensi ({$pencatat})";
        } elseif (in_array($status, ['alpha', 'reset'])) {
            $status = 'alpha';
            $jamMasuk = null;
            $jamPulang = null;
            $ketFinal = $keterangan ?: "Intervensi Piket: Dikembalikan ke Alpha / Belum Scan ({$pencatat})";
        } elseif (in_array($status, ['sakit', 'izin', 'dispen'])) {
            if (empty($jamMasuk)) $jamMasuk = null;
            if (empty($jamPulang)) $jamPulang = null;
            $ketFinal = $keterangan ?: "Dikoreksi oleh {$pencatat}";
        } elseif ($status === 'hadir') {
            if (empty($jamMasuk)) $jamMasuk = $absensi->jam_masuk ?: '07:10:00';
            $ketFinal = $keterangan ?: "Dikoreksi oleh {$pencatat}";
        } elseif ($status === 'terlambat') {
            if (empty($jamMasuk)) $jamMasuk = $absensi->jam_masuk ?: '07:25:00';
            $ketFinal = $keterangan ?: "Dikoreksi oleh {$pencatat}";
        } elseif ($status === 'bolos') {
            if (empty($jamMasuk)) $jamMasuk = $absensi->jam_masuk ?: '07:10:00';
            $jamPulang = null;
            $ketFinal = $keterangan ?: "Intervensi Piket: Siswa Bolos ({$pencatat})";
        } else {
            $ketFinal = $keterangan ?: "Dikoreksi oleh {$pencatat}";
        }

        $sumberInput = $isInterfensiTitipKartu ? 'interfensi_titip_kartu' : 'koreksi_piket_manual';

        $absensi->update([
            'status'       => $status,
            'jam_masuk'    => $jamMasuk,
            'jam_pulang'   => $jamPulang,
            'sumber_absen' => $sumberInput,
            'keterangan'   => $ketFinal,
        ]);

        // Catat ke AuditLog
        $namaTarget = $absensi->pemilik_type === 'siswa' ? ($absensi->siswa?->nama ?? 'Siswa') : ($absensi->guru?->nama ?? 'Guru');
        \App\Models\AuditLog::catat(
            $isInterfensiTitipKartu ? 'interfensi_piket_titip_kartu' : 'koreksi_presensi_piket',
            'absensi',
            "Guru Piket ({$pencatat}) mengintervensi status presensi {$absensi->pemilik_type} {$namaTarget} menjadi {$status}. Catatan: {$ketFinal}"
        );

        // Jika siswa dan status perizinan, sinkronkan ke IzinSiswa
        $isSiswa = ($absensi->pemilik_type === 'siswa' || empty($absensi->pemilik_type) || !empty($absensi->siswa_rombel_id));
        if ($isSiswa) {
            $siswaId = $absensi->pemilik_id ?: ($absensi->siswaRombel?->siswa_id);
            $siswaObj = $absensi->siswa ?: ($absensi->siswaRombel?->siswa ?: Siswa::find($siswaId));

            if ($siswaObj) {
                $siswaId = $siswaObj->id;
                if (in_array($status, ['izin', 'sakit', 'dispen'])) {
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
                } else {
                    IzinSiswa::where('siswa_id', $siswaId)->where('tanggal', $absensi->tanggal)->delete();
                }

                // Sinkronisasi Buku Kasus Disiplin & Sinkronisasi Notifikasi Presensi WhatsApp
                KasusDisiplin::syncFromPresensi($siswaId);
                \App\Services\NotifikasiDraftService::sinkronkanPresensiSiswa($siswaObj, $absensi->tanggal, $status, $jamMasuk, $ketFinal);

                // Kirim Notifikasi Push Real-Time ke Portal & HP Orang Tua
                try {
                    $nisn = !empty($siswaObj->nisn) ? $siswaObj->nisn : (string)$siswaObj->id;
                    $labelStatus = match($status) {
                        'sakit'     => 'Sakit',
                        'izin'      => 'Izin',
                        'dispen'    => 'Dispensasi',
                        'hadir'     => 'Hadir',
                        'terlambat' => 'Terlambat',
                        'alpha'     => 'Alpha',
                        'bolos'     => 'Bolos',
                        default     => ucfirst($status),
                    };
                    $ikon = match($status) {
                        'sakit'     => '🤒',
                        'izin'      => '📋',
                        'dispen'    => '🎖️',
                        'hadir'     => '✅',
                        'terlambat' => '⏰',
                        'alpha'     => '⚠️',
                        'bolos'     => '🚨',
                        default     => '📢',
                    };

                    // Catat ke riwayat notifikasi database agar selalu tampil di portal orang tua
                    try {
                        NotifikasiOrtu::create([
                            'siswa_id'    => $siswaObj->id,
                            'kategori'    => 'koreksi_presensi',
                            'tanggal'     => $absensi->tanggal,
                            'no_tujuan'   => $siswaObj->no_hp_ortu ?: ($siswaObj->no_hp ?: '-'),
                            'nama_ortu'   => $siswaObj->nama_ortu ?: 'Orang Tua / Wali Murid',
                            'judul'       => "Koreksi Presensi: {$siswaObj->nama} ({$labelStatus})",
                            'pesan'       => "Data kehadiran ananda tanggal " . Carbon::parse($absensi->tanggal)->translatedFormat('d M Y') . " diperbarui menjadi {$labelStatus}. Catatan: {$ketFinal}",
                            'status'      => 'terkirim',
                            'dibuat_oleh' => $pencatat,
                            'waktu_kirim' => now(),
                        ]);
                    } catch (\Throwable $eDb) {
                        \Illuminate\Support\Facades\Log::warning("Gagal simpan NotifikasiOrtu koreksi: " . $eDb->getMessage());
                    }

                    \App\Services\PushNotificationService::sendToSiswa(
                        $nisn,
                        "{$ikon} Koreksi Presensi: {$siswaObj->nama} ({$labelStatus})",
                        "Data kehadiran ananda tanggal {$absensi->tanggal} diperbarui menjadi {$labelStatus}. Catatan: {$ketFinal}",
                        '/presensi-siswa/' . urlencode($nisn)
                    );
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("Gagal kirim push notif koreksi: " . $e->getMessage());
                }
            }
        }

        $msgSuccess = $isInterfensiTitipKartu 
            ? 'Intervensi Piket Berhasil: Kehadiran dibatalkan dan dikembalikan ke status ALPHA karena terindikasi titip kartu.'
            : 'Data presensi berhasil dikoreksi oleh Guru Piket.';

        return redirect()->back()->with('success', $msgSuccess);
    }

    /**
     * Buka atau Tutup Sesi Gerbang Presensi oleh Guru Piket / Admin
     */
    public function toggleSesiGerbang(Request $request)
    {
        return redirect()->back()->with('info', 'Smart Gate selalu aktif otomatis mengikuti jadwal operasional sekolah.');
    }


    /**
     * Validasi kehadiran siswa yang belum tap presensi langsung dari tabel tindak lanjut meja piket.
     */
    public function validasiPresensiSiswa(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();

        // 1. Hak Akses: Guru Piket hari ini, Panitia STS, atau Admin
        $isAuthorized = $user && (
            $user->isAdmin() || 
            $user->isPiketHariIni()
        );

        if (!$isAuthorized) {
            return redirect()->back()->with('error', 'Akses ditolak: Hanya Guru Piket yang terjadwal bertugas pada hari ini yang berwenang melakukan validasi presensi.');
        }

        $request->validate([
            'siswa_id'       => 'required|exists:siswas,id',
            'status'         => 'required|in:hadir,terlambat,alpha,sakit,izin,dispen,bolos',
            'jam_masuk'      => 'nullable',
            'keterangan'     => 'nullable|string|max:500',
            'file_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
        ]);

        $siswa = Siswa::findOrFail($request->input('siswa_id'));
        $status = $request->input('status');
        $keterangan = $request->input('keterangan');
        $jamMasuk = $request->input('jam_masuk');
        $pencatat = $user->name ?? 'Guru Piket';

        $filePath = null;
        if ($request->hasFile('file_pendukung')) {
            $filePath = $request->file('file_pendukung')->store('surat_izin/siswa', 'public');
        }

        $siswaRombel = $siswa->siswaRombels->where('status_keanggotaan', 'aktif')->first();
        $siswaRombelId = $siswaRombel?->id;

        if (in_array($status, ['alpha', 'sakit', 'izin', 'dispen'])) {
            $jamMasuk = null;
        } elseif ($status === 'hadir' && empty($jamMasuk)) {
            $jamMasuk = '07:10:00';
        } elseif ($status === 'terlambat' && empty($jamMasuk)) {
            $jamMasuk = Carbon::now()->format('H:i:s');
        } elseif ($status === 'bolos' && empty($jamMasuk)) {
            $jamMasuk = '07:10:00';
        }

        $ketFinal = $keterangan ?: "Validasi Guru Piket ({$pencatat})";

        // Buat atau update catatan absensi hari ini
        Absensi::updateOrCreate(
            [
                'pemilik_type' => 'siswa',
                'pemilik_id'   => $siswa->id,
                'tanggal'      => $today,
            ],
            [
                'siswa_rombel_id' => $siswaRombelId,
                'status'          => $status,
                'jam_masuk'       => $jamMasuk,
                'sumber_absen'    => 'koreksi_piket_manual',
                'keterangan'      => $ketFinal,
            ]
        );

        // Jika status perizinan, sinkronkan ke modul IzinSiswa
        if (in_array($status, ['izin', 'sakit', 'dispen'])) {
            $existing = IzinSiswa::where('siswa_id', $siswa->id)->where('tanggal', $today)->first();
            $finalFile = $filePath ?: ($existing?->file_pendukung);

            IzinSiswa::updateOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'tanggal'  => $today,
                ],
                [
                    'jenis'          => $status,
                    'status'         => 'disetujui',
                    'keterangan'     => $ketFinal,
                    'file_pendukung' => $finalFile,
                    'disetujui_oleh' => $pencatat,
                ]
            );
        } else {
            // Hapus izin jika sebelumnya tercatat izin lalu divalidasi hadir/alpha
            IzinSiswa::where('siswa_id', $siswa->id)->where('tanggal', $today)->delete();
        }

        // Sinkronisasi Buku Kasus Disiplin & Sinkronisasi Notifikasi Presensi WhatsApp
        KasusDisiplin::syncFromPresensi($siswa->id);
        \App\Services\NotifikasiDraftService::sinkronkanPresensiSiswa($siswa, $today, $status, $jamMasuk, $ketFinal);

        // Kirim Notifikasi Push Real-Time ke Portal & HP Orang Tua
        try {
            if (!empty($siswa->nisn)) {
                $labelStatus = match($status) {
                    'sakit'     => 'Sakit',
                    'izin'      => 'Izin',
                    'dispen'    => 'Dispensasi',
                    'hadir'     => 'Hadir',
                    'terlambat' => 'Terlambat',
                    'alpha'     => 'Alpha',
                    'bolos'     => 'Bolos',
                    default     => ucfirst($status),
                };
                $ikon = match($status) {
                    'sakit'     => '🤒',
                    'izin'      => '📋',
                    'dispen'    => '🎖️',
                    'hadir'     => '✅',
                    'terlambat' => '⏰',
                    'alpha'     => '⚠️',
                    'bolos'     => '🚨',
                    default     => '📢',
                };

                // Catat ke riwayat notifikasi database agar selalu tampil di portal orang tua
                try {
                    NotifikasiOrtu::create([
                        'siswa_id'    => $siswa->id,
                        'kategori'    => 'koreksi_presensi',
                        'tanggal'     => $today,
                        'no_tujuan'   => $siswa->no_hp_ortu ?: ($siswa->no_hp ?: '-'),
                        'nama_ortu'   => $siswa->nama_ortu ?: 'Orang Tua / Wali Murid',
                        'judul'       => "Validasi Presensi: {$siswa->nama} ({$labelStatus})",
                        'pesan'       => "Presensi ananda tanggal " . Carbon::parse($today)->translatedFormat('d M Y') . " tercatat sebagai {$labelStatus}. Catatan: {$ketFinal}",
                        'status'      => 'terkirim',
                        'dibuat_oleh' => $pencatat,
                        'waktu_kirim' => now(),
                    ]);
                } catch (\Throwable $eDb) {
                    \Illuminate\Support\Facades\Log::warning("Gagal simpan NotifikasiOrtu validasi: " . $eDb->getMessage());
                }

                \App\Services\PushNotificationService::sendToSiswa(
                    $siswa->nisn,
                    "{$ikon} Validasi Presensi: {$siswa->nama} ({$labelStatus})",
                    "Presensi ananda tanggal {$today} tercatat sebagai {$labelStatus}. Catatan: {$ketFinal}",
                    '/presensi-siswa/' . urlencode($siswa->nisn)
                );
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal kirim push notif validasi: " . $e->getMessage());
        }

        return redirect()->back()->with('success', "Presensi ananda {$siswa->nama} berhasil divalidasi sebagai: " . strtoupper($status));
    }

    /**
     * Kirim pesan konfirmasi/pengingat kehadiran via WhatsApp Gateway resmi sekolah.
     */
    public function kirimWaGateway(Request $request)
    {
        $request->validate([
            'tipe'      => 'required|in:guru_individu,semua_guru,siswa_individu,semua_siswa',
            'target_id' => 'nullable|integer',
            'pesan'     => 'nullable|string|max:1000',
        ]);

        $waService = app(WhatsAppNotificationService::class);
        $todayStr  = Carbon::today()->locale('id')->isoFormat('dddd, D MMMM Y');
        $tipe      = $request->input('tipe');

        if ($tipe === 'guru_individu') {
            $guru = Guru::findOrFail($request->input('target_id'));
            $noHp = $guru->no_hp;
            if (!$noHp) {
                return response()->json(['success' => false, 'message' => "Guru {$guru->nama} belum memiliki nomor WhatsApp terdaftar."]);
            }

            $pesan = $request->input('pesan') ?: "Yth. Bapak/Ibu *{$guru->nama}*,\n\nKami dari Petugas Guru Piket SMKN 1 Air Naningan mengonfirmasi kehadiran Bapak/Ibu pada hari ini ({$todayStr}).\n\nBerdasarkan pantauan sistem presensi, Bapak/Ibu tercatat *Belum Melakukan Pemindaian Presensi Masuk*.\n\nMohon konfirmasi status kehadiran atau jadwal mengajar Anda hari ini. Terima kasih.\n\n_Salam hangat,_\n*Petugas Piket SMKN 1 Air Naningan*";

            $res = $waService->kirimDirect($noHp, $pesan, 'KONFIRMASI PRESENSI GURU');
            return response()->json([
                'success' => $res['success'] ?? false,
                'message' => ($res['success'] ?? false)
                    ? "Pesan WhatsApp Gateway berhasil dikirim ke {$guru->nama} ({$noHp})."
                    : ($res['message'] ?? 'Gagal mengirim pesan via WhatsApp Gateway.'),
                'mode'    => $res['mode'] ?? 'live'
            ]);
        }

        if ($tipe === 'semua_guru') {
            $hariIniIndo = Carbon::today()->locale('id')->isoFormat('dddd');
            $absensiGuruIds = Absensi::where('pemilik_type', 'guru')
                ->where('tanggal', Carbon::today()->toDateString())
                ->pluck('pemilik_id');

            $gurus = Guru::where('status', 'aktif')
                ->whereNotIn('id', $absensiGuruIds)
                ->get()
                ->filter(fn($g) => $g->isWajibHadirHari($hariIniIndo));

            $countSent = 0;
            $countSkipped = 0;

            foreach ($gurus as $guru) {
                if (empty($guru->no_hp)) {
                    $countSkipped++;
                    continue;
                }
                $pesan = "Yth. Bapak/Ibu *{$guru->nama}*,\n\nKami dari Petugas Guru Piket SMKN 1 Air Naningan mengingatkan bahwa pada hari ini ({$todayStr}), Anda tercatat *Belum Melakukan Pemindaian Presensi Masuk* di sekolah.\n\nMohon segera melakukan scan di gerbang atau mengonfirmasi kepada Guru Piket jika berhalangan/tugas dinas luar. Terima kasih.\n\n_Salam hormat,_\n*Petugas Piket SMKN 1 Air Naningan*";
                $res = $waService->kirimDirect($guru->no_hp, $pesan, 'PENGINGAT PRESENSI GURU');
                if (!empty($res['success'])) {
                    $countSent++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Pengingat via WhatsApp Gateway berhasil dikirim ke {$countSent} guru" . ($countSkipped > 0 ? " ({$countSkipped} guru tanpa nomor WA dilewati)." : "."),
            ]);
        }

        if ($tipe === 'siswa_individu') {
            $siswa = Siswa::findOrFail($request->input('target_id'));
            $noHp = $siswa->no_hp_siswa ?: $siswa->no_hp_ortu;
            if (!$noHp) {
                return response()->json(['success' => false, 'message' => "Siswa {$siswa->nama} tidak memiliki kontak WhatsApp tersimpan."]);
            }

            $pesan = $request->input('pesan') ?: "Yth. Bapak/Ibu Orang Tua / Wali dari ananda *{$siswa->nama}*,\n\nKami dari Petugas Guru Piket SMKN 1 Air Naningan menginformasikan bahwa pada hari ini ({$todayStr}), ananda tercatat *Belum Hadir / Belum Melakukan Presensi di Sekolah*.\n\nMohon konfirmasi keberadaan ananda kepada pihak sekolah. Terima kasih.\n\n*Meja Piket SMKN 1 Air Naningan*";

            $res = $waService->kirimDirect($noHp, $pesan, 'KONFIRMASI KEHADIRAN SISWA');
            return response()->json([
                'success' => $res['success'] ?? false,
                'message' => ($res['success'] ?? false)
                    ? "Pesan WhatsApp Gateway berhasil dikirim ke {$siswa->nama} ({$noHp})."
                    : ($res['message'] ?? 'Gagal mengirim pesan via WhatsApp Gateway.'),
                'mode'    => $res['mode'] ?? 'live'
            ]);
        }

        if ($tipe === 'semua_siswa') {
            @set_time_limit(900);
            $absensiSiswaIds = Absensi::where('pemilik_type', 'siswa')
                ->where('tanggal', Carbon::today()->toDateString())
                ->pluck('pemilik_id');

            $siswas = Siswa::where('status', 'aktif')
                ->whereNotIn('id', $absensiSiswaIds)
                ->get();

            $countSent = 0;
            $countSkipped = 0;

            foreach ($siswas as $siswa) {
                $noHp = $siswa->no_hp_siswa ?: $siswa->no_hp_ortu;
                if (empty($noHp)) {
                    $countSkipped++;
                    continue;
                }

                if ($countSent > 0) {
                    sleep(rand(5, 7));
                }

                $pesan = "Yth. Bapak/Ibu Wali dari ananda *{$siswa->nama}*,\n\nKami dari Petugas Piket SMKN 1 Air Naningan menginformasikan bahwa pada hari ini ({$todayStr}), ananda tercatat *Belum Melakukan Tap Presensi Masuk* di sekolah.\n\nMohon konfirmasi jika ananda sedang sakit atau izin. Terima kasih.\n\n*Meja Piket SMKN 1 Air Naningan*";
                $res = $waService->kirimDirect($noHp, $pesan, 'PENGINGAT KEHADIRAN SISWA');
                if (!empty($res['success'])) {
                    $countSent++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Pengingat via WhatsApp Gateway berhasil dikirim ke {$countSent} wali murid" . ($countSkipped > 0 ? " ({$countSkipped} siswa tanpa kontak WA dilewati)." : "."),
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Tipe pesan tidak dikenali.']);
    }

    /**
     * Catat status cepat presensi Guru langsung dari Meja Piket (Hadir Manual, Izin, Sakit, Dinas Luar).
     */
    public function storeStatusGuruCepat(Request $request)
    {
        $request->validate([
            'guru_id'    => 'required|exists:gurus,id',
            'status'     => 'required|in:hadir,terlambat,izin,sakit,dispen,alpha',
            'keterangan' => 'nullable|string|max:255',
            'jam_masuk'  => 'nullable',
        ]);

        $guru       = Guru::findOrFail($request->input('guru_id'));
        $status     = $request->input('status');
        $keterangan = $request->input('keterangan');
        $today      = Carbon::today()->toDateString();
        $pencatat   = auth()->user()?->name ?? 'Guru Piket';
        $nowTime    = Carbon::now()->toTimeString();
        $jamMasuk   = $request->input('jam_masuk') ? ($request->input('jam_masuk') . ':00') : $nowTime;

        if (in_array($status, ['izin', 'sakit', 'dispen', 'alpha'])) {
            $jamMasuk = null;
        }

        $ketFinal = $keterangan ?: match($status) {
            'hadir'     => 'Hadir dicatat manual oleh Petugas Piket (' . $pencatat . ')',
            'terlambat' => 'Terlambat dicatat manual oleh Petugas Piket (' . $pencatat . ')',
            'izin'      => 'Izin dikonfirmasi Petugas Piket (' . $pencatat . ')',
            'sakit'     => 'Sakit dikonfirmasi Petugas Piket (' . $pencatat . ')',
            'dispen'    => 'Tugas Dinas Luar / Dispensasi (' . $pencatat . ')',
            'alpha'     => 'Alpha (Tanpa Keterangan)',
        };

        Absensi::updateOrCreate(
            [
                'pemilik_type' => 'guru',
                'pemilik_id'   => $guru->id,
                'tanggal'      => $today,
            ],
            [
                'jam_masuk'    => $jamMasuk,
                'status'       => $status,
                'sumber_absen' => 'manual_piket',
                'keterangan'   => $ketFinal,
            ]
        );

        // Sinkronkan ke IzinGuru jika status perizinan
        if (in_array($status, ['izin', 'sakit', 'dispen'])) {
            IzinGuru::updateOrCreate(
                [
                    'guru_id' => $guru->id,
                    'tanggal' => $today,
                ],
                [
                    'jenis'          => $status,
                    'status'         => 'disetujui',
                    'keterangan'     => $ketFinal,
                    'disetujui_oleh' => $pencatat,
                ]
            );
        } else {
            IzinGuru::where('guru_id', $guru->id)->where('tanggal', $today)->delete();
        }

        return redirect()->back()->with('success', "Status presensi {$guru->nama} berhasil disimpan sebagai: " . strtoupper($status));
    }

    /**
     * Trigger manual: Kunci status Alpha sekarang dari dashboard Piket.
     */
    public function kunciAlphaSekarang(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();

        $isAuthorized = $user && (
            $user->isAdmin() ||
            $user->isPiketHariIni()
        );
        if (!$isAuthorized) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        if (\App\Models\HariLibur::isLibur($today)) {
            return redirect()->back()->with('error', 'Hari ini adalah hari libur sekolah. Penguncian status Alpha dilewati.');
        }

        \Artisan::call('piket:kunci-alpha', ['tanggal' => $today]);
        $output = trim(\Artisan::output());

        \App\Models\AuditLog::catat('kunci_alpha_manual', 'piket', "Kunci Alpha dipicu manual oleh {$user->name}: {$output}");
        return redirect()->back()->with('success', "✅ Kunci Alpha berhasil dijalankan. {$output}");
    }

    /**
     * Trigger manual: Kirim WA pengingat massal ke semua yang belum hadir.
     */
    public function flaggingWaMassal(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();

        $isAuthorized = $user && (
            $user->isAdmin() ||
            $user->isPiketHariIni()
        );
        if (!$isAuthorized) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        if (\App\Models\HariLibur::isLibur($today)) {
            return redirect()->back()->with('error', 'Hari ini adalah hari libur sekolah. Pengiriman WA pengingat dilewati.');
        }

        \Artisan::call('piket:flagging-belum-hadir', ['tanggal' => $today]);
        $output = trim(\Artisan::output());

        \App\Models\AuditLog::catat('flagging_wa_manual', 'piket', "WA Massal dipicu manual oleh {$user->name}: {$output}");
        return redirect()->back()->with('success', "📱 WA Pengingat berhasil dikirim. {$output}");
    }
}

