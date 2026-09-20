<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AkademikP5bkProyek extends Model {
    protected $table = 'akademik_p5bk_proyeks';
    protected $fillable = ['rombel_id','tahun_ajaran_id','semester','nama_proyek','tema','deskripsi','tanggal_mulai','tanggal_selesai'];
    protected $casts = ['tanggal_mulai'=>'date','tanggal_selesai'=>'date'];

    public function rombel(): BelongsTo { return $this->belongsTo(Rombel::class, 'rombel_id'); }
    public function tahunAjaran(): BelongsTo { return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id'); }
    public function nilais(): HasMany { return $this->hasMany(AkademikP5bkNilai::class, 'p5bk_proyek_id'); }
}
