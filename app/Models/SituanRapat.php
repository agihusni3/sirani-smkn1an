<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SituanRapat extends Model
{
    use HasFactory;

    protected $table = 'situan_rapats';

    protected $fillable = [
        'nomor_surat',
        'judul_rapat',
        'tipe_rapat',
        'tanggal_rapat',
        'jam_mulai',
        'jam_selesai',
        'tempat',
        'pimpinan_rapat_id',
        'pimpinan_nama',
        'pimpinan_jabatan',
        'notulis_id',
        'notulis_nama',
        'agenda',
        'peserta_tipe',
        'peserta_ids',
        'peserta_custom',
        'susunan_acara',
        'jalannya_rapat',
        'keputusan_rapat',
        'berita_acara',
        'status',
        'jumlah_hadir',
        'jumlah_tidak_hadir',
        'created_by',
    ];

    protected $casts = [
        'tanggal_rapat' => 'date',
        'peserta_ids'   => 'array',
    ];

    public function pimpinan(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'pimpinan_rapat_id');
    }

    public function notulis(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'notulis_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTipeLabelAttribute(): string
    {
        return match ($this->tipe_rapat) {
            'dinas_guru'       => 'Rapat Dinas Guru & Tendik',
            'komite_ortu'      => 'Rapat Komite & Wali Murid',
            'pleno_kelulusan'  => 'Rapat Pleno Kelulusan Siswa',
            'pleno_kenaikan'   => 'Rapat Pleno Kenaikan Kelas',
            'kurikulum_kosp'   => 'Rapat Kurikulum & KOSP',
            'kepanitiaan'      => 'Rapat Koordinasi Kepanitiaan',
            'evaluasi_bulanan' => 'Rapat Evaluasi Bulanan',
            default            => ucfirst(str_replace('_', ' ', $this->tipe_rapat ?? 'Rapat')),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'selesai'    => '<span class="badge" style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; font-weight:800; font-size:11px; padding:3px 8px; border-radius:6px;"><i class="bi bi-check2-circle"></i> Selesai</span>',
            'dibatalkan' => '<span class="badge" style="background:#fef2f2; color:#dc2626; border:1px solid #fecaca; font-weight:800; font-size:11px; padding:3px 8px; border-radius:6px;"><i class="bi bi-x-circle"></i> Dibatalkan</span>',
            default      => '<span class="badge" style="background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; font-weight:800; font-size:11px; padding:3px 8px; border-radius:6px;"><i class="bi bi-clock"></i> Dijadwalkan</span>',
        };
    }

    /**
     * Mengambil koleksi peserta rapat resmi (Guru/GTK).
     */
    public function getPesertaList()
    {
        if ($this->peserta_tipe === 'terpilih' && !empty($this->peserta_ids)) {
            return Guru::whereIn('id', (array) $this->peserta_ids)
                ->where('status', 'aktif')
                ->orderBy('nama')
                ->get();
        }

        if ($this->peserta_tipe === 'tendik') {
            return Guru::where('status', 'aktif')
                ->where(function ($q) {
                    $q->where('jenis_ptk', 'like', '%tata usaha%')
                      ->orWhere('jenis_ptk', 'like', '%tendik%')
                      ->orWhere('jenis_ptk', 'like', '%tenaga kependidikan%')
                      ->orWhere('jabatan', 'like', '%tata usaha%');
                })
                ->orderBy('nama')
                ->get();
        }

        // Default 'semua_gtk' atau 'guru'
        return Guru::where('status', 'aktif')
            ->orderBy('nama')
            ->get();
    }
}
