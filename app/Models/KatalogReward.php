<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KatalogReward extends Model
{
    use HasFactory;

    protected $table = 'katalog_rewards';

    protected $fillable = [
        'nama_reward',
        'kategori',
        'poin_deduksi',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'poin_deduksi' => 'integer',
        'is_active'    => 'boolean',
    ];

    public function riwayatRewards(): HasMany
    {
        return $this->hasMany(KasusDisiplinReward::class, 'katalog_reward_id');
    }

    /**
     * Seed item reward default saat pertama kali diinisialisasi atau sinkronisasi.
     */
    public static function seedDefault(): void
    {
        if (self::count() === 0) {
            self::seedComprehensive();
        }
    }

    /**
     * Seeder komprehensif katalog self-reward & restorative justice standar SMK.
     */
    public static function seedComprehensive(): void
    {
        $items = [
            // KATEGORI 1: KARAKTER, IBADAH & MORAL
            [
                'nama_reward'  => 'Menjadi Petugas Sholat Berjamaah (Imam / Muadzin / Kultum)',
                'kategori'     => 'karakter',
                'poin_deduksi' => 5,
                'deskripsi'    => 'Aktif menjadi imam, muadzin, atau mengisi kultum pada Sholat Dzuhur/Ashar/Jumat berjamaah di musholla sekolah.',
                'is_active'    => true,
            ],
            [
                'nama_reward'  => 'Hafalan Juz \'Amma / Hafalan Doa-Doa Pilihan di Depan Pembina',
                'kategori'     => 'karakter',
                'poin_deduksi' => 10,
                'deskripsi'    => 'Menyetorkan hafalan surat-surat pendek atau doa harian dengan fasih kepada guru PAI / pembina keagamaan.',
                'is_active'    => true,
            ],
            [
                'nama_reward'  => 'Tugas Karakter & Resume Buku Literasi / Motivasi Perpustakaan',
                'kategori'     => 'karakter',
                'poin_deduksi' => 10,
                'deskripsi'    => 'Menyelesaikan pembacaan dan resume buku pengembangan kepribadian/motivasi di perpustakaan sekolah.',
                'is_active'    => true,
            ],
            [
                'nama_reward'  => 'Menunjukkan Peningkatan Sikap Disiplin & Sopan Santun Nyata',
                'kategori'     => 'karakter',
                'poin_deduksi' => 10,
                'deskripsi'    => 'Mendapat apresiasi tertulis dari Wali Kelas dan Guru atas perubahan karakter dan etika yang konsisten.',
                'is_active'    => true,
            ],

            // KATEGORI 2: PRESTASI AKADEMIK & KEJURUAN
            [
                'nama_reward'  => 'Juara 1 / 2 / 3 Lomba Kompetensi Siswa (LKS) Tingkat Kabupaten',
                'kategori'     => 'prestasi',
                'poin_deduksi' => 25,
                'deskripsi'    => 'Meraih juara kejuaraan vokasi keahlian teknik tingkat kabupaten mewakili SMKN 1 Air Naningan.',
                'is_active'    => true,
            ],
            [
                'nama_reward'  => 'Juara 1 / 2 / 3 LKS / O2SN / FLS2N Tingkat Provinsi atau Nasional',
                'kategori'     => 'prestasi',
                'poin_deduksi' => 50,
                'deskripsi'    => 'Meraih medali atau penghargaan kejuaraan bergengsi tingkat provinsi maupun nasional.',
                'is_active'    => true,
            ],
            [
                'nama_reward'  => 'Juara Lomba Akademik / Non-Akademik Antar Sekolah / Umum',
                'kategori'     => 'prestasi',
                'poin_deduksi' => 20,
                'deskripsi'    => 'Memenangkan kompetisi olahraga, seni, debat, atau sains tingkat regional.',
                'is_active'    => true,
            ],
            [
                'nama_reward'  => 'Masuk Peringkat 1 s.d. 3 Besar Nilai Rapor di Kelas',
                'kategori'     => 'prestasi',
                'poin_deduksi' => 15,
                'deskripsi'    => 'Meraih peringkat terbaik dalam capaian hasil belajar semester di rombongan belajar.',
                'is_active'    => true,
            ],
            [
                'nama_reward'  => 'Hasil Sangat Memuaskan pada Uji Kompetensi Keahlian (UKK) / PKL',
                'kategori'     => 'prestasi',
                'poin_deduksi' => 15,
                'deskripsi'    => 'Memperoleh predikat Sangat Kompeten (A) dari penguji industri eksternal.',
                'is_active'    => true,
            ],

            // KATEGORI 3: KEBERSIHAN, BENGKEL & BAKTI SOSIAL
            [
                'nama_reward'  => 'Piket Kebersihan & Perawatan Bengkel Praktik / Lab Komputer',
                'kategori'     => 'kebersihan',
                'poin_deduksi' => 5,
                'deskripsi'    => 'Membantu teknisi dan kepala bengkel merapikan peralatan kerja (tool set), mesin, dan lantai bengkel.',
                'is_active'    => true,
            ],
            [
                'nama_reward'  => 'Bakti Sosial & Gotong Royong Kebersihan Lingkungan Sekolah',
                'kategori'     => 'kebersihan',
                'poin_deduksi' => 5,
                'deskripsi'    => 'Terlibat aktif dalam kegiatan Jumat Bersih, sanitasi taman, dan pengelolaan bank sampah sekolah.',
                'is_active'    => true,
            ],
            [
                'nama_reward'  => 'Menjadi Relawan Donor Darah / Satgas Bencana / PMR Sekolah',
                'kategori'     => 'kebersihan',
                'poin_deduksi' => 10,
                'deskripsi'    => 'Berpartisipasi aktif dalam kegiatan kemanusiaan dan kepalangmerahan di sekolah.',
                'is_active'    => true,
            ],
            [
                'nama_reward'  => 'Menanam & Merawat Tanaman Penghijauan / Apotek Hidup Sekolah',
                'kategori'     => 'kebersihan',
                'poin_deduksi' => 5,
                'deskripsi'    => 'Menyumbang bibit tanaman dan memelihara area hijau sekolah hingga tumbuh subur.',
                'is_active'    => true,
            ],

            // KATEGORI 4: KEDISIPLINAN & PRESENSI
            [
                'nama_reward'  => 'Zero Violation Streak (14 Hari Tepat Waktu Tanpa Pelanggaran)',
                'kategori'     => 'kehadiran',
                'poin_deduksi' => 5,
                'deskripsi'    => 'Apresiasi bagi siswa yang hadir tepat waktu dan tertib selama 14 hari efektif berturut-turut.',
                'is_active'    => true,
            ],
            [
                'nama_reward'  => 'Kehadiran Sempurna 100% Selama 1 Bulan Penuh (Tanpa Terlambat & Alpha)',
                'kategori'     => 'kehadiran',
                'poin_deduksi' => 15,
                'deskripsi'    => 'Rekor kehadiran 100% tanpa catatan keterlambatan dan tanpa alpha dalam satu bulan kalender sekolah.',
                'is_active'    => true,
            ],
            [
                'nama_reward'  => 'Melaksanakan Tugas Piket Gerbang / Baris Tertib dengan Sangat Baik',
                'kategori'     => 'kehadiran',
                'poin_deduksi' => 5,
                'deskripsi'    => 'Membantu guru piket menyambut siswa dan menertibkan barisan gerbang masuk sekolah.',
                'is_active'    => true,
            ],

            // KATEGORI 5: KONSELING & RESTORATIVE JUSTICE
            [
                'nama_reward'  => 'Konseling Tuntas & Pemenuhan Surat Komitmen Bimbingan BK',
                'kategori'     => 'konseling',
                'poin_deduksi' => 15,
                'deskripsi'    => 'Menuntaskan seluruh sesi bimbingan individual dengan Guru BK dan mematuhi komitmen tertulis.',
                'is_active'    => true,
            ],
            [
                'nama_reward'  => 'Melakukan Restorasi / Perbaikan Fasilitas yang Pernah Dirusak',
                'kategori'     => 'konseling',
                'poin_deduksi' => 15,
                'deskripsi'    => 'Memperbaiki, mengecat ulang, atau mengganti sarana sekolah yang rusak sebagai wujud tanggung jawab.',
                'is_active'    => true,
            ],
            [
                'nama_reward'  => 'Inisiator Perdamaian / Mediasi Damai Perselisihan Antar Teman',
                'kategori'     => 'konseling',
                'poin_deduksi' => 10,
                'deskripsi'    => 'Menyelesaikan konflik secara kekeluargaan tanpa melibatkan kekerasan fisik.',
                'is_active'    => true,
            ],

            // KATEGORI 6: KEPEMIMPINAN & ORGANISASI
            [
                'nama_reward'  => 'Menjadi Petugas / Pemimpin Upacara Bendera Hari Senin / Hari Besar',
                'kategori'     => 'custom',
                'poin_deduksi' => 5,
                'deskripsi'    => 'Bertugas sebagai pengibar bendera, pembaca teks pembukaan UUD 1945, dirigen, atau pemimpin upacara.',
                'is_active'    => true,
            ],
            [
                'nama_reward'  => 'Aktif Berkontribusi Sebagai Pengurus Organisasi (OSIS/Pramuka/PMR/Paskibra)',
                'kategori'     => 'custom',
                'poin_deduksi' => 10,
                'deskripsi'    => 'Menjadi panitia atau penggerak suksesnya kegiatan sekolah di bawah binaan kesiswaan.',
                'is_active'    => true,
            ],
            [
                'nama_reward'  => 'Menjadi Tutor Sebaya Membantu Praktikum Teman Seangkatan',
                'kategori'     => 'custom',
                'poin_deduksi' => 5,
                'deskripsi'    => 'Membimbing teman sekelas yang mengalami kesulitan dalam memahami job sheet praktik kejuruan.',
                'is_active'    => true,
            ],
        ];

        foreach ($items as $item) {
            self::updateOrCreate(
                ['nama_reward' => $item['nama_reward']],
                $item
            );
        }
    }
}
