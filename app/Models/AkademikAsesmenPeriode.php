<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AkademikAsesmenPeriode extends Model
{
    use HasFactory;

    protected $table = 'akademik_asesmen_periodes';

    protected $fillable = [
        'tahun_ajaran_id',
        'semester',
        'nama_event',
        'jenis_asesmen',
        'tanggal_mulai',
        'tanggal_selesai',
        'sk_nomor',
        'sk_tanggal',
        'status',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'sk_tanggal' => 'date',
        'semester' => 'integer',
    ];

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function panitias()
    {
        return $this->hasMany(AkademikAsesmenPanitia::class, 'periode_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getJenisLabelAttribute(): string
    {
        return match ($this->jenis_asesmen) {
            'pts' => 'Sumatif Tengah Semester (STS / PTS)',
            'pas' => 'Sumatif Akhir Semester (SAS / PAS)',
            'pat' => 'Sumatif Akhir Tahun (SAT / PAT)',
            'us' => 'Asesmen Sumatif Akhir Jenjang (ASAJ / US)',
            'anbk' => 'Asesmen Nasional Berbasis Komputer (ANBK)',
            'ukk' => 'Uji Kompetensi Keahlian (UKK)',
            default => 'Asesmen Sekolah Terjadwal',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'aktif' => '<span class="badge bg-success" style="font-size:11px; padding:3px 8px; border-radius:6px;">Aktif Berjalan</span>',
            'selesai' => '<span class="badge bg-secondary" style="font-size:11px; padding:3px 8px; border-radius:6px;">Selesai</span>',
            default => '<span class="badge bg-warning text-dark" style="font-size:11px; padding:3px 8px; border-radius:6px;">Draft / Persiapan</span>',
        };
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
