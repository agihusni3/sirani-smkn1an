<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;

    protected $table = 'jurusans';

    protected $fillable = [
        'kode_jurusan',
        'nama_jurusan',
    ];

    public function rombels(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Rombel::class, 'jurusan_id');
    }
}
