<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratMasuk extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_agenda',
        'tahun_agenda',
        'nomor_surat_asal',
        'pengirim',
        'tanggal_surat',
        'tanggal_diterima',
        'perihal',
        'tingkat_urgensi',
        'file_lampiran',
        'status_disposisi',
        'created_by',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'tanggal_diterima' => 'date',
    ];

    public function disposisis()
    {
        return $this->hasMany(DisposisiSurat::class, 'surat_masuk_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate nomor agenda otomatis berikutnya per tahun.
     */
    public static function nextNomorAgenda(int $year): int
    {
        $max = static::where('tahun_agenda', $year)->max('nomor_agenda');
        return ($max ? (int) $max : 0) + 1;
    }
}
