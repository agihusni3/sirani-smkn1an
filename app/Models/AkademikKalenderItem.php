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
        'jenis',
        'kategori',
        'keterangan',
        'warna',
    ];

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
