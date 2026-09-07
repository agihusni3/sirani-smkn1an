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

    protected static function booted()
    {
        static::saved(function ($model) {
            $model->syncGuruKepsek();
        });
    }

    /**
     * Sinkronisasi data Kepala Sekolah dua arah ke tabel Guru dan User.
     */
    public function syncGuruKepsek(): void
    {
        if (empty($this->nama_kepala_sekolah) && empty($this->nip_kepala_sekolah)) {
            return;
        }

        $cleanNip = !empty($this->nip_kepala_sekolah) ? preg_replace('/[^0-9]/', '', $this->nip_kepala_sekolah) : null;

        $guru = null;
        if ($cleanNip) {
            $guru = Guru::whereRaw("REPLACE(REPLACE(nip, ' ', ''), '-', '') = ?", [$cleanNip])->first();
        }

        if (!$guru && !empty($this->nama_kepala_sekolah)) {
            $namaClean = trim(explode(',', $this->nama_kepala_sekolah)[0]);
            $guru = Guru::where('nama', 'like', "%{$namaClean}%")->first();
        }

        if ($guru) {
            if ($guru->jabatan !== 'Kepala Sekolah' || $guru->tugas_tambahan !== 'Kepala Sekolah') {
                $guru->withoutEvents(function () use ($guru) {
                    $guru->update([
                        'jabatan'        => 'Kepala Sekolah',
                        'tugas_tambahan' => 'Kepala Sekolah',
                        'jenis_ptk'      => 'Kepala Sekolah',
                    ]);
                });
            }

            if ($guru->user && $guru->user->role !== 'kepala_sekolah') {
                $guru->user->update(['role' => 'kepala_sekolah']);
            }

            // Demote Kepala Sekolah lain jika ada (karena kepsek aktif hanya 1 orang)
            Guru::where('id', '!=', $guru->id)
                ->where('jabatan', 'Kepala Sekolah')
                ->each(function ($g) {
                    $g->withoutEvents(function () use ($g) {
                        $g->update([
                            'jabatan'        => 'Guru Mata Pelajaran',
                            'tugas_tambahan' => null,
                        ]);
                    });
                });
        }
    }

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
                'npsn'                => '70011825',
                'alamat'              => 'Jl. Makam Baturuguk, Pekon Karang Sari',
                'desa_kelurahan'      => 'Air Naningan',
                'kecamatan'           => 'Air Naningan',
                'kabupaten'           => 'Kab. Tanggamus',
                'provinsi'            => 'Lampung',
                'kode_pos'            => '35379',
                'telepon'             => '(0721) 892110',
                'email'               => 'smkn1airnaningan@gmail.com',
                'website'             => 'smkn1airnaningan.sch.id',
                'nama_kepala_sekolah' => 'Aprida, S.Si.',
                'nip_kepala_sekolah'  => '197904172008012019',
            ]);
        }
        return $setting;
    }

    /**
     * Dapatkan nama resmi Kepala Sekolah selalu bersumber dari Data PTK (Tabel Guru).
     */
    public function getNamaKepalaSekolahAttribute($val): string
    {
        $guru = Guru::where('jabatan', 'like', '%Kepala Sekolah%')
            ->orWhere('tugas_tambahan', 'like', '%Kepala Sekolah%')
            ->orWhere('nama', 'like', '%Aprida%')
            ->first();
        if ($guru) {
            return $guru->nama_lengkap_gelar ?: $guru->nama;
        }
        return $val ?: 'Aprida, S.Si.';
    }

    /**
     * Dapatkan NIP resmi Kepala Sekolah selalu bersumber dari Data PTK (Tabel Guru).
     */
    public function getNipKepalaSekolahAttribute($val): ?string
    {
        $guru = Guru::where('jabatan', 'like', '%Kepala Sekolah%')
            ->orWhere('tugas_tambahan', 'like', '%Kepala Sekolah%')
            ->orWhere('nama', 'like', '%Aprida%')
            ->first();
        if ($guru && !empty($guru->nip)) {
            return $guru->nip;
        }
        return $val ?: '197904172008012019';
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
