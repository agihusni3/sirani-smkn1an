<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisposisiSurat extends Model
{
    use HasFactory;

    protected $fillable = [
        'surat_masuk_id',
        'pemberi_user_id',
        'penerima_user_id',
        'instruksi_flags',
        'catatan_kepsek',
        'batas_waktu',
        'is_read',
        'read_at',
        'is_selesai',
        'selesai_at',
        'laporan_tindak_lanjut',
        'file_tindak_lanjut',
    ];

    protected $casts = [
        'instruksi_flags' => 'array',
        'batas_waktu' => 'date',
        'is_read' => 'boolean',
        'is_selesai' => 'boolean',
        'read_at' => 'datetime',
        'selesai_at' => 'datetime',
    ];

    public function suratMasuk()
    {
        return $this->belongsTo(SuratMasuk::class, 'surat_masuk_id');
    }

    public function pemberi()
    {
        return $this->belongsTo(User::class, 'pemberi_user_id');
    }

    public function penerima()
    {
        return $this->belongsTo(User::class, 'penerima_user_id');
    }
}
