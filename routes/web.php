<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupDatabaseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\GuruPiketController;
use App\Http\Controllers\HariLiburController;
use App\Http\Controllers\IzinSiswaController;
use App\Http\Controllers\JadwalPiketController;
use App\Http\Controllers\KasusDisiplinController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PengaturanSekolahController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\PeringkatController;
use App\Http\Controllers\PortalOrtuController;
use App\Http\Controllers\PresensiManualController;
use App\Http\Controllers\RombelController;
use App\Http\Controllers\SiklusSiswaController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\SuratKesiswaanController;
use Illuminate\Support\Facades\Route;

// ══ 1. Halaman Depan Publik: Mengarah Langsung ke Login ══
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});



// Portal Kehadiran Terpadu Siswa & Orang Tua (Cek Presensi Mandiri - Dilindungi Rate Limiting)
Route::middleware('throttle:300,1')->group(function () {
    Route::get('/cek-presensi', [PortalOrtuController::class, 'index'])->name('portal.ortu.index');
    Route::post('/cek-presensi', [PortalOrtuController::class, 'index'])->name('portal.ortu.cari');
    Route::get('/cek-presensi/{nisn}', [PortalOrtuController::class, 'detail'])->name('portal.ortu.detail');
    Route::get('/presensi-siswa/{nisn}', [PortalOrtuController::class, 'detail'])->name('portal.ortu.direct');
    
    // Redirect Alias dari rute lama ke portal terpadu
    Route::get('/portal-siswa/{nisn?}', [\App\Http\Controllers\RfidController::class, 'portalSiswa'])->name('portal.siswa');
    Route::get('/kartu-digital/{nisn}', [\App\Http\Controllers\RfidController::class, 'kartuDigital'])->name('kartu.digital');
    Route::get('/kartu-digital-guru/{id}', [\App\Http\Controllers\RfidController::class, 'kartuDigitalGuru'])->name('kartu.digital.guru');
});

// Kirim WA Gateway dari Halaman Kartu Digital Publik (Akses Siswa/Ortu/Guru via HP tanpa login)
Route::post('/kartu-digital/kirim-wa', [\App\Http\Controllers\RfidController::class, 'kirimWaPersonal'])
    ->middleware('throttle:20,1')
    ->name('kartu.digital.kirim.wa');

// Download QR Code PNG Server-Side (konsisten dengan tampilan kartu digital)
Route::get('/qr/{type}/{id}', [\App\Http\Controllers\RfidController::class, 'generateQrImage'])
    ->middleware('throttle:60,1')
    ->where('type', 'guru|siswa')
    ->name('qr.download');

