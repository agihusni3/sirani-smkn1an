<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KasusDisiplinPelanggaran extends Model
{
    use HasFactory;

    protected $table = 'kasus_disiplin_pelanggarans';

    protected $fillable = [
        'kasus_disiplin_id',
        'siswa_id',
        'katalog_pelanggaran_id',
        'nama_pelanggaran',
        'poin_ditambah',
        'tanggal',
        'dicatat_oleh',
        'catatan',
    ];

    protected $casts = [
        'poin_ditambah' => 'integer',
        'tanggal'       => 'date:Y-m-d',
    ];

    public function kasusDisiplin(): BelongsTo
    {
        return $this->belongsTo(KasusDisiplin::class, 'kasus_disiplin_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function katalogPelanggaran(): BelongsTo
    {
        return $this->belongsTo(KatalogPelanggaran::class, 'katalog_pelanggaran_id');
    }
}
