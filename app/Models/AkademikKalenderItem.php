<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AkademikKalenderItem extends Model
{
    use HasFactory;

    protected $table = 'akademik_kalender_items';

    protected $fillable = [
        'akademik_kalender_id',
        'bulan',
        'minggu_ke',
        'minggu_ke_semester',
        'tanggal_mulai',
        'tanggal_selesai',
        'jenis',
        'kategori',
        'keterangan',
        'warna',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function formatRentangTanggal(): string
    {
        if (!$this->tanggal_mulai) {
            return '';
        }
        if (!$this->tanggal_selesai || $this->tanggal_mulai->equalTo($this->tanggal_selesai)) {
            return $this->tanggal_mulai->translatedFormat('d F Y');
        }
        if ($this->tanggal_mulai->format('Y-m') === $this->tanggal_selesai->format('Y-m')) {
            return $this->tanggal_mulai->format('d') . ' - ' . $this->tanggal_selesai->translatedFormat('d F Y');
        }
        return $this->tanggal_mulai->translatedFormat('d M Y') . ' - ' . $this->tanggal_selesai->translatedFormat('d M Y');
    }

    public function isDateCovered(string $date): bool
    {
        if (!$this->tanggal_mulai) return false;
        $d = substr($date, 0, 10);
        $start = $this->tanggal_mulai->format('Y-m-d');
        $end = ($this->tanggal_selesai ? $this->tanggal_selesai->format('Y-m-d') : $start);
        return $d >= $start && $d <= $end;
    }

    public function kalender()
    {
        return $this->belongsTo(AkademikKalender::class, 'akademik_kalender_id');
    }

    public function isEfektif(): bool
    {
        return $this->jenis === 'efektif';
    }

    public function getLabelSingkat(): string
    {
        return match ($this->kategori) {
            'mpls' => 'MPLS',
            'sts' => 'STS',
            'sas' => 'SAS',
            'sat' => 'SAT',
            'ukk' => 'UKK',
            'rapor' => 'RAPOR',
            'libur' => 'LIBUR',
            'pkl' => 'PKL',
            'kbm' => 'KBM',
            default => strtoupper(substr($this->keterangan ?: 'AGENDA', 0, 5)),
        };
    }
}
