<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotifikasiOrtu extends Model
{
    use HasFactory;

    protected $table = 'notifikasi_ortus';

    protected $fillable = [
        'siswa_id',
        'kategori',
        'tanggal',
        'no_tujuan',
        'nama_ortu',
        'judul',
        'pesan',
        'foto_diskusi',
        'dokumen_pendukung',
        'catatan_hasil_diskusi',
        'waktu_diskusi',
        'nama_wali_hadir',
        'status_pembinaan',
        'status_validasi_kepsek',
        'nama_kepsek_validasi',
        'waktu_validasi_kepsek',
        'catatan_kepala_sekolah',
        'status',
        'dibuat_oleh',
        'diverifikasi_oleh',
        'waktu_verifikasi',
        'waktu_kirim',
        'catatan_error',
    ];

    protected $casts = [
        'tanggal'               => 'date',
        'waktu_diskusi'         => 'datetime',
        'waktu_validasi_kepsek' => 'datetime',
        'waktu_verifikasi'      => 'datetime',
        'waktu_kirim'           => 'datetime',
    ];

    public function getFotoDiskusiUrlAttribute(): ?string
    {
        if ($this->foto_diskusi) {
            return asset('storage/' . $this->foto_diskusi);
        }
        return null;
    }

    public function getDokumenPendukungUrlAttribute(): ?string
    {
        if ($this->dokumen_pendukung) {
            return asset('storage/' . $this->dokumen_pendukung);
        }
        return null;
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDiverifikasi($query)
    {
        return $query->where('status', 'diverifikasi');
    }

    public function scopeTerkirim($query)
    {
        return $query->where('status', 'terkirim');
    }

    public function scopeDibatalkan($query)
    {
        return $query->where('status', 'dibatalkan');
    }

    /**
     * Format nomor HP agar standar internasional WhatsApp (628...).
     */
    public static function formatNomorWa(?string $nomor): ?string
    {
        if (!$nomor) {
            return null;
        }

        // Hapus karakter selain angka
        $clean = preg_replace('/[^0-9]/', '', $nomor);

        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '62' . $clean;
        }

        return $clean;
    }
}
