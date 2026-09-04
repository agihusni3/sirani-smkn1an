<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;

    protected $table = 'jurusans';

    protected $fillable = [
        'kode_jurusan',
        'nama_jurusan',
    ];

    public function rombels(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Rombel::class, 'jurusan_id');
    }

    public function getKodeAttribute(): string
    {
        return $this->kode_jurusan ?? '';
    }

    public function getProfilMetadata(): array
    {
        $profil = $this->profil;
        return [
            'tagline' => $profil['tagline'] ?? '',
            'deskripsi' => $profil['deskripsi'] ?? '',
            'icon' => match ($this->kode_jurusan) {
                'RPL' => 'fa-solid fa-code',
                'APHP' => 'fa-solid fa-seedling',
                'TSM' => 'fa-solid fa-wrench',
                default => 'fa-solid fa-graduation-cap'
            },
            'badge_color' => $profil['badge_color'] ?? '#3b82f6',
            'gradient' => $profil['gradient'] ?? '',
            'kompetensi' => $profil['kompetensi'] ?? [],
            'karir' => $profil['prospek_karir'] ?? [],
        ];
    }

    public function getProfilAttribute(): array
    {
        return match ($this->kode_jurusan) {
            'RPL' => [
                'tagline'     => 'Coding, Software, & Era Industri 4.0',
                'deskripsi'   => 'Membekali siswa dengan keahlian rekayasa perangkat lunak, pemrograman web dan mobile, basis data, serta kecerdasan buatan untuk siap berkarir di industri teknologi modern.',
                'icon'        => 'bi-laptop',
                'badge_color' => '#2563EB',
                'gradient'    => 'linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%)',
                'kompetensi'  => [
                    'Pemrograman Web & Framework Modern',
                    'Pemrograman Aplikasi Mobile (Android/iOS)',
                    'Manajemen Basis Data Relasional & Cloud',
                    'UI/UX Design & Frontend Engineering',
                    'Pengujian Perangkat Lunak & Deployment'
                ],
                'prospek_karir' => [
                    'Web & Software Developer',
                    'Mobile Application Developer',
                    'Database Administrator',
                    'IT Technical Support',
                    'Technopreneur / Freelancer Digital'
                ],
            ],
            'APHP' => [
                'tagline'     => 'Inovasi Teknologi Pangan Hasil Tani Lokal',
                'deskripsi'   => 'Fokus pada pengolahan komoditas hasil pertanian lokal menjadi produk pangan berkualitas bernilai ekonomi tinggi, higienis, dan berstandar keamanan pangan nasional.',
                'icon'        => 'bi-flower1',
                'badge_color' => '#10B981',
                'gradient'    => 'linear-gradient(135deg, #065F46 0%, #10B981 100%)',
                'kompetensi'  => [
                    'Pengolahan Hasil Nabati & Hewani',
                    'Pengendalian Mutu & Keamanan Pangan (HACCP)',
                    'Teknik Pengemasan & Pengawetan Makanan',
                    'Uji Laboratorium Kimia & Organoleptik Pangan',
                    'Manajemen Bisnis Teaching Factory Olahan Pangan'
                ],
                'prospek_karir' => [
                    'Quality Control / QA Industri Pangan',
                    'Operator Mesin Produksi Makanan & Minuman',
                    'Wirausahawan Kuliner & Pangan Olahan Mandiri',
                    'Laboran Uji Mutu Bahan Pangan',
                    'Penyuluh Pertanian & Pasca Panen'
                ],
            ],
            'TSM' => [
                'tagline'     => 'Teknologi Otomotif & Standar Industri Pabrikan',
                'deskripsi'   => 'Mencetak teknisi ahli sepeda motor handal yang menguasai sistem kelistrikan, mesin bahan bakar injeksi, chasis, serta manajemen bengkel berstandar industri modern.',
                'icon'        => 'bi-gear-wide-connected',
                'badge_color' => '#F59E0B',
                'gradient'    => 'linear-gradient(135deg, #B45309 0%, #F59E0B 100%)',
                'kompetensi'  => [
                    'Perawatan & Perbaikan Mesin Sepeda Motor',
                    'Sistem Injeksi Elektronik & Diagnosis Scanner',
                    'Sistem Kelistrikan & Penerangan Kendaraan',
                    'Sistem Pemindah Tenaga, Rem & Suspensi',
                    'Manajemen Bengkel Otomotif & Layanan Servis'
                ],
                'prospek_karir' => [
                    'Mekanik / Teknisi Bengkel Resmi Sepeda Motor',
                    'Service Advisor & Part Analyst Dealer Otomotif',
                    'Pemilik Bengkel & Usaha Modifikasi Mandiri',
                    'Operator Perakitan Pabrik Otomotif',
                    'Konsultan Perawatan Armada Kendaraan'
                ],
            ],
            default => [
                'tagline'       => 'Pendidikan Kejuruan Berkualitas',
                'deskripsi'     => 'Kompetensi keahlian unggulan di SMK Negeri 1 Air Naningan yang siap mencetak lulusan berkarakter dan siap kerja.',
                'icon'          => 'bi-mortarboard',
                'badge_color'   => '#6366F1',
                'gradient'      => 'linear-gradient(135deg, #4338CA 0%, #6366F1 100%)',
                'kompetensi'    => ['Keahlian Kejuruan Praktis', 'Praktik Industri', 'Etos Kerja'],
                'prospek_karir' => ['Tenaga Kerja Terampil', 'Wirausahawan Mandiri', 'Melanjutkan Pendidikan'],
            ],
        };
    }
}
