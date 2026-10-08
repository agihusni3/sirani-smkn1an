<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\AuditLog;
use App\Models\Guru;
use App\Models\IzinGuru;
use App\Models\IzinSiswa;
use App\Models\JadwalHariIni;
use App\Models\KasusDisiplin;
use App\Models\NotifikasiOrtu;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Services\NotifikasiDraftService;
use App\Services\PushNotificationService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KoreksiPresensiService
{
    /**
     * Jalankan proses koreksi satu catatan presensi (Siswa atau Guru).
     *
     * @param Absensi $absensi
     * @param array $data ['status', 'jam_masuk', 'jam_pulang', 'keterangan']
     * @param User|null $user
     * @param string $sumberAsal
     * @return array
     */
    public static function koreksi(Absensi $absensi, array $data, ?User $user = null, string $sumberAsal = 'koreksi_piket_manual'): array
    {
        return DB::transaction(function () use ($absensi, $data, $user, $sumberAsal) {
            $pencatat = $user?->name ?? 'Guru Piket / Admin';
            $today = Carbon::today()->toDateString();
            $rawStatus = strtolower(trim($data['status'] ?? 'hadir'));
            $keterangan = trim($data['keterangan'] ?? '');
            $inputJamMasuk = !empty($data['jam_masuk']) ? trim($data['jam_masuk']) : null;
            $inputJamPulang = !empty($data['jam_pulang']) ? trim($data['jam_pulang']) : null;

            // Format jam jika hanya HH:MM
            if ($inputJamMasuk && strlen($inputJamMasuk) === 5) {
                $inputJamMasuk .= ':00';
            }
            if ($inputJamPulang && strlen($inputJamPulang) === 5) {
                $inputJamPulang .= ':00';
            }

            $isTitipKartu = false;
            $statusFinal = $rawStatus;
            $jamMasuk = $inputJamMasuk;
            $jamPulang = $inputJamPulang;
            $sumberAbsen = $sumberAsal;

            // Dapatkan jadwal aktif untuk menentukan batas jam kepulangan
            $jadwal = JadwalHariIni::getJadwalAktif($absensi->tanggal);
            $jamPulangMulai = $jadwal?->jam_pulang_mulai ?: '15:30:00';
            if (strlen($jamPulangMulai) === 5) {
                $jamPulangMulai .= ':00';
            }

            // Normalisasi Status dan Jam Masuk/Pulang
            if ($rawStatus === 'titip_kartu') {
                $statusFinal = 'alpha';
                $jamMasuk = null;
                $jamPulang = null;
                $isTitipKartu = true;
                $sumberAbsen = 'interfensi_titip_kartu';
                $ketFinal = $keterangan ?: "Dibatalkan oleh Petugas Piket — Terindikasi Titip Kartu Presensi ({$pencatat})";
            } elseif (in_array($rawStatus, ['alpha', 'reset'])) {
                $statusFinal = 'alpha';
                $jamMasuk = null;
                $jamPulang = null;
                $ketFinal = $keterangan ?: "Koreksi: Status Alpha / Reset oleh {$pencatat}";
            } elseif (in_array($rawStatus, ['sakit', 'izin', 'dispen', 'dispensasi', 'cuti'])) {
                $statusFinal = ($rawStatus === 'dispensasi') ? 'dispen' : $rawStatus;
                if (empty($jamMasuk)) $jamMasuk = null;
                if (empty($jamPulang)) $jamPulang = null;
                $ketFinal = $keterangan ?: "Koreksi (" . ucfirst($statusFinal) . ") oleh {$pencatat}";
            } elseif ($rawStatus === 'hadir') {
                $statusFinal = 'hadir';
                if (empty($jamMasuk)) {
                    $jamMasuk = $absensi->jam_masuk ?: '07:10:00';
                }
                // Penanganan cerdas jam pulang agar tidak memicu jebakan evaluasi bolos sore hari
                if (empty($jamPulang)) {
                    if (!empty($absensi->jam_pulang)) {
                        $jamPulang = $absensi->jam_pulang;
                    } else {
                        // Jika tanggal lampau ATAU hari ini tapi sudah melewati jam pulang sekolah
                        $isLampau = ($absensi->tanggal < $today);
                        $isSudahLewatJamPulang = ($absensi->tanggal === $today && Carbon::now()->format('H:i:s') >= $jamPulangMulai);
                        if ($isLampau || $isSudahLewatJamPulang) {
                            $jamPulang = $jamPulangMulai;
                        } else {
                            $jamPulang = null; // Masih jam sekolah hari ini, biarkan null agar siswa bisa tap pulang
                        }
                    }
                }
                $ketFinal = $keterangan ?: "Koreksi Hadir oleh {$pencatat}";
            } elseif ($rawStatus === 'terlambat') {
                $statusFinal = 'terlambat';
                if (empty($jamMasuk)) {
                    $jamMasuk = $absensi->jam_masuk ?: '07:25:00';
                }
                if (empty($jamPulang)) {
                    if (!empty($absensi->jam_pulang)) {
                        $jamPulang = $absensi->jam_pulang;
                    } else {
                        $isLampau = ($absensi->tanggal < $today);
                        $isSudahLewatJamPulang = ($absensi->tanggal === $today && Carbon::now()->format('H:i:s') >= $jamPulangMulai);
                        if ($isLampau || $isSudahLewatJamPulang) {
                            $jamPulang = $jamPulangMulai;
                        } else {
                            $jamPulang = null;
                        }
                    }
                }
                $ketFinal = $keterangan ?: "Koreksi Terlambat oleh {$pencatat}";
            } elseif ($rawStatus === 'bolos') {
                $statusFinal = 'bolos';
                if (empty($jamMasuk)) {
                    $jamMasuk = $absensi->jam_masuk ?: '07:10:00';
                }
                $jamPulang = null; // Bolos tidak memiliki scan pulang
                $ketFinal = $keterangan ?: "Intervensi: Dinyatakan Bolos Kelas oleh {$pencatat}";
            } else {
                $ketFinal = $keterangan ?: "Koreksi oleh {$pencatat}";
            }

            // Update Catatan Presensi Utama
            $absensi->update([
                'status'       => $statusFinal,
                'jam_masuk'    => $jamMasuk,
                'jam_pulang'   => $jamPulang,
                'sumber_absen' => $sumberAbsen,
                'keterangan'   => $ketFinal,
            ]);

            // Cek apakah pemilik_type adalah Siswa atau Guru
            $isSiswa = ($absensi->pemilik_type === 'siswa' || empty($absensi->pemilik_type) || !empty($absensi->siswa_rombel_id));
            $targetNama = 'Pengguna';

            if ($isSiswa) {
                $siswaId = $absensi->pemilik_id ?: ($absensi->siswaRombel?->siswa_id);
                $siswaObj = $absensi->siswa ?: ($absensi->siswaRombel?->siswa ?: Siswa::find($siswaId));

                if ($siswaObj) {
                    $siswaId = $siswaObj->id;
                    $targetNama = $siswaObj->nama;

                    // 1. Sinkronisasi IzinSiswa
                    if (in_array($statusFinal, ['izin', 'sakit', 'dispen', 'dispensasi'])) {
                        $jenisIzin = ($statusFinal === 'dispen') ? 'dispensasi' : $statusFinal;
                        $existingIzin = IzinSiswa::where('siswa_id', $siswaId)->where('tanggal', $absensi->tanggal)->first();
                        $finalFile = !empty($data['file_pendukung']) ? $data['file_pendukung'] : ($existingIzin?->file_pendukung);

                        IzinSiswa::updateOrCreate(
                            [
                                'siswa_id' => $siswaId,
                                'tanggal'  => $absensi->tanggal,
                            ],
                            [
                                'jenis'          => $jenisIzin,
                                'status'         => 'disetujui',
                                'keterangan'     => $ketFinal,
                                'file_pendukung' => $finalFile,
                                'disetujui_oleh' => $pencatat,
                            ]
                        );
                    } else {
                        // Jika diubah ke hadir / terlambat / alpha / bolos, bersihkan IzinSiswa tanggal tsb
                        IzinSiswa::where('siswa_id', $siswaId)
                            ->where('tanggal', $absensi->tanggal)
                            ->delete();
                    }

                    // 2. Sinkronisasi Buku Kasus & Poin Disiplin
                    KasusDisiplin::syncFromPresensi($siswaId);

                    // 3. Sinkronisasi Antrean WhatsApp & Notifikasi Draf
                    NotifikasiDraftService::sinkronkanPresensiSiswa($siswaObj, $absensi->tanggal, $statusFinal, $jamMasuk, $ketFinal);

                    // 4. Catat ke Riwayat Notifikasi Database Orang Tua & Kirim Push Notification
                    try {
                        $nisn = !empty($siswaObj->nisn) ? $siswaObj->nisn : ($siswaObj->nis ?: (string)$siswaObj->id);
                        $labelStatus = match($statusFinal) {
                            'sakit'     => 'Sakit',
                            'izin'      => 'Izin',
                            'dispen'    => 'Dispensasi',
                            'hadir'     => 'Hadir Tepat Waktu',
                            'terlambat' => 'Terlambat',
                            'alpha'     => 'Alpha',
                            'bolos'     => 'Bolos',
                            default     => ucfirst($statusFinal),
                        };
                        $ikon = match($statusFinal) {
                            'sakit'     => '🤒',
                            'izin'      => '📋',
                            'dispen'    => '🎖️',
                            'hadir'     => '✅',
                            'terlambat' => '⏰',
                            'alpha'     => '⚠️',
                            'bolos'     => '🚨',
                            default     => '📢',
                        };

                        $tglFmt = Carbon::parse($absensi->tanggal)->translatedFormat('d M Y');
                        NotifikasiOrtu::create([
                            'siswa_id'    => $siswaObj->id,
                            'kategori'    => 'koreksi_presensi',
                            'tanggal'     => $absensi->tanggal,
                            'no_tujuan'   => $siswaObj->no_hp_ortu ?: ($siswaObj->no_hp ?: '-'),
                            'nama_ortu'   => $siswaObj->nama_ortu ?: 'Orang Tua / Wali Murid',
                            'judul'       => "{$ikon} Koreksi Presensi: {$siswaObj->nama} ({$labelStatus})",
                            'pesan'       => "Data kehadiran ananda tanggal {$tglFmt} dikoreksi menjadi {$labelStatus}. Catatan: {$ketFinal}",
                            'status'      => 'terkirim',
                            'dibuat_oleh' => $pencatat,
                            'waktu_kirim' => now(),
                        ]);

                        PushNotificationService::sendToSiswa(
                            $nisn,
                            "{$ikon} Koreksi Presensi: {$siswaObj->nama} ({$labelStatus})",
                            "Data kehadiran ananda tanggal {$absensi->tanggal} diperbarui menjadi {$labelStatus}. Catatan: {$ketFinal}",
                            '/presensi-siswa/' . urlencode($nisn)
                        );
                    } catch (\Throwable $eNotif) {
                        Log::warning("Gagal kirim notifikasi/push koreksi presensi: " . $eNotif->getMessage());
                    }
                }
            } elseif ($absensi->pemilik_type === 'guru') {
                $guruId = $absensi->pemilik_id;
                $guruObj = $absensi->guru ?: ($guruId ? Guru::find($guruId) : null);
                if ($guruObj) {
                    $targetNama = $guruObj->nama;

                    // Sinkronisasi IzinGuru
                    if (in_array($statusFinal, ['izin', 'sakit', 'dispen', 'dispensasi', 'cuti'])) {
                        $jenisIzin = ($statusFinal === 'dispensasi') ? 'dispen' : $statusFinal;
                        IzinGuru::updateOrCreate(
                            [
                                'guru_id' => $guruObj->id,
                                'tanggal' => $absensi->tanggal,
                            ],
                            [
                                'jenis'          => $jenisIzin,
                                'status'         => 'disetujui',
                                'keterangan'     => $ketFinal,
                                'disetujui_oleh' => $pencatat,
                            ]
                        );
                    } else {
                        // Jika hadir / terlambat / alpha, bersihkan izin guru
                        IzinGuru::where('guru_id', $guruObj->id)
                            ->where('tanggal', $absensi->tanggal)
                            ->delete();
                    }
                }
            }

            // Catat ke AuditLog
            $tipeLog = $isTitipKartu ? 'interfensi_piket_titip_kartu' : 'koreksi_presensi';
            $pemilikLabel = $isSiswa ? 'Siswa' : 'Guru';
            AuditLog::catat(
                $tipeLog,
                'absensi',
                "{$pencatat} mengoreksi status presensi {$pemilikLabel} {$targetNama} (Tgl: {$absensi->tanggal}) menjadi {$statusFinal}. Catatan: {$ketFinal}"
            );

            $msgSuccess = $isTitipKartu
                ? "Intervensi Berhasil: Kehadiran {$targetNama} dibatalkan dan disetel ALPHA karena terindikasi titip kartu."
                : "Catatan presensi {$targetNama} berhasil dikoreksi menjadi " . strtoupper($statusFinal) . ".";

            return [
                'success' => true,
                'message' => $msgSuccess,
                'status'  => $statusFinal,
                'absensi' => $absensi->fresh(),
            ];
        });
    }

    /**
     * Jalankan proses koreksi masal presensi untuk beberapa siswa sekaligus.
     *
     * @param array $siswaIds
     * @param array $data ['status', 'jam_masuk', 'jam_pulang', 'keterangan', 'tanggal']
     * @param User|null $user
     * @return array
     */
    public static function koreksiMassal(array $siswaIds, array $data, ?User $user = null): array
    {
        $today = Carbon::today()->toDateString();
        $tanggal = !empty($data['tanggal']) ? $data['tanggal'] : $today;
        $taAktif = TahunAjaran::where('is_active', true)->first();
        $successCount = 0;

        foreach ($siswaIds as $sId) {
            $siswa = Siswa::find($sId);
            if (!$siswa) continue;

            $siswaRombel = $siswa->siswaRombels()
                ->where('status_keanggotaan', 'aktif')
                ->when($taAktif, fn($q) => $q->where('tahun_ajaran_id', $taAktif->id))
                ->first();
            $siswaRombelId = $siswaRombel?->id;

            // Cari atau buat record presensi
            $absensi = Absensi::firstOrCreate(
                [
                    'pemilik_type' => 'siswa',
                    'pemilik_id'   => $siswa->id,
                    'tanggal'      => $tanggal,
                ],
                [
                    'siswa_rombel_id' => $siswaRombelId,
                    'status'          => 'alpha',
                    'sumber_absen'    => 'koreksi_piket_massal',
                ]
            );

            if ($absensi && !$absensi->siswa_rombel_id && $siswaRombelId) {
                $absensi->update(['siswa_rombel_id' => $siswaRombelId]);
            }

            self::koreksi($absensi, $data, $user, 'koreksi_piket_massal');
            $successCount++;
        }

        return [
            'success' => true,
            'count'   => $successCount,
            'message' => "Koreksi massal berhasil diterapkan pada {$successCount} siswa.",
        ];
    }
}
