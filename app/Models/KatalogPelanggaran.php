<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KatalogPelanggaran extends Model
{
    use HasFactory;

    protected $table = 'katalog_pelanggarans';

    protected $fillable = [
        'nama_pelanggaran',
        'kategori',
        'poin_pelanggaran',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'poin_pelanggaran' => 'integer',
        'is_active'        => 'boolean',
    ];

    public function riwayatPelanggarans(): HasMany
    {
        return $this->hasMany(KasusDisiplinPelanggaran::class, 'katalog_pelanggaran_id');
    }

    /**
     * Seed katalog pelanggaran standar tata tertib sekolah lengkap jika masih kosong atau sinkronisasi.
     */
    public static function seedDefault(): void
    {
        if (self::count() === 0) {
            self::seedComprehensive();
        }
    }

    /**
     * Seeder komprehensif referensi Buku Saku Tata Tertib SMK & Kemdikbudristek.
     */
    public static function seedComprehensive(): void
    {
        $setting = PengaturanDisiplin::getPengaturan();

        $items = [
            // KATEGORI 1: PRESENSI & KEHADIRAN
            [
                'nama_pelanggaran' => 'Keterlambatan Hadir (>07:15 WIB)',
                'kategori'         => 'presensi',
                'poin_pelanggaran' => (int) $setting->bobot_terlambat,
                'deskripsi'        => 'Tiba di sekolah melebihi batas jam masuk gerbang resmi.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Alpha / Tidak Hadir Tanpa Keterangan',
                'kategori'         => 'presensi',
                'poin_pelanggaran' => (int) $setting->bobot_alpha,
                'deskripsi'        => 'Tidak masuk sekolah dan tidak mengirimkan surat perizinan sah.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Bolos / Meninggalkan Sekolah Tanpa Izin',
                'kategori'         => 'presensi',
                'poin_pelanggaran' => (int) $setting->bobot_bolos,
                'deskripsi'        => 'Keluar dari lingkungan sekolah saat jam KBM tanpa surat izin piket.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Tidak Mengikuti Upacara Bendera / Apel Pagi',
                'kategori'         => 'presensi',
                'poin_pelanggaran' => 5,
                'deskripsi'        => 'Sengaja bersembunyi di kelas/kantin saat upacara bendera hari Senin atau peringatan nasional.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Terlambat Masuk Kelas Setelah Jam Istirahat',
                'kategori'         => 'presensi',
                'poin_pelanggaran' => 3,
                'deskripsi'        => 'Masuk ruang kelas/bengkel melebihi 10 menit setelah bel istirahat berbunyi.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Membuat Surat Izin Palsu / Memalsukan Tanda Tangan Ortu',
                'kategori'         => 'presensi',
                'poin_pelanggaran' => 20,
                'deskripsi'        => 'Menulis surat izin sendiri atau memalsukan tanda tangan orang tua/wali murid.',
                'is_active'        => true,
            ],

            // KATEGORI 2: TATA TERTIB, SERAGAM & KERAPIAN
            [
                'nama_pelanggaran' => 'Atribut Seragam Tidak Lengkap (Dasi/Sabuk/Topi/Bet)',
                'kategori'         => 'tata_tertib',
                'poin_pelanggaran' => 5,
                'deskripsi'        => 'Tidak memakai dasi, sabuk logo sekolah, bet lokasi/jurusan, atau topi saat upacara.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Memakai Seragam Tidak Sesuai Ketentuan Hari / Tidak Rapi',
                'kategori'         => 'tata_tertib',
                'poin_pelanggaran' => 5,
                'deskripsi'        => 'Baju tidak dimasukkan, mengenakan seragam hari lain, atau seragam tidak disetrika rapi.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Celana / Rok Tidak Standar (Model Pensil/Ketat/Sobek/Mini)',
                'kategori'         => 'tata_tertib',
                'poin_pelanggaran' => 10,
                'deskripsi'        => 'Memodifikasi celana menjadi cutbray/pensil ketat atau rok di atas lutut tidak standar dinas.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Memakai Sepatu / Kaos Kaki Tidak Sesuai Aturan',
                'kategori'         => 'tata_tertib',
                'poin_pelanggaran' => 3,
                'deskripsi'        => 'Memakai sepatu selain warna hitam dominan atau tidak memakai kaos kaki putih/hitam sesuai jadwal.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Rambut Tidak Rapi / Gondrong / Model Punk (Siswa Putra)',
                'kategori'         => 'tata_tertib',
                'poin_pelanggaran' => 5,
                'deskripsi'        => 'Panjang rambut melebihi kerah baju, menutupi daun telinga, atau potongan tidak rapi.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Mengecat / Mewarnai Rambut Selain Warna Hitam',
                'kategori'         => 'tata_tertib',
                'poin_pelanggaran' => 15,
                'deskripsi'        => 'Menyemir rambut dengan warna pirang, merah, cokelat, atau warna selain hitam alami.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Memakai Make-up Berlebihan / Cat Kuku / Kutek / Perhiasan Emas',
                'kategori'         => 'tata_tertib',
                'poin_pelanggaran' => 5,
                'deskripsi'        => 'Berdandan berlebihan (lipstik mencolok, blush on tebal) atau memakai kutek kuku.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Memakai Tindik (Siswa Putra) / Tato (Permanen / Temporer)',
                'kategori'         => 'tata_tertib',
                'poin_pelanggaran' => 25,
                'deskripsi'        => 'Memakai anting/tindik di telinga/lidah/hidung atau memiliki rajah tato di badan.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Tidak Memakai Wearpack / APD Lengkap Saat Praktik Bengkel',
                'kategori'         => 'tata_tertib',
                'poin_pelanggaran' => 10,
                'deskripsi'        => 'Tidak mengenakan pakaian kerja wearpack dan sepatu keselamatan saat praktik di bengkel.',
                'is_active'        => true,
            ],

            // KATEGORI 3: SIKAP & PERILAKU KBM
            [
                'nama_pelanggaran' => 'Mengoperasikan HP / Main Game Saat KBM Tanpa Izin Guru',
                'kategori'         => 'sikap',
                'poin_pelanggaran' => 10,
                'deskripsi'        => 'Bermain game, media sosial, atau menonton video saat jam belajar mengajar aktif.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Makan / Minum di Kelas Saat Pembelajaran Berlangsung',
                'kategori'         => 'sikap',
                'poin_pelanggaran' => 3,
                'deskripsi'        => 'Makan snack atau makanan berat di meja saat guru sedang menjelaskan materi.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Tidur di Kelas Saat Jam Pembelajaran',
                'kategori'         => 'sikap',
                'poin_pelanggaran' => 3,
                'deskripsi'        => 'Sengaja tidur di meja kelas saat aktivitas belajar mengajar berlangsung.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Mengabaikan / Tidak Mengumpulkan Tugas Sekolah Berulang Kali',
                'kategori'         => 'sikap',
                'poin_pelanggaran' => 5,
                'deskripsi'        => 'Tidak mengumpulkan tugas harian, pekerjaan rumah, atau laporan praktikum kejuruan.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Berkata Kotor / Kasar / Berteriak Tidak Sopan di Sekolah',
                'kategori'         => 'sikap',
                'poin_pelanggaran' => 5,
                'deskripsi'        => 'Mengeluarkan kata-kata makian, umpatan, atau bahasa tidak senonoh di area sekolah.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Bersikap Tidak Sopan / Membantah Perintah Guru atau Staf',
                'kategori'         => 'sikap',
                'poin_pelanggaran' => 20,
                'deskripsi'        => 'Menunjukkan gestur menantang, membentak, atau tidak menghargai guru/tenaga kependidikan.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Pacaran Berlebihan di Lingkungan Sekolah',
                'kategori'         => 'sikap',
                'poin_pelanggaran' => 20,
                'deskripsi'        => 'Berduaan di tempat sepi atau melakukan tindakan tidak pantas di area sekolah.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Mencoret-coret Fasilitas Sekolah / Dinding / Meja (Vandalisme)',
                'kategori'         => 'sikap',
                'poin_pelanggaran' => 15,
                'deskripsi'        => 'Mencoret bangku, dinding toilet, pintu, atau peralatan bengkel dengan spidol/cat semprot.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Membuang Sampah Sembarangan / Mengotori Bengkel & Kelas',
                'kategori'         => 'sikap',
                'poin_pelanggaran' => 3,
                'deskripsi'        => 'Meninggalkan bungkus plastik, botol, atau sisa makanan di laci meja / lantai.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Membawa Motor Knalpot Brong / Racing / Ugal-ugalan di Sekolah',
                'kategori'         => 'sikap',
                'poin_pelanggaran' => 20,
                'deskripsi'        => 'Mengendarai sepeda motor bising tidak standar atau mengebut di area sekolah.',
                'is_active'        => true,
            ],

            // KATEGORI 4: PELANGGARAN BERAT
            [
                'nama_pelanggaran' => 'Membawa, Menghisap Rokok / Vape / Pod di Lingkungan Sekolah',
                'kategori'         => 'berat',
                'poin_pelanggaran' => 30,
                'deskripsi'        => 'Kedapatan membawa atau merokok/vape di toilet, kantin, bengkel, atau radius 100m dari sekolah.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Perundungan / Bullying / Intimidasi / Cyberbullying',
                'kategori'         => 'berat',
                'poin_pelanggaran' => 50,
                'deskripsi'        => 'Melakukan pelecehan verbal, pemalakan, penganiayaan psikologis, atau penghinaan di medsos.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Berkelahi / Terlibat Tawuran Antar Pelajar',
                'kategori'         => 'berat',
                'poin_pelanggaran' => 75,
                'deskripsi'        => 'Terlibat aksi baku hantam fisik antar perorangan maupun kelompok siswa di dalam/luar sekolah.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Membawa Senjata Tajam / Pemukul / Benda Berbahaya',
                'kategori'         => 'berat',
                'poin_pelanggaran' => 50,
                'deskripsi'        => 'Membawa pisau, celurit, gir motor, atau benda tajam tanpa kaitan resmi dengan KBM.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Menyimpan, Menonton, atau Menyebarkan Konten Pornografi',
                'kategori'         => 'berat',
                'poin_pelanggaran' => 50,
                'deskripsi'        => 'Memiliki atau menyebarkan video/gambar asusila di HP atau perangkat sekolah.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Melakukan Tindakan Perjudian (Online / Offline) di Sekolah',
                'kategori'         => 'berat',
                'poin_pelanggaran' => 40,
                'deskripsi'        => 'Bermain judi kartu, domino, taruhan uang, atau mengakses slot judi online di sekolah.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Memeras / Memalak Uang atau Barang Siswa Lain',
                'kategori'         => 'berat',
                'poin_pelanggaran' => 40,
                'deskripsi'        => 'Memaksa siswa lain menyerahkan uang jajan, barang berharga, atau makanan dengan ancaman.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Melakukan Tindakan Pencurian di Lingkungan Sekolah',
                'kategori'         => 'berat',
                'poin_pelanggaran' => 60,
                'deskripsi'        => 'Mengambil uang, HP, helm, suku cadang bengkel, atau barang milik orang lain/sekolah.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Membawa / Mengonsumsi / Mengedarkan Minuman Keras atau Narkoba',
                'kategori'         => 'berat',
                'poin_pelanggaran' => 100,
                'deskripsi'        => 'Membawa atau mabuk miras, obat terlarang, lem aibon, narkotika, atau zat adiktif terlarang.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Melakukan Tindakan Pelecehan Seksual / Asusila Berat',
                'kategori'         => 'berat',
                'poin_pelanggaran' => 100,
                'deskripsi'        => 'Melakukan kontak fisik asusila tanpa persetujuan atau pelecehan seksual di lingkungan sekolah.',
                'is_active'        => true,
            ],
            [
                'nama_pelanggaran' => 'Melawan Guru / Kepala Sekolah dengan Kekerasan Fisik / Ancaman',
                'kategori'         => 'berat',
                'poin_pelanggaran' => 100,
                'deskripsi'        => 'Melakukan penyerangan fisik, pemukulan, atau ancaman pembunuhan terhadap guru/staf sekolah.',
                'is_active'        => true,
            ],
        ];

        foreach ($items as $item) {
            self::updateOrCreate(
                ['nama_pelanggaran' => $item['nama_pelanggaran']],
                $item
            );
        }
    }
}
