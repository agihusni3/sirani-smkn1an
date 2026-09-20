<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AkademikNilai extends Model {
    protected $table = 'akademik_nilais';
    protected $fillable = ['distribusi_id','siswa_id','semester','jenis_penilaian','nama_penilaian','nilai','deskripsi_capaian'];
    protected $casts = ['nilai' => 'decimal:2'];

    public function distribusi(): BelongsTo { return $this->belongsTo(AkademikDistribusiMengajar::class, 'distribusi_id'); }
    public function siswa(): BelongsTo { return $this->belongsTo(Siswa::class, 'siswa_id'); }
}
