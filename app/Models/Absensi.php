<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensis';

    protected $fillable = [
        'pemilik_type',
        'pemilik_id',
        'siswa_rombel_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'status',
        'sumber_absen',
        'keterangan',
    ];

    public function siswaRombel(): BelongsTo
    {
        return $this->belongsTo(SiswaRombel::class, 'siswa_rombel_id');
    }

    /** Relasi ke Siswa (dipakai ketika pemilik_type = 'siswa') */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'pemilik_id');
    }

    /** Relasi ke Guru (dipakai ketika pemilik_type = 'guru') */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'pemilik_id');
    }

    /**
     * Label deskriptif asal-usul data presensi (Smart Gate, Koreksi Piket, atau Otomatis Sistem).
     */
    public function getSumberAbsenLabelAttribute(): string
    {
        return match($this->sumber_absen) {
            'auto_kunci_piket'         => 'Otomatis Sistem (09:00)',
            'koreksi_piket_manual'    => 'Koreksi Guru Piket',
            'manual_piket'            => 'Manual Guru Piket',
            'manual_izin_piket'       => 'Izin Petugas Piket',
            'rfid', 'kios_rfid'       => 'Smart Gate RFID',
            'barcode', 'scan_barcode' => 'Scan Barcode',
            'kios_wajah', 'face_kiosk'=> 'Face ID Kiosk',
            'evaluasi_sore_alpha'     => 'Evaluasi Sore (17:00)',
            default                   => !empty($this->sumber_absen) ? ucwords(str_replace('_', ' ', $this->sumber_absen)) : 'Smart Gate',
        };
    }
}
