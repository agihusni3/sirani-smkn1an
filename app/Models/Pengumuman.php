<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumumans';

    protected $fillable = [
        'judul',
        'isi_pesan',
        'banner_gambar',
        'kategori',
        'target_tipe',
        'target_id',
        'target_nama',
        'kirim_wa',
        'target_penerima_wa',
        'tampil_portal',
        'tampil_kios',
        'is_active',
        'tanggal_mulai',
        'tanggal_selesai',
        'total_target',
        'total_terkirim',
        'status_pengiriman',
        'created_by',
    ];

    protected $appends = ['banner_url'];

    protected $casts = [
        'kirim_wa'        => 'boolean',
        'tampil_portal'   => 'boolean',
        'tampil_kios'     => 'boolean',
        'is_active'       => 'boolean',
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function getBannerUrlAttribute(): ?string
    {
        if ($this->banner_gambar && file_exists(public_path('storage/' . $this->banner_gambar))) {
            return asset('storage/' . $this->banner_gambar);
        }
        return null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope untuk pengumuman aktif hari ini
     */
    public function scopeActiveToday($query)
    {
        $today = Carbon::today()->toDateString();
        return $query->where('is_active', true)
            ->where(function ($q) use ($today) {
                $q->whereNull('tanggal_mulai')->orWhereDate('tanggal_mulai', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('tanggal_selesai')->orWhereDate('tanggal_selesai', '>=', $today);
            });
    }

    /**
     * Scope untuk portal ortu/siswa
     */
    public function scopeForPortal($query)
    {
        return $query->activeToday()->where('tampil_portal', true);
    }

    /**
     * Scope untuk Kios Gerbang
     */
    public function scopeForKios($query)
    {
        return $query->activeToday()->where('tampil_kios', true);
    }

    public function getKategoriBadgeAttribute(): array
    {
        return match ($this->kategori) {
            'darurat'      => ['bg' => 'rgba(220,38,38,0.15)', 'color' => '#DC2626', 'label' => 'PENTING / DARURAT', 'icon' => 'bi-exclamation-triangle-fill'],
            'kedisiplinan' => ['bg' => 'rgba(234,179,8,0.15)', 'color' => '#CA8A04', 'label' => 'KEDISIPLINAN', 'icon' => 'bi-shield-check'],
            'kegiatan'     => ['bg' => 'rgba(59,130,246,0.15)', 'color' => '#2563EB', 'label' => 'KEGIATAN SEKOLAH', 'icon' => 'bi-calendar-event-fill'],
            'akademik'     => ['bg' => 'rgba(147,51,234,0.15)', 'color' => '#7E22CE', 'label' => 'AKADEMIK & KBM', 'icon' => 'bi-book-fill'],
            default        => ['bg' => 'rgba(16,185,129,0.15)', 'color' => '#10B981', 'label' => 'PENGUMUMAN UMUM', 'icon' => 'bi-megaphone-fill'],
        };
    }
}
