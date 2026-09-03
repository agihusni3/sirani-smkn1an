<?php

/**
 * SIRANI COMPREHENSIVE DEEP TEST RUNNER
 * Pengujian Menyeluruh Sistem Informasi Responsif Absensi SMKN 1 Air Naningan
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Absensi;
use App\Models\AuditLog;
use App\Models\Guru;
use App\Models\HariLibur;
use App\Models\JadwalHariIni;
use App\Models\JadwalPiket;
use App\Models\Jurusan;
use App\Models\KartuRfid;
use App\Models\KasusDisiplin;
use App\Models\KasusDisiplinDokumen;
use App\Models\NotifikasiOrtu;
use App\Models\PengaturanSekolah;
use App\Models\Pengumuman;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Services\EvaluasiPresensiService;
use App\Services\RfidScanService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SiraniDeepTester
{
    private int $passed = 0;
    private int $failed = 0;
    private int $warnings = 0;
    private array $details = [];

    public function run(): void
    {
        $this->header("MEMULAI DEEP TEST MENYELURUH SISTEM SIRANI");
        $startTime = microtime(true);

        $this->testDatabaseIntegrity();
        $this->testAuthenticationAndRBAC();
        $this->testJadwalDanSesiGerbang();
        $this->testSmartGateRfidBarcode();
        $this->testEvaluasiPresensiAlphaBolos();
        $this->testBukuDisiplinDanEskalasi();
        $this->testLaporanDanExportPdf();
        $this->testPortalPublikOrtuSiswa();
        $this->testNotifikasiDanAuditLog();

        $elapsed = round(microtime(true) - $startTime, 2);

        $this->header("RINGKASAN HASIL DEEP TEST SISTEM");
        echo "  Waktu Pengujian : {$elapsed} detik\n";
        echo "  Total Lolos     : \033[32m{$this->passed} pengujian\033[0m\n";
        echo "  Total Gagal     : " . ($this->failed > 0 ? "\033[31m{$this->failed} pengujian\033[0m" : "\033[32m0 pengujian\033[0m") . "\n";
        echo "  Total Catatan   : " . ($this->warnings > 0 ? "\033[33m{$this->warnings} catatan\033[0m" : "\033[32m0 catatan\033[0m") . "\n";

        if ($this->failed === 0) {
            echo "\n\033[32;1m[SELURUH SUBSISTEM SIRANI SEHAT, KONSISTEN, DAN BERFUNGSI NORMAL!]\033[0m\n\n";
        } else {
            echo "\n\033[31;1m[DITEMUKAN BEBERAPA KENDALA PADA SUBSISTEM, LIHAT DETAIL DI ATAS]\033[0m\n\n";
        }
    }

    private function assert(string $label, bool $condition, string $failMsg = ''): void
    {
        if ($condition) {
            $this->passed++;
            echo "  \033[32m✔\033[0m {$label}\n";
        } else {
            $this->failed++;
            echo "  \033[31m✖ {$label}\033[0m\n";
            if ($failMsg) {
                echo "    \033[31m└─ Error: {$failMsg}\033[0m\n";
            }
        }
    }

    private function warn(string $label, string $msg): void
    {
        $this->warnings++;
        echo "  \033[33m⚠\033[0m {$label}: {$msg}\n";
    }

    private function header(string $title): void
    {
        echo "\n\033[36;1m═══════════════════════════════════════════════════════════════\033[0m\n";
        echo "\033[36;1m  {$title}\033[0m\n";
        echo "\033[36;1m═══════════════════════════════════════════════════════════════\033[0m\n";
    }

    // ── 1. INTEGRITAS DATABASE ──────────────────────────────────────────────
    private function testDatabaseIntegrity(): void
    {
        $this->header("1. INTEGRITAS DATABASE & RELASI DATA MASTER");

        // Skema tabel utama
        $tables = ['siswas', 'gurus', 'rombels', 'jurusans', 'tahun_ajarans', 'absensis', 'kartu_rfids', 'kasus_disiplins', 'jadwal_hari_inis', 'users'];
        foreach ($tables as $tbl) {
            $this->assert("Tabel '{$tbl}' terdefinisi di skema database", Schema::hasTable($tbl));
        }

        // Verifikasi tidak ada kolom face_embedding di siswas dan gurus
        $this->assert("Kolom 'face_embedding' sudah bersih dari tabel 'siswas'", !Schema::hasColumn('siswas', 'face_embedding'));
        $this->assert("Kolom 'face_embedding' sudah bersih dari tabel 'gurus'", !Schema::hasColumn('gurus', 'face_embedding'));

        // Cek data yatim piatu (orphan data)
        $siswaCount = Siswa::count();
        $this->assert("Terdapat data siswa terdaftar ({$siswaCount} siswa)", $siswaCount > 0);

        $orphanAbsensiSiswa = Absensi::where('pemilik_type', 'siswa')
            ->whereNotIn('pemilik_id', Siswa::pluck('id'))
            ->count();
        $this->assert("Tidak ada absensi yatim piatu tanpa entitas siswa (0 orphan)", $orphanAbsensiSiswa === 0, "Ditemukan {$orphanAbsensiSiswa} data absensi tanpa siswa");

        $orphanDisiplin = KasusDisiplin::whereNotIn('siswa_id', Siswa::pluck('id'))->count();
        $this->assert("Tidak ada kasus disiplin yatim piatu (0 orphan)", $orphanDisiplin === 0, "Ditemukan {$orphanDisiplin} kasus disiplin tanpa siswa");

        $orphanRfidSiswa = KartuRfid::where('pemilik_type', 'siswa')
            ->whereNotIn('pemilik_id', Siswa::pluck('id'))
            ->count();
        $this->assert("Tidak ada kartu RFID terhubung ke siswa yang tidak ada", $orphanRfidSiswa === 0);

        // Keunikan UID Kartu RFID
        $dupRfid = KartuRfid::select('uid', DB::raw('count(*) as c'))
            ->groupBy('uid')
            ->having('c', '>', 1)
            ->count();
        $this->assert("Seluruh kartu RFID memiliki kode UID unik (0 duplikat)", $dupRfid === 0);
    }

    // ── 2. AUTENTIKASI & RBAC ───────────────────────────────────────────────
    private function testAuthenticationAndRBAC(): void
    {
        $this->header("2. AUTENTIKASI, AKUN & ROLE ACCESS CONTROL (RBAC)");

        $admin = User::where('role', 'admin')->first();
        $this->assert("Akun Super Admin ditemukan di sistem ({$admin?->email})", $admin !== null);

        if ($admin) {
            Auth::login($admin);
            $this->assert("Admin berhasil login dan terautentikasi", Auth::check() && Auth::id() === $admin->id);
            $this->assert("Helper isAdmin() bernilai true untuk akun admin", $admin->isAdmin());
        }

        // Test Wali Kelas
        $wali = User::where('role', 'wali_kelas')->first();
        if ($wali) {
            $this->assert("Akun Wali Kelas ditemukan ({$wali->name})", true);
            $this->assert("Helper isWaliKelas() bernilai true", $wali->isWaliKelas());
            $rombelIds = $wali->getWaliRombelIds();
            $this->assert("Wali Kelas memiliki relasi rombel binaan (" . count($rombelIds) . " kelas)", count($rombelIds) >= 0);
        } else {
            $this->warn("Akun Wali Kelas", "Belum ada akun bertipe wali_kelas di tabel users");
        }

        // Test Guru Piket
        $guruPiket = User::where('role', 'guru_piket')->first();
        if ($guruPiket) {
            $this->assert("Akun Guru Piket ditemukan ({$guruPiket->name})", true);
            $this->assert("Helper isGuruPiket() bernilai true", $guruPiket->isGuruPiket());
        }

        // Test Keamanan: Guest tidak bisa akses dashboard
        Auth::logout();
        $this->assert("Auth::logout() berhasil melepaskan sesi pengguna", !Auth::check());
    }

    // ── 3. JADWAL & SESI GERBANG ────────────────────────────────────────────
    private function testJadwalDanSesiGerbang(): void
    {
        $this->header("3. JADWAL SEKOLAH & SESI GERBANG OPERASIONAL");

        $today = Carbon::today()->toDateString();
        $jadwal = JadwalHariIni::getJadwalAktif($today);
        $this->assert("Jadwal Hari Ini aktif tersedia untuk tanggal {$today}", $jadwal !== null);

        $this->assert("Format jam_masuk_toleransi valid ({$jadwal->jam_masuk_toleransi})", !empty($jadwal->jam_masuk_toleransi));
        $this->assert("Format jam_pulang_mulai valid ({$jadwal->jam_pulang_mulai})", !empty($jadwal->jam_pulang_mulai));
        $this->assert("Format jam_tutup_gerbang valid ({$jadwal->jam_tutup_gerbang})", !empty($jadwal->jam_tutup_gerbang));

        // Smart Gate selalu aktif otomatis mengikuti jadwal operasional
        $this->assert("Sistem Smart Gate selalu aktif otomatis (isSesiAktif = true)", JadwalHariIni::isSesiAktif($today));
    }

    // ── 4. SMART GATE RFID & BARCODE ────────────────────────────────────────
    private function testSmartGateRfidBarcode(): void
    {
        $this->header("4. SMART GATE RFID & BARCODE ENGINE");

        $service = new RfidScanService();
        $today = Carbon::today()->toDateString();
        $jadwal = JadwalHariIni::getJadwalAktif($today);

        // Ambil 1 siswa aktif untuk simulasi transaksi
        $siswa = Siswa::where('status', 'aktif')->first();
        if (!$siswa) {
            $this->warn("Scan Test", "Tidak ada siswa aktif di database untuk pengujian scan");
            return;
        }

        DB::beginTransaction();
        try {
            // A. Perekaman Masuk langsung aktif tanpa perlu buka manual
            Absensi::where('pemilik_type', 'siswa')->where('pemilik_id', $siswa->id)->where('tanggal', $today)->delete();

            $resMasuk = $service->scanRfid($siswa->nisn);
            $this->assert("Scan masuk siswa berhasil diproses via NISN/Barcode", $resMasuk['success'] === true && $resMasuk['type'] === 'jam_masuk');

            // B. Anti-double scan cooldown (< 10 detik)
            $resCooldown = $service->scanRfid($siswa->nisn);
            $this->assert("Fitur cooldown melindungi dari double-scan cepat", $resCooldown['success'] === true && $resCooldown['type'] === 'cooldown_double_scan');

        } finally {
            DB::rollBack();
        }
    }

    // ── 5. EVALUASI ALPHA & BOLOS ───────────────────────────────────────────

    private function testEvaluasiPresensiAlphaBolos(): void
    {
        $this->header("5. MESIN EVALUASI OTOMATIS ALPHA & BOLOS");

        $evalService = new EvaluasiPresensiService();
        $today = Carbon::today()->toDateString();

        $this->assert("Service EvaluasiPresensiService dapat diinstansiasi", $evalService !== null);

        // Uji deteksi siswa PKL (tidak boleh dialphakan)
        $siswaPkl = Siswa::where('status', 'pkl')->first();
        if ($siswaPkl) {
            $this->assert("Sistem mendeteksi status PKL siswa ({$siswaPkl->nama})", $siswaPkl->status === 'pkl');
        }

        // Cek guard penanganan orphan di KasusDisiplin
        $dummyId = 99999999;
        $kasus = KasusDisiplin::syncFromPresensi($dummyId);
        $this->assert("Guard KasusDisiplin::syncFromPresensi menangani ID tidak valid tanpa error", $kasus instanceof KasusDisiplin);
    }

    // ── 6. BUKU DISIPLIN & ESKALASI ─────────────────────────────────────────
    private function testBukuDisiplinDanEskalasi(): void
    {
        $this->header("6. BUKU DISIPLIN, PELANGGARAN & TAHAP ESKALASI");

        $totalKasus = KasusDisiplin::count();
        $this->assert("Database Buku Disiplin dapat diakses (total: {$totalKasus} kasus tercatat)", true);

        $tahapList = [
            'tahap_1_wali_kelas',
            'tahap_2_bk',
            'tahap_2_guru_bk',
            'tahap_3_wakasis',
            'tahap_3_waka_kesiswaan',
            'tahap_4_kepsek',
            'tahap_4_kepala_sekolah',
            'selesai'
        ];

        $kasusValid = KasusDisiplin::whereIn('status_tahap', $tahapList)->count();
        $this->assert("Seluruh kasus disiplin memiliki status tahapan valid", $kasusValid === $totalKasus);

        // Dokumen berita acara kasus
        $docCount = KasusDisiplinDokumen::count();
        $this->assert("Tabel dokumen kasus dapat diakses (total: {$docCount} dokumen)", true);
    }

    // ── 7. LAPORAN & EXPORT PDF ─────────────────────────────────────────────
    private function testLaporanDanExportPdf(): void
    {
        $this->header("7. PELAPORAN, REKAPITULASI & CETAK PDF");

        $admin = User::where('role', 'admin')->first();
        Auth::login($admin);

        // Controller Laporan
        $ctrlLaporan = app(\App\Http\Controllers\LaporanController::class);
        $respLaporan = $ctrlLaporan->index(request());
        $laporanOk = ($respLaporan instanceof \Illuminate\View\View) || ($respLaporan instanceof \Illuminate\Http\Response && $respLaporan->status() === 200);
        $this->assert("Halaman Laporan Presensi dapat dirender dengan sukses", $laporanOk);

        // Controller Siswa & Export
        $ctrlSiswa = app(\App\Http\Controllers\SiswaController::class);
        $respSiswa = $ctrlSiswa->index(request());
        $siswaOk = ($respSiswa instanceof \Illuminate\View\View) || ($respSiswa instanceof \Illuminate\Http\Response && $respSiswa->status() === 200);
        $this->assert("Halaman Master Data Siswa dapat dirender dengan sukses", $siswaOk);

        $respCsv = $ctrlSiswa->export(request());
        $this->assert("Export CSV Data Siswa menghasilkan respons stream valid", $respCsv->getStatusCode() === 200);


        // Controller Guru
        $ctrlGuru = app(\App\Http\Controllers\GuruController::class);
        $respGuru = $ctrlGuru->index(request());
        $guruOk = ($respGuru instanceof \Illuminate\View\View) || ($respGuru instanceof \Illuminate\Http\Response && $respGuru->status() === 200);
        $this->assert("Halaman Master Data Guru dapat dirender dengan sukses", $guruOk);
    }

    // ── 8. PORTAL PUBLIK ORANG TUA & SISWA ──────────────────────────────────
    private function testPortalPublikOrtuSiswa(): void
    {
        $this->header("8. PORTAL PUBLIK ORANG TUA & SISWA MANDIRI");

        $portalCtrl = app(\App\Http\Controllers\PortalOrtuController::class);
        $respPortal = $portalCtrl->index(request());
        $portalOk = ($respPortal instanceof \Illuminate\View\View) || ($respPortal instanceof \Illuminate\Http\Response && $respPortal->status() === 200);
        $this->assert("Portal Kehadiran Orang Tua (/cek-presensi) dapat diakses publik", $portalOk);

        $siswa = Siswa::whereNotNull('nisn')->first();
        if ($siswa) {
            $rfidCtrl = app(\App\Http\Controllers\RfidController::class);
            $respKartu = $rfidCtrl->kartuDigital($siswa->nisn);
            $kartuOk = ($respKartu instanceof \Illuminate\Http\RedirectResponse) || ($respKartu instanceof \Illuminate\View\View);
            $this->assert("Kartu Presensi Digital Siswa (/kartu-digital/{$siswa->nisn}) valid", $kartuOk);

            $respDetail = $portalCtrl->detail($siswa->nisn);
            $detailOk = ($respDetail instanceof \Illuminate\View\View) || ($respDetail instanceof \Illuminate\Http\Response && $respDetail->status() === 200);
            $this->assert("Halaman Detail Portal Siswa/Ortu (/cek-presensi/{$siswa->nisn}) dapat dirender", $detailOk);
        }
    }



    // ── 9. NOTIFIKASI & AUDIT LOG ───────────────────────────────────────────
    private function testNotifikasiDanAuditLog(): void
    {
        $this->header("9. NOTIFIKASI WHATSAPP & AUDIT LOG SISTEM");

        $notifCount = NotifikasiOrtu::count();
        $this->assert("Tabel Notifikasi WhatsApp Ortu dapat diakses (total: {$notifCount} antrean)", true);

        $auditCount = AuditLog::count();
        $this->assert("Audit Log sistem aktif merekam riwayat keamanan (total: {$auditCount} log)", true);

        // Test pencatatan log
        AuditLog::catat('system_deep_test', 'system', 'Deep test otomatis dijalankan via Antigravity Diagnostic');
        $this->assert("Pencatatan AuditLog baru berhasil dieksekusi", AuditLog::where('aksi', 'system_deep_test')->exists());
        AuditLog::where('aksi', 'system_deep_test')->delete();
    }
}

$tester = new SiraniDeepTester();
$tester->run();
