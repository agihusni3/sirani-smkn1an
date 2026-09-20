<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AkademikP5bkNilai extends Model {
    protected $table = 'akademik_p5bk_nilais';
    protected $fillable = ['p5bk_proyek_id','siswa_id','beriman_bertaqwa','berkebhinekaan_global','bergotong_royong','mandiri','bernalar_kritis','kreatif','nilai_budaya_kerja','catatan'];

    public function proyek(): BelongsTo { return $this->belongsTo(AkademikP5bkProyek::class, 'p5bk_proyek_id'); }
    public function siswa(): BelongsTo { return $this->belongsTo(Siswa::class, 'siswa_id'); }

    public static function labelPredikat(int $val): string {
        return match($val) { 1=>'BB', 2=>'MB', 3=>'BSH', 4=>'SB', default=>'BB' };
    }
}
