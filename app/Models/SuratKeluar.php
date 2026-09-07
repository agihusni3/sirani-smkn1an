<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKeluar extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_agenda',
        'tahun_agenda',
        'klasifikasi_id',
        'kode_klasifikasi',
        'nomor_surat_lengkap',
        'tujuan_surat',
        'perihal',
        'tanggal_surat',
        'penandatangan',
        'jenis_surat',
        'file_arsip',
        'created_by',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
    ];

    public function klasifikasi()
    {
        return $this->belongsTo(KlasifikasiSurat::class, 'klasifikasi_id');
    }

    public function pelayanans()
    {
        return $this->hasMany(PelayananSurat::class, 'surat_keluar_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Konversi bulan angka ke Romawi untuk nomor surat dinas.
     */
    public static function romawiBulan(int $bulan): string
    {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $map[$bulan] ?? 'I';
    }

    /**
     * Generate nomor surat lengkap otomatis sesuai standar Kemendikdasmen:
     * Format: [Nomor Urut]/[Kode Klasifikasi]/SMKN1AN/[Bulan Romawi]/[Tahun]
     */
    public static function generateNomorSurat(string $kodeKlasifikasi, ?\DateTimeInterface $tanggal = null): array
    {
        $tgl = $tanggal ? \Carbon\Carbon::parse($tanggal) : now();
        $year = (int) $tgl->format('Y');
        $month = (int) $tgl->format('n');
        $romawi = static::romawiBulan($month);

        $max = static::where('tahun_agenda', $year)->max('nomor_agenda');
        $nextNomor = ($max ? (int) $max : 0) + 1;

        $padNomor = str_pad((string) $nextNomor, 3, '0', STR_PAD_LEFT);
        $nomorLengkap = "{$padNomor}/{$kodeKlasifikasi}/SMKN1AN/{$romawi}/{$year}";

        return [
            'nomor_agenda' => $nextNomor,
            'tahun_agenda' => $year,
            'nomor_surat_lengkap' => $nomorLengkap,
        ];
    }
}
