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

            // ── PARAMETER 5: Pengaturan Jam Operasional & Sesi Gerbang ──
            $jadwal = JadwalHariIni::getJadwalAktif($today);

            if ($jadwal && !$jadwal->is_sesi_buka) {
                return [
                    'success' => false,
                    'status'  => 'warning',
                    'type'    => 'gerbang_ditutup',
                    'message' => "Gerbang Presensi sedang ditutup oleh Petugas Piket.",
                    'error'   => "Gerbang belum dibuka.",
                    'data'    => [
                        'nama'                => $person->nama,
                        'tipe'                => $type,
                        'sub'                 => $rombelOrJabatan,
                        'identitas'           => $identitas,
                        'rombel_atau_jabatan' => $rombelOrJabatan,
                        'foto'                => $person->foto_url,
                        'foto_url'            => $person->foto_url,
                        'jam'                 => $timeNow,
                    ]
                ];
            }

            $jamMasukMaks = $jadwal?->jam_masuk_toleransi ?? $jadwal?->jam_masuk_selesai ?? '07:15:00';
            $jamPulangMulai = $jadwal?->jam_pulang_mulai ?? '15:00:00';

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

            // ── PARAMETER 8: Anti-Double-Scan Cooldown (Mencegah scanner menembak 2x berturut-turut < 10 detik) ──
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
                        'status'              => $absensi->status,
                        'jam'                 => $absensi->jam_masuk ?: $timeNow,
                        'jam_masuk'           => $absensi->jam_masuk,
                        'jam_pulang'          => $absensi->jam_pulang,
                    ]
                ];
            }


            // ── SCENARIO A: Perekaman Presensi Masuk (Pertama Kali di Hari Ini) ──
            if (!$absensi) {
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

                // Notifikasi WhatsApp Orang Tua untuk Siswa
                if ($type === 'siswa') {
                    $noTujuan = $person->no_hp_ortu ?: ($person->no_hp_siswa ?: null);
                    if ($noTujuan) {
                        try {
                            $pesanWa = $isTerlambat
                                ? "Pemberitahuan SMKN 1 Air Naningan: Ananda {$person->nama} ({$rombelOrJabatan}) telah melakukan presensi masuk (TERLAMBAT) pada pukul {$timeNow} WIB via Scan Barcode/RFID."
                                : "Pemberitahuan SMKN 1 Air Naningan: Ananda {$person->nama} ({$rombelOrJabatan}) telah hadir di sekolah pada pukul {$timeNow} WIB via Scan Barcode/RFID.";

                            NotifikasiOrtu::create([
                                'siswa_id'    => $id,
                                'kategori'    => $isTerlambat ? 'terlambat' : 'masuk',
                                'no_tujuan'   => $noTujuan,
                                'nama_ortu'   => $person->nama_ortu,
                                'judul'       => $isTerlambat ? "Presensi Terlambat: {$person->nama}" : "Presensi Masuk: {$person->nama}",
                                'pesan'       => $pesanWa,
                                'status'      => 'pending',
                                'tanggal'     => $today,
                                'dibuat_oleh' => 'sistem_rfid',
                            ]);
                        } catch (\Throwable $e) {
                            \Illuminate\Support\Facades\Log::warning("Gagal membuat draf notifikasi ortu masuk RFID: " . $e->getMessage());
                        }
                    }
                }

                $message = $isTerlambat
                    ? "Presensi Masuk (Terlambat) Berhasil! Pukul {$timeNow} WIB."
                    : "Presensi Masuk Berhasil! Pukul {$timeNow} WIB.";

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

            // ── SCENARIO B: Sudah Ada Jam Masuk -> Perekaman Presensi Pulang ──
            if (empty($absensi->jam_pulang)) {
                $jamPulangJadwal = Carbon::parse($jamPulangMulai);

                // Validasi: Belum Waktunya Pulang
                if ($now->lessThan($jamPulangJadwal)) {
                    $selisihMenit = $now->diffInMinutes($jamPulangJadwal);
                    return [
                        'success' => false,
                        'status'  => 'info',
                        'type'    => 'belum_waktunya_pulang',
                        'message' => "Anda sudah presensi masuk pukul {$absensi->jam_masuk} WIB. Belum waktunya absen pulang (Kurang {$selisihMenit} menit).",
                        'data'    => [
                            'nama'                => $person->nama,
                            'tipe'                => $type,
                            'sub'                 => $rombelOrJabatan,
                            'identitas'           => $identitas,
                            'rombel_atau_jabatan' => $rombelOrJabatan,
                            'foto'                => $person->foto_url,
                            'foto_url'            => $person->foto_url,
                            'status'              => $absensi->status,
                            'jam'                 => $timeNow,
                            'jam_masuk'           => $absensi->jam_masuk,
                            'jam_pulang'          => null,
                        ]
                    ];
                }

                // Catat Jam Pulang Resmi
                $absensi->update([
                    'jam_pulang' => $timeNow,
                ]);

                // Notifikasi WhatsApp Pulang
                if ($type === 'siswa') {
                    $noTujuan = $person->no_hp_ortu ?: ($person->no_hp_siswa ?: null);
                    if ($noTujuan) {
                        try {
                            NotifikasiOrtu::create([
                                'siswa_id'    => $id,
                                'kategori'    => 'pulang',
                                'no_tujuan'   => $noTujuan,
                                'nama_ortu'   => $person->nama_ortu,
                                'judul'       => "Presensi Pulang: {$person->nama}",
                                'pesan'       => "Pemberitahuan SMKN 1 Air Naningan: Ananda {$person->nama} ({$rombelOrJabatan}) telah melakukan presensi PULANG pada pukul {$timeNow} WIB via Scan Barcode/RFID.",
                                'status'      => 'pending',
                                'tanggal'     => $today,
                                'dibuat_oleh' => 'sistem_rfid',
                            ]);
                        } catch (\Throwable $e) {
                            \Illuminate\Support\Facades\Log::warning("Gagal membuat draf notifikasi ortu pulang RFID: " . $e->getMessage());
                        }
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
                        'status'              => 'pulang',
                        'jam'                 => $timeNow,
                        'jam_masuk'           => $absensi->jam_masuk,
                        'jam_pulang'          => $timeNow,
                    ]
                ];
            }

            // ── SCENARIO C: Presensi Lengkap (Masuk & Pulang Sudah Tercatat) ──
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
                    'status'              => $absensi->status,
                    'jam'                 => $timeNow,
                    'jam_masuk'           => $absensi->jam_masuk,
                    'jam_pulang'          => $absensi->jam_pulang,
                ]
            ];

        });
    }
}
