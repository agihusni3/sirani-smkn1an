<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AkademikKktpItem extends Model
{
    use HasFactory;

    protected $table = 'akademik_kktp_items';

    protected $fillable = [
        'perangkat_id',
        'atp_item_id',
        'pendekatan',
        'keterangan_tuntas',
        'keterangan_remedial',
        'skala_kriteria',
    ];

    protected $casts = [
        'skala_kriteria' => 'array',
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
