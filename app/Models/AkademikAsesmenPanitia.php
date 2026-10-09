<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AkademikAsesmenPanitia extends Model
{
    use HasFactory;

    protected $table = 'akademik_asesmen_panitias';

    protected $fillable = [
        'periode_id',
        'guru_id',
        'peran',
        'tugas_khusus',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function periode()
    {
        return $this->belongsTo(AkademikAsesmenPeriode::class, 'periode_id');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function getPeranLabelAttribute(): string
    {
        return match ($this->peran) {
            'penanggung_jawab' => 'Penanggung Jawab',
            'pengarah' => 'Pengarah / Pengawas Teknis',
            'ketua' => 'Ketua Pelaksana',
            'sekretaris' => 'Sekretaris',
            'bendahara' => 'Bendahara',
            'proktor_utama' => 'Proktor Utama (CBT)',
            'teknisi' => 'Teknisi Jaringan & Lab',
            'koordinator_soal' => 'Koordinator Naskah & Kisi-Kisi',
            'pengawas' => 'Pengawas Ruang',
            default => 'Anggota Panitia',
        };
    }
}
