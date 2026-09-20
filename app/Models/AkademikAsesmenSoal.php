<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AkademikAsesmenSoal extends Model {
    protected $table = 'akademik_asesmen_soals';
    protected $fillable = ['asesmen_id','nomor','pertanyaan','tipe','gambar_url','opsi_a','opsi_b','opsi_c','opsi_d','opsi_e','kunci_jawaban','pembahasan','bobot'];

    public function asesmen(): BelongsTo { return $this->belongsTo(AkademikAsesmenOnline::class, 'asesmen_id'); }
    public function getOpsiAttribute(): array {
        $out = [];
        foreach (['A','B','C','D','E'] as $h) { $k = 'opsi_'.strtolower($h); if ($this->$k) $out[$h] = $this->$k; }
        return $out;
    }
}
