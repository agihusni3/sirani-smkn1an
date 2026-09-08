<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArsipSekolah extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori_arsip',
        'nama_arsip',
        'nomor_dokumen',
        'mitra_instansi',
        'tanggal_dokumen',
        'tanggal_berakhir',
        'file_path',
        'file_type',
        'file_size',
        'keterangan',
        'diunggah_oleh_user_id',
    ];

    protected $casts = [
        'tanggal_dokumen' => 'date',
        'tanggal_berakhir' => 'date',
        'file_size' => 'integer',
    ];

    public function pengunggah()
    {
        return $this->belongsTo(User::class, 'diunggah_oleh_user_id');
    }

    /**
     * Label representasi kategori berkas lembaga
     */
    public function getLabelKategoriAttribute(): string
    {
        return match ($this->kategori_arsip) {
            'akreditasi'       => 'Akreditasi Sekolah (BAN-SM)',
            'izin_operasional' => 'Izin Operasional & Pendirian',
            'mou_industri'     => 'MoU Kemitraan DUDI / Industri',
            'sertifikat_aset'  => 'Sertifikat Tanah & Aset',
            'kurikulum_kosp'   => 'Dokumen Kurikulum (KOSP)',
            'pedoman_sop'      => 'Pedoman Mutu & SOP',
            'sk_kelembagaan'   => 'SK Kelembagaan / Komite',
            default            => 'Dokumen Lainnya',
        };
    }

    /**
     * Badge CSS class untuk kategori berkas
     */
    public function getBadgeClassAttribute(): string
    {
        return match ($this->kategori_arsip) {
            'akreditasi'       => 'bg-danger-subtle text-danger border border-danger-subtle',
            'mou_industri'     => 'bg-primary-subtle text-primary border border-primary-subtle',
            'izin_operasional' => 'bg-success-subtle text-success border border-success-subtle',
            'sertifikat_aset'  => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            'kurikulum_kosp'   => 'bg-info-subtle text-info border border-info-subtle',
            default            => 'bg-secondary-subtle text-secondary',
        };
    }
}
