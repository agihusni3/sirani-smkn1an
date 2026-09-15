<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class JadwalPiket extends Model
{
    use HasFactory;

    protected $table = 'jadwal_pikets';

    protected $fillable = [
        'hari',
        'guru_id',
        'keterangan',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    /**
     * Konversi tanggal ke nama hari bahasa Indonesia (Senin s/d Minggu).
     */
    public static function getHariIndonesia($tanggal = null): string
    {
        $dateObj = $tanggal ? Carbon::parse($tanggal) : Carbon::today();
        $map = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            0 => 'Minggu',
            7 => 'Minggu',
        ];

        return $map[$dateObj->dayOfWeek] ?? 'Senin';
    }

    /**
     * Cek apakah seorang guru terdaftar sebagai Guru Piket pada hari ini.
     */
    public static function isGuruPiketHariIni(int $guruId, $tanggal = null): bool
    {
        $guru = Guru::find($guruId);
        if (!$guru || $guru->status !== 'aktif') {
            return false;
        }

        $hari = self::getHariIndonesia($tanggal);

        // Hari Sabtu & Minggu adalah hari libur sekolah (tidak ada tugas piket)
        if (in_array($hari, ['Sabtu', 'Minggu'])) {
            return false;
        }

        return self::where('hari', $hari)
            ->where('guru_id', $guruId)
            ->exists();
    }

    /**
     * Ambil seluruh jadwal piket hari ini beserta relasi guru.
     */
    public static function getJadwalHariIni($tanggal = null)
    {
        $hari = self::getHariIndonesia($tanggal);
        return self::where('hari', $hari)->with('guru')->get();
    }

    /**
     * Catat Presensi Masuk Otomatis Guru Piket saat Login ke Sistem.
     * Jika guru bertugas hari ini dan belum ada rekaman jam masuk,
     * sistem langsung membuat record Absensi masuk tanpa perlu tap kartu.
     */
    public static function catatAbsenMasukPiket(Guru $guru, $tanggal = null, $waktu = null): array
    {
        $dateObj   = $tanggal ? Carbon::parse($tanggal) : Carbon::today();
        $today     = $dateObj->toDateString();
        $timeNow   = $waktu ? Carbon::parse($waktu)->toTimeString() : Carbon::now()->toTimeString();

        // Validasi apakah guru terdaftar piket pada hari ini
        if (!self::isGuruPiketHariIni($guru->id, $today)) {
            return [
                'success' => false,
                'is_piket' => false,
                'message' => "Guru {$guru->nama} tidak terdaftar di jadwal piket hari ini.",
            ];
        }

        // Cek record absensi hari ini
        $absensi = Absensi::where('tanggal', $today)
            ->where('pemilik_type', 'guru')
            ->where('pemilik_id', $guru->id)
            ->first();

        if ($absensi && !empty($absensi->jam_masuk)) {
            return [
                'success' => true,
                'is_piket' => true,
                'created' => false,
                'absensi' => $absensi,
                'message' => "Presensi masuk {$guru->nama} sudah tercatat sebelumnya pada pukul " . substr($absensi->jam_masuk, 0, 5) . " WIB.",
            ];
        }

        // Tentukan batas waktu keterlambatan dari jadwal aktif hari ini
        $jamMasukMaks = '07:30:00';
        try {
            $jadwalAktif = JadwalHariIni::getJadwalAktif($today);
            if ($jadwalAktif && !empty($jadwalAktif->jam_masuk_maks)) {
                $jamMasukMaks = $jadwalAktif->jam_masuk_maks;
            }
        } catch (\Throwable $e) {}

        $isTerlambat = ($timeNow > $jamMasukMaks);
        $status = $isTerlambat ? 'terlambat' : 'hadir';
        $jamFormatted = substr($timeNow, 0, 5);

        if (!$absensi) {
            $absensi = Absensi::create([
                'pemilik_type'    => 'guru',
                'pemilik_id'      => $guru->id,
                'siswa_rombel_id' => null,
                'tanggal'         => $today,
                'jam_masuk'       => $timeNow,
                'status'          => $status,
                'sumber_absen'    => 'login_piket',
                'keterangan'      => "Hadir Tugas Piket (Login Sistem SIRANI pukul {$jamFormatted} WIB)",
            ]);
        } else {
            // Jika record sudah ada (misal sebelumnya alpha/kosong), perbarui jam masuk
            $absensi->update([
                'jam_masuk'    => $timeNow,
                'status'       => $status,
                'sumber_absen' => 'login_piket',
                'keterangan'   => "Hadir Tugas Piket (Login Sistem SIRANI pukul {$jamFormatted} WIB)",
            ]);
        }

        return [
            'success'      => true,
            'is_piket'     => true,
            'created'      => true,
            'absensi'      => $absensi,
            'jam'          => $jamFormatted,
            'is_terlambat' => $isTerlambat,
            'message'      => "Selamat bertugas! Presensi masuk Guru Piket ({$guru->nama}) otomatis tercatat pukul {$jamFormatted} WIB.",
        ];
    }

    /**
     * Dapatkan status kehadiran & pelaksanaan tugas guru piket hari ini.
     * Mengembalikan: 'aktif' (sudah login/hadir), 'pulang' (sudah tap pulang), atau 'belum_login'.
     */
    public static function getStatusKehadiranPiket(int $guruId, $tanggal = null): array
    {
        $dateObj = $tanggal ? Carbon::parse($tanggal) : Carbon::today();
        $today   = $dateObj->toDateString();

        $absensi = Absensi::where('tanggal', $today)
            ->where('pemilik_type', 'guru')
            ->where('pemilik_id', $guruId)
            ->first();

        if ($absensi && !empty($absensi->jam_pulang)) {
            return [
                'status'       => 'pulang',
                'badge_class'  => 'pulang',
                'label'        => 'Pulang ' . substr($absensi->jam_pulang, 0, 5),
                'keterangan'   => 'Selesai Bertugas (Sudah Scan Pulang ' . substr($absensi->jam_pulang, 0, 5) . ' WIB)',
                'jam_masuk'    => substr($absensi->jam_masuk, 0, 5),
                'jam_pulang'   => substr($absensi->jam_pulang, 0, 5),
                'sumber_absen' => $absensi->sumber_absen,
            ];
        }

        if ($absensi && !empty($absensi->jam_masuk)) {
            return [
                'status'       => 'aktif',
                'badge_class'  => 'aktif',
                'label'        => 'Aktif Bertugas (' . substr($absensi->jam_masuk, 0, 5) . ')',
                'keterangan'   => 'Aktif Bertugas (Hadir pukul ' . substr($absensi->jam_masuk, 0, 5) . ' WIB)',
                'jam_masuk'    => substr($absensi->jam_masuk, 0, 5),
                'jam_pulang'   => null,
                'sumber_absen' => $absensi->sumber_absen,
            ];
        }

        return [
            'status'       => 'belum_login',
            'badge_class'  => 'belum',
            'label'        => 'Belum Login',
            'keterangan'   => 'Belum Melaksanakan Tugas (Belum Login ke SIRANI)',
            'jam_masuk'    => null,
            'jam_pulang'   => null,
            'sumber_absen' => null,
        ];
    }
}
