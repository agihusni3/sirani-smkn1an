<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukuSkKepsek extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_urut_sk',
        'tahun_sk',
        'nomor_sk_lengkap',
        'tentang_sk',
        'tanggal_ditetapkan',
        'kategori_sk',
        'file_dokumen',
        'is_active',
    ];

    protected $casts = [
        'tanggal_ditetapkan' => 'date',
        'is_active' => 'boolean',
    ];

    public static function nextNomorUrut(int $year): int
    {
        $max = static::where('tahun_sk', $year)->max('nomor_urut_sk');
        return ($max ? (int) $max : 0) + 1;
    }

    public function distribusiPtks()
    {
        return $this->hasMany(ArsipDokumenPtk::class, 'buku_sk_id');
    }
}
