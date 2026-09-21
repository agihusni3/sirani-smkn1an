<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AkademikAtpItem extends Model
{
    use HasFactory;

    protected $table = 'akademik_atp_items';

    protected $fillable = [
        'perangkat_id',
        'urutan',
        'kode_tp',
        'elemen_cp',
        'tujuan_pembelajaran',
        'materi_pokok',
        'alokasi_jp',
        'profil_pancasila',
        'semester',
        'asesmen_rencana',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'alokasi_jp' => 'integer',
        'semester' => 'integer',
    ];

    public function perangkatAjar(): BelongsTo
    {
        return $this->belongsTo(AkademikPerangkatAjar::class, 'perangkat_id');
    }

    public function modulAjars(): HasMany
    {
        return $this->hasMany(AkademikModulAjar::class, 'atp_item_id');
    }

    public function kktpItem(): BelongsTo
    {
        return $this->belongsTo(AkademikKktpItem::class, 'id', 'atp_item_id');
    }
}
