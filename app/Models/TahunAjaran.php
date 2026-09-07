<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajarans';

    protected $fillable = [
        'nama',
        'is_active',
    ];

    /**
     * Ambil tahun awal dari format nama tahun ajaran (contoh: '2026/2027' -> 2026)
     */
    public function getTahunAwalAttribute(): int
    {
        if (preg_match('/(\d{4})/', $this->nama, $matches)) {
            return (int) $matches[1];
        }
        return (int) date('Y');
    }

    /**
     * Cek apakah tahun ajaran ini sudah lewat / lampau dibanding tahun ajaran aktif saat ini.
     * Jika lampau, statusnya terkunci sebagai Arsip / Histori.
     */
    public function isLampau(): bool
    {
        if ($this->is_active) {
            return false;
        }

        $taAktif = self::where('is_active', true)->first();
        if ($taAktif && $this->tahun_awal < $taAktif->tahun_awal) {
            return true;
        }

        return false;
    }

    /**
     * Cek apakah tahun ajaran ini adalah tahun ajaran baru mendatang (belum aktif).
     */
    public function isMendatang(): bool
    {
        if ($this->is_active) {
            return false;
        }

        $taAktif = self::where('is_active', true)->first();
        if ($taAktif && $this->tahun_awal > $taAktif->tahun_awal) {
            return true;
        }

        return false;
    }
}
