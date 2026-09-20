<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AkademikPklTempat extends Model {
    protected $table = 'akademik_pkl_tempats';
    protected $fillable = ['nama_dudi','bidang_usaha','alamat','kota','nama_pembimbing_dudi','kontak_dudi','is_active','catatan'];
    protected $casts = ['is_active' => 'boolean'];

    public function siswaPkls(): HasMany { return $this->hasMany(AkademikPklSiswa::class, 'pkl_tempat_id'); }
    public function getTotalSiswaAktifAttribute(): int { return $this->siswaPkls()->where('status','aktif')->count(); }
}
