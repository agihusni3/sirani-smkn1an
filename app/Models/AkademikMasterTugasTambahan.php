<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AkademikMasterTugasTambahan extends Model
{
    protected $table = 'akademik_master_tugas_tambahans';

    protected $fillable = [
        'nama_tugas',
        'ekuivalensi_jam',
        'kategori',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'ekuivalensi_jam' => 'integer',
        'urutan' => 'integer',
        'is_active' => 'boolean',
    ];
}
