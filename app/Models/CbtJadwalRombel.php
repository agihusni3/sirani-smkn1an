<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CbtJadwalRombel extends Model
{
    use HasFactory;

    protected $table = 'cbt_jadwal_rombels';

    protected $fillable = [
        'jadwal_ujian_id',
        'rombel_id',
    ];

    public function jadwal()
    {
        return $this->belongsTo(CbtJadwalUjian::class, 'jadwal_ujian_id');
    }

    public function rombel()
    {
        return $this->belongsTo(Rombel::class, 'rombel_id');
    }
}
