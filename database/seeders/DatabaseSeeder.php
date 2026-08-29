<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\AuditLog;
use App\Models\Guru;
use App\Models\HariLibur;
use App\Models\IzinGuru;
use App\Models\IzinSiswa;
use App\Models\JadwalHariIni;
use App\Models\JadwalPiket;
use App\Models\Jurusan;
use App\Models\KasusDisiplin;
use App\Models\KasusDisiplinDokumen;
use App\Models\KasusDisiplinLog;
use App\Models\KasusDisiplinPelanggaran;
use App\Models\KasusDisiplinReward;
use App\Models\NotifikasiOrtu;
use App\Models\PengaturanDisiplin;
use App\Models\PengaturanNotifikasi;
use App\Models\PengaturanSekolah;
use App\Models\Pengumuman;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed initial clean production configuration for SMKN 1 Air Naningan.
     */
    public function run(): void
    {
        // 1. Reset seluruh tabel data dummy / operasional secara bersih
        Schema::disableForeignKeyConstraints();

        Absensi::truncate();
        IzinSiswa::truncate();
        IzinGuru::truncate();
        KasusDisiplinDokumen::truncate();
        KasusDisiplinLog::truncate();
        KasusDisiplinPelanggaran::truncate();
        KasusDisiplinReward::truncate();
        KasusDisiplin::truncate();
        NotifikasiOrtu::truncate();
        Pengumuman::truncate();
        AuditLog::truncate();
        SiswaRombel::truncate();
        Siswa::truncate();
        JadwalPiket::truncate();
        Rombel::truncate();
        Jurusan::truncate();
        Guru::truncate();
        HariLibur::truncate();
        TahunAjaran::truncate();
        JadwalHariIni::truncate();
        PengaturanNotifikasi::truncate();
        PengaturanSekolah::truncate();
        User::truncate();

        Schema::enableForeignKeyConstraints();

        // 2. Profil Resmi Sekolah (SMKN 1 Air Naningan)
        PengaturanSekolah::create([
            'nama_instansi_atas' => 'PEMERINTAH PROVINSI LAMPUNG',
            'nama_dinas'          => 'DINAS PENDIDIKAN DAN KEBUDAYAAN',
            'nama_sekolah'        => 'SMK NEGERI 1 AIR NANINGAN',
            'npsn'                => '69888999',
            'alamat'              => 'Jl. Raya Air Naningan No. 01, Kec. Air Naningan',
            'desa_kelurahan'      => 'Air Naningan',
            'kecamatan'           => 'Air Naningan',
            'kabupaten'           => 'Kab. Tanggamus',
            'provinsi'            => 'Lampung',
            'kode_pos'            => '35379',
            'telepon'             => '(0721) 892110',
            'email'               => 'smkn1airnaningan@gmail.com',
            'website'             => 'smkn1airnaningan.sch.id',
            'nama_kepala_sekolah' => 'Drs. H. Ahmad Sudrajat, M.Pd.',
            'nip_kepala_sekolah'  => '19750510 200003 1 005',
        ]);

        // 3. Konfigurasi Gateway Notifikasi WhatsApp & Template Pesan Resmi
        PengaturanNotifikasi::create([
            'wa_provider'                => 'simulasi',
            'wa_api_token'               => 'fonnte_demo_token_smkn1an_2026',
            'wa_endpoint_url'            => 'https://api.fonnte.com/send',
            'is_active'                  => true,
            'ambang_batas_alpha'         => 3,
            'hitung_bolos_bersama_alpha' => true,
            'auto_notif_wali_kelas'      => true,
            'template_terlambat'         => "🔔 *PEMBERITAHUAN KEHADIRAN SISWA*\n*SMK NEGERI 1 AIR NANINGAN*\n\nYth. Bapak/Ibu Wali dari *{nama_siswa}*,\nKami informasikan bahwa ananda telah tiba di sekolah:\n\n• Tanggal : {tanggal}\n• Waktu Masuk : {jam} WIB\n• Status : ⚠️ *TERLAMBAT* (Batas: {batas_jam})\n• Rombel : {kelas}\n\nMohon bimbingan dan motivasinya agar ananda dapat hadir tepat waktu. Terima kasih.\n_SIRANI — Sistem Informasi Responsif Absensi & Penegakan Disiplin SMKN 1 Air Naningan_",
            'template_alpha'             => "⚠️ *PERINGATAN KETIDAKHADIRAN SISWA*\n*SMK NEGERI 1 AIR NANINGAN*\n\nYth. Bapak/Ibu Wali dari *{nama_siswa}*,\nHingga pukul *{jam} WIB* hari ini, ananda tercatat:\n\n• Tanggal : {tanggal}\n• Status : ❌ *ALPHA (Tanpa Keterangan)*\n• Rombel : {kelas}\n\nJika ananda berhalangan hadir karena sakit atau ada keperluan penting, mohon segera menghubungi Wali Kelas atau Guru Piket. Terima kasih.\n_SIRANI — Sistem Informasi Responsif Absensi & Penegakan Disiplin SMKN 1 Air Naningan_",
            'template_izin'              => "📋 *KONFIRMASI PERIZINAN SISWA*\n*SMK NEGERI 1 AIR NANINGAN*\n\nYth. Bapak/Ibu Wali Murid,\nPermohonan izin ananda *{nama_siswa}* ({kelas}) telah diverifikasi oleh Guru Piket:\n\n• Status : 📝 *IZIN*\n• Keterangan : {keterangan}\n• Tanggal : {tanggal}\n\nTerima kasih atas pemberitahuan yang telah disampaikan.\n_SIRANI — Sistem Informasi Responsif Absensi & Penegakan Disiplin SMKN 1 Air Naningan_",
            'template_sakit'             => "🩺 *KONFIRMASI PERIZINAN SISWA*\n*SMK NEGERI 1 AIR NANINGAN*\n\nYth. Bapak/Ibu Wali Murid,\nSurat izin sakit ananda *{nama_siswa}* ({kelas}) telah diverifikasi oleh Guru Piket:\n\n• Status : 🩺 *SAKIT*\n• Keterangan : {keterangan}\n• Tanggal : {tanggal}\n\nSemoga ananda lekas sembuh dan dapat beraktivitas kembali. Terima kasih.\n_SIRANI — Sistem Informasi Responsif Absensi & Penegakan Disiplin SMKN 1 Air Naningan_",
            'template_bolos'             => "🚫 *PERINGATAN PULANG TANPA IZIN (BOLOS)*\n*SMK NEGERI 1 AIR NANINGAN*\n\nYth. Bapak/Ibu Wali dari *{nama_siswa}*,\nKami informasikan ananda terdeteksi mencoba tap kartu pulang sebelum jam pulang resmi tanpa izin Guru Piket:\n\n• Tanggal : {tanggal}\n• Waktu : {jam} WIB\n• Status : 🚫 *PULANG SEBELUM WAKTU (BOLOS)*\n• Rombel : {kelas}\n\nMohon perhatian dan konfirmasi dari Bapak/Ibu. Terima kasih.\n_SIRANI — Sistem Informasi Responsif Absensi & Penegakan Disiplin SMKN 1 Air Naningan_",
            'template_wali_kelas'        => "🚨 *PERINGATAN KESISWAAN SMKN 1 AIR NANINGAN*\nStatus: {tingkat_urgensi}\n\nYth. Bapak/Ibu Wali Kelas *{nama_wali_kelas}* ({kelas}),\n\nSiswa binaan Anda telah memenuhi ketentuan batas pelanggaran kehadiran:\n\n👤 *Data Siswa:*\n• Nama : *{nama_siswa}* (NIS: {nis})\n• Kelas : {kelas} ({jurusan})\n• Kontak Ortu : {nama_ortu} ({no_hp_ortu})\n\n📊 *Akumulasi Pelanggaran: {total_pelanggaran}x Pelanggaran*\n• Alpha : {total_alpha}x\n• Bolos : {total_bolos}x\n• Terlambat : {total_terlambat}x\n\n📋 *Rincian Tanggal Ketidakhadiran:*\n{rincian_pelanggaran}\n\n⚠️ *Rekomendasi Tindakan:*\n{rekomendasi_tindakan}\n\n📄 Lembar Cetak Surat A4: {link_cetak_surat}\n📊 Dasbor Wali Kelas: {link_dasbor_wali}\n\n_SIRANI — Sistem Informasi Responsif Absensi & Penegakan Disiplin SMKN 1 Air Naningan_",
        ]);

        // 4. Tahun Ajaran Aktif
        $taAktif = TahunAjaran::create([
            'nama'      => '2026/2027 Ganjil',
            'is_active' => true,
        ]);

        // 5. Master Program Keahlian / Jurusan Resmi
        $jurRpl  = Jurusan::create(['kode_jurusan' => 'RPL',  'nama_jurusan' => 'Rekayasa Perangkat Lunak']);
        $jurAphp = Jurusan::create(['kode_jurusan' => 'APHP', 'nama_jurusan' => 'Agribisnis Pengolahan Hasil Pertanian']);
        $jurTsm  = Jurusan::create(['kode_jurusan' => 'TSM',  'nama_jurusan' => 'Teknik Sepeda Motor']);

        // 6. Master Rombongan Belajar (Kelas X, XI, XII Lengkap)
        // RPL
        Rombel::create(['tahun_ajaran_id' => $taAktif->id, 'jurusan_id' => $jurRpl->id,  'nama_rombel' => 'X RPL 1',   'tingkat' => 'X']);
        Rombel::create(['tahun_ajaran_id' => $taAktif->id, 'jurusan_id' => $jurRpl->id,  'nama_rombel' => 'XI RPL 1',  'tingkat' => 'XI']);
        Rombel::create(['tahun_ajaran_id' => $taAktif->id, 'jurusan_id' => $jurRpl->id,  'nama_rombel' => 'XII RPL 1', 'tingkat' => 'XII']);

        // APHP
        Rombel::create(['tahun_ajaran_id' => $taAktif->id, 'jurusan_id' => $jurAphp->id, 'nama_rombel' => 'X APHP 1',   'tingkat' => 'X']);
        Rombel::create(['tahun_ajaran_id' => $taAktif->id, 'jurusan_id' => $jurAphp->id, 'nama_rombel' => 'XI APHP 1',  'tingkat' => 'XI']);
        Rombel::create(['tahun_ajaran_id' => $taAktif->id, 'jurusan_id' => $jurAphp->id, 'nama_rombel' => 'XII APHP 1', 'tingkat' => 'XII']);

        // TSM
        Rombel::create(['tahun_ajaran_id' => $taAktif->id, 'jurusan_id' => $jurTsm->id,  'nama_rombel' => 'X TSM 1',   'tingkat' => 'X']);
        Rombel::create(['tahun_ajaran_id' => $taAktif->id, 'jurusan_id' => $jurTsm->id,  'nama_rombel' => 'XI TSM 1',  'tingkat' => 'XI']);
        Rombel::create(['tahun_ajaran_id' => $taAktif->id, 'jurusan_id' => $jurTsm->id,  'nama_rombel' => 'XII TSM 1', 'tingkat' => 'XII']);

        // 7. Jadwal Hari Ini Aktif (Smart Gate)
        $isJumat = (Carbon::today()->dayOfWeek === Carbon::FRIDAY);
        JadwalHariIni::create([
            'tanggal'             => Carbon::today()->toDateString(),
            'jam_masuk_toleransi' => '07:15:00',
            'jam_pulang_mulai'    => $isJumat ? '11:30:00' : '15:30:00',
            'keterangan'          => $isJumat ? 'Jadwal Hari Jumat (Pulang Cepat)' : 'Jadwal Reguler SMKN 1 Air Naningan',
            'is_sesi_buka'        => true,
            'dibuka_oleh'         => 'Administrator Sistem',
            'waktu_buka_sesi'     => '06:00:00',
        ]);

        // 8. Master Konfigurasi Disiplin & Katalog Tata Tertib
        PengaturanDisiplin::getPengaturan();

        // 9. Akun Utama Administrator Sistem
        User::create([
            'name'     => 'Administrator Sistem',
            'email'    => 'admin@smkn1airnaningan.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        AuditLog::catat('system_reset', 'system', 'Inisialisasi master Jurusan (RPL, APHP, TSM) dan Rombel (X, XI, XII).');
    }
}
