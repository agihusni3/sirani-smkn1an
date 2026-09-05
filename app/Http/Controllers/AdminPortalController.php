<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\BeritaSekolah;
use App\Models\Guru;
use App\Models\JadwalHariIni;
use App\Models\KasusDisiplin;
use App\Models\PpdbPendaftar;
use App\Models\Siswa;
use App\Models\WebsiteBanner;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminPortalController extends Controller
{
    /**
     * Tampilkan Pusat Kendali Ekosistem Digital SMKN 1 Air Naningan.
     * Launchpad modular terpadu untuk Administrator & Pimpinan.
     */
    public function index()
    {
        $today = Carbon::today()->toDateString();

        // 1. KPI Modul SIRANI (Presensi & Disiplin)
        $totalSiswa = Siswa::where('status', 'aktif')->count();
        $siswaHadirToday = Absensi::where('pemilik_type', 'siswa')
            ->where('tanggal', $today)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count();
        $persenSiswaHadir = $totalSiswa > 0 ? round(($siswaHadirToday / $totalSiswa) * 100, 1) : 0;

        $totalGuru = Guru::where('status', 'aktif')->count();
        $guruHadirToday = Absensi::where('pemilik_type', 'guru')
            ->where('tanggal', $today)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count();

        $kasusDisiplinAktif = KasusDisiplin::where('is_active', true)
            ->where('status_tahap', '!=', 'selesai_pembinaan')
            ->count();
        $isGerbangAktif = JadwalHariIni::isSesiAktif($today);

        // 2. KPI Modul Web Profil Sekolah & CMS Humas
        $totalBerita = BeritaSekolah::count();
        $totalBannerAktif = WebsiteBanner::where('is_active', true)->count();
        $beritaTerbaru = BeritaSekolah::latest()->first();

        // 3. KPI Modul PPDB Online 2026
        $totalPendaftar = PpdbPendaftar::count();
        $ppdbMenunggu = PpdbPendaftar::whereIn('status', ['menunggu', 'draft'])->count();
        $ppdbDiterima = PpdbPendaftar::where('status', 'diterima')->count();
        $ppdbDitolak  = PpdbPendaftar::where('status', 'ditolak')->count();
        $ppdbToday    = PpdbPendaftar::whereDate('created_at', $today)->count();

        // 4. Daftar Roadmap Modul Masa Depan (Rancang Bangun)
        $futureModules = [
            [
                'id'          => 'situan',
                'name'        => 'SITUAN',
                'subtitle'    => 'Tata Usaha & Persuratan',
                'icon'        => 'bi-envelope-paper-heart-fill',
                'color'       => '#0284c7',
                'badge'       => 'Tahap Rancang',
                'description' => 'Sistem Informasi Tata Usaha: agenda surat masuk/keluar, disposisi digital kepala sekolah, arsip SK kepegawaian & mutasi siswa.',
                'lead'        => 'Kepala Tata Usaha & Staf Administrasi',
            ],
            [
                'id'          => 'akademik',
                'name'        => 'AKADEMIK & KBM',
                'subtitle'    => 'Kurikulum & Penilaian',
                'icon'        => 'bi-journal-bookmark-fill',
                'color'       => '#7c3aed',
                'badge'       => 'Tahap Rancang',
                'description' => 'Manajemen Kurikulum Merdeka: pembagian jam mengajar, perangkat ajar/modul, jurnal KBM harian, rekap nilai & rapor digital siswa.',
                'lead'        => 'Waka Kurikulum & Tenaga Pendidik',
            ],
            [
                'id'          => 'sarpras',
                'name'        => 'SARPRAS & ASET',
                'subtitle'    => 'Inventaris Bengkel & Lab',
                'icon'        => 'bi-tools',
                'color'       => '#d97706',
                'badge'       => 'Tahap Rancang',
                'description' => 'Aset sarana & prasarana: inventaris alat mesin bengkel kejuruan, BHP lab praktik, pelaporan kerusakan fasilitas & jadwal perawatan rutin.',
                'lead'        => 'Waka Sarpras & Kepala Bengkel / Toolman',
            ],
            [
                'id'          => 'tefa',
                'name'        => 'TEFA & UNIT PRODUKSI',
                'subtitle'    => 'Teaching Factory Kejuruan',
                'icon'        => 'bi-gear-wide-connected',
                'color'       => '#059669',
                'badge'       => 'Tahap Rancang',
                'description' => 'Teaching Factory & Unit Produksi Vokasi: katalog produk kejuruan, pemesanan jasa servis/konveksi/olahan pangan & pencatatan omzet.',
                'lead'        => 'Waka Hubin & Kepala Program Keahlian (Kaprog)',
            ],
            [
                'id'          => 'perpustakaan',
                'name'        => 'PERPUSTAKAAN DIGITAL',
                'subtitle'    => 'Literasi & Sirkulasi Buku',
                'icon'        => 'bi-book-half',
                'color'       => '#db2777',
                'badge'       => 'Tahap Rancang',
                'description' => 'Sistem perpustakaan terpadu: katalog e-book, sirkulasi peminjaman buku cetak, barcode kartu anggota & riwayat literasi siswa.',
                'lead'        => 'Kepala Perpustakaan & Pengelola Literasi',
            ],
        ];

        return view('admin.portal.index', compact(
            'today',
            'totalSiswa',
            'siswaHadirToday',
            'persenSiswaHadir',
            'totalGuru',
            'guruHadirToday',
            'kasusDisiplinAktif',
            'isGerbangAktif',
            'totalBerita',
            'totalBannerAktif',
            'beritaTerbaru',
            'totalPendaftar',
            'ppdbMenunggu',
            'ppdbDiterima',
            'ppdbDitolak',
            'ppdbToday',
            'futureModules'
        ));
    }
}
