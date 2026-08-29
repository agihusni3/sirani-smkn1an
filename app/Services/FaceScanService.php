<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\HariLibur;
use App\Models\JadwalHariIni;
use App\Models\NotifikasiOrtu;
use App\Models\PengaturanSekolah;
use App\Models\Siswa;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class FaceScanService
{
    /**
     * Memproses absensi untuk Siswa atau Guru berdasarkan ID biometrik wajah.
     */
    public function scanPerson(string $type, int $id, string $device = 'face_kiosk'): array
    {
        return DB::transaction(function () use ($type, $id, $device) {
            $now = Carbon::now();
            $today = $now->toDateString();
            $timeNow = $now->format('H:i:s');

            if ($type === 'siswa') {
                $person = Siswa::with(['siswaRombels' => function ($q) {
                    $q->where('status_keanggotaan', 'aktif')->with('rombel');
                }])->findOrFail($id);

                if (!in_array($person->status, ['aktif', 'pkl'])) {
                    throw new Exception("Siswa {$person->nama} berstatus non-aktif ({$person->status}).");
                }

                $rombelNama = $person->siswaRombels->first()?->rombel?->nama_rombel ?? 'Siswa';
                $identitas = 'NIS: ' . $person->nis;
                $rombelOrJabatan = $rombelNama;
                $srId = $person->siswaRombels->first()?->id;
            } else {
                $person = Guru::findOrFail($id);

                if ($person->status !== 'aktif') {
                    throw new Exception("Guru/Pegawai {$person->nama} berstatus non-aktif.");
                }

                $identitas = $person->nip ? 'NIP: ' . $person->nip : $person->label_kepegawaian;
                $rombelOrJabatan = $person->jabatan ?? 'Guru / Staf';
                $srId = null;
            }

            // Cek apakah hari libur
            $libur = HariLibur::where('tanggal', $today)->first();
            if ($libur) {
                return [
                    'status'  => 'info',
                    'type'    => 'hari_libur',
                    'message' => "Hari ini libur: {$libur->keterangan}",
                    'data'    => [
                        'nama'                => $person->nama,
                        'identitas'           => $identitas,
                        'rombel_atau_jabatan' => $rombelOrJabatan,
                        'foto_url'            => $person->foto_url,
                        'waktu'               => $timeNow,
                    ],
                ];
            }

            // Ambil jadwal operasional sekolah
            $jadwal = JadwalHariIni::getJadwalAktif($today);

            // Cek apakah sesi gerbang dibuka oleh Guru Piket / Admin
            if (!$jadwal->is_sesi_buka) {
                return [
                    'status'  => 'warning',
                    'type'    => 'gerbang_ditutup',
                    'message' => "Gerbang Presensi Face ID belum dibuka oleh Guru Piket.",
                    'data'    => [
                        'nama'                => $person->nama,
                        'identitas'           => $identitas,
                        'rombel_atau_jabatan' => $rombelOrJabatan,
                        'foto_url'            => $person->foto_url,
                        'waktu'               => $timeNow,
                    ],
                ];
            }

            $jamMasukMaks = $jadwal?->jam_masuk_toleransi ?? $jadwal?->jam_masuk_selesai ?? '07:15:00';
            $jamPulangMulai = $jadwal?->jam_pulang_mulai ?? '15:00:00';

            // Catatan khusus guru honor
            $catatanHonor = '';
            if ($type === 'guru' && $person->isHonor() && !$person->isHariMengajar($today)) {
                $catatanHonor = ' (Luar Jadwal Mengajar)';
            }

            // Cari absensi hari ini
            $absensi = Absensi::where('pemilik_type', $type)
                ->where('pemilik_id', $person->id)
                ->where('tanggal', $today)
                ->first();

            if (!$absensi) {
                // Record Jam Masuk Pertama Kali
                $statusPresensi = ($timeNow > $jamMasukMaks) ? 'terlambat' : 'hadir';

                $absensi = Absensi::create([
                    'pemilik_type'    => $type,
                    'pemilik_id'      => $person->id,
                    'siswa_rombel_id' => $srId,
                    'tanggal'         => $today,
                    'jam_masuk'       => $timeNow,
                    'status'          => $statusPresensi,
                    'metode'          => 'wajah',
                    'sumber_absen'    => $device,
                ]);

                // Buat draf notifikasi WA untuk ortu jika siswa terlambat
                if ($type === 'siswa' && $statusPresensi === 'terlambat') {
                    try {
                        \App\Services\NotifikasiDraftService::buatDraft($person, 'terlambat', [
                            'tanggal'       => $today,
                            'jam_terlambat' => $timeNow,
                        ], 'kiosk_face');
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning("Gagal membuat draf notifikasi terlambat: " . $e->getMessage());
                    }
                }

                $statusLabel = ($statusPresensi === 'terlambat') ? 'Terlambat' : 'Hadir Tepat Waktu';

                return [
                    'status'  => 'success',
                    'type'    => 'jam_masuk',
                    'message' => "Presensi masuk berhasil ({$statusLabel}){$catatanHonor}",
                    'data'    => [
                        'nama'                => $person->nama,
                        'identitas'           => $identitas,
                        'rombel_atau_jabatan' => $rombelOrJabatan,
                        'foto_url'            => $person->foto_url,
                        'jam_masuk'           => substr($timeNow, 0, 5) . ' WIB',
                        'jam_pulang'          => null,
                        'status'              => $statusPresensi,
                    ],
                ];
            }

            // Jika sudah ada jam masuk, cek apakah sudah waktunya jam pulang
            if ($timeNow < $jamPulangMulai) {
                return [
                    'status'  => 'info',
                    'type'    => 'sudah_masuk',
                    'message' => "Anda sudah tercatat masuk pukul " . substr($absensi->jam_masuk, 0, 5) . " WIB",
                    'data'    => [
                        'nama'                => $person->nama,
                        'identitas'           => $identitas,
                        'rombel_atau_jabatan' => $rombelOrJabatan,
                        'foto_url'            => $person->foto_url,
                        'jam_masuk'           => substr($absensi->jam_masuk, 0, 5) . ' WIB',
                        'jam_pulang'          => $absensi->jam_pulang ? substr($absensi->jam_pulang, 0, 5) . ' WIB' : null,
                        'status'              => $absensi->status,
                    ],
                ];
            }

            // Jika sudah mencatat kepulangan hari ini, tolak duplikasi scan berulang
            if ($absensi->jam_pulang) {
                return [
                    'status'  => 'info',
                    'type'    => 'sudah_pulang',
                    'message' => "Anda sudah tercatat pulang pukul " . substr($absensi->jam_pulang, 0, 5) . " WIB",
                    'data'    => [
                        'nama'                => $person->nama,
                        'identitas'           => $identitas,
                        'rombel_atau_jabatan' => $rombelOrJabatan,
                        'foto_url'            => $person->foto_url,
                        'jam_masuk'           => substr($absensi->jam_masuk, 0, 5) . ' WIB',
                        'jam_pulang'          => substr($absensi->jam_pulang, 0, 5) . ' WIB',
                        'status'              => $absensi->status,
                    ],
                ];
            }

            // Catat Kepulangan Pertama Kali
            $absensi->update([
                'jam_pulang' => $timeNow,
            ]);

            return [
                'status'  => 'success',
                'type'    => 'jam_pulang',
                'message' => "Presensi pulang berhasil. Hati-hati di jalan!{$catatanHonor}",
                'data'    => [
                    'nama'                => $person->nama,
                    'identitas'           => $identitas,
                    'rombel_atau_jabatan' => $rombelOrJabatan,
                    'foto_url'            => $person->foto_url,
                    'jam_masuk'           => substr($absensi->jam_masuk, 0, 5) . ' WIB',
                    'jam_pulang'          => substr($timeNow, 0, 5) . ' WIB',
                    'status'              => $absensi->status,
                ],
            ];
        });
    }
}
