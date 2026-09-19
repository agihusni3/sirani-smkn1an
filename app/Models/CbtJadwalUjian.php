<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CbtJadwalUjian extends Model
{
    use HasFactory;

    protected $table = 'cbt_jadwal_ujians';

    protected $fillable = [
        'bank_soal_id',
        'guru_id',
        'parent_jadwal_id',
        'nama_ujian',
        'tipe_ujian',
        'is_remedial',
        'remedial_policy',
        'kktp',
        'waktu_mulai',
        'waktu_selesai',
        'durasi_menit',
        'token_ujian',
        'acak_soal',
        'acak_opsi',
        'tampilkan_nilai',
        'status',
    ];

    protected $casts = [
        'is_remedial'      => 'boolean',
        'kktp'             => 'float',
        'waktu_mulai'      => 'datetime',
        'waktu_selesai'    => 'datetime',
        'durasi_menit'     => 'integer',
        'acak_soal'        => 'boolean',
        'acak_opsi'        => 'boolean',
        'tampilkan_nilai'  => 'boolean',
    ];

    public static function generateToken(int $length = 6): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $token = '';
        for ($i = 0; $i < $length; $i++) {
            $token .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $token;
    }

    public function isAktifSekarang(): bool
    {
        if ($this->status !== 'aktif') {
            return false;
        }
        $now = Carbon::now();
        return $now->between($this->waktu_mulai, $this->waktu_selesai);
    }

    public function bankSoal()
    {
        return $this->belongsTo(CbtBankSoal::class, 'bank_soal_id');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function parentJadwal()
    {
        return $this->belongsTo(CbtJadwalUjian::class, 'parent_jadwal_id');
    }

    public function remedialJadwals()
    {
        return $this->hasMany(CbtJadwalUjian::class, 'parent_jadwal_id');
    }

    public function rombels()
    {
        return $this->hasMany(CbtJadwalRombel::class, 'jadwal_ujian_id');
    }

    public function pesertas()
    {
        return $this->hasMany(CbtPesertaUjian::class, 'jadwal_ujian_id');
    }
}
