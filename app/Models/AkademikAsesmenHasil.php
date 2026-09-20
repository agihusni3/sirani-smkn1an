<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AkademikAsesmenHasil extends Model {
    protected $table = 'akademik_asesmen_hasils';
    protected $fillable = ['asesmen_id','siswa_id','jawaban','nilai','is_selesai','mulai_pada','selesai_pada','durasi_detik'];
    protected $casts = ['jawaban'=>'array','nilai'=>'decimal:2','is_selesai'=>'boolean','mulai_pada'=>'datetime','selesai_pada'=>'datetime'];

    public function asesmen(): BelongsTo { return $this->belongsTo(AkademikAsesmenOnline::class, 'asesmen_id'); }
    public function siswa(): BelongsTo { return $this->belongsTo(Siswa::class, 'siswa_id'); }

    public function hitungNilai(): float {
        $asesmen = $this->asesmen;
        $soals = $asesmen->soals()->get();
        $totalBobot = $soals->sum('bobot');
        if (!$totalBobot) return 0;
        $benar = 0;
        $jawaban = $this->jawaban ?? [];
        foreach ($soals as $s) {
            if (isset($jawaban[$s->id]) && strtoupper($jawaban[$s->id]) === strtoupper($s->kunci_jawaban ?? '')) {
                $benar += $s->bobot;
            }
        }
        return round(($benar / $totalBobot) * 100, 2);
    }
}
