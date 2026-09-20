<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AkademikLeger extends Model {
    protected $table = 'akademik_legerss';
    protected $fillable = ['distribusi_id','siswa_id','semester','nilai_formatif_avg','nilai_sumatif_avg','nilai_akhir','predikat','deskripsi_rapor','status_lulus','is_locked'];
    protected $casts = ['nilai_formatif_avg'=>'decimal:2','nilai_sumatif_avg'=>'decimal:2','nilai_akhir'=>'decimal:2','status_lulus'=>'boolean','is_locked'=>'boolean'];

    public function distribusi(): BelongsTo { return $this->belongsTo(AkademikDistribusiMengajar::class, 'distribusi_id'); }
    public function siswa(): BelongsTo { return $this->belongsTo(Siswa::class, 'siswa_id'); }

    public static function hitungPredikat(float $nilai): string {
        if ($nilai >= 90) return 'A';
        if ($nilai >= 80) return 'B';
        if ($nilai >= 70) return 'C';
        return 'D';
    }
}
