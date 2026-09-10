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

use App\Http\Controllers\Web\BerandaController;
use App\Http\Controllers\Web\ProfilController;
use App\Http\Controllers\Web\JurusanController;
use App\Http\Controllers\Web\KontakController;
use App\Http\Controllers\Web\BeritaWebController;
use App\Http\Controllers\Web\EkosistemController;
use App\Http\Controllers\Ppdb\PpdbDaftarController;
use App\Http\Controllers\Ppdb\PpdbAdminController;
use App\Http\Controllers\Ppdb\PpdbUjianController;
use App\Http\Controllers\Ppdb\PpdbSoalController;
use App\Http\Controllers\Ppdb\PpdbPresensiUjianController;
use App\Http\Controllers\Admin\BeritaAdminController;
use App\Http\Controllers\Admin\WebsiteBannerController;
use App\Http\Controllers\Admin\WebsiteStatistikController;
use App\Http\Controllers\SituanPersuratanController;
use App\Http\Controllers\SituanPelayananSuratController;
use App\Http\Controllers\SituanKepegawaianController;
use App\Http\Controllers\SituanEKabinetController;

// ══ 1. Website Resmi Publik SMKN 1 Air Naningan (Dilindungi Pelacak Kunjungan Otomatis) ══
Route::middleware('track.visitor')->group(function () {
    Route::get('/', [BerandaController::class, 'index'])->name('web.beranda');
    Route::get('/profil-sekolah', [ProfilController::class, 'index'])->name('web.profil');
    Route::get('/konsentrasi-keahlian', [JurusanController::class, 'index'])->name('web.jurusan.index');
    Route::get('/konsentrasi-keahlian/{slug}', [JurusanController::class, 'show'])->name('web.jurusan.show');
    Route::get('/ekosistem', [EkosistemController::class, 'index'])->name('web.ekosistem.index');
    Route::get('/ekosistem/{slug}', [EkosistemController::class, 'show'])->name('web.ekosistem.show');
    Route::get('/kontak', [KontakController::class, 'index'])->name('web.kontak');
    Route::get('/kabar-sekolah', [BeritaWebController::class, 'index'])->name('web.berita.index');
    Route::get('/kabar-sekolah/{slug}', [BeritaWebController::class, 'show'])->name('web.berita.show');

    // ══ 2. PPDB Online Publik (Penerimaan Peserta Didik Baru) ══
    Route::prefix('ppdb')->name('ppdb.')->group(function () {
        Route::get('/', [PpdbDaftarController::class, 'index'])->name('index');
        Route::get('/daftar', [PpdbDaftarController::class, 'formulir'])->name('formulir');
        Route::post('/daftar', [PpdbDaftarController::class, 'simpan'])->name('simpan');
        Route::get('/sukses/{nomor}', [PpdbDaftarController::class, 'sukses'])->name('sukses');
        Route::get('/status', [PpdbDaftarController::class, 'status'])->name('status');
        Route::get('/cetak-kartu/{nomor}', [PpdbDaftarController::class, 'cetakKartu'])->name('cetak');

        // CBT Tes Tertulis Online (Portal Masuk & Ujian)
        Route::match(['get', 'post'], '/ujian', [PpdbUjianController::class, 'portal'])->name('ujian.portal');
        Route::get('/ujian/{nomor}', [PpdbUjianController::class, 'konfirmasi'])->name('ujian.konfirmasi');
        Route::get('/ujian/{nomor}/kerjakan', [PpdbUjianController::class, 'kerjakan'])->name('ujian.kerjakan');
        Route::post('/ujian/{nomor}/simpan-draft', [PpdbUjianController::class, 'simpanDraft'])->name('ujian.simpan_draft');
        Route::post('/ujian/{nomor}/selesai', [PpdbUjianController::class, 'selesai'])->name('ujian.selesai');
        Route::get('/ujian/{nomor}/selesai', [PpdbUjianController::class, 'halamanSelesai'])->name('ujian.selesai_view');
    });
});



