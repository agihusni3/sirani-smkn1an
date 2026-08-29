<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\IzinSiswa;
use App\Models\IzinGuru;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;
use Exception;

class IzinSiswaService
{
    /**
     * Mengajukan dan menyetujui izin siswa secara otomatis mengaitkannya ke absensi harian.
     */
    public function ajukanIzin(int $siswaId, string $tanggal, string $jenis, string $keterangan = null, string $filePendukung = null): IzinSiswa
    {
        return DB::transaction(function () use ($siswaId, $tanggal, $jenis, $keterangan, $filePendukung) {
            // 1. Simpan data izin beserta keterangan penyetuju
            $user = auth()->user();
            if ($user) {
                $roleLabel = ($user->isGuruPiket() || $user->isPiketHariIni()) 
                    ? 'Guru Piket' 
                    : ($user->isAdmin() ? 'Administrator' : $user->role_display_name);
                $disetujuiOleh = $user->name . ' (' . $roleLabel . ')';
            } else {
                $disetujuiOleh = session('guru_piket_nama') ? session('guru_piket_nama') . ' (Guru Piket)' : 'Guru Piket';
            }

            // Normalisasi jenis izin agar kompatibel penuh dengan skema database
            $jenisDb = in_array($jenis, ['pulang_cepat', 'pulang_awal']) ? 'pulang_awal' : $jenis;

            $izin = IzinSiswa::create([
                'siswa_id'       => $siswaId,
                'tanggal'        => $tanggal,
                'jenis'          => $jenisDb,
                'status'         => 'disetujui',
                'keterangan'     => $keterangan,
                'file_pendukung' => $filePendukung,
                'disetujui_oleh' => $disetujuiOleh,
            ]);

            // 2. Cari keanggotaan rombel siswa pada tahun ajaran aktif
            $taAktif = TahunAjaran::where('is_active', true)->first();
            $membership = null;
            if ($taAktif) {
                $membership = SiswaRombel::where('siswa_id', $siswaId)
                    ->where('tahun_ajaran_id', $taAktif->id)
                    ->where('status_keanggotaan', 'aktif')
                    ->first();
            }

            // 3. Perbarui/Buat catatan absensi harian dengan status sesuai izin
            $statusAbsensi = in_array($jenis, ['sakit', 'izin']) ? $jenis : 'izin';

            if (!in_array($jenis, ['pulang_cepat', 'pulang_awal'])) {
                $detailKet = "Disetujui: {$disetujuiOleh}" . ($keterangan ? " — {$keterangan}" : '');
                Absensi::updateOrCreate(
                    [
                        'pemilik_type' => 'siswa',
                        'pemilik_id' => $siswaId,
                        'tanggal' => $tanggal,
                    ],
                    [
                        'siswa_rombel_id' => $membership->id ?? null,
                        'status' => $statusAbsensi,
                        'sumber_absen' => 'manual_izin_piket',
                        'keterangan' => $detailKet,
                    ]
                );
            }

            // 4. Buat draf notifikasi orang tua (Izin / Sakit)
            try {
                $siswa = \App\Models\Siswa::find($siswaId);
                if ($siswa && in_array($jenis, ['sakit', 'izin'])) {
                    \App\Services\NotifikasiDraftService::buatDraft($siswa, $jenis, [
                        'tanggal'    => $tanggal,
                        'keterangan' => $keterangan ?: 'Izin tercatat oleh ' . $disetujuiOleh,
                    ], $disetujuiOleh);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Gagal membuat draf notifikasi izin: " . $e->getMessage());
            }

            return $izin;
        });
    }

    /**
     * Mengajukan dan menyetujui izin guru secara otomatis mengaitkannya ke absensi harian guru.
     */
    public function ajukanIzinGuru(int $guruId, string $tanggal, string $jenis, string $keterangan = null, string $filePendukung = null): IzinGuru
    {
        return DB::transaction(function () use ($guruId, $tanggal, $jenis, $keterangan, $filePendukung) {
            $user = auth()->user();
            if ($user) {
                $roleLabel = ($user->isGuruPiket() || $user->isPiketHariIni()) 
                    ? 'Guru Piket' 
                    : ($user->isAdmin() ? 'Administrator' : $user->role_display_name);
                $disetujuiOleh = $user->name . ' (' . $roleLabel . ')';
            } else {
                $disetujuiOleh = session('guru_piket_nama') ? session('guru_piket_nama') . ' (Guru Piket)' : 'Guru Piket';
            }

            $jenisDb = in_array($jenis, ['pulang_cepat', 'pulang_awal']) ? 'pulang_cepat' : $jenis;

            $izin = IzinGuru::create([
                'guru_id'        => $guruId,
                'tanggal'        => $tanggal,
                'jenis'          => $jenisDb,
                'status'         => 'disetujui',
                'keterangan'     => $keterangan,
                'file_pendukung' => $filePendukung,
                'disetujui_oleh' => $disetujuiOleh,
            ]);

            // Perbarui/Buat catatan absensi harian guru
            $statusAbsensi = in_array($jenis, ['sakit', 'izin', 'cuti']) ? $jenis : 'izin';
            if ($jenis === 'dinas_luar') {
                $statusAbsensi = 'izin';
            }

            if (!in_array($jenis, ['pulang_cepat', 'pulang_awal'])) {
                $detailKet = "Disetujui: {$disetujuiOleh}" . ($keterangan ? " — {$keterangan}" : '');
                Absensi::updateOrCreate(
                    [
                        'pemilik_type' => 'guru',
                        'pemilik_id'   => $guruId,
                        'tanggal'      => $tanggal,
                    ],
                    [
                        'status'       => $statusAbsensi,
                        'sumber_absen' => 'manual_izin_piket',
                        'keterangan'   => $detailKet,
                    ]
                );
            }

            return $izin;
        });
    }
}
