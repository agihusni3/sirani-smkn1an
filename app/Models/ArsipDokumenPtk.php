<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArsipDokumenPtk extends Model
{
    use HasFactory;

    protected $fillable = [
        'guru_id',
        'buku_sk_id',
        'kategori_berkas',
        'nama_dokumen',
        'nomor_dokumen',
        'tanggal_dokumen',
        'file_path',
    ];

    protected $casts = [
        'tanggal_dokumen' => 'date',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function bukuSk()
    {
        return $this->belongsTo(BukuSkKepsek::class, 'buku_sk_id');
    }

    public function getFileUrlAttribute(): string
    {
        if (!$this->file_path) return '#';
        if (str_starts_with($this->file_path, 'http')) {
            return $this->file_path;
        }
        return route('situan.ekabinet.file', ['type' => 'ptk', 'id' => $this->id]);
    }
}
