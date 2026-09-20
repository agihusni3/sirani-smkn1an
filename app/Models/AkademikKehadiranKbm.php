<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AkademikKehadiranKbm extends Model {
    protected $table = 'akademik_kehadiran_kbms';
    protected $fillable = ['jurnal_id','siswa_id','status','keterangan'];

    public function jurnal(): BelongsTo { return $this->belongsTo(AkademikJurnalKbm::class, 'jurnal_id'); }
    public function siswa(): BelongsTo { return $this->belongsTo(Siswa::class, 'siswa_id'); }
}
