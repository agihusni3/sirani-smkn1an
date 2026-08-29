<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanSekolah extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_sekolahs';

    protected $fillable = [
        'nama_instansi_atas',
        'nama_dinas',
        'nama_sekolah',
        'npsn',
        'alamat',
        'desa_kelurahan',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'kode_pos',
        'telepon',
        'email',
        'website',
        'nama_kepala_sekolah',
        'nip_kepala_sekolah',
        'logo_sekolah',
        'template_piagam',
        'template_piagam_config',
    ];

    /**
     * Ambil pengaturan profil sekolah aktif (Singleton).
     */
    public static function getAktif(): self
    {
        $setting = self::first();
        if (!$setting) {
            $setting = self::create([
                'nama_instansi_atas' => 'PEMERINTAH PROVINSI LAMPUNG',
                'nama_dinas'          => 'DINAS PENDIDIKAN DAN KEBUDAYAAN',
                'nama_sekolah'        => 'SMK NEGERI 1 AIR NANINGAN',
                'npsn'                => '69888999',
                'alamat'              => 'Jl. Raya Air Naningan, Kec. Air Naningan',
                'desa_kelurahan'      => 'Air Naningan',
                'kecamatan'           => 'Air Naningan',
                'kabupaten'           => 'Kab. Tanggamus',
                'provinsi'            => 'Lampung',
                'kode_pos'            => '35379',
                'telepon'             => '(0721) 123456',
                'email'               => 'smkn1airnaningan@gmail.com',
                'website'             => 'smkn1airnaningan.sch.id',
                'nama_kepala_sekolah' => 'Drs. H. Ahmad Sudrajat, M.Pd.',
                'nip_kepala_sekolah'  => '19750510 200003 1 005',
            ]);
        }
        return $setting;
    }

    /**
     * Format alamat lengkap sekolah (termasuk Desa, Kecamatan, Kab, Provinsi, Kode Pos).
     */
    public function getAlamatLengkapAttribute(): string
    {
        $parts = [];
        if (!empty($this->alamat)) {
            $parts[] = rtrim($this->alamat, ', ');
        }
        if (!empty($this->kecamatan) && !str_contains($this->alamat ?? '', $this->kecamatan)) {
            $parts[] = 'Kec. ' . $this->kecamatan;
        }
        if (!empty($this->kabupaten) && !str_contains($this->alamat ?? '', $this->kabupaten)) {
            $parts[] = $this->kabupaten;
        }
        if (!empty($this->provinsi) && !str_contains($this->alamat ?? '', $this->provinsi)) {
            $parts[] = $this->provinsi;
        }

        $alamatStr = implode(', ', $parts);
        if (!empty($this->kode_pos) && !str_contains($alamatStr, $this->kode_pos)) {
            $alamatStr .= ' ' . $this->kode_pos;
        }

        return $alamatStr;
    }
}
