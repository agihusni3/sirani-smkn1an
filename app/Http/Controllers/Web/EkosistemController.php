<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PengaturanSekolah;
use Illuminate\Http\Request;

class EkosistemController extends Controller
{
    /**
     * Master Data Modul Ekosistem Digital SMKN 1 Air Naningan
     */
    private function getModules(): array
    {
        return [
            'sirani-core' => [
                'kode' => 'sirani-core',
                'pilar' => 'Pilar 1: Core Engine & Presensi',
                'judul' => 'SIRANI Core — Sistem Presensi & Disiplin Digital',
                'tagline' => 'Pondasi utama data presensi gerbang RFID, kesiswaan & notifikasi WhatsApp otomatis.',
                'icon' => 'fa-solid fa-id-card-clip',
                'color' => '#2563eb',
                'subtle' => '#eff6ff',
                'status' => 'aktif',
                'status_label' => 'Operasional Aktif',
                'badge_color' => '#10b981',
                'tahap' => 'Tahap 1 (Inti)',
                'sasaran' => 'Siswa, Guru, Staf TU, Tim Ketertiban & Orang Tua',
                'deskripsi' => 'SIRANI adalah sistem terpadu pengawal kedisiplinan dan kehadiran siswa berbasis kartu RFID sekolah. Dilengkapi integrasi gerbang WhatsApp notifikasi real-time ke wali murid, pencatatan poin pelanggaran & buku konseling BK, serta laporan dinas otomatis.',
                'fitur_unggulan' => [
                    'Presensi tapping kartu RFID & PIN darurat di gerbang sekolah.',
                    'Notifikasi WhatsApp otomatis ke nomor orang tua siswa detik itu juga.',
                    'Buku Disiplin Digital, reward poin prestasi, dan konseling BK.',
                    'Laporan rekapitulasi kehadiran berkala standar Dinas Pendidikan.',
                    'Meja Piket Harian digital & portal mandiri orang tua.',
                ],
                'integrasi_sirani' => 'Menjadi Master Data Inti (Single Source of Truth) bagi seluruh modul lainnya.',
                'action_url' => route('portal.ortu.index'),
                'action_label' => 'Buka Portal Presensi Mandiri',
            ],
            'ppdb-online' => [
                'kode' => 'ppdb-online',
                'pilar' => 'Pilar 2: Hulu Data Pendaftaran',
                'judul' => 'PPDB Online — Penerimaan Murid Baru Terintegrasi',
                'tagline' => 'Satu pintu pendaftaran online siswa baru dengan fitur 1-klik mutasi ke SIRANI.',
                'icon' => 'fa-solid fa-file-signature',
                'color' => '#dc2626',
                'subtle' => '#fee2e2',
                'status' => 'aktif',
                'status_label' => 'Operasional Aktif (2026/2027)',
                'badge_color' => '#10b981',
                'tahap' => 'Tahap 1B (Prioritas)',
                'sasaran' => 'Calon Peserta Didik Baru & Panitia PPDB Sekolah',
                'deskripsi' => 'Portal pendaftaran online mandiri berbasis web yang ramah smartphone. Calon siswa mendaftar dari rumah, mengunggah berkas, mencetak kartu tanda peserta ber-QR Code, dan melihat pengumuman seleksi secara transparan.',
                'fitur_unggulan' => [
                    'Formulir pendaftaran digital dengan pilihan 3 jurusan vokasi (RPL, APHP, TSM).',
                    'Cetak Kartu Tanda Peserta PPDB format PDF resmi ber-KOP dinas.',
                    'Cek status hasil seleksi mandiri via nomor registrasi atau NISN.',
                    'Panel verifikasi berkas bagi Panitia PPDB sekolah.',
                    '⚡ Fitur 1-Klik Mutasi: Calon siswa diterima otomatis disalin menjadi Siswa Aktif kelas X SIRANI.',
                ],
                'integrasi_sirani' => 'Calon siswa yang diterima langsung otomatis mendapatkan rombel kelas X dan kartu RFID siap cetak.',
                'action_url' => route('ppdb.index'),
                'action_label' => 'Kunjungi Portal PPDB Online',
            ],
            'sim-pkl' => [
                'kode' => 'sim-pkl',
                'pilar' => 'Pilar 3: Vokasi & Kemitraan Industri',
                'judul' => 'SIM-PKL — Presensi & Monitoring Prakerin Industri',
                'tagline' => 'Sistem monitoring PKL siswa dengan GPS Geotagging, selfie kehadiran & jurnal harian.',
                'icon' => 'fa-solid fa-business-time',
                'color' => '#0284c7',
                'subtle' => '#e0f2fe',
                'status' => 'roadmap',
                'status_label' => 'Roadmap Tahap 2',
                'badge_color' => '#6366f1',
                'tahap' => 'Tahap 2 (Q4 2026)',
                'sasaran' => 'Siswa Kelas XI/XII, Guru Pembimbing PKL, & Pembimbing DUDI',
                'deskripsi' => 'Modul pemantauan Praktek Kerja Lapangan (PKL) siswa vokasi di dunia usaha dan industri rekanan. Siswa melakukan presensi dari lokasi magang dengan validasi koordinat GPS dan foto selfie, serta mencatat logbook kegiatan harian secara digital.',
                'fitur_unggulan' => [
                    'Presensi magang mobile dengan deteksi lokasi GPS industri (Anti Fake GPS).',
                    'Jurnal / logbook kegiatan praktik harian yang langsung direview pembimbing.',
                    'Penilaian kompetensi kerja berkala oleh instruktur DUDI.',
                    'Peta sebaran lokasi penempatan magang siswa SMKN 1 Air Naningan.',
                    'Penerbitan Sertifikat PKL digital terverifikasi QR Code sekolah.',
                ],
                'integrasi_sirani' => 'Sinkronisasi otomatis dengan status presensi sekolah; siswa magang tidak ditandai alpa di dasbor piket.',
                'action_url' => null,
                'action_label' => 'Dalam Tahap Perancangan',
            ],
            'smart-toolman' => [
                'kode' => 'smart-toolman',
                'pilar' => 'Pilar 4: Sarana & Manajemen Bengkel',
                'judul' => 'Smart Toolman — Manajemen Peminjaman Alat Bengkel & Lab',
                'tagline' => 'Sistem peminjaman peralatan bengkel APHP, TSM & lab komputer via tapping kartu RFID.',
                'icon' => 'fa-solid fa-wrench',
                'color' => '#b45309',
                'subtle' => '#fef3c7',
                'status' => 'roadmap',
                'status_label' => 'Roadmap Tahap 3',
                'badge_color' => '#6366f1',
                'tahap' => 'Tahap 3 (2027)',
                'sasaran' => 'Toolman Bengkel, Guru Praktik Vokasi & Siswa',
                'deskripsi' => 'Manajemen inventaris peralatan dan instrumen praktik di bengkel otomotif TSM, laboratorium agro-industri APHP, serta lab rekayasa software RPL. Siswa meminjam peralatan cukup dengan men-tap kartu pelajar RFID SIRANI yang sudah dimiliki.',
                'fitur_unggulan' => [
                    'Peminjaman & pengembalian alat praktik dalam hitungan detik via scan RFID.',
                    'Riwayat penanggung jawab alat untuk meminimalisir risiko hilang/rusak.',
                    'Katalog inventaris alat, kalibrasi instrumen, dan stok bahan habis pakai praktik.',
                    'Peringatan otomatis alat yang belum kembali saat jam bengkel berakhir.',
                    'Laporan pemakaian aset laboratorium untuk kebutuhan audit sekolah.',
                ],
                'integrasi_sirani' => 'Menggunakan kartu identitas RFID yang sama persis dengan kartu presensi gerbang sekolah (One Card Policy).',
                'action_url' => null,
                'action_label' => 'Dalam Tahap Perancangan',
            ],
            'bkk-tracer' => [
                'kode' => 'bkk-tracer',
                'pilar' => 'Pilar 5: Karir, Kemitraan & Alumni',
                'judul' => 'BKK & Tracer Study — Bursa Karir & Penelusuran Alumni',
                'tagline' => 'Portal penyerapan kerja alumni vokasi (BMW: Bekerja, Melanjutkan, Wirausaha).',
                'icon' => 'fa-solid fa-user-tie',
                'color' => '#059669',
                'subtle' => '#ecfdf5',
                'status' => 'roadmap',
                'status_label' => 'Roadmap Tahap 4',
                'badge_color' => '#6366f1',
                'tahap' => 'Tahap 4 (2027)',
                'sasaran' => 'Alumni, Tim BKK Sekolah & Mitra Industri Rekanan',
                'deskripsi' => 'Wadah resmi Bursa Kerja Khusus (BKK) SMKN 1 Air Naningan untuk mempublikasikan lowongan kerja dari industri rekanan di Lampung maupun nasional, serta survei pelacakan karir lulusan (Tracer Study) untuk akreditasi dan evaluasi kurikulum.',
                'fitur_unggulan' => [
                    'Papan informasi lowongan kerja khusus lulusan SMK terverifikasi.',
                    'Pendaftaran seleksi rekrutmen kerja dan magang kerja pasca-lulus.',
                    'Kuesioner Tracer Study resmi Kemendikbudristek untuk pelaporan BMW.',
                    'Pangkalan data jejaring alumni vokasi Tanggamus lintas angkatan.',
                    'Statistik penyerapan lulusan di dunia kerja real-time untuk akreditasi.',
                ],
                'integrasi_sirani' => 'Data alumni langsung diwariskan dari data kelulusan kelas XII SIRANI tanpa perlu input ulang.',
                'action_url' => null,
                'action_label' => 'Dalam Tahap Perancangan',
            ],
            'e-library' => [
                'kode' => 'e-library',
                'pilar' => 'Pilar 6: Sumber Belajar & Literasi',
                'judul' => 'E-Perpustakaan & Bank Modul Ajar Vokasi',
                'tagline' => 'Akses materi modul ajar kejuruan, e-book, dan jobsheet praktik bengkel 24/7.',
                'icon' => 'fa-solid fa-book-bookmark',
                'color' => '#7c3aed',
                'subtle' => '#f5f3ff',
                'status' => 'roadmap',
                'status_label' => 'Roadmap Tahap 5',
                'badge_color' => '#6366f1',
                'tahap' => 'Tahap 5 (2027)',
                'sasaran' => 'Seluruh Siswa, Guru Mata Pelajaran & Pustakawan',
                'deskripsi' => 'Pusat repositori digital materi ajar, jobsheet instruksi bengkel, modul ajar kurikulum merdeka kejuruan, serta literatur teknik yang dapat diunduh dan dipelajari mandiri oleh siswa melalui laptop maupun smartphone.',
                'fitur_unggulan' => [
                    'Katalog digital e-book referensi kejuruan (Software, Agroindustri, Otomotif).',
                    'Repositori jobsheet panduan langkah kerja praktik bengkel terstandar.',
                    'Peminjaman buku fisik perpustakaan berbasis barcode / kartu RFID siswa.',
                    'Akses unduhan materi ajar mandiri dari rumah maupun asrama.',
                ],
                'integrasi_sirani' => 'Login siswa terhubung langsung dengan NISN dan akun kesiswaan SIRANI.',
                'action_url' => null,
                'action_label' => 'Dalam Tahap Perancangan',
            ],
            'teaching-factory' => [
                'kode' => 'teaching-factory',
                'pilar' => 'Pilar 7: Unit Produksi & Kewirausahaan',
                'judul' => 'Teaching Factory (TeFa) Showcase & Produk Vokasi',
                'tagline' => 'Showcase karya inovasi teknologi, olahan pangan agro, dan jasa servis bengkel siswa.',
                'icon' => 'fa-solid fa-boxes-stacked',
                'color' => '#ea580c',
                'subtle' => '#fff7ed',
                'status' => 'roadmap',
                'status_label' => 'Roadmap Pengembangan',
                'badge_color' => '#6366f1',
                'tahap' => 'Pengembangan Berkelanjutan',
                'sasaran' => 'Masyarakat Umum, Mitra Konsumen & Siswa Wirausaha',
                'deskripsi' => 'Etalase produk dan jasa unggulan karya siswa 3 konsentrasi keahlian: Software & Web dari RPL, produk pangan olahan sehat dari APHP, serta layanan bengkel servis motor berkala dari TSM.',
                'fitur_unggulan' => [
                    'Katalog produk olahan hasil tani kemasan higienis binaan TeFa APHP.',
                    'Portofolio web & aplikasi sistem informasi karya siswa TeFa RPL.',
                    'Layanan booking servis sepeda motor standar bengkel resmi TeFa TSM.',
                    'Pemberdayaan jiwa enterpreneurship dan kemandirian finansial vokasi.',
                ],
                'integrasi_sirani' => 'Pencatatan jam kerja TeFa siswa terakumulasi sebagai poin portofolio praktik kejuruan.',
                'action_url' => null,
                'action_label' => 'Dalam Tahap Perancangan',
            ],
        ];
    }

    /**
     * Halaman Utama Peta Arsitektur Ekosistem Digital (Master Plan)
     */
    public function index()
    {
        $sekolah = PengaturanSekolah::getAktif();
        $modules = $this->getModules();

        return view('web.ekosistem_index', compact('sekolah', 'modules'));
    }

    /**
     * Halaman Pratinjau Blueprint Spesifik per Modul
     */
    public function show(string $slug)
    {
        $sekolah = PengaturanSekolah::getAktif();
        $modules = $this->getModules();

        if (!isset($modules[$slug])) {
            abort(404, 'Modul ekosistem tidak ditemukan dalam Master Plan.');
        }

        $module = $modules[$slug];

        return view('web.ekosistem_show', compact('sekolah', 'module', 'modules'));
    }
}
