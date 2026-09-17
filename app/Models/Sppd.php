<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sppd extends Model
{
    use HasFactory;

    protected $table = 'sppds';

    protected $fillable = [
        'surat_tugas_id',
        'surat_tugas_anggota_id',
        'guru_id',
        'nomor_sppd',
        'nama_pelaksana',
        'nip_pelaksana',
        'pangkat_golongan',
        'jabatan',
        'tingkat_biaya',
        'maksud_perjalanan',
        'alat_angkut',
        'tempat_berangkat',
        'tempat_tujuan',
        'lama_perjalanan',
        'tanggal_berangkat',
        'tanggal_harus_kembali',
        'instansi_pembeban_anggaran',
        'mata_anggaran',
        'keterangan_lain',
        'status',
        'created_by',
    ];

    protected $casts = [
        'tanggal_berangkat'     => 'date',
        'tanggal_harus_kembali' => 'date',
        'lama_perjalanan'       => 'integer',
    ];

    public function suratTugas()
    {
        return $this->belongsTo(SuratTugas::class, 'surat_tugas_id');
    }

    public function anggota()
    {
        return $this->belongsTo(SuratTugasAnggota::class, 'surat_tugas_anggota_id');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
