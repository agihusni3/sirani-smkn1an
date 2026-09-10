<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArsipDokumenSiswa extends Model
{
    use HasFactory;

    protected $table = 'arsip_dokumen_siswas';

    protected $fillable = [
        'siswa_id',
        'pelayanan_surat_id',
        'ppdb_pendaftar_id',
        'kategori_berkas',
        'nama_dokumen',
        'nomor_dokumen',
        'tanggal_dokumen',
        'file_path',
        'file_size',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_dokumen' => 'date',
        'file_size' => 'integer',
    ];

    protected $appends = [
        'formatted_file_size',
        'file_url',
    ];

    public static function getKamusKategori(): array
    {
        return [
            'ijazah_smp'       => ['label' => 'Ijazah SMP / MTs', 'icon' => 'bi-mortarboard-fill', 'badge' => 'primary'],
            'skhu_skl_smp'     => ['label' => 'SKHU / SKL SMP', 'icon' => 'bi-file-earmark-text-fill', 'badge' => 'info'],
            'akta_kelahiran'   => ['label' => 'Akta Kelahiran', 'icon' => 'bi-file-earmark-person-fill', 'badge' => 'success'],
            'kartu_keluarga'   => ['label' => 'Kartu Keluarga (KK)', 'icon' => 'bi-people-fill', 'badge' => 'warning'],
            'ktp_kia'          => ['label' => 'KTP Siswa / KIA / KTP Ortu', 'icon' => 'bi-person-badge-fill', 'badge' => 'secondary'],
            'kip_pip_pkh'      => ['label' => 'Kartu KIP / PIP / PKH / KKS', 'icon' => 'bi-award-fill', 'badge' => 'danger'],
            'piagam_prestasi'  => ['label' => 'Piagam / Sertifikat Prestasi', 'icon' => 'bi-trophy-fill', 'badge' => 'success'],
            'rapor_berkala'    => ['label' => 'Buku Rapor / Lembar Nilai', 'icon' => 'bi-journal-check', 'badge' => 'info'],
            'berkas_pkl'       => ['label' => 'Laporan / Sertifikat PKL', 'icon' => 'bi-briefcase-fill', 'badge' => 'dark'],
            'surat_keterangan' => ['label' => 'Surat Keterangan TU (Aktif/SKL)', 'icon' => 'bi-file-earmark-check-fill', 'badge' => 'primary'],
            'lainnya'          => ['label' => 'Dokumen Lainnya', 'icon' => 'bi-folder-fill', 'badge' => 'secondary'],
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function pelayananSurat(): BelongsTo
    {
        return $this->belongsTo(PelayananSurat::class, 'pelayanan_surat_id');
    }

    public function ppdbPendaftar(): BelongsTo
    {
        return $this->belongsTo(PpdbPendaftar::class, 'ppdb_pendaftar_id');
    }

    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) return '-';
        if ($this->file_size >= 1048576) {
            return number_format($this->file_size / 1048576, 2) . ' MB';
        }
        return number_format($this->file_size / 1024, 1) . ' KB';
    }

    public function getFileUrlAttribute(): string
    {
        if (!$this->file_path) return '#';
        if (str_starts_with($this->file_path, 'http')) {
            return $this->file_path;
        }
        return asset('storage/' . $this->file_path);
    }
}
