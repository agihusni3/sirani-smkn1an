<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AkademikPklSiswa extends Model {
    protected $table = 'akademik_pkl_siswas';
    protected $fillable = ['siswa_id','pkl_tempat_id','guru_pembimbing_id','tahun_ajaran_id','tanggal_mulai','tanggal_selesai','status','nilai_pkl','predikat_pkl','catatan'];
    protected $casts = ['tanggal_mulai'=>'date','tanggal_selesai'=>'date','nilai_pkl'=>'decimal:2'];

    public function siswa(): BelongsTo { return $this->belongsTo(Siswa::class, 'siswa_id'); }
    public function pklTempat(): BelongsTo { return $this->belongsTo(AkademikPklTempat::class, 'pkl_tempat_id'); }
    public function guruPembimbing(): BelongsTo { return $this->belongsTo(Guru::class, 'guru_pembimbing_id'); }
    public function tahunAjaran(): BelongsTo { return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id'); }
}
