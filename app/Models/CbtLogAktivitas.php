<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CbtLogAktivitas extends Model
{
    use HasFactory;

    protected $table = 'cbt_log_aktivitas';

    protected $fillable = [
        'peserta_ujian_id',
        'tipe_event',
        'detail',
        'waktu_catat',
    ];

    protected $casts = [
        'waktu_catat' => 'datetime',
    ];

    public function peserta()
    {
        return $this->belongsTo(CbtPesertaUjian::class, 'peserta_ujian_id');
    }
}
