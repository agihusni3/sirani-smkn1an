<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PelayananSurat extends Model
{
    use HasFactory;

    protected $fillable = [
        'surat_keluar_id',
        'siswa_id',
        'jenis_pelayanan',
        'keperluan',
        'kode_verifikasi_qr',
        'is_valid',
        'payload_snapshot',
        'created_by',
    ];

    protected $casts = [
        'is_valid' => 'boolean',
        'payload_snapshot' => 'array',
    ];

    public function suratKeluar()
    {
        return $this->belongsTo(SuratKeluar::class, 'surat_keluar_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->kode_verifikasi_qr)) {
                $model->kode_verifikasi_qr = Str::random(32);
            }
        });
    }

    /**
     * Label jenis pelayanan yang mudah dibaca.
     */
    public function getJenisLabelAttribute(): string
    {
        return match ($this->jenis_pelayanan) {
            'suket_aktif' => 'Surat Keterangan Siswa Aktif',
            'suket_berkelakuan_baik' => 'Surat Keterangan Berkelakuan Baik',
            'suket_mutasi_keluar' => 'Surat Rekomendasi Pindah Sekolah',
            'suket_skl' => 'Surat Keterangan Lulus (SKL)',
            'suket_pengantar_pkl' => 'Surat Pengantar PKL Industri',
            default => 'Surat Keterangan Resmi',
        };
    }
}
