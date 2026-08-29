<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KasusDisiplinReward extends Model
{
    use HasFactory;

    protected $table = 'kasus_disiplin_rewards';

    protected $fillable = [
        'kasus_disiplin_id',
        'siswa_id',
        'katalog_reward_id',
        'nama_tindakan',
        'poin_dikurangi',
        'tanggal',
        'dicatat_oleh',
        'catatan',
    ];

    protected $casts = [
        'poin_dikurangi' => 'integer',
        'tanggal'        => 'date:Y-m-d',
    ];

    public function kasusDisiplin(): BelongsTo
    {
        return $this->belongsTo(KasusDisiplin::class, 'kasus_disiplin_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function katalogReward(): BelongsTo
    {
        return $this->belongsTo(KatalogReward::class, 'katalog_reward_id');
    }
}
