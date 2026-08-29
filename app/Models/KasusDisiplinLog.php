<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasusDisiplinLog extends Model
{
    use HasFactory;

    protected $table = 'kasus_disiplin_logs';

    protected $fillable = [
        'kasus_disiplin_id',
        'tahap',
        'judul_kegiatan',
        'uraian_tindakan',
        'poin_perubahan',
        'petugas_nama',
        'petugas_role',
        'tanggal_kegiatan',
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date:Y-m-d',
        'poin_perubahan'   => 'integer',
    ];

    public function kasusDisiplin(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(KasusDisiplin::class, 'kasus_disiplin_id');
    }
}
