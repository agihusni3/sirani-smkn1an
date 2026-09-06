<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpdbUjianPeserta extends Model
{
    use HasFactory;

    protected $table = 'ppdb_ujian_pesertas';

    protected $fillable = [
        'ppdb_pendaftar_id',
        'ppdb_ujian_setting_id',
        'waktu_mulai',
        'waktu_selesai',
        'jawaban_pg',
        'jawaban_esai',
        'jumlah_pg_benar',
        'jumlah_pg_salah',
        'jumlah_pg_kosong',
        'nilai_pg',
        'nilai_per_nomor_esai',
        'nilai_esai',
        'nilai_total_tertulis',
        'status_pengerjaan',
        'catatan_koreksi_esai',
        'diperiksa_oleh',
        'diperiksa_pada',
    ];

    protected $casts = [
        'jawaban_pg'           => 'array',
        'jawaban_esai'         => 'array',
        'nilai_per_nomor_esai' => 'array',
        'waktu_mulai'          => 'datetime',
        'waktu_selesai'        => 'datetime',
        'diperiksa_pada'       => 'datetime',
        'nilai_pg'             => 'decimal:2',
        'nilai_esai'           => 'decimal:2',
        'nilai_total_tertulis' => 'decimal:2',
    ];

    public function pendaftar()
    {
        return $this->belongsTo(PpdbPendaftar::class, 'ppdb_pendaftar_id');
    }

    public function setting()
    {
        return $this->belongsTo(PpdbUjianSetting::class, 'ppdb_ujian_setting_id');
    }

    public function pemeriksa()
    {
        return $this->belongsTo(User::class, 'diperiksa_oleh');
    }

    /**
     * Hitung total skor tertulis dan sinkronkan ke PpdbPendaftar
     */
    public function sinkronNilaiKePendaftar(): void
    {
        $total = round(((float) $this->nilai_pg) + ((float) $this->nilai_esai), 2);
        $this->nilai_total_tertulis = $total;
        $this->save();

        if ($this->pendaftar) {
            $this->pendaftar->nilai_tes_tertulis = $total;
            $this->pendaftar->hitungNilaiAkhir();
            $this->pendaftar->save();
        }
    }
}
