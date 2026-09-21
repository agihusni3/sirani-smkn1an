<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AkademikModulAjar extends Model
{
    use HasFactory;

    protected $table = 'akademik_modul_ajars';

    protected $fillable = [
        'perangkat_id',
        'atp_item_id',
        'judul_modul',
        'pertemuan_ke_mulai',
        'pertemuan_ke_selesai',
        'alokasi_jp',
        'model_pembelajaran',
        'metode_pembelajaran',
        'pemahaman_bermakna',
        'pertanyaan_pemantik',
        'kegiatan_pendahuluan',
        'kegiatan_inti',
        'kegiatan_penutup',
        'refleksi_guru_siswa',
        'file_modul_pdf',
        'file_lkpd_pdf',
        'file_jobsheet_praktik',
        'link_media_pembelajaran',
    ];

    protected $casts = [
        'pertemuan_ke_mulai' => 'integer',
        'pertemuan_ke_selesai' => 'integer',
        'alokasi_jp' => 'integer',
    ];

    public function perangkatAjar(): BelongsTo
    {
        return $this->belongsTo(AkademikPerangkatAjar::class, 'perangkat_id');
    }

    public function atpItem(): BelongsTo
    {
        return $this->belongsTo(AkademikAtpItem::class, 'atp_item_id');
    }
}