// Monitoring Absen Mandiri Siswa & Orang Tua (Dilindungi Rate Limiting)
Route::middleware('throttle:300,1')->group(function () {
    Route::get('/monitoring-absen', [PortalOrtuController::class, 'index'])->name('monitoring.absen');
    Route::post('/monitoring-absen', [PortalOrtuController::class, 'index'])->name('monitoring.absen.cari');
    Route::get('/monitoring-absen/{nisn}', [PortalOrtuController::class, 'detail'])->name('monitoring.absen.detail');

    Route::get('/cek-presensi', [PortalOrtuController::class, 'index'])->name('portal.ortu.index');
    Route::post('/cek-presensi', [PortalOrtuController::class, 'index'])->name('portal.ortu.cari');
    Route::get('/cek-presensi/{nisn}', [PortalOrtuController::class, 'detail'])->name('portal.ortu.detail');
    Route::get('/presensi-siswa/{nisn}', [PortalOrtuController::class, 'detail'])->name('portal.ortu.direct');
    
    // Redirect Alias dari rute lama ke monitoring absen mandiri
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

use App\Http\Controllers\AdminPortalController;

// Rute Internal Terproteksi Dasbor Utama & Master Data (Hanya Staf/Admin Terautentikasi)
Route::middleware('auth')->group(function () {
    // 00. DCC - Digital Command Center (Pusat Kontrol Data & Launchpad Terpadu Seluruh Pengguna Sekolah)
    Route::get('/dcc', [AdminPortalController::class, 'index'])->name('admin.portal');
    Route::get('/DCC', [AdminPortalController::class, 'index']);
    Route::get('/portal', function () {
        return redirect()->route('admin.portal');
    });
    Route::get('/hub', function () {
        return redirect()->route('admin.portal');
    });
    Route::get('/admin/portal', function () {
        return redirect()->route('admin.portal');
    });

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

    // 0c-2. Portal Mandiri PTK (Biodata & Lemari Berkas Digital Pribadi)
    Route::get('/ptk/profil-saya', [\App\Http\Controllers\PtkProfilMandiriController::class, 'index'])->name('ptk.profil-saya');
    Route::post('/ptk/profil-saya/unggah-berkas', [\App\Http\Controllers\PtkProfilMandiriController::class, 'unggahBerkasMandiri'])->name('ptk.unggah-berkas');
    Route::delete('/ptk/profil-saya/berkas/{id}', [\App\Http\Controllers\PtkProfilMandiriController::class, 'hapusBerkasMandiri'])->name('ptk.hapus-berkas');

    // 0c-3. Pintasan ke Modul PPDB — Meja Wawancara 2026
    Route::get('/guru/ppdb/wawancara', fn() => redirect()->route('admin.ppdb.wawancara'))->name('guru.ppdb.wawancara');
    Route::post('/guru/ppdb/wawancara/nilai/{id}', [\App\Http\Controllers\Ppdb\PpdbGuruWawancaraController::class, 'simpanNilai'])->name('guru.ppdb.wawancara.simpan');
    Route::get('/guru/ppdb/wawancara/cetak/{id}', [\App\Http\Controllers\Ppdb\PpdbGuruWawancaraController::class, 'cetak'])->name('guru.ppdb.wawancara.cetak');

    // 0d. Role Switcher Mode (Multi-Role Switching)
    Route::post('/switch-role', [AuthController::class, 'switchRole'])->name('switch-role');

    // 0e. SITUAN — SMKN 1 AN (Sistem Informasi Tata Usaha & Data Pokok Sekolah)
    Route::middleware('role:admin,kepala_sekolah,staf_tu,waka_kurikulum,waka_kesiswaan,waka_sarpras,waka_hubin,wali_kelas')->group(function () {
        Route::get('/situan', [\App\Http\Controllers\SituanDashboardController::class, 'index'])->name('situan.index');
        Route::get('/situan/dashboard', [\App\Http\Controllers\SituanDashboardController::class, 'index'])->name('situan.dashboard');
        Route::get('/situan/log', [\App\Http\Controllers\SituanDashboardController::class, 'log'])->name('situan.log');
        Route::get('/admin/situan', [\App\Http\Controllers\SituanDashboardController::class, 'index']);

        // Persuratan Kedinasan & E-Disposisi
        Route::get('/situan/surat-masuk', [SituanPersuratanController::class, 'suratMasukIndex'])->name('situan.surat-masuk.index');
        Route::post('/situan/surat-masuk', [SituanPersuratanController::class, 'suratMasukStore'])->name('situan.surat-masuk.store');
        Route::post('/situan/surat-masuk/{id}/disposisi', [SituanPersuratanController::class, 'suratMasukDisposisi'])->name('situan.surat-masuk.disposisi');
        Route::get('/situan/surat-masuk/{id}/cetak-disposisi', [SituanPersuratanController::class, 'disposisiCetakLembar'])->name('situan.surat-masuk.cetak-disposisi');
        Route::get('/situan/surat-keluar', [SituanPersuratanController::class, 'suratKeluarIndex'])->name('situan.surat-keluar.index');
        Route::post('/situan/surat-keluar', [SituanPersuratanController::class, 'suratKeluarStore'])->name('situan.surat-keluar.store');
        Route::put('/situan/surat-keluar/{id}', [SituanPersuratanController::class, 'suratKeluarUpdate'])->name('situan.surat-keluar.update');
        Route::delete('/situan/surat-keluar/{id}', [SituanPersuratanController::class, 'suratKeluarDestroy'])->name('situan.surat-keluar.destroy');
        Route::get('/situan/surat-keluar/{id}/cetak', [SituanPersuratanController::class, 'suratKeluarCetak'])->name('situan.surat-keluar.cetak');
        Route::get('/situan/buku-sk', [SituanPersuratanController::class, 'bukuSkIndex'])->name('situan.buku-sk.index');
        Route::post('/situan/buku-sk', [SituanPersuratanController::class, 'bukuSkStore'])->name('situan.buku-sk.store');

        // Loket Pelayanan Mandiri Siswa
        Route::get('/situan/pelayanan', [SituanPelayananSuratController::class, 'index'])->name('situan.pelayanan.index');
        Route::post('/situan/pelayanan/buat', [SituanPelayananSuratController::class, 'buatSurat'])->name('situan.pelayanan.buat');
        Route::get('/situan/pelayanan/{id}/cetak', [SituanPelayananSuratController::class, 'cetakSurat'])->name('situan.pelayanan.cetak');

        // E-Kepegawaian: Radar KGB, Pangkat & E-Arsip PTK
        Route::get('/situan/radar-kgb', [SituanKepegawaianController::class, 'radarKgbIndex'])->name('situan.radar-kgb.index');
        Route::post('/situan/radar-kgb/{id}/update', [SituanKepegawaianController::class, 'updateTmtGuru'])->name('situan.radar-kgb.update');
        Route::get('/situan/radar-kgb/{id}/cetak-pengantar', [SituanKepegawaianController::class, 'cetakPengantarKgb'])->name('situan.radar-kgb.cetak-pengantar');
        Route::get('/situan/arsip-ptk/{guruId}', [SituanKepegawaianController::class, 'arsipPtkIndex'])->name('situan.arsip-ptk.index');
        Route::post('/situan/arsip-ptk/{guruId}', [SituanKepegawaianController::class, 'arsipPtkStore'])->name('situan.arsip-ptk.store');
        Route::delete('/situan/arsip-ptk/{id}', [SituanKepegawaianController::class, 'arsipPtkDestroy'])->name('situan.arsip-ptk.destroy');

        // E-Kabinet & E-Arsip Digital Terpusat (Sentral Dokumen PTK, Lembaga & MoU)
        Route::get('/situan/ekabinet', [SituanEKabinetController::class, 'index'])->name('situan.ekabinet.index');
        Route::post('/situan/ekabinet/ptk', [SituanEKabinetController::class, 'storePtk'])->name('situan.ekabinet.ptk.store');
        Route::delete('/situan/ekabinet/ptk/{id}', [SituanEKabinetController::class, 'destroyPtk'])->name('situan.ekabinet.ptk.destroy');
        Route::post('/situan/ekabinet/lembaga', [SituanEKabinetController::class, 'storeLembaga'])->name('situan.ekabinet.lembaga.store');
        Route::delete('/situan/ekabinet/lembaga/{id}', [SituanEKabinetController::class, 'destroyLembaga'])->name('situan.ekabinet.lembaga.destroy');
    });

    // 1. Modul SIRANI (Sistem Informasi Responsif Absensi & Kedisiplinan)
    Route::get('/sirani', [DashboardController::class, 'index'])
        ->name('sirani.index')
        ->middleware('role:admin,kepala_sekolah,waka_kesiswaan,waka_kurikulum,waka_sarpras,waka_hubin,kaprog,kepala_bengkel,pustakawan,guru_bk,wali_kelas,guru_piket,staf_tu,guru');
    Route::get('/sirani/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:admin,kepala_sekolah,waka_kesiswaan,waka_kurikulum,waka_sarpras,waka_hubin,kaprog,kepala_bengkel,pustakawan,guru_bk,wali_kelas,guru_piket,staf_tu,guru');

    // Backward compatibility: redirect otomatis dari /dashboard ke /sirani
    Route::get('/dashboard', function () {
        return redirect()->route('sirani.index');
    })->name('dashboard');

    // 2. Laporan & Rekapitulasi Presensi
    Route::middleware('role:admin,kepala_sekolah,waka_kesiswaan,waka_kurikulum,waka_sarpras,waka_hubin,kaprog,kepala_bengkel,pustakawan,guru_bk,wali_kelas,guru_piket,staf_tu,guru')->group(function () {
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
        Route::post('/notifikasi/bersihkan-kadaluarsa', [NotifikasiController::class, 'bersihkanKadaluarsa'])->name('notifikasi.bersihkan-kadaluarsa');
        Route::post('/notifikasi/pengaturan', [NotifikasiController::class, 'updatePengaturan'])->name('notifikasi.pengaturan.update')->middleware('role:admin,guru_piket');
        Route::post('/notifikasi/test-kirim', [NotifikasiController::class, 'testKirim'])->name('notifikasi.test-kirim')->middleware('role:admin,guru_piket');
    });

    // 5b. Pusat Pengumuman & Broadcast Sekolah (Admin, Kepsek, Wakasis, Waka Kurikulum, BK, Wali Kelas, Guru Piket, Guru)
    Route::middleware('role:admin,kepala_sekolah,waka_kesiswaan,waka_kurikulum,waka_sarpras,waka_hubin,kaprog,kepala_bengkel,pustakawan,guru_bk,wali_kelas,guru_piket,guru')->group(function () {
        Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
        Route::post('/pengumuman', [PengumumanController::class, 'store'])->name('pengumuman.store');
        Route::post('/pengumuman/{id}/toggle', [PengumumanController::class, 'toggleStatus'])->name('pengumuman.toggle');
        Route::delete('/pengumuman/{id}', [PengumumanController::class, 'destroy'])->name('pengumuman.destroy')->middleware('role:admin,waka_kesiswaan,waka_kurikulum,guru_piket');
    });

    // 6. Master Data Siswa & Rombel (Admin, Kepsek, Wakasis, Waka Kurikulum, BK, Wali Kelas, Staf TU, Guru)
    Route::middleware('role:admin,kepala_sekolah,waka_kesiswaan,waka_kurikulum,waka_sarpras,waka_hubin,kaprog,kepala_bengkel,pustakawan,guru_bk,wali_kelas,staf_tu,guru')->group(function () {
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

    // 7. Siklus Siswa & Transisi Akademik / PKL (Admin, Wakasis, Waka Kurikulum, Waka Hubin, Staf TU)
    Route::middleware('role:admin,waka_kesiswaan,waka_kurikulum,waka_hubin,staf_tu')->group(function () {
        Route::get('/siklus-siswa', [SiklusSiswaController::class, 'index'])->name('siklus-siswa.index');
        Route::post('/siklus-siswa/transisi', [SiklusSiswaController::class, 'processTransisi'])->name('siklus-siswa.transisi');
        Route::post('/siklus-siswa/transisi-massal', [SiklusSiswaController::class, 'processTransisiMassal'])->name('siklus-siswa.transisi-massal');
        Route::post('/siklus-siswa/alumni', [SiklusSiswaController::class, 'luluskanMassal'])->name('siklus-siswa.alumni');
    });

    // 8. Kios Presensi Mandiri (Hanya Admin & Guru Piket)
    Route::middleware('role:admin,guru_piket')->group(function () {
        Route::get('/kiosk', [\App\Http\Controllers\RfidController::class, 'kiosk'])->name('kiosk.index');
        Route::post('/kiosk/tap', [\App\Http\Controllers\RfidController::class, 'scan'])->name('kiosk.tap');
    });

    // 9. Surat Izin Siswa Terpadu (Admin, Wakasis & Guru Piket)
    Route::middleware('role:admin,waka_kesiswaan,guru_piket')->group(function () {
        Route::get('/izin-siswa', [\App\Http\Controllers\IzinSiswaController::class, 'index'])->name('izin.index');
        Route::get('/izin-siswa/cetak-pdf', [\App\Http\Controllers\IzinSiswaController::class, 'cetakPdf'])->name('izin.cetak-pdf');
        Route::post('/izin-siswa', [\App\Http\Controllers\IzinSiswaController::class, 'store'])->name('izin.store');
        Route::post('/izin-siswa/store', [\App\Http\Controllers\IzinSiswaController::class, 'store'])->name('izin-siswa.store');
        Route::delete('/izin-siswa/{id}', [\App\Http\Controllers\IzinSiswaController::class, 'destroy'])->name('izin-siswa.destroy');
        Route::delete('/izin-guru/{id}', [\App\Http\Controllers\IzinSiswaController::class, 'destroyGuru'])->name('izin-guru.destroy');
    });

    // 10. Guru Piket Operasional Meja Piket (Admin, Wakasis & Guru Piket)
    Route::middleware('role:admin,waka_kesiswaan,guru_piket')->group(function () {
        Route::get('/piket', [GuruPiketController::class, 'index'])->name('piket.index');
        Route::get('/piket/data', [GuruPiketController::class, 'data'])->name('piket.data');
        Route::post('/piket/presensi-manual', [GuruPiketController::class, 'storePresensiManual'])->name('piket.presensi-manual.store');
        Route::put('/piket/absensi/{id}', [GuruPiketController::class, 'updateAbsensi'])->name('piket.absensi.update');
        Route::post('/piket/toggle-gerbang', [GuruPiketController::class, 'toggleSesiGerbang'])->name('piket.toggle-gerbang');
        Route::post('/piket/validasi-presensi', [GuruPiketController::class, 'validasiPresensiSiswa'])->name('piket.validasi-siswa');
        Route::post('/piket/validasi-siswa', [GuruPiketController::class, 'validasiPresensiSiswa']);
        Route::post('/piket/kirim-wa', [GuruPiketController::class, 'kirimWaGateway'])->name('piket.kirim-wa');
        Route::post('/piket/alasan-telat', [GuruPiketController::class, 'storeAlasanTelat'])->name('piket.alasan-telat');
        Route::post('/piket/set-status-guru', [GuruPiketController::class, 'storeStatusGuruCepat'])->name('piket.set-status-guru');
        Route::post('/piket/kunci-alpha', [GuruPiketController::class, 'kunciAlphaSekarang'])->name('piket.kunci-alpha');
        Route::post('/piket/flagging-wa', [GuruPiketController::class, 'flaggingWaMassal'])->name('piket.flagging-wa');
    });

    // 12. Audit Trail System (Admin, Kepala Sekolah & Waka Kesiswaan)
    Route::middleware('role:admin,kepala_sekolah,waka_kesiswaan,waka_sarpras,waka_hubin')->group(function () {
        Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
        Route::get('/audit/{id}', [AuditController::class, 'show'])->name('audit.show');
    });

    // 13. Master Guru & RFID (Admin, Kepala Sekolah, Waka Kesiswaan, Waka Kurikulum & Staf TU)
    Route::middleware('role:admin,kepala_sekolah,waka_kesiswaan,waka_kurikulum,waka_sarpras,waka_hubin,staf_tu')->group(function () {
        Route::get('/guru', [GuruController::class, 'index'])->name('guru.index');
        Route::get('/guru/export', [GuruController::class, 'export'])->name('guru.export');
        Route::get('/guru/template-csv', [GuruController::class, 'downloadTemplate'])->name('guru.template-csv');
        Route::get('/guru/cetak-pdf', [GuruController::class, 'cetakPdf'])->name('guru.cetak-pdf');
        Route::get('/guru/{id}/biodata', [GuruController::class, 'cetakBiodata'])->name('guru.biodata.cetak');
        Route::post('/guru', [GuruController::class, 'store'])->name('guru.store')->middleware('role:admin,staf_tu');
        Route::post('/guru/import', [GuruController::class, 'import'])->name('guru.import')->middleware('role:admin,staf_tu');
        Route::put('/guru/{id}', [GuruController::class, 'update'])->name('guru.update')->middleware('role:admin,staf_tu');
        Route::delete('/guru/{id}', [GuruController::class, 'destroy'])->name('guru.destroy')->middleware('role:admin');
        Route::post('/guru/{id}/akun', [GuruController::class, 'storeAkun'])->name('guru.akun.store')->middleware('role:admin');
        Route::delete('/guru/{id}/akun', [GuruController::class, 'destroyAkun'])->name('guru.akun.destroy')->middleware('role:admin');
        Route::post('/guru/{id}/sertifikat', [GuruController::class, 'storeSertifikat'])->name('guru.sertifikat.store')->middleware('role:admin,staf_tu,guru');
        Route::delete('/guru/{id}/sertifikat/{sertifikatId}', [GuruController::class, 'destroySertifikat'])->name('guru.sertifikat.destroy')->middleware('role:admin,staf_tu,guru');
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
    Route::middleware('role:admin,kepala_sekolah,waka_kesiswaan,waka_kurikulum,waka_sarpras,waka_hubin,guru_piket,staf_tu,guru')->group(function () {
        Route::get('/jadwal-sekolah', [DashboardController::class, 'jadwalSekolah'])->name('admin.jadwal.sekolah');
        Route::get('/jam-operasional', [DashboardController::class, 'jadwalSekolah'])->name('admin.jadwal.index');
        Route::post('/jam-operasional', [DashboardController::class, 'updateJadwal'])->name('admin.jadwal.update')->middleware('role:admin,waka_kurikulum,waka_kesiswaan,kepala_sekolah,waka_sarpras,waka_hubin');
        Route::post('/jadwal-sekolah', [DashboardController::class, 'updateJadwal'])->name('admin.jadwal.sekolah.update')->middleware('role:admin,waka_kurikulum,waka_kesiswaan,kepala_sekolah,waka_sarpras,waka_hubin');
        Route::post('/jadwal-mingguan', [DashboardController::class, 'updateJadwalMingguan'])->name('admin.jadwal.mingguan.update')->middleware('role:admin,waka_kurikulum,waka_kesiswaan,kepala_sekolah,waka_sarpras,waka_hubin');
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

    // ══ 16. Modul Terpadu: Panitia PPDB 2026 & Manajemen Konten Web ══
    Route::prefix('admin')->name('admin.')->group(function () {
        // Meja Wawancara PPDB (Akses: Admin, Kepsek, Panitia PPDB, Waka Kesiswaan, dan Guru Penguji)
        Route::middleware('role:admin,kepala_sekolah,panitia_ppdb,waka_kesiswaan,guru')->group(function () {
            Route::get('/ppdb/wawancara', [\App\Http\Controllers\Ppdb\PpdbGuruWawancaraController::class, 'index'])->name('ppdb.wawancara');
            Route::post('/ppdb/wawancara/nilai/{id}', [\App\Http\Controllers\Ppdb\PpdbGuruWawancaraController::class, 'simpanNilai'])->name('ppdb.wawancara.simpan');
            Route::get('/ppdb/wawancara/cetak/{id}', [\App\Http\Controllers\Ppdb\PpdbGuruWawancaraController::class, 'cetak'])->name('ppdb.wawancara.cetak');
        });

        // Panitia PPDB Online (Admin, Kepsek, Panitia PPDB, Waka Kesiswaan)
        Route::middleware('role:admin,kepala_sekolah,panitia_ppdb,waka_kesiswaan')->group(function () {
            Route::get('/ppdb', [PpdbAdminController::class, 'index'])->name('ppdb.index');
            Route::get('/ppdb/log', [PpdbAdminController::class, 'log'])->name('ppdb.log');
            
            // Seleksi Ujian & Wawancara PPDB 2026
            Route::get('/ppdb/seleksi', [PpdbAdminController::class, 'seleksi'])->name('ppdb.seleksi');
            Route::post('/ppdb/seleksi/setting', [PpdbAdminController::class, 'simpanSettingUjian'])->name('ppdb.seleksi.setting');
            Route::post('/ppdb/seleksi/jadwalkan', [PpdbAdminController::class, 'jadwalkanMassal'])->name('ppdb.seleksi.jadwalkan');
            Route::post('/ppdb/seleksi/jadwalkan-serentak', [PpdbAdminController::class, 'jadwalkanJuknisSerentak'])->name('ppdb.seleksi.jadwalkan_serentak');
            Route::post('/ppdb/seleksi/jadwalkan-single/{id}', [PpdbAdminController::class, 'jadwalkanSingle'])->name('ppdb.seleksi.jadwalkan_single');
            Route::post('/ppdb/seleksi/nilai-esai/{id}', [PpdbAdminController::class, 'simpanNilaiEsai'])->name('ppdb.seleksi.nilai_esai');
            Route::post('/ppdb/seleksi/nilai-wawancara/{id}', [PpdbAdminController::class, 'simpanNilaiWawancara'])->name('ppdb.seleksi.nilai_wawancara');
            Route::post('/ppdb/seleksi/plot-wawancara', [PpdbAdminController::class, 'plotPewawancaraMassal'])->name('ppdb.seleksi.plot_wawancara');
            Route::post('/ppdb/seleksi/plot-wawancara-single/{id}', [PpdbAdminController::class, 'plotPewawancaraSingle'])->name('ppdb.seleksi.plot_wawancara_single');
            Route::get('/ppdb/seleksi/cetak-wawancara/{id?}', [PpdbAdminController::class, 'cetakInstrumenWawancara'])->name('ppdb.seleksi.cetak_wawancara');
            Route::post('/ppdb/seleksi/materi-wawancara', [PpdbAdminController::class, 'simpanMateriWawancara'])->name('ppdb.seleksi.materi_wawancara');
            Route::post('/ppdb/seleksi/reset-materi-wawancara', [PpdbAdminController::class, 'resetMateriWawancara'])->name('ppdb.seleksi.reset_materi_wawancara');
            Route::get('/ppdb/seleksi/tes-buta-warna', [PpdbAdminController::class, 'tesButaWarna'])->name('ppdb.seleksi.tes_buta_warna');
            Route::post('/ppdb/seleksi/kalkulasi', [PpdbAdminController::class, 'kalkulasiKelulusan'])->name('ppdb.seleksi.kalkulasi');

            // Presensi Ujian Tulis/CBT PPDB Berbasis Barcode 2D / QR & Kios Scanner
            Route::get('/ppdb/presensi-ujian', [PpdbPresensiUjianController::class, 'kios'])->name('ppdb.presensi.kios');
            Route::post('/ppdb/presensi-ujian/scan', [PpdbPresensiUjianController::class, 'scan'])->name('ppdb.presensi.scan');
            Route::post('/ppdb/presensi-ujian/{id}/manual', [PpdbPresensiUjianController::class, 'manualHadir'])->name('ppdb.presensi.manual');
            Route::delete('/ppdb/presensi-ujian/{id}', [PpdbPresensiUjianController::class, 'batalHadir'])->name('ppdb.presensi.batal');
            Route::get('/ppdb/presensi-ujian/cetak', [PpdbPresensiUjianController::class, 'cetakDaftarHadir'])->name('ppdb.presensi.cetak');

            Route::get('/ppdb/{id}', [PpdbAdminController::class, 'show'])->name('ppdb.show');
            Route::put('/ppdb/{id}/status', [PpdbAdminController::class, 'updateStatus'])->name('ppdb.update_status');
            Route::post('/ppdb/{id}/mutasi', [PpdbAdminController::class, 'mutasi'])->name('ppdb.mutasi');
            Route::post('/ppdb/mutasi-massal', [PpdbAdminController::class, 'mutasiMassal'])->name('ppdb.mutasi_massal');
            Route::post('/ppdb/{id}/koreksi-jurusan', [PpdbAdminController::class, 'koreksiJurusan'])->name('ppdb.koreksi_jurusan');

            // Bank Soal CBT PPDB
            Route::get('/ppdb/soal/{settingId}', [PpdbSoalController::class, 'index'])->name('ppdb.soal.index');
            Route::get('/ppdb/soal/{settingId}/tambah', [PpdbSoalController::class, 'create'])->name('ppdb.soal.create');
            Route::post('/ppdb/soal/{settingId}', [PpdbSoalController::class, 'store'])->name('ppdb.soal.store');
            Route::get('/ppdb/soal-edit/{id}', [PpdbSoalController::class, 'edit'])->name('ppdb.soal.edit');
            Route::put('/ppdb/soal-edit/{id}', [PpdbSoalController::class, 'update'])->name('ppdb.soal.update');
            Route::delete('/ppdb/soal-edit/{id}', [PpdbSoalController::class, 'destroy'])->name('ppdb.soal.destroy');
        });

        // Kelola Berita, Pengumuman, Agenda & Hero Banner (Admin, Kepsek, Humas)
        Route::middleware('role:admin,kepala_sekolah,humas')->group(function () {
            Route::get('/berita/log', [BeritaAdminController::class, 'log'])->name('berita.log');
            Route::resource('/berita', BeritaAdminController::class);
            Route::resource('/banner', WebsiteBannerController::class);
            Route::post('/banner/{banner}/toggle', [WebsiteBannerController::class, 'toggle'])->name('banner.toggle');
        });

        // Analisis & Grafik Pengunjung Website (Eksklusif Administrator)
        Route::middleware('role:admin')->group(function () {
            Route::get('/statistik-web', [WebsiteStatistikController::class, 'index'])->name('statistik.web');
        });
    });

    // ══ Shortcut URL Modular Terpadu ══
    Route::get('/humas', function () {
        return redirect()->route('admin.berita.index');
    })->name('humas.index');

    Route::get('/ppdb/admin', function () {
        return redirect()->route('admin.ppdb.index');
    })->name('ppdb.admin.index');
});

// Format Cetak Surat Resmi Kesiswaan
Route::get('/surat', [SuratKesiswaanController::class, 'cetak'])->name('surat.index');
Route::get('/surat/cetak/{id?}', [SuratKesiswaanController::class, 'cetak'])->name('surat.cetak');

// Verifikasi Keabsahan Surat Resmi via QR Code (Akses Publik Tanpa Login)
Route::get('/verifikasi-surat/{hash}', [SituanPelayananSuratController::class, 'verifikasiSuratPublik'])->name('situan.verifikasi-surat');