// Autentikasi (Login & Logout - Proteksi Anti Brute Force)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:60,1');

Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Internal Terproteksi Dasbor Utama & Master Data (Hanya Staf/Admin Terautentikasi)
Route::middleware('auth')->group(function () {
    // 0a. Smart Gate Kiosk RFID & Barcode (Hanya Admin & Guru Piket)
    Route::middleware('role:admin,guru_piket')->group(function () {
        Route::get('/smart-gate', [\App\Http\Controllers\RfidController::class, 'kiosk'])->name('rfid.kiosk');
        Route::get('/kios-rfid', [\App\Http\Controllers\RfidController::class, 'kiosk']);
        Route::get('/rfid', [\App\Http\Controllers\RfidController::class, 'kiosk']);
    });


    // 0b. Pairing Kartu RFID (Khusus Admin & Staf TU)
    Route::middleware('role:admin,staf_tu')->group(function () {
        Route::post('/api/v1/rfid-pair', [\App\Http\Controllers\RfidController::class, 'pair'])->middleware('throttle:60,1')->name('api.rfid.pair');
        Route::post('/api/v1/rfid-unpair', [\App\Http\Controllers\RfidController::class, 'unpair'])->middleware('throttle:60,1')->name('api.rfid.unpair');
    });

    // 0c. Pengaturan Profil & Ganti Password Mandiri (Semua Pengguna Terautentikasi)
    Route::post('/profil/update', [AuthController::class, 'updateProfil'])->name('profil.update');

    // 1. Dashboard Utama (Tampilan Cerdas Terisolasi Sesuai Hak Akses Peran)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Laporan & Rekapitulasi Presensi
    Route::middleware('role:admin,kepala_sekolah,waka_kesiswaan,waka_kurikulum,guru_bk,wali_kelas,guru_piket,staf_tu,guru')->group(function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export-csv', [LaporanController::class, 'exportCsv'])->name('laporan.export-csv');
        Route::get('/laporan/cetak-pdf', [LaporanController::class, 'cetakPdf'])->name('laporan.cetak-pdf');
    });
    Route::put('/laporan/{id}', [LaporanController::class, 'update'])->name('laporan.update')->middleware('role:admin,waka_kesiswaan,guru');
    Route::delete('/laporan/{id}', [LaporanController::class, 'destroy'])->name('laporan.destroy')->middleware('role:admin');

    // 2b. Peringkat & Apresiasi Kehadiran Siswa & Guru (Leaderboard & Piagam Fleksibel)
    Route::get('/peringkat', [PeringkatController::class, 'index'])->name('peringkat.index');
    Route::get('/peringkat/piagam-siswa/{id}', [PeringkatController::class, 'cetakPiagamSiswa'])->name('peringkat.piagam-siswa');
    Route::get('/peringkat/piagam-guru/{id}', [PeringkatController::class, 'cetakPiagamGuru'])->name('peringkat.piagam-guru');
    Route::post('/peringkat/upload-template', [PeringkatController::class, 'uploadTemplate'])->name('peringkat.upload-template');
    Route::post('/peringkat/save-template-config', [PeringkatController::class, 'saveTemplateConfig'])->name('peringkat.save-template-config');
    Route::post('/peringkat/reset-template', [PeringkatController::class, 'resetTemplate'])->name('peringkat.reset-template');
    Route::get('/peringkat/export-csv', [PeringkatController::class, 'exportCsv'])->name('peringkat.export-csv');

    // 3. Jadwal Piket Harian
    Route::get('/jadwal-piket', [JadwalPiketController::class, 'index'])->name('jadwal-piket.index')->middleware('role:admin,kepala_sekolah,waka_kesiswaan,waka_kurikulum,staf_tu');
    Route::post('/jadwal-piket', [JadwalPiketController::class, 'store'])->name('jadwal-piket.store')->middleware('role:admin,waka_kesiswaan,waka_kurikulum');
    Route::delete('/jadwal-piket/{id}', [JadwalPiketController::class, 'destroy'])->name('jadwal-piket.destroy')->middleware('role:admin,waka_kesiswaan,waka_kurikulum');

    // 4. Buku Kasus & Penegakan Disiplin Siswa Berjenjang (Admin, Kepsek, Wakasis, BK, Wali Kelas)
    Route::middleware('role:admin,kepala_sekolah,waka_kesiswaan,guru_bk,wali_kelas')->group(function () {
        Route::get('/disiplin', [KasusDisiplinController::class, 'index'])->name('admin.disiplin.index');
        Route::post('/disiplin', [KasusDisiplinController::class, 'store'])->name('admin.disiplin.store');
        Route::post('/disiplin/pengaturan-poin', [KasusDisiplinController::class, 'updatePengaturanPoin'])->name('admin.disiplin.pengaturan-poin');
        Route::post('/disiplin/recalculate', [KasusDisiplinController::class, 'hitungUlangSemuaPoin'])->name('admin.disiplin.recalculate');
        Route::post('/disiplin/katalog-reward', [KasusDisiplinController::class, 'storeKatalogReward'])->name('admin.disiplin.katalog-reward.store');
        Route::put('/disiplin/katalog-reward/{id}', [KasusDisiplinController::class, 'updateKatalogReward'])->name('admin.disiplin.katalog-reward.update');
        Route::delete('/disiplin/katalog-reward/{id}', [KasusDisiplinController::class, 'deleteKatalogReward'])->name('admin.disiplin.katalog-reward.destroy');
        Route::post('/disiplin/katalog-pelanggaran', [KasusDisiplinController::class, 'storeKatalogPelanggaran'])->name('admin.disiplin.katalog-pelanggaran.store');
        Route::put('/disiplin/katalog-pelanggaran/{id}', [KasusDisiplinController::class, 'updateKatalogPelanggaran'])->name('admin.disiplin.katalog-pelanggaran.update');
        Route::delete('/disiplin/katalog-pelanggaran/{id}', [KasusDisiplinController::class, 'deleteKatalogPelanggaran'])->name('admin.disiplin.katalog-pelanggaran.destroy');
        Route::get('/disiplin/{id}', [KasusDisiplinController::class, 'show'])->name('admin.disiplin.show');
        Route::post('/disiplin/{id}/log', [KasusDisiplinController::class, 'storeLog'])->name('admin.disiplin.log.store');
        Route::put('/disiplin/{id}/log/{logId}', [KasusDisiplinController::class, 'updateLog'])->name('admin.disiplin.log.update');
        Route::delete('/disiplin/{id}/log/{logId}', [KasusDisiplinController::class, 'destroyLog'])->name('admin.disiplin.log.destroy');
        Route::post('/disiplin/{id}/reward', [KasusDisiplinController::class, 'storeRewardSiswa'])->name('admin.disiplin.reward.store');
        Route::delete('/disiplin/{id}/reward/{rewardId}', [KasusDisiplinController::class, 'deleteRewardSiswa'])->name('admin.disiplin.reward.destroy');
        Route::post('/disiplin/{id}/pelanggaran', [KasusDisiplinController::class, 'storePelanggaranSiswa'])->name('admin.disiplin.pelanggaran.store');
        Route::delete('/disiplin/{id}/pelanggaran/{pelanggaranId}', [KasusDisiplinController::class, 'deletePelanggaranSiswa'])->name('admin.disiplin.pelanggaran.destroy');
        Route::post('/disiplin/{id}/upload', [KasusDisiplinController::class, 'uploadDokumen'])->name('admin.disiplin.dokumen.upload');
        Route::delete('/disiplin/{id}/dokumen/{dokumenId}', [KasusDisiplinController::class, 'hapusDokumen'])->name('admin.disiplin.dokumen.destroy');
        Route::post('/disiplin/{id}/tindak-lanjut', [KasusDisiplinController::class, 'tindakLanjut'])->name('admin.disiplin.tindak-lanjut');
        Route::post('/disiplin/{id}/selesaikan', [KasusDisiplinController::class, 'selesaikan'])->name('admin.disiplin.selesaikan');
        Route::get('/disiplin/{id}/resume-cetak', [KasusDisiplinController::class, 'cetakResume'])->name('admin.disiplin.resume.cetak');
        Route::get('/disiplin/{id}/sk-cetak', [KasusDisiplinController::class, 'cetakSkKepsek'])->name('admin.disiplin.sk.cetak');
        Route::delete('/disiplin/{id}', [KasusDisiplinController::class, 'destroy'])->name('admin.disiplin.destroy')->middleware('role:admin');
    });

    // 5. Meja Verifikasi & Notifikasi WhatsApp (Admin, Wakasis, Waka Kurikulum, BK, Wali Kelas, Guru Piket)
    Route::middleware('role:admin,waka_kesiswaan,waka_kurikulum,guru_bk,wali_kelas,guru_piket')->group(function () {
        Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
        Route::post('/notifikasi/{id}/approve', [NotifikasiController::class, 'approve'])->name('notifikasi.approve');
        Route::post('/notifikasi/batch-approve', [NotifikasiController::class, 'batchApprove'])->name('notifikasi.batch-approve');
        Route::post('/notifikasi/{id}/reject', [NotifikasiController::class, 'reject'])->name('notifikasi.reject');
        Route::post('/notifikasi/batch-reject', [NotifikasiController::class, 'batchReject'])->name('notifikasi.batch-reject');
        Route::post('/notifikasi/pengaturan', [NotifikasiController::class, 'updatePengaturan'])->name('notifikasi.pengaturan.update')->middleware('role:admin,guru_piket');
        Route::post('/notifikasi/test-kirim', [NotifikasiController::class, 'testKirim'])->name('notifikasi.test-kirim')->middleware('role:admin,guru_piket');
    });

    // 5b. Pusat Pengumuman & Broadcast Sekolah (Admin, Kepsek, Wakasis, Waka Kurikulum, BK, Wali Kelas, Guru Piket, Guru)
    Route::middleware('role:admin,kepala_sekolah,waka_kesiswaan,waka_kurikulum,guru_bk,wali_kelas,guru_piket,guru')->group(function () {
        Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
        Route::post('/pengumuman', [PengumumanController::class, 'store'])->name('pengumuman.store');
        Route::post('/pengumuman/{id}/toggle', [PengumumanController::class, 'toggleStatus'])->name('pengumuman.toggle');
        Route::delete('/pengumuman/{id}', [PengumumanController::class, 'destroy'])->name('pengumuman.destroy')->middleware('role:admin,waka_kesiswaan,waka_kurikulum,guru_piket');
    });

    // 6. Master Data Siswa & Rombel (Admin, Kepsek, Wakasis, Waka Kurikulum, BK, Wali Kelas, Staf TU, Guru)
    Route::middleware('role:admin,kepala_sekolah,waka_kesiswaan,waka_kurikulum,guru_bk,wali_kelas,staf_tu,guru')->group(function () {
        Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
        Route::get('/siswa/export', [SiswaController::class, 'export'])->name('siswa.export');
        Route::get('/siswa/template-csv', [SiswaController::class, 'downloadTemplate'])->name('siswa.template-csv');
        Route::get('/siswa/cetak-pdf', [SiswaController::class, 'cetakPdf'])->name('siswa.cetak-pdf');
        Route::get('/siswa/{id}/surat-bebas-masalah', [SuratKesiswaanController::class, 'cetakSuratBebasMasalah'])->name('siswa.surat-bebas-masalah');
        Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store')->middleware('role:admin,staf_tu,wali_kelas');
        Route::post('/siswa/import', [SiswaController::class, 'import'])->name('siswa.import')->middleware('role:admin,staf_tu,wali_kelas');
        Route::put('/siswa/{id}', [SiswaController::class, 'update'])->name('siswa.update')->middleware('role:admin,staf_tu,wali_kelas');
        Route::delete('/siswa/{id}', [SiswaController::class, 'destroy'])->name('siswa.destroy')->middleware('role:admin,staf_tu,wali_kelas');

        Route::get('/rombel', [RombelController::class, 'index'])->name('rombel.index');
        Route::post('/rombel', [RombelController::class, 'storeRombel'])->name('rombel.store')->middleware('role:admin,staf_tu,waka_kurikulum');
        Route::put('/rombel/{id}', [RombelController::class, 'updateRombel'])->name('rombel.update')->middleware('role:admin,staf_tu,waka_kurikulum');
        Route::delete('/rombel/{id}', [RombelController::class, 'destroyRombel'])->name('rombel.destroy')->middleware('role:admin');
        Route::post('/tahun-ajaran', [RombelController::class, 'storeTahunAjaran'])->name('tahun-ajaran.store')->middleware('role:admin,staf_tu,waka_kurikulum');
        Route::post('/tahun-ajaran/{id}/aktifkan', [RombelController::class, 'setActiveTahunAjaran'])->name('tahun-ajaran.aktifkan')->middleware('role:admin,staf_tu,waka_kurikulum');
        Route::post('/jurusan', [RombelController::class, 'storeJurusan'])->name('jurusan.store')->middleware('role:admin,staf_tu,waka_kurikulum');
        Route::delete('/jurusan/{id}', [RombelController::class, 'destroyJurusan'])->name('jurusan.destroy')->middleware('role:admin');
    });

    // 7. Siklus Siswa & Transisi Akademik / PKL (Admin, Wakasis, Waka Kurikulum, Staf TU)
    Route::middleware('role:admin,waka_kesiswaan,waka_kurikulum,staf_tu')->group(function () {
        Route::get('/siklus-siswa', [SiklusSiswaController::class, 'index'])->name('siklus-siswa.index');
        Route::post('/siklus-siswa/transisi', [SiklusSiswaController::class, 'processTransisi'])->name('siklus-siswa.transisi');
        Route::post('/siklus-siswa/transisi-massal', [SiklusSiswaController::class, 'processTransisiMassal'])->name('siklus-siswa.transisi-massal');
    });

    // 9. Perizinan Siswa & Guru (Hanya Guru Piket & Admin)
    Route::middleware('role:admin,guru_piket')->group(function () {
        Route::get('/izin-siswa', [IzinSiswaController::class, 'index'])->name('izin-siswa.index');
        Route::post('/izin-siswa', [IzinSiswaController::class, 'store'])->name('izin-siswa.store');
        Route::delete('/izin-siswa/{id}', [IzinSiswaController::class, 'destroy'])->name('izin-siswa.destroy');
        Route::delete('/izin-guru/{id}', [IzinSiswaController::class, 'destroyGuru'])->name('izin-guru.destroy');
    });

    // 10. Presensi Manual / Lupa Kartu RFID (Admin, Wakasis, Guru Piket)
    Route::middleware('role:admin,waka_kesiswaan,guru_piket')->group(function () {
        Route::get('/presensi-manual', [PresensiManualController::class, 'index'])->name('presensi-manual.index');
        Route::post('/presensi-manual', [PresensiManualController::class, 'store'])->name('presensi-manual.store');
    });

    // 11. Guru Piket — Dashboard & Operasional Harian
    Route::middleware('role:admin,waka_kesiswaan,guru_piket')->group(function () {
        Route::get('/piket', [GuruPiketController::class, 'index'])->name('piket.index');
        Route::post('/piket/presensi-manual', [GuruPiketController::class, 'storePresensiManual'])->name('piket.presensi-manual.store');
        Route::post('/piket/validasi-siswa', [GuruPiketController::class, 'validasiPresensiSiswa'])->name('piket.validasi-siswa');
        Route::put('/piket/absensi/{id}', [GuruPiketController::class, 'updateAbsensi'])->name('piket.absensi.update');
        Route::post('/piket/toggle-gerbang', [GuruPiketController::class, 'toggleSesiGerbang'])->name('piket.toggle-gerbang');
        Route::post('/piket/kirim-wa', [GuruPiketController::class, 'kirimWaGateway'])->name('piket.kirim-wa');
        Route::post('/piket/set-status-guru', [GuruPiketController::class, 'storeStatusGuruCepat'])->name('piket.set-status-guru');
        Route::post('/piket/kunci-alpha', [GuruPiketController::class, 'kunciAlphaSekarang'])->name('piket.kunci-alpha');
        Route::post('/piket/flagging-wa', [GuruPiketController::class, 'flaggingWaMassal'])->name('piket.flagging-wa');
    });

    // 12. Audit Trail System (Admin, Kepala Sekolah & Waka Kesiswaan)
    Route::middleware('role:admin,kepala_sekolah,waka_kesiswaan')->group(function () {
        Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
        Route::get('/audit/{id}', [AuditController::class, 'show'])->name('audit.show');
    });

    // 13. Master Guru & RFID (Admin, Kepala Sekolah, Waka Kesiswaan, Waka Kurikulum & Staf TU)
    Route::middleware('role:admin,kepala_sekolah,waka_kesiswaan,waka_kurikulum,staf_tu')->group(function () {
        Route::get('/guru', [GuruController::class, 'index'])->name('guru.index');
        Route::get('/guru/export', [GuruController::class, 'export'])->name('guru.export');
        Route::get('/guru/template-csv', [GuruController::class, 'downloadTemplate'])->name('guru.template-csv');
        Route::get('/guru/cetak-pdf', [GuruController::class, 'cetakPdf'])->name('guru.cetak-pdf');
        Route::post('/guru', [GuruController::class, 'store'])->name('guru.store')->middleware('role:admin,staf_tu');
        Route::post('/guru/import', [GuruController::class, 'import'])->name('guru.import')->middleware('role:admin,staf_tu');
        Route::put('/guru/{id}', [GuruController::class, 'update'])->name('guru.update')->middleware('role:admin,staf_tu');
        Route::delete('/guru/{id}', [GuruController::class, 'destroy'])->name('guru.destroy')->middleware('role:admin');
        Route::post('/guru/{id}/akun', [GuruController::class, 'storeAkun'])->name('guru.akun.store')->middleware('role:admin');
        Route::match(['post', 'put'], '/guru/{id}/user/{userId?}', [GuruController::class, 'storeAkun'])->middleware('role:admin');
        Route::delete('/guru/{id}/akun', [GuruController::class, 'destroyAkun'])->name('guru.akun.destroy')->middleware('role:admin');
        Route::delete('/guru/{id}/user/{userId?}', [GuruController::class, 'destroyAkun'])->middleware('role:admin');
    });

    // 13b. Pusat Manajemen Kartu RFID & Cetak Barcode Kartu (Admin & Staf TU)
    Route::middleware('role:admin,staf_tu')->group(function () {
        Route::get('/kartu-rfid', [\App\Http\Controllers\RfidController::class, 'index'])->name('rfid.index');
        Route::get('/kartu-rfid/cetak', [\App\Http\Controllers\RfidController::class, 'cetak'])->name('rfid.cetak');
        Route::post('/kartu-rfid/broadcast-wa', [\App\Http\Controllers\RfidController::class, 'broadcastWa'])->name('rfid.broadcast.wa');
        Route::get('/manajemen-rfid', [\App\Http\Controllers\RfidController::class, 'index']);
    });

    // Kirim WA Personal (Bisa diakses Admin, Staf TU, Guru, dan Pegawai)
    Route::post('/kartu-rfid/kirim-wa-personal', [\App\Http\Controllers\RfidController::class, 'kirimWaPersonal'])->name('rfid.kirim.wa.personal');
    Route::post('/kartu-rfid/kirim-wa-personal-alias', [\App\Http\Controllers\RfidController::class, 'kirimWaPersonal'])->name('rfid.kirimWaPersonal');

    // 14. Jam Operasional & Jadwal Sekolah (Admin, Kepsek, Wakasis, Waka Kurikulum, Guru Piket, Staf TU, Guru)
    Route::middleware('role:admin,kepala_sekolah,waka_kesiswaan,waka_kurikulum,guru_piket,staf_tu,guru')->group(function () {
        Route::get('/jadwal-sekolah', [DashboardController::class, 'jadwalSekolah'])->name('admin.jadwal.sekolah');
        Route::get('/jam-operasional', [DashboardController::class, 'jadwalSekolah'])->name('admin.jadwal.index');
        Route::post('/jam-operasional', [DashboardController::class, 'updateJadwal'])->name('admin.jadwal.update')->middleware('role:admin,waka_kurikulum,waka_kesiswaan,kepala_sekolah');
        Route::post('/jadwal-sekolah', [DashboardController::class, 'updateJadwal'])->name('admin.jadwal.sekolah.update')->middleware('role:admin,waka_kurikulum,waka_kesiswaan,kepala_sekolah');
        Route::post('/jadwal-mingguan', [DashboardController::class, 'updateJadwalMingguan'])->name('admin.jadwal.mingguan.update')->middleware('role:admin,waka_kurikulum,waka_kesiswaan,kepala_sekolah');
    });


    // 15. Kalender Hari Libur (Read: Semua Role; CRUD: Admin & Waka Kurikulum)
    Route::get('/hari-libur', [HariLiburController::class, 'index'])->name('admin.hari-libur.index');
    Route::middleware('role:admin,waka_kurikulum')->group(function () {
        Route::post('/hari-libur', [HariLiburController::class, 'store'])->name('admin.hari-libur.store');
        Route::delete('/hari-libur/{id}', [HariLiburController::class, 'destroy'])->name('admin.hari-libur.destroy');
        Route::post('/hari-libur/preset', [HariLiburController::class, 'isiPreset'])->name('admin.hari-libur.preset');
    });

    // 15. Pengaturan Profil & Backup (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/pengaturan-sekolah', [PengaturanSekolahController::class, 'index'])->name('admin.pengaturan-sekolah.index');
        Route::post('/pengaturan-sekolah', [PengaturanSekolahController::class, 'update'])->name('admin.pengaturan-sekolah.update');
        Route::get('/backup', [BackupDatabaseController::class, 'index'])->name('admin.backup.index');
        Route::get('/backup/download', [BackupDatabaseController::class, 'download'])->name('admin.backup.download');
        Route::post('/backup/restore', [BackupDatabaseController::class, 'restore'])->name('admin.backup.restore');
        Route::post('/backup/auto-run', [BackupDatabaseController::class, 'triggerAutoBackup'])->name('admin.backup.auto-run');
        Route::get('/backup/saved/{filename}', [BackupDatabaseController::class, 'downloadSaved'])->name('admin.backup.download-saved');
        Route::post('/backup/restore-saved/{filename}', [BackupDatabaseController::class, 'restoreSaved'])->name('admin.backup.restore-saved');
        Route::delete('/backup/saved/{filename}', [BackupDatabaseController::class, 'deleteSaved'])->name('admin.backup.delete-saved');
    });
});

// Format Cetak Surat Resmi Kesiswaan
Route::get('/surat', [SuratKesiswaanController::class, 'cetak'])->name('surat.index');
Route::get('/surat/cetak/{id?}', [SuratKesiswaanController::class, 'cetak'])->name('surat.cetak');
