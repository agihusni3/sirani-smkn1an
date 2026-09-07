<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KlasifikasiSurat extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'uraian',
        'is_active',
    ];

    public function suratKeluars()
    {
        return $this->hasMany(SuratKeluar::class, 'klasifikasi_id');
    }
}
