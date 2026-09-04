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

            // ── PARAMETER 4: Validasi Kalender Akademik & Hari Libur ──
            $libur = HariLibur::where('tanggal', $today)->first();
            if ($libur) {
                return [
                    'success' => true,
                    'status'  => 'info',
                    'type'    => 'hari_libur',
                    'message' => "Hari ini libur: {$libur->keterangan}",
                    'data'    => [
                        'nama'                => $person->nama,
                        'tipe'                => $type,
                        'sub'                 => $rombelOrJabatan,
                        'identitas'           => $identitas,
                        'rombel_atau_jabatan' => $rombelOrJabatan,
                        'foto'                => $person->foto_url,
                        'foto_url'            => $person->foto_url,
                        'status'              => 'libur',
                        'jam'                 => $timeNow,
                        'jam_masuk'           => null,
                        'jam_pulang'          => null,
                    ]
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
                    'data'    => [
                        'nama'                => $person->nama,
                        'tipe'                => $type,
                        'sub'                 => $rombelOrJabatan,
                        'identitas'           => $identitas,
                        'rombel_atau_jabatan' => $rombelOrJabatan,
                        'foto'                => $person->foto_url,
                        'foto_url'            => $person->foto_url,
                        'status'              => $absensi->status,
                        'jam'                 => $absensi->jam_masuk ?: $timeNow,
                        'jam_masuk'           => $absensi->jam_masuk,
                        'jam_pulang'          => $absensi->jam_pulang,
                    ]
                ];
            }

            // ── PARAMETER 8: Anti-Double-Scan Cooldown (< 10 detik) ──
            if ($absensi && $absensi->updated_at && $absensi->updated_at->diffInSeconds($now) < 10) {
                return [
                    'success' => true,
                    'status'  => 'info',
                    'type'    => 'cooldown_double_scan',
                    'message' => "Presensi {$person->nama} sudah berhasil tercatat baru saja. Silakan lanjutkan ke antrean berikutnya.",
                    'data'    => [
                        'nama'                => $person->nama,
                        'tipe'                => $type,
                        'sub'                 => $rombelOrJabatan,
                        'identitas'           => $identitas,
                        'rombel_atau_jabatan' => $rombelOrJabatan,
                        'foto'                => $person->foto_url,
                        'foto_url'            => $person->foto_url,
                        'status'              => !empty($absensi->jam_pulang) ? 'selesai' : $absensi->status,
                        'jam'                 => !empty($absensi->jam_pulang) ? $absensi->jam_pulang : ($absensi->jam_masuk ?: $timeNow),
                        'jam_masuk'           => $absensi->jam_masuk,
                        'jam_pulang'          => $absensi->jam_pulang,
                    ]
                ];
            }

            // ── PARAMETER 9: KONDISI KARTU SUDAH LENGKAP (MASUK & PULANG TERCATAT) ──
            if ($absensi && !empty($absensi->jam_masuk) && !empty($absensi->jam_pulang)) {
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
                }

                $message = $isTerlambat
                    ? "Presensi Masuk (TERLAMBAT) Berhasil Dicatat. Batas toleransi adalah {$jamMasukMaks} WIB."
                    : "Presensi Masuk Berhasil! Tepat waktu pukul {$timeNow} WIB.";

                return [
                    'success' => true,
                    'status'  => $isTerlambat ? 'warning' : 'success',
                    'type'    => 'jam_masuk',
                    'message' => $message,
                    'data'    => [
                        'nama'                => $person->nama,
                        'tipe'                => $type,
                        'sub'                 => $rombelOrJabatan,
                        'identitas'           => $identitas,
                        'rombel_atau_jabatan' => $rombelOrJabatan,
                        'foto'                => $person->foto_url,
                        'foto_url'            => $person->foto_url,
                        'status'              => $statusKehadiran,
                        'jam'                 => $timeNow,
                        'jam_masuk'           => $absensi->jam_masuk,
                        'jam_pulang'          => null,
                    ]
                ];
            }

            // ── SCENARIO B: Sudah Ada Jam Masuk -> Validasi Pulang ──
            if (empty($absensi->jam_pulang)) {
                $jamPulangJadwal = Carbon::parse($jamPulangMulai);

                // Validasi: Belum Waktunya Pulang (< jamPulangMulai)
                if ($now->lessThan($jamPulangJadwal)) {
                    $selisihMenit = $now->diffInMinutes($jamPulangJadwal);
                    return [
                        'success' => true,
                        'status'  => 'info',
                        'type'    => 'belum_waktunya_pulang',
                        'message' => "Anda sudah presensi masuk pukul {$absensi->jam_masuk} WIB. Kepulangan dimulai pukul " . substr($jamPulangMulai, 0, 5) . " WIB (Kurang {$selisihMenit} menit).",
                        'data'    => [
                            'nama'                => $person->nama,
                            'tipe'                => $type,
                            'sub'                 => $rombelOrJabatan,
                            'identitas'           => $identitas,
                            'rombel_atau_jabatan' => $rombelOrJabatan,
                            'foto'                => $person->foto_url,
                            'foto_url'            => $person->foto_url,
                            'status'              => 'sudah_masuk',
                            'jam'                 => $absensi->jam_masuk,
                            'jam_masuk'           => $absensi->jam_masuk,
                            'jam_pulang'          => null,
                        ]
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
                }

                return [
                    'success' => true,
                    'status'  => 'success',
                    'type'    => 'jam_pulang',
                    'message' => "Presensi Pulang Berhasil! Hati-hati di jalan. Pukul {$timeNow} WIB.",
                    'data'    => [
                        'nama'                => $person->nama,
                        'tipe'                => $type,
                        'sub'                 => $rombelOrJabatan,
                        'identitas'           => $identitas,
                        'rombel_atau_jabatan' => $rombelOrJabatan,
                        'foto'                => $person->foto_url,
                        'foto_url'            => $person->foto_url,
                        'status'              => 'selesai',
                        'jam'                 => $timeNow,
                        'jam_masuk'           => $absensi->jam_masuk,
                        'jam_pulang'          => $timeNow,
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
