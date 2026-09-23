<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\HariLibur;
use App\Models\JadwalHariIni;
use App\Models\KartuRfid;
use App\Models\NotifikasiOrtu;
use App\Models\PengaturanSekolah;
use App\Models\Siswa;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class RfidScanService
{
    /**
     * Memproses absensi untuk Siswa atau Guru berdasarkan pemindaian kartu RFID fisik.
     *
     * @param string $uid Kode UID kartu RFID
     * @param string $device Identifier perangkat / kios
     * @return array Data respons hasil absensi
     * @throws Exception
     */
    public function scanRfid(string $uid, string $device = 'kios_rfid'): array
    {
        $res = $this->executeScanRfid($uid, $device);

        // Jika pemindaian gagal, ditolak, atau di luar ketentuan operasional, catat ke log pantau gagal hari ini
        if (!$res['success'] || in_array($res['type'] ?? '', ['belum_waktunya_pulang', 'di_luar_jam_operasional', 'jam_tutup_terlewat', 'kartu_tidak_dikenal', 'siswa_nonaktif', 'guru_nonaktif', 'tanpa_jam_masuk', 'verifikasi_piket', 'invalid_input'])) {
            static::recordFailedScan($uid, $res);
        }

        return $res;
    }

    /**
     * Catat log pemindaian yang gagal / ditolak ke Cache hari ini untuk pemantauan real-time gerbang.
     */
    public static function recordFailedScan(string $uid, array $res): void
    {
        try {
            $today = Carbon::today()->toDateString();
            $key = 'rfid_failed_scans_' . $today;
            $failedList = \Illuminate\Support\Facades\Cache::get($key, []);
            if (!is_array($failedList)) $failedList = [];

            $d = $res['data'] ?? null;
            $msg = $res['message'] ?? 'Pemindaian ditolak sistem';
            $jam = now()->format('H:i');
            $entry = [
                'id'        => uniqid('fail_'),
                'time'      => $jam,
                'jam'       => $jam,
                'timestamp' => now()->timestamp,
                'uid'       => strtoupper(trim($uid)),
                'type'      => $res['type'] ?? 'gagal',
                'message'   => $msg,
                'pesan'     => $msg,
                'alasan'    => $msg,
                'nama'      => $d['nama'] ?? null,
                'rombel'    => $d['rombel_atau_jabatan'] ?? ($d['sub'] ?? null),
                'sub'       => $d['sub'] ?? ($d['rombel_atau_jabatan'] ?? null),
                'identitas' => $d['identitas'] ?? null,
                'foto'      => $d['foto'] ?? ($d['foto_url'] ?? '/img/user-default.png'),
            ];

            array_unshift($failedList, $entry);
            $failedList = array_slice($failedList, 0, 80); // simpan maks 80 kegagalan terakhir
            \Illuminate\Support\Facades\Cache::put($key, $failedList, now()->endOfDay());
        } catch (\Throwable $e) {
            // Abaikan kesalahan cache
        }
    }

    /**
     * Dapatkan daftar pemindaian yang gagal / ditolak hari ini.
     */
    public static function getFailedScansToday(): array
    {
        try {
            $today = Carbon::today()->toDateString();
            $list = \Illuminate\Support\Facades\Cache::get('rfid_failed_scans_' . $today, []);
            return is_array($list) ? $list : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Eksekusi inti verifikasi pemindaian RFID / Barcode.
     */
    protected function executeScanRfid(string $uid, string $device = 'kios_rfid'): array
    {
        // ── PARAMETER 1: Sanitasi & Normalisasi Input Barcode / RFID ──
        // Bersihkan whitespace, kontrol karakter ASCII, dan prefix/suffix bawaan scanner USB
        $cleanUid = preg_replace('/[[:^print:]]/', '', trim($uid));
        $cleanUid = preg_replace('/^\][a-zA-Z0-9]{2}/', '', $cleanUid); // Strip AIM Code Identifier jika ada
        $cleanUid = strtoupper(trim($cleanUid));

        if (empty($cleanUid)) {
            return [
                'success' => false,
                'status'  => 'error',
                'type'    => 'invalid_input',
                'message' => 'Kode Barcode / RFID kosong atau tidak terbaca.',
                'error'   => 'Input tidak valid.',
                'data'    => null,
            ];
        }

        return DB::transaction(function () use ($cleanUid, $device) {
            $now = Carbon::now();
            $today = $now->toDateString();
            $timeNow = $now->format('H:i:s');

            // ── PARAMETER 2: Identifikasi Pemilik (RFID UID -> NIS Siswa -> NIP Guru) ──
            $kartu = KartuRfid::where('uid', $cleanUid)->where('status', 'aktif')->first();

            $person = null;
            $type = null;
            $id = null;
            $identitas = '';
            $rombelOrJabatan = '';
            $srId = null;

            if ($kartu) {
                $type = $kartu->pemilik_type;
                $id   = $kartu->pemilik_id;
            } else {
                // Fallback A: Cek apakah kode barcode adalah NISN Siswa
                $siswaByNisn = Siswa::where('nisn', $cleanUid)->first();
                if ($siswaByNisn) {
                    $type = 'siswa';
                    $id   = $siswaByNisn->id;
                } else {
                    // Fallback B: Cek apakah kode barcode adalah NIS Siswa
                    $siswaByNis = Siswa::where('nis', $cleanUid)->first();
                    if ($siswaByNis) {
                        $type = 'siswa';
                        $id   = $siswaByNis->id;
                    } elseif (str_starts_with($cleanUid, 'SISWA-')) {
                        // Fallback C: Cek kode barcode SISWA-{id}
                        $sId = (int) substr($cleanUid, 6);
                        $siswaById = Siswa::find($sId);
                        if ($siswaById) {
                            $type = 'siswa';
                            $id   = $siswaById->id;
                        }
                    } else {
                        // Fallback D: Cek apakah kode barcode adalah NIP Guru
                        $guruByNip = Guru::where('nip', $cleanUid)->first();
                        if ($guruByNip) {
                            $type = 'guru';
                            $id   = $guruByNip->id;
                        } elseif (str_starts_with($cleanUid, 'GURU-')) {
                            // Fallback E: Cek kode barcode GURU-{id}
                            $gId = (int) substr($cleanUid, 5);
                            $guruById = Guru::find($gId);
                            if ($guruById) {
                                $type = 'guru';
                                $id   = $guruById->id;
                            }
                        }
                    }
                }
            }

            if (!$type || !$id) {
                return [
                    'success' => false,
                    'status'  => 'error',
                    'type'    => 'kartu_tidak_dikenal',
                    'message' => "Kode Barcode / RFID ({$cleanUid}) belum terdaftar pada sistem.",
                    'error'   => "Kode kartu/barcode belum terdaftar.",
                    'data'    => null,
                ];
            }

            // ── PARAMETER 3: Validasi Status Keaktifan Data Master ──
            if ($type === 'siswa') {
                $person = Siswa::with(['siswaRombels' => function ($q) {
                    $q->where('status_keanggotaan', 'aktif')->with('rombel');
                }])->find($id);

                if (!$person || !in_array($person->status, ['aktif', 'pkl'])) {
                    return [
                        'success' => false,
                        'status'  => 'error',
                        'type'    => 'siswa_nonaktif',
                        'message' => "Siswa {$person?->nama} berstatus non-aktif atau telah lulus/mutasi.",
                        'error'   => "Siswa tidak aktif.",
                        'data'    => null,
                    ];
                }

                $rombelNama = $person->siswaRombels->first()?->rombel?->nama_rombel ?? 'Siswa';
                $identitas = 'NISN: ' . ($person->nisn ?: '-');
                $rombelOrJabatan = $rombelNama;
                $srId = $person->siswaRombels->first()?->id;
            } else {
                $person = Guru::find($id);

                if (!$person || $person->status !== 'aktif') {
                    return [
                        'success' => false,
                        'status'  => 'error',
                        'type'    => 'guru_nonaktif',
                        'message' => "Pendidik/Pegawai {$person?->nama} berstatus non-aktif.",
                        'error'   => "Guru tidak aktif.",
                        'data'    => null,
                    ];
                }

                $identitas = $person->nip ? 'NIP: ' . $person->nip : $person->label_kepegawaian;
                $rombelOrJabatan = $person->jabatan ?? 'Guru / Staf';
                $srId = null;
            }

            // Helper terpusat untuk membungkus profil lengkap dari orang yang di-scan
            $formatProfileData = function ($status, $jam = null, $jamMasuk = null, $jamPulang = null, $statusLabel = null) use ($person, $type, $rombelOrJabatan, $identitas, $timeNow) {
                $jurusan = null;
                $nisn = null;
                $nis = null;
                $nip = null;
                $noHpOrtu = null;

                if ($type === 'siswa') {
                    $sr = $person->siswaRombels->first();
                    $jurusan = $sr?->rombel?->jurusan?->nama_jurusan ?? ($sr?->rombel?->nama_rombel ?? '-');
                    $nisn = $person->nisn;
                    $nis = $person->nis;
                    if (!empty($person->no_hp_ortu)) {
                        $noHpOrtu = substr($person->no_hp_ortu, 0, 4) . '****' . substr($person->no_hp_ortu, -3);
                    }
                } else {
                    $nip = $person->nip;
                    $jurusan = $person->jabatan ?? 'Guru / PTK';
                }

                return [
                    'nama'                => $person->nama,
                    'tipe'                => $type,
                    'tipe_label'          => ($type === 'siswa') ? 'SISWA AKTIF' : 'GURU / PTK',
                    'sub'                 => $rombelOrJabatan,
                    'identitas'           => $identitas,
                    'rombel_atau_jabatan' => $rombelOrJabatan,
                    'kelas'               => $rombelOrJabatan,
                    'jurusan'             => $jurusan,
                    'nisn'                => $nisn,
                    'nis'                 => $nis,
                    'nip'                 => $nip,
                    'no_hp_ortu'          => $noHpOrtu,
                    'foto'                => $person->foto_url,
                    'foto_url'            => $person->foto_url,
                    'status'              => $status,
                    'status_label'        => $statusLabel ?: strtoupper(str_replace('_', ' ', $status)),
                    'jam'                 => $jam ?: $timeNow,
                    'jam_masuk'           => $jamMasuk,
                    'jam_pulang'          => $jamPulang,
                ];
            };

            // ── PARAMETER 4: Validasi Kalender Akademik & Hari Libur ──
            $isLibur = HariLibur::isLibur($today);
            if ($isLibur) {
                $liburModel = HariLibur::getLiburHariIni($today);
                $keteranganLibur = $liburModel ? $liburModel->keterangan : (Carbon::parse($today)->isWeekend() ? 'Hari Libur Akhir Pekan' : 'Hari Libur Sekolah');
                return [
                    'success' => true,
                    'status'  => 'info',
                    'type'    => 'hari_libur',
                    'message' => "Hari ini libur: {$keteranganLibur}. Presensi tidak dicatat.",
                    'data'    => $formatProfileData('libur', $timeNow, null, null, 'HARI LIBUR'),
                ];
            }

            // ── PARAMETER 5: Pengaturan Jam Operasional Sekolah ──
            $jadwal = JadwalHariIni::getJadwalAktif($today);

            $jamMasukMaks    = $jadwal?->jam_masuk_toleransi ?? $jadwal?->jam_masuk_selesai ?? '07:15:00';
            $jamPulangMulai  = $jadwal?->jam_pulang_mulai ?? '14:30:00';
            $jamTutupSekolah = $jadwal?->jam_tutup_gerbang ?? '17:00:00';

            // Catatan khusus guru honor
            $catatanHonor = '';
            if ($type === 'guru' && method_exists($person, 'isHonor') && $person->isHonor() && !$person->isHariMengajar($today)) {
                $catatanHonor = ' (Luar Jadwal Mengajar)';
            }

            // ── PARAMETER 6: Pengecekan Record Absensi Hari Ini (Lock For Update) ──
            $absensi = Absensi::where('pemilik_type', $type)
                ->where('pemilik_id', $id)
                ->where('tanggal', $today)
                ->lockForUpdate()
                ->first();

            // ── PARAMETER 7: Proteksi Status Khusus (Izin / Sakit / Dispensasi / Cuti) ──
            if ($absensi && in_array($absensi->status, ['izin', 'sakit', 'dispensasi', 'cuti'])) {
                $statusLabel = strtoupper($absensi->status);
                return [
                    'success' => true,
                    'status'  => 'info',
                    'type'    => 'status_khusus',
                    'message' => "{$person->nama} tercatat berstatus {$statusLabel} hari ini. Hubungi petugas piket untuk verifikasi kehadiran.",
                    'data'    => $formatProfileData($absensi->status, $absensi->jam_masuk ?: $timeNow, $absensi->jam_masuk, $absensi->jam_pulang, $statusLabel),
                ];
            }

            // ── PARAMETER 7B: Proteksi Status Alpha (Terkunci Alpha Tanpa Jam Masuk) ──
            if ($absensi && in_array($absensi->status, ['alpha', 'alfa']) && empty($absensi->jam_masuk)) {
                return [
                    'success' => false,
                    'status'  => 'warning',
                    'type'    => 'tercatat_alpha',
                    'message' => "Presensi ditolak. {$person->nama} tercatat Alpha (tidak ada rekaman jam masuk pagi). Silakan melapor ke Petugas Piket.",
                    'data'    => $formatProfileData('alpha', $timeNow, null, null, 'ALPHA'),
                ];
            }

            // ── PARAMETER 8: Anti-Double-Scan Cooldown (< 10 detik) ──
            if ($absensi && $absensi->updated_at && $absensi->updated_at->diffInSeconds($now) < 10) {
                return [
                    'success' => true,
                    'status'  => 'info',
                    'type'    => 'cooldown_double_scan',
                    'message' => "Presensi {$person->nama} sudah berhasil tercatat baru saja. Silakan lanjutkan ke antrean berikutnya.",
                    'data'    => $formatProfileData(!empty($absensi->jam_pulang) ? 'selesai' : $absensi->status, !empty($absensi->jam_pulang) ? $absensi->jam_pulang : ($absensi->jam_masuk ?: $timeNow), $absensi->jam_masuk, $absensi->jam_pulang),
                ];
            }

            // ── PARAMETER 9: KONDISI KARTU SUDAH LENGKAP (MASUK & PULANG TERCATAT) ──
            if ($absensi && !empty($absensi->jam_masuk) && !empty($absensi->jam_pulang)) {
                return [
                    'success' => true,
                    'status'  => 'info',
                    'type'    => 'sudah_lengkap',
                    'message' => "Presensi hari ini sudah lengkap (Masuk: {$absensi->jam_masuk} WIB · Pulang: {$absensi->jam_pulang} WIB).",
                    'data'    => $formatProfileData('selesai', $absensi->jam_pulang, $absensi->jam_masuk, $absensi->jam_pulang, 'PRESENSI LENGKAP'),
                ];
            }

            // ── PARAMETER 10: BATASAN WAKTU TUTUP SEKOLAH (> 17:00 WIB) ──
            if ($timeNow >= $jamTutupSekolah) {
                if (!$absensi) {
                    return [
                        'success' => false,
                        'status'  => 'warning',
                        'type'    => 'di_luar_jam_operasional',
                        'message' => "Layanan presensi hari ini telah berakhir (Jam tutup sekolah: " . substr($jamTutupSekolah, 0, 5) . " WIB).",
                        'data'    => [
                            'nama'                => $person->nama,
                            'tipe'                => $type,
                            'sub'                 => $rombelOrJabatan,
                            'identitas'           => $identitas,
                            'rombel_atau_jabatan' => $rombelOrJabatan,
                            'foto'                => $person->foto_url,
                            'foto_url'            => $person->foto_url,
                            'status'              => 'ditolak',
                            'jam'                 => $timeNow,
                        ]
                    ];
                }

                // Jika sudah ada jam masuk tapi belum tap pulang sampai lewat jam tutup
                return [
                    'success' => false,
                    'status'  => 'warning',
                    'type'    => 'jam_tutup_terlewat',
                    'message' => "Batas kepulangan sekolah telah berakhir pukul " . substr($jamTutupSekolah, 0, 5) . " WIB. Anda tercatat masuk pukul {$absensi->jam_masuk} WIB.",
                    'data'    => [
                        'nama'                => $person->nama,
                        'tipe'                => $type,
                        'sub'                 => $rombelOrJabatan,
                        'identitas'           => $identitas,
                        'rombel_atau_jabatan' => $rombelOrJabatan,
                        'foto'                => $person->foto_url,
                        'foto_url'            => $person->foto_url,
                        'status'              => 'terlewat',
                        'jam'                 => $absensi->jam_masuk,
                        'jam_masuk'           => $absensi->jam_masuk,
                        'jam_pulang'          => null,
                    ]
                ];
            }

            // ── SCENARIO A: Perekaman Presensi Masuk (Pertama Kali di Hari Ini) ──
            if (!$absensi) {
                // Jika scan pertama kali terjadi setelah jam 12:00 siang (tidak pernah absen pagi)
                if ($timeNow >= '12:00:00') {
                    if ($timeNow >= $jamPulangMulai) {
                        return [
                            'success' => false,
                            'status'  => 'warning',
                            'type'    => 'tanpa_jam_masuk',
                            'message' => "Presensi pulang ditolak karena tidak ada rekaman presensi masuk pagi ini.",
                            'data'    => [
                                'nama'                => $person->nama,
                                'tipe'                => $type,
                                'sub'                 => $rombelOrJabatan,
                                'identitas'           => $identitas,
                                'rombel_atau_jabatan' => $rombelOrJabatan,
                                'foto'                => $person->foto_url,
                                'foto_url'            => $person->foto_url,
                                'status'              => 'ditolak',
                                'jam'                 => $timeNow,
                            ]
                        ];
                    }

                    // Jam 12:00 - jamPulangMulai:
                    return [
                        'success' => false,
                        'status'  => 'warning',
                        'type'    => 'verifikasi_piket',
                        'message' => "Presensi pagi tidak terekam. Silakan melapor ke Petugas Piket untuk verifikasi kehadiran siang.",
                        'data'    => [
                            'nama'                => $person->nama,
                            'tipe'                => $type,
                            'sub'                 => $rombelOrJabatan,
                            'identitas'           => $identitas,
                            'rombel_atau_jabatan' => $rombelOrJabatan,
                            'foto'                => $person->foto_url,
                            'foto_url'            => $person->foto_url,
                            'status'              => 'ditolak',
                            'jam'                 => $timeNow,
                        ]
                    ];
                }

                $isTerlambat = ($timeNow > $jamMasukMaks);
                $statusKehadiran = $isTerlambat ? 'terlambat' : 'hadir';

                $absensi = Absensi::create([
                    'pemilik_type'    => $type,
                    'pemilik_id'      => $id,
                    'siswa_rombel_id' => $srId,
                    'tanggal'         => $today,
                    'jam_masuk'       => $timeNow,
                    'status'          => $statusKehadiran,
                    'sumber_absen'    => 'rfid',
                    'keterangan'      => $isTerlambat ? "Terlambat (Scan Barcode/RFID {$timeNow}){$catatanHonor}" : "Tepat Waktu (Scan Barcode/RFID {$timeNow}){$catatanHonor}",
                ]);

                // Notifikasi WhatsApp Orang Tua untuk Siswa (Hanya jika kategori aktif & lewat NotifikasiDraftService)
                if ($type === 'siswa') {
                    try {
                        $kategori = $isTerlambat ? 'terlambat' : 'masuk';
                        $settingNotif = \App\Models\PengaturanNotifikasi::getPengaturan();
                        if ($settingNotif->isKategoriAktif($kategori)) {
                            \App\Services\NotifikasiDraftService::buatDraft($person, $kategori, [
                                'tanggal'    => $today,
                                'jam'        => $timeNow,
                                'batas_jam'  => $jamMasukMaks,
                                'keterangan' => $absensi->keterangan,
                            ], 'sistem_rfid');
                        }
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning("Gagal memproses draf notifikasi ortu masuk RFID: " . $e->getMessage());
                    }

                    // Push Notification Real-Time ke Aplikasi HP Orang Tua
                    try {
                        if (!empty($person->nisn)) {
                            $statusTxt = $isTerlambat ? 'TERLAMBAT' : 'Tepat Waktu';
                            $pushTitle = "Presensi Masuk: {$person->nama}";
                            $pushBody = "Ananda telah hadir di sekolah pukul {$timeNow} WIB ({$statusTxt}).";
                            \App\Services\PushNotificationService::sendToSiswa($person->nisn, $pushTitle, $pushBody);
                        }
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning("Gagal kirim push notif masuk: " . $e->getMessage());
                    }
                }

                $message = $isTerlambat
                    ? "Presensi Masuk (TERLAMBAT) Berhasil Dicatat. Batas toleransi adalah {$jamMasukMaks} WIB."
                    : "Presensi Masuk Berhasil! Tepat waktu pukul {$timeNow} WIB.";

                return [
                    'success' => true,
                    'status'  => $isTerlambat ? 'warning' : 'success',
                    'type'    => 'jam_masuk',
                    'message' => $message,
                    'data'    => $formatProfileData($statusKehadiran, $timeNow, $absensi->jam_masuk, null, $isTerlambat ? 'TERLAMBAT' : 'BERHASIL HADIR'),
                ];
            }

            // ── SCENARIO B: Sudah Ada Jam Masuk -> Validasi Pulang ──
            if (!empty($absensi->jam_masuk) && empty($absensi->jam_pulang)) {
                $jamPulangJadwal = Carbon::parse($jamPulangMulai);

                // Validasi: Belum Waktunya Pulang (< jamPulangMulai)
                if ($now->lessThan($jamPulangJadwal)) {
                    $selisihMenit = $now->diffInMinutes($jamPulangJadwal);
                    return [
                        'success' => true,
                        'status'  => 'info',
                        'type'    => 'belum_waktunya_pulang',
                        'message' => "Anda sudah presensi masuk pukul {$absensi->jam_masuk} WIB. Kepulangan dimulai pukul " . substr($jamPulangMulai, 0, 5) . " WIB (Kurang {$selisihMenit} menit).",
                        'data'    => $formatProfileData('sudah_masuk', $absensi->jam_masuk, $absensi->jam_masuk, null, 'SUDAH MASUK'),
                    ];
                }

                // Catat Jam Pulang Resmi
                $absensi->update([
                    'jam_pulang' => $timeNow,
                ]);

                // Notifikasi WhatsApp Pulang (Hanya jika kategori pulang diaktifkan)
                if ($type === 'siswa') {
                    try {
                        $settingNotif = \App\Models\PengaturanNotifikasi::getPengaturan();
                        if ($settingNotif->isKategoriAktif('pulang')) {
                            \App\Services\NotifikasiDraftService::buatDraft($person, 'pulang', [
                                'tanggal'    => $today,
                                'jam'        => $timeNow,
                                'keterangan' => 'Presensi Pulang Resmi via RFID/Barcode',
                            ], 'sistem_rfid');
                        }
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning("Gagal memproses draf notifikasi ortu pulang RFID: " . $e->getMessage());
                    }

                    // Push Notification Real-Time ke Aplikasi HP Orang Tua
                    try {
                        if (!empty($person->nisn)) {
                            $pushTitle = "Presensi Pulang: {$person->nama}";
                            $pushBody = "Ananda telah selesai kegiatan belajar dan melakukan presensi kepulangan pada pukul {$timeNow} WIB.";
                            \App\Services\PushNotificationService::sendToSiswa($person->nisn, $pushTitle, $pushBody);
                        }
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning("Gagal kirim push notif pulang: " . $e->getMessage());
                    }
                }

                return [
                    'success' => true,
                    'status'  => 'success',
                    'type'    => 'jam_pulang',
                    'message' => "Presensi Pulang Berhasil! Hati-hati di jalan. Pukul {$timeNow} WIB.",
                    'data'    => $formatProfileData('pulang', $timeNow, $absensi->jam_masuk, $timeNow, 'BERHASIL PULANG'),
                ];
            }

            // ── SCENARIO C: Record Ada Namun Tanpa Jam Masuk (Terkunci Alpha / Belum Scan Pagi) ──
            if (empty($absensi->jam_masuk)) {
                return [
                    'success' => false,
                    'status'  => 'warning',
                    'type'    => 'tanpa_jam_masuk',
                    'message' => "Presensi ditolak. Tidak ada rekaman jam masuk pagi ini untuk {$person->nama}. Silakan melapor ke Petugas Piket.",
                    'data'    => [
                        'nama'                => $person->nama,
                        'tipe'                => $type,
                        'sub'                 => $rombelOrJabatan,
                        'identitas'           => $identitas,
                        'rombel_atau_jabatan' => $rombelOrJabatan,
                        'foto'                => $person->foto_url,
                        'foto_url'            => $person->foto_url,
                        'status'              => $absensi->status ?: 'ditolak',
                        'jam'                 => $timeNow,
                        'jam_masuk'           => null,
                        'jam_pulang'          => null,
                    ]
                ];
            }

            // Fallback (jika sudah lengkap)
            return [
                'success' => true,
                'status'  => 'info',
                'type'    => 'sudah_lengkap',
                'message' => "Presensi hari ini sudah lengkap (Masuk: {$absensi->jam_masuk} WIB · Pulang: {$absensi->jam_pulang} WIB).",
                'data'    => [
                    'nama'                => $person->nama,
                    'tipe'                => $type,
                    'sub'                 => $rombelOrJabatan,
                    'identitas'           => $identitas,
                    'rombel_atau_jabatan' => $rombelOrJabatan,
                    'foto'                => $person->foto_url,
                    'foto_url'            => $person->foto_url,
                    'status'              => 'selesai',
                    'jam'                 => $absensi->jam_pulang,
                    'jam_masuk'           => $absensi->jam_masuk,
                    'jam_pulang'          => $absensi->jam_pulang,
                ]
            ];

        });
    }
}
