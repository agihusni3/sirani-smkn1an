<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SuratTugas extends Model
{
    use HasFactory;

    protected $table = 'surat_tugas';

    protected $fillable = [
        'surat_keluar_id',
        'nomor_surat_tugas',
        'kode_klasifikasi',
        'dasar_penugasan',
        'maksud_tugas',
        'tempat_berangkat',
        'tempat_tujuan',
        'lokasi_spesifik',
        'tanggal_mulai',
        'tanggal_selesai',
        'lama_hari',
        'alat_transportasi',
        'sumber_anggaran',
        'pejabat_penandatangan',
        'nama_pejabat',
        'nip_pejabat',
        'pangkat_pejabat',
        'jabatan_pejabat',
        'status',
        'kode_verifikasi_qr',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'lama_hari'       => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->kode_verifikasi_qr)) {
                $model->kode_verifikasi_qr = 'ST-' . strtoupper(substr(hash('sha256', uniqid('st_', true) . microtime()), 0, 24));
            }
        });
    }

    public function suratKeluar()
    {
        return $this->belongsTo(SuratKeluar::class, 'surat_keluar_id');
    }

    public function anggotas()
    {
        return $this->hasMany(SuratTugasAnggota::class, 'surat_tugas_id')->orderBy('urutan', 'asc');
    }

    public function ketua()
    {
        return $this->hasOne(SuratTugasAnggota::class, 'surat_tugas_id')->where('peran', 'Ketua Rombongan')->orderBy('urutan', 'asc');
    }

    public function sppds()
    {
        return $this->hasMany(Sppd::class, 'surat_tugas_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Helper untuk generate nomor urut SPPD.
     */
    public static function nextNomorSppd(int $year): string
    {
        $max = Sppd::whereYear('created_at', $year)->count();
        $next = $max + 1;
        $pad = str_pad((string)$next, 3, '0', STR_PAD_LEFT);
        $monthRomawi = SuratKeluar::romawiBulan((int)now()->format('n'));
        return "094/SPPD.{$pad}/SMKN1AN/{$monthRomawi}/{$year}";
    }
}
