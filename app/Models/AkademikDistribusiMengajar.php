<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AkademikDistribusiMengajar extends Model {
    protected $table = 'akademik_distribusi_mengajars';
    protected $fillable = ['tahun_ajaran_id','guru_id','mata_pelajaran_id','rombel_id','semester','total_jam_per_minggu','catatan'];

    public function tahunAjaran(): BelongsTo { return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id'); }
    public function guru(): BelongsTo { return $this->belongsTo(Guru::class, 'guru_id'); }
    public function mataPelajaran(): BelongsTo { return $this->belongsTo(AkademikMataPelajaran::class, 'mata_pelajaran_id'); }
    public function rombel(): BelongsTo { return $this->belongsTo(Rombel::class, 'rombel_id'); }
    public function jurnalKbms(): HasMany { return $this->hasMany(AkademikJurnalKbm::class, 'distribusi_id'); }
    public function nilais(): HasMany { return $this->hasMany(AkademikNilai::class, 'distribusi_id'); }
    public function legerss(): HasMany { return $this->hasMany(AkademikLeger::class, 'distribusi_id'); }
    public function asesmenOnlines(): HasMany { return $this->hasMany(AkademikAsesmenOnline::class, 'distribusi_id'); }
}
