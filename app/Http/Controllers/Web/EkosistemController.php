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
                'pilar' => 'Pilar 1: Presensi & Disiplin Siswa',
                'judul' => 'SIRANI Core — Sistem Presensi & Disiplin Digital',
                'tagline' => 'Pondasi utama data presensi gerbang RFID, kesiswaan & notifikasi WhatsApp otomatis.',
                'icon' => 'fa-solid fa-id-card-clip',
                'color' => '#2563eb',
                'subtle' => '#eff6ff',
                'status' => 'aktif',
                'status_label' => 'Operasional Aktif',
                'badge_color' => '#10b981',
                'tahap' => 'Operasional Inti',
                'sasaran' => 'Siswa, Guru, Staf TU, Tim Ketertiban & Orang Tua',
                'deskripsi' => 'SIRANI adalah sistem terpadu pengawal kedisiplinan dan kehadiran siswa berbasis kartu RFID sekolah. Dilengkapi integrasi gerbang WhatsApp notifikasi real-time ke wali murid, pencatatan poin pelanggaran & buku konseling BK, serta laporan dinas otomatis.',
                'fitur_unggulan' => [
                    'Presensi tapping kartu RFID & PIN darurat di gerbang sekolah.',
                    'Notifikasi WhatsApp otomatis ke nomor orang tua siswa detik itu juga.',
                    'Buku Disiplin Digital, reward poin prestasi, dan konseling BK.',
                    'Laporan rekapitulasi kehadiran berkala standar Dinas Pendidikan.',
                    'Meja Piket Harian digital & portal monitoring mandiri orang tua.',
                ],
                'integrasi_sirani' => 'Menjadi Master Data Inti (Single Source of Truth) bagi seluruh modul sekolah.',
                'action_url' => route('portal.ortu.index'),
                'action_label' => 'Buka Monitoring Presensi',
            ],
            'akademik-cbt' => [
                'kode' => 'akademik-cbt',
                'pilar' => 'Pilar 2: Akademik & Pembelajaran KBM',
                'judul' => 'AKADEMIK & CBT — Asesmen Online & Kurikulum Merdeka',
                'tagline' => 'Portal ujian digital siswa mandiri via NISN, bank soal terpadu, dan jadwal KBM.',
                'icon' => 'fa-solid fa-laptop-code',
                'color' => '#059669',
                'subtle' => '#f0fdf4',
                'status' => 'aktif',
                'status_label' => 'Operasional Aktif',
                'badge_color' => '#10b981',
                'tahap' => 'Operasional Inti',
                'sasaran' => 'Seluruh Siswa, Guru Mata Pelajaran, & Waka Kurikulum',
                'deskripsi' => 'Modul pembelajaran dan asesmen komputer terpadu yang memfasilitasi ujian sumatif, asesmen harian, penjadwalan pelajaran mingguan, bank soal pilihan ganda & esai, serta rekap penilaian kurikulum merdeka langsung di server sekolah.',
                'fitur_unggulan' => [
                    'Ruang Asesmen CBT Siswa berbasis token sesi & validasi NISN.',
                    'Bank Soal fleksibel (pilihan ganda, esai) dengan editor format soal modern.',
                    'Sistem anti-curang, acak opsi & soal ujian otomatis.',
                    'Penyusunan jadwal pelajaran otomatis per jam & rombel kejuruan.',
                    'Pengelolaan Capaian Pembelajaran (CP) dan Tujuan Pembelajaran (TP) terintegrasi.',
                ],
                'integrasi_sirani' => 'Daftar peserta ujian otomatis tersinkronisasi dari basis data siswa aktif SIRANI tanpa perlu import ulang.',
                'action_url' => route('portal.asesmen.index'),
                'action_label' => 'Buka Ruang Ujian Asesmen CBT',
            ],
            'ppdb-online' => [
                'kode' => 'ppdb-online',
                'pilar' => 'Pilar 3: Penerimaan Siswa Baru',
                'judul' => 'PPDB Online 2026/2027 — Pendaftaran & Seleksi CBT Mandiri',
                'tagline' => 'Satu pintu pendaftaran online siswa baru, ujian seleksi CBT minat bakat, dan mutasi otomatis.',
                'icon' => 'fa-solid fa-file-signature',
                'color' => '#dc2626',
                'subtle' => '#fee2e2',
                'status' => 'aktif',
                'status_label' => 'Operasional Aktif',
                'badge_color' => '#10b981',
                'tahap' => 'Operasional Aktif (2026/2027)',
                'sasaran' => 'Calon Peserta Didik Baru & Panitia PPDB Sekolah',
                'deskripsi' => 'Portal pendaftaran online mandiri berbasis web yang ramah smartphone. Calon siswa mendaftar dari rumah, mengunggah berkas, mengikuti ujian seleksi CBT online, mencetak kartu tanda peserta ber-QR Code, dan melihat pengumuman seleksi secara transparan.',
                'fitur_unggulan' => [
                    'Formulir pendaftaran digital dengan pilihan 3 jurusan vokasi (RPL, APHP, TSM).',
                    'Ujian seleksi CBT online mandiri khusus calon siswa baru (tes minat & bakat).',
                    'Cetak Kartu Tanda Peserta PPDB format PDF resmi ber-KOP dinas.',
                    'Cek status hasil seleksi mandiri via nomor registrasi atau NISN.',
                    '⚡ Fitur 1-Klik Mutasi: Calon siswa diterima otomatis disalin menjadi Siswa Aktif kelas X SIRANI.',
                ],
                'integrasi_sirani' => 'Calon siswa yang diterima langsung otomatis mendapatkan rombel kelas X dan kartu RFID siap cetak.',
                'action_url' => route('ppdb.index'),
                'action_label' => 'Kunjungi Portal PPDB Online',
            ],
            'situan-tu' => [
                'kode' => 'situan-tu',
                'pilar' => 'Pilar 4: Tata Usaha & Kesiswaan',
                'judul' => 'SITUAN — Sistem Informasi Tata Usaha & Persuratan Siswa',
                'tagline' => 'Layanan mandiri permohonan surat keterangan aktif, legalisir, dan arsip kesiswaan.',
                'icon' => 'fa-solid fa-envelope-open-text',
                'color' => '#d97706',
                'subtle' => '#fef3c7',
                'status' => 'aktif',
                'status_label' => 'Operasional Aktif',
                'badge_color' => '#10b981',
                'tahap' => 'Operasional Inti',
                'sasaran' => 'Siswa, Orang Tua, & Tenaga Administrasi Sekolah (Tata Usaha)',
                'deskripsi' => 'SITUAN memberikan kemudahan bagi peserta didik dan orang tua untuk mengajukan permohonan surat dinas resmi seperti Surat Keterangan Aktif Sekolah, pengantar beasiswa, dan legalisir ijazah secara mandiri dan cepat.',
                'fitur_unggulan' => [
                    'Pengajuan surat keterangan aktif sekolah mandiri cukup dengan memasukkan NISN.',
                    'Tracking status berkas permohonan surat secara transparan.',
                    'Template dokumen otomatis ber-QR Code verifikasi keaslian berkas.',
                    'Dashboard kerja staf tata usaha untuk validasi dan cetak surat dinas.',
                ],
                'integrasi_sirani' => 'Biodata siswa, NISN, nama wali, dan rombel ditarik langsung dari database SIRANI.',
                'action_url' => route('situan.pelayanan.index'),
                'action_label' => 'Buka Loket Pelayanan Surat',
            ],
            'dcc-portal' => [
                'kode' => 'dcc-portal',
                'pilar' => 'Pilar 5: Pusat Komando & Pengendalian Data',
                'judul' => 'DCC Portal — Data Control Center SMKN 1 Air Naningan',
                'tagline' => 'Single sign-on terpadu untuk Kepala Sekolah, Dewan Guru, Staf TU, dan Administrator.',
                'icon' => 'fa-solid fa-sliders',
                'color' => '#0f172a',
                'subtle' => '#f1f5f9',
                'status' => 'aktif',
                'status_label' => 'Operasional Aktif',
                'badge_color' => '#10b981',
                'tahap' => 'Pusat Komando Inti',
                'sasaran' => 'Kepala Sekolah, Guru Mapel, Wali Kelas, Guru BK & Administrator IT',
                'deskripsi' => 'Pusat pengendali dan integrasi seluruh modul sistem informasi sekolah. Menghubungkan seluruh hak akses pengguna dalam satu antarmuka aman dan efisien.',
                'fitur_unggulan' => [
                    'Single Sign-On (SSO) login terpadu akun GTK dan staf.',
                    'Dashboard eksekutif pantauan kehadiran harian, grafik pelanggaran, dan statistik KBM.',
                    'Pengaturan identitas sekolah, branding, logo, dan konektivitas perangkat.',
                    'Manajemen log keamanan sistem dan cadangan basis data otomatis.',
                ],
                'integrasi_sirani' => 'Menjadi gerbang induk administrasi untuk mengelola SIRANI, AKADEMIK, SITUAN, PPDB, dan Portal Berita.',
                'action_url' => route('admin.portal'),
                'action_label' => 'Masuk Portal DCC GTK',
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
