<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AkademikAsesmenOnline extends Model {
    protected $table = 'akademik_asesmen_onlines';
    protected $fillable = ['distribusi_id','judul','deskripsi','jenis','semester','durasi_menit','dibuka_pada','ditutup_pada','acak_soal','tampilkan_nilai','is_active','passing_grade'];
    protected $casts = ['dibuka_pada'=>'datetime','ditutup_pada'=>'datetime','acak_soal'=>'boolean','tampilkan_nilai'=>'boolean','is_active'=>'boolean'];

    public function distribusi(): BelongsTo { return $this->belongsTo(AkademikDistribusiMengajar::class, 'distribusi_id'); }
    public function soals(): HasMany { return $this->hasMany(AkademikAsesmenSoal::class, 'asesmen_id'); }
    public function hasils(): HasMany { return $this->hasMany(AkademikAsesmenHasil::class, 'asesmen_id'); }

    public function getTotalSoalAttribute(): int { return $this->soals()->count(); }
    public function getTotalPesertaAttribute(): int { return $this->hasils()->where('is_selesai', true)->count(); }
    public function isOpen(): bool { $now = now(); return $this->is_active && (!$this->dibuka_pada || $now->gte($this->dibuka_pada)) && (!$this->ditutup_pada || $now->lte($this->ditutup_pada)); }
    public function getJenisLabelAttribute(): string {
        return match($this->jenis) { 'kuis'=>'Kuis', 'ulangan_harian'=>'Ulangan Harian', 'pts'=>'PTS', 'pas'=>'PAS', 'tugas'=>'Tugas', default=>$this->jenis };
    }
}
