<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasusDisiplinDokumen extends Model
{
    use HasFactory;

    protected $table = 'kasus_disiplin_dokumens';

    protected $fillable = [
        'kasus_disiplin_id',
        'judul_dokumen',
        'kategori',
        'tahap',
        'file_path',
        'file_type',
        'diupload_oleh',
    ];

    public function kasusDisiplin(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(KasusDisiplin::class, 'kasus_disiplin_id');
    }

    public function getTahapLabelAttribute(): string
    {
        switch ($this->tahap) {
            case 'tahap_1_wali_kelas':
                return 'Tahap 1 (Wali Kelas)';
            case 'tahap_2_bk':
                return 'Tahap 2 (Guru BK)';
            case 'tahap_3_wakasis':
                return 'Tahap 3 (Waka Kesiswaan)';
            case 'tahap_4_kepsek':
                return 'Tahap 4 (Kepala Sekolah)';
            case 'selesai_pembinaan':
                return 'Selesai Pembinaan';
            default:
                return 'Berkas Umum';
        }
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function isImage(): bool
    {
        return in_array(strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']);
    }

    public function getKategoriBadgeAttribute(): string
    {
        switch ($this->kategori) {
            case 'surat_pernyataan':
                return '<span class="badge" style="background:rgba(239,68,68,0.12); color:#DC2626; font-size:11px; font-weight:700;">Surat Pernyataan SP</span>';
            case 'foto_dokumentasi':
                return '<span class="badge" style="background:rgba(59,130,246,0.12); color:#2563EB; font-size:11px; font-weight:700;">Foto Pertemuan</span>';
            case 'berita_acara':
                return '<span class="badge" style="background:rgba(245,158,11,0.12); color:#D97706; font-size:11px; font-weight:700;">Berita Acara</span>';
            case 'surat_dokter':
                return '<span class="badge" style="background:rgba(16,185,129,0.12); color:#059669; font-size:11px; font-weight:700;">Surat Dokter</span>';
            case 'lainnya':
            default:
                return '<span class="badge" style="background:var(--bg-3); color:var(--text); font-size:11px; font-weight:700;">Berkas Lampiran</span>';
        }
    }
}
