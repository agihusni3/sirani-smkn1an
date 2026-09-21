<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AkademikGuruPiket extends Model
{
    protected $table = 'akademik_guru_pikets';

    protected $fillable = [
        'tahun_ajaran_id',
        'semester',
        'hari',
        'waka_piket_id',
        'guru_ids',
        'catatan',
    ];

    protected $casts = [
        'guru_ids' => 'array',
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function wakaPiket(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'waka_piket_id');
    }

    public function getGuruListAttribute()
    {
        if (empty($this->guru_ids)) return collect([]);
        return Guru::whereIn('id', $this->guru_ids)->get();
    }

    /**
     * Event booted model: sinkronkan otomatis ke tabel jadwal_pikets (SIRANI)
     */
    protected static function booted()
    {
        static::saved(function ($piket) {
            $piket->syncDayToSirani();
        });

        static::deleted(function ($piket) {
            $hariUc = ucfirst(strtolower($piket->hari));
            JadwalPiket::where('hari', $hariUc)->delete();
        });
    }

    /**
     * Sinkronkan penugasan hari ini ke tabel jadwal_pikets (SIRANI & Meja Piket)
     */
    public function syncDayToSirani(): void
    {
        $hariUc = ucfirst(strtolower($this->hari));
        
        $validDays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        if (!in_array($hariUc, $validDays)) {
            return;
        }

        $assignedGurus = [];

        // 1. Waka Piket sebagai Koordinator
        if (!empty($this->waka_piket_id)) {
            $assignedGurus[(int)$this->waka_piket_id] = 'Koordinator';
        }

        // 2. Daftar Guru Piket sebagai Anggota
        if (!empty($this->guru_ids) && is_array($this->guru_ids)) {
            foreach ($this->guru_ids as $gid) {
                $gid = (int)$gid;
                if ($gid > 0 && !isset($assignedGurus[$gid])) {
                    $assignedGurus[$gid] = 'Anggota';
                }
            }
        }

        // Hapus guru pada hari tersebut yang sudah tidak ditugaskan
        JadwalPiket::where('hari', $hariUc)
            ->whereNotIn('guru_id', array_keys($assignedGurus))
            ->delete();

        // Upsert setiap guru yang ditugaskan beserta peran / keterangan
        foreach ($assignedGurus as $guruId => $keterangan) {
            JadwalPiket::updateOrCreate(
                [
                    'hari' => $hariUc,
                    'guru_id' => $guruId,
                ],
                [
                    'keterangan' => $keterangan,
                ]
            );
        }
    }

    /**
     * Sinkronkan semua data akademik_guru_pikets ke jadwal_pikets (SIRANI)
     */
    public static function syncAllToSirani(?int $tahunAjaranId = null, ?int $semester = null): int
    {
        $query = self::query();
        if ($tahunAjaranId) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        }
        if ($semester) {
            $query->where('semester', $semester);
        }

        $records = $query->get();
        foreach ($records as $record) {
            $record->syncDayToSirani();
        }

        return $records->count();
    }
}
