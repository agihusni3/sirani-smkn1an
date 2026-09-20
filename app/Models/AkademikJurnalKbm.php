<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AkademikJurnalKbm extends Model {
    protected $table = 'akademik_jurnal_kbms';
    protected $fillable = ['distribusi_id','tanggal','pertemuan_ke','materi_ajar','metode_pembelajaran','catatan_guru','refleksi','is_published'];
    protected $casts = ['tanggal' => 'date', 'is_published' => 'boolean'];

    public function distribusi(): BelongsTo { return $this->belongsTo(AkademikDistribusiMengajar::class, 'distribusi_id'); }
    public function kehadirans(): HasMany { return $this->hasMany(AkademikKehadiranKbm::class, 'jurnal_id'); }

    public function getTotalHadirAttribute(): int { return $this->kehadirans()->where('status','hadir')->count(); }
    public function getTotalAlfaAttribute(): int { return $this->kehadirans()->where('status','alfa')->count(); }
}
