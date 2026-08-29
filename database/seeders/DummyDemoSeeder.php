<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\IzinSiswa;
use App\Models\JadwalHariIni;
use App\Models\JadwalPiket;
use App\Models\Jurusan;
use App\Models\NotifikasiOrtu;
use App\Models\PengaturanNotifikasi;
use App\Models\PengaturanSekolah;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DummyDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Nonaktifkan foreign key checks untuk reset bersih
        Schema::disableForeignKeyConstraints();

        Absensi::truncate();
        IzinSiswa::truncate();
        SiswaRombel::truncate();
        Siswa::truncate();
        Guru::truncate();
        Rombel::truncate();
        Jurusan::truncate();
        TahunAjaran::truncate();
        JadwalPiket::truncate();
        JadwalHariIni::truncate();
        NotifikasiOrtu::truncate();
        PengaturanNotifikasi::truncate();
        PengaturanSekolah::truncate();
        User::truncate();

        Schema::enableForeignKeyConstraints();

        // 2. Pengaturan Profil Sekolah Resmi (SMKN 1 Air Naningan)
        $sekolah = PengaturanSekolah::create([
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

        // 3. Pengaturan Gateway Notifikasi WhatsApp & Template Lengkap
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

        // 4. Tahun Ajaran
        $taLalu = TahunAjaran::create([
            'nama'      => '2025/2026',
            'is_active' => false,
        ]);

        $taAktif = TahunAjaran::create([
            'nama'      => '2026/2027',
            'is_active' => true,
        ]);

        // 5. Program Keahlian / Jurusan Resmi
        $jurRpl  = Jurusan::create(['kode_jurusan' => 'RPL',  'nama_jurusan' => 'Rekayasa Perangkat Lunak']);
        $jurAphp = Jurusan::create(['kode_jurusan' => 'APHP', 'nama_jurusan' => 'Agribisnis Pengolahan Hasil Pertanian']);
        $jurTsm  = Jurusan::create(['kode_jurusan' => 'TSM',  'nama_jurusan' => 'Teknik Sepeda Motor']);

        $makeFaceEmbedding = function (int $id, string $type = 'guru') {
            return array_map(function ($idx) use ($id, $type) {
                $factor = ($type === 'guru') ? ($id * 13.7) : ($id * 7.3);
                return round(sin($factor + $idx * 0.12) * 0.75 + cos($idx * 0.08) * 0.2, 6);
            }, range(0, 127));
        };

        // 6. Master Guru & Tenaga Kependidikan (8 Orang + 1 Admin TU)
        $guruKepsek = Guru::create([
            'nip'                => '197505102000031005',
            'nama'               => 'Drs. H. Ahmad Sudrajat, M.Pd.',
            'jabatan'            => 'Kepala Sekolah',
            'no_hp'              => '081272001001',
            'status'             => 'aktif',
            'face_embedding'     => $makeFaceEmbedding(1, 'guru'),
            'face_registered_at' => now(),
        ]);

        $guruRpl = Guru::create([
            'nip'                => '198503152010011008',
            'nama'               => 'Budi Santoso, S.Kom.',
            'jabatan'            => 'Wali Kelas',
            'no_hp'              => '081272001002',
            'status'             => 'aktif',
            'face_embedding'     => $makeFaceEmbedding(2, 'guru'),
            'face_registered_at' => now(),
        ]);

        $guruAphp = Guru::create([
            'nip'                => '198807202014022003',
            'nama'               => 'Siti Aminah, S.TP.',
            'jabatan'            => 'Wali Kelas',
            'no_hp'              => '081272001003',
            'status'             => 'aktif',
            'face_embedding'     => $makeFaceEmbedding(3, 'guru'),
            'face_registered_at' => now(),
        ]);

        $guruTsm = Guru::create([
            'nip'                => '198611122011011005',
            'nama'               => 'Agus Prasetyo, S.T.',
            'jabatan'            => 'Wali Kelas',
            'no_hp'              => '081272001004',
            'status'             => 'aktif',
            'face_embedding'     => $makeFaceEmbedding(4, 'guru'),
            'face_registered_at' => now(),
        ]);

        $guruBk = Guru::create([
            'nip'                => '199004252018022004',
            'nama'               => 'Dewi Lestari, S.Pd.',
            'jabatan'            => 'Guru BK',
            'no_hp'              => '081272001005',
            'status'             => 'aktif',
            'face_embedding'     => $makeFaceEmbedding(5, 'guru'),
            'face_registered_at' => now(),
        ]);

        $guruRian = Guru::create([
            'nip'                => '199208102019031007',
            'nama'               => 'Rian Kurniawan, S.Pd.',
            'jabatan'            => 'Guru Penjaskes',
            'no_hp'              => '081272001006',
            'status'             => 'aktif',
            'face_embedding'     => $makeFaceEmbedding(6, 'guru'),
            'face_registered_at' => now(),
        ]);

        $guruNurul = Guru::create([
            'nip'                => '199401152020122009',
            'nama'               => 'Nurul Hidayah, S.Pd.',
            'jabatan'            => 'Guru Bahasa',
            'no_hp'              => '081272001007',
            'status'             => 'aktif',
            'face_embedding'     => $makeFaceEmbedding(7, 'guru'),
            'face_registered_at' => now(),
        ]);

        $guruHendra = Guru::create([
            'nip'                => '199103202017011002',
            'nama'               => 'Hendra Pratama, S.Pd.',
            'jabatan'            => 'Guru Kejuruan RPL',
            'no_hp'              => '081272001008',
            'status'             => 'aktif',
            'face_embedding'     => $makeFaceEmbedding(8, 'guru'),
            'face_registered_at' => now(),
        ]);

        $guruAdmin = Guru::create([
            'nama'               => 'Admin Tata Usaha',
            'jabatan'            => 'Administrator Sistem',
            'no_hp'              => '081272001999',
            'status'             => 'aktif',
            'face_embedding'     => $makeFaceEmbedding(9, 'guru'),
            'face_registered_at' => now(),
        ]);

        // 7. Akun Login Sistem Multi-Role
        User::create([
            'name'     => 'Admin Tata Usaha',
            'email'    => 'admin@smkn1airnaningan.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'guru_id'  => $guruAdmin->id,
        ]);

        User::create([
            'name'     => $guruKepsek->nama,
            'email'    => 'kepsek@smkn1airnaningan.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'kepala_sekolah',
            'guru_id'  => $guruKepsek->id,
        ]);

        User::create([
            'name'     => $guruRpl->nama,
            'email'    => 'walikelas.rpl@smkn1airnaningan.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'wali_kelas',
            'guru_id'  => $guruRpl->id,
        ]);

        User::create([
            'name'     => $guruAphp->nama,
            'email'    => 'walikelas.aphp@smkn1airnaningan.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'wali_kelas',
            'guru_id'  => $guruAphp->id,
        ]);

        User::create([
            'name'     => $guruTsm->nama,
            'email'    => 'walikelas.tsm@smkn1airnaningan.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'wali_kelas',
            'guru_id'  => $guruTsm->id,
        ]);

        User::create([
            'name'     => $guruBk->nama,
            'email'    => 'bk@smkn1airnaningan.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'guru',
            'guru_id'  => $guruBk->id,
        ]);

        User::create([
            'name'     => $guruRian->nama,
            'email'    => 'piket.rian@smkn1airnaningan.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'guru',
            'guru_id'  => $guruRian->id,
        ]);

        User::create([
            'name'     => $guruNurul->nama,
            'email'    => 'guru.nurul@smkn1airnaningan.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'guru',
            'guru_id'  => $guruNurul->id,
        ]);

        // 9. Jadwal Guru Piket Harian (Senin - Jumat)
        JadwalPiket::create(['hari' => 'Senin',  'guru_id' => $guruRpl->id,   'keterangan' => 'Piket Pagi & Kedisiplinan']);
        JadwalPiket::create(['hari' => 'Senin',  'guru_id' => $guruBk->id,    'keterangan' => 'Piket Bimbingan Siswa']);
        JadwalPiket::create(['hari' => 'Selasa', 'guru_id' => $guruAphp->id,  'keterangan' => 'Piket Pagi']);
        JadwalPiket::create(['hari' => 'Selasa', 'guru_id' => $guruRian->id,  'keterangan' => 'Piket Gerbang & Tertib Siswa']);
        JadwalPiket::create(['hari' => 'Rabu',   'guru_id' => $guruTsm->id,   'keterangan' => 'Piket Pagi']);
        JadwalPiket::create(['hari' => 'Rabu',   'guru_id' => $guruNurul->id, 'keterangan' => 'Piket Administrasi Presensi']);
        JadwalPiket::create(['hari' => 'Kamis',  'guru_id' => $guruRpl->id,   'keterangan' => 'Piket Pagi']);
        JadwalPiket::create(['hari' => 'Kamis',  'guru_id' => $guruRian->id,  'keterangan' => 'Piket Gerbang & Kedisiplinan']);
        JadwalPiket::create(['hari' => 'Jumat',  'guru_id' => $guruAphp->id,  'keterangan' => 'Piket Pagi & Kebersihan']);
        JadwalPiket::create(['hari' => 'Jumat',  'guru_id' => $guruNurul->id, 'keterangan' => 'Piket Presensi']);

        // Jadwal Hari Ini Aktif
        $isJumat = (Carbon::today()->dayOfWeek === Carbon::FRIDAY);
        JadwalHariIni::create([
            'tanggal'             => Carbon::today()->toDateString(),
            'jam_masuk_toleransi' => '07:15:00',
            'jam_pulang_mulai'    => $isJumat ? '11:30:00' : '15:30:00',
            'keterangan'          => $isJumat ? 'Jadwal Hari Jumat (Pulang Cepat)' : 'Jadwal Reguler SMKN 1 Air Naningan',
            'diubah_oleh'         => 'Sistem Otomatis',
        ]);

        // 10. Master Rombel (Kelas)
        $rombelX_RPL   = Rombel::create(['tahun_ajaran_id' => $taAktif->id, 'jurusan_id' => $jurRpl->id,  'nama_rombel' => 'X RPL 1',   'tingkat' => 'X',   'wali_kelas_id' => $guruRpl->id]);
        $rombelXI_RPL  = Rombel::create(['tahun_ajaran_id' => $taAktif->id, 'jurusan_id' => $jurRpl->id,  'nama_rombel' => 'XI RPL 1',  'tingkat' => 'XI',  'wali_kelas_id' => $guruRpl->id]);
        $rombelXII_RPL = Rombel::create(['tahun_ajaran_id' => $taAktif->id, 'jurusan_id' => $jurRpl->id,  'nama_rombel' => 'XII RPL 1', 'tingkat' => 'XII', 'wali_kelas_id' => $guruRpl->id]);

        $rombelX_APHP   = Rombel::create(['tahun_ajaran_id' => $taAktif->id, 'jurusan_id' => $jurAphp->id, 'nama_rombel' => 'X APHP 1',   'tingkat' => 'X',   'wali_kelas_id' => $guruAphp->id]);
        $rombelXI_APHP  = Rombel::create(['tahun_ajaran_id' => $taAktif->id, 'jurusan_id' => $jurAphp->id, 'nama_rombel' => 'XI APHP 1',  'tingkat' => 'XI',  'wali_kelas_id' => $guruAphp->id]);
        $rombelXII_APHP = Rombel::create(['tahun_ajaran_id' => $taAktif->id, 'jurusan_id' => $jurAphp->id, 'nama_rombel' => 'XII APHP 1', 'tingkat' => 'XII', 'wali_kelas_id' => $guruAphp->id]);

        $rombelX_TSM   = Rombel::create(['tahun_ajaran_id' => $taAktif->id, 'jurusan_id' => $jurTsm->id,  'nama_rombel' => 'X TSM 1',   'tingkat' => 'X',   'wali_kelas_id' => $guruTsm->id]);
        $rombelXI_TSM  = Rombel::create(['tahun_ajaran_id' => $taAktif->id, 'jurusan_id' => $jurTsm->id,  'nama_rombel' => 'XI TSM 1',  'tingkat' => 'XI',  'wali_kelas_id' => $guruTsm->id]);
        $rombelXII_TSM = Rombel::create(['tahun_ajaran_id' => $taAktif->id, 'jurusan_id' => $jurTsm->id,  'nama_rombel' => 'XII TSM 1', 'tingkat' => 'XII', 'wali_kelas_id' => $guruTsm->id]);

        $rombelAlumni_RPL = Rombel::create(['tahun_ajaran_id' => $taLalu->id, 'jurusan_id' => $jurRpl->id, 'nama_rombel' => 'XII RPL 1 (25/26)', 'tingkat' => 'XII', 'wali_kelas_id' => $guruRpl->id]);

        // 11. Data Siswa Realistis & Skenario Lengkap Simulasi Seluruh Aturan
        $daftarSiswa = [
            // ─── KELAS XII RPL 1 (Binaan Utama Budi Santoso, S.Kom.) ───
            [
                'nis' => '20240101', 'nisn' => '0071234001', 'nama' => 'Ahmad Fauzi',
                'nama_ortu' => 'Bambang Sutedjo', 'no_hp_ortu' => '081369010001',
                'rombel' => $rombelXII_RPL, 'status' => 'aktif', 'rfid' => '2476421242', // Kartu Fisik User
                'skenario' => 'teladan_100'
            ],
            [
                'nis' => '20240102', 'nisn' => '0071234002', 'nama' => 'Dimas Arya Pratama',
                'nama_ortu' => 'Joko Susilo', 'no_hp_ortu' => '081369010002',
                'rombel' => $rombelXII_RPL, 'status' => 'aktif', 'rfid' => 'UID20240102',
                'skenario' => 'sering_terlambat'
            ],
            [
                'nis' => '20240103', 'nisn' => '0071234003', 'nama' => 'Rangga Aditya',
                'nama_ortu' => 'Slamet Riyadi', 'no_hp_ortu' => '081369010003',
                'rombel' => $rombelXII_RPL, 'status' => 'aktif', 'rfid' => 'UID20240103',
                'skenario' => 'kasus_3alpha_disahkan'
            ],
            [
                'nis' => '20240104', 'nisn' => '0071234004', 'nama' => 'Fajar Ramadhan',
                'nama_ortu' => 'Hendra Gunawan', 'no_hp_ortu' => '081369010004',
                'rombel' => $rombelXII_RPL, 'status' => 'aktif', 'rfid' => 'UID20240104',
                'skenario' => 'kasus_3alpha_menunggu_kepsek'
            ],
            [
                'nis' => '20240105', 'nisn' => '0071234005', 'nama' => 'Citra Ayu Lestari',
                'nama_ortu' => 'Supriadi', 'no_hp_ortu' => '081369010005',
                'rombel' => $rombelXII_RPL, 'status' => 'aktif', 'rfid' => 'UID20240105',
                'skenario' => 'izin_dan_sakit'
            ],
            [
                'nis' => '20240106', 'nisn' => '0071234006', 'nama' => 'Eko Prasetyo',
                'nama_ortu' => 'Sugeng Widodo', 'no_hp_ortu' => '081369010006',
                'rombel' => $rombelXII_RPL, 'status' => 'aktif', 'rfid' => 'UID20240106',
                'skenario' => 'pulang_cepat_dispensasi'
            ],

            // ─── KELAS XI RPL 1 (Ada Siswa PKL & Presensi Manual) ───
            [
                'nis' => '20250101', 'nisn' => '0081234001', 'nama' => 'Bayu Saputra',
                'nama_ortu' => 'Yudi Irawan', 'no_hp_ortu' => '081369020001',
                'rombel' => $rombelXI_RPL, 'status' => 'pkl', 'rfid' => 'UID20250101',
                'skenario' => 'pkl_industri'
            ],
            [
                'nis' => '20250102', 'nisn' => '0081234002', 'nama' => 'Galih Wicaksono',
                'nama_ortu' => 'Agus Salim', 'no_hp_ortu' => '081369020002',
                'rombel' => $rombelXI_RPL, 'status' => 'pkl', 'rfid' => 'UID20250102',
                'skenario' => 'pkl_industri'
            ],
            [
                'nis' => '20250103', 'nisn' => '0081234003', 'nama' => 'Intan Permata',
                'nama_ortu' => 'Hartono', 'no_hp_ortu' => '081369020003',
                'rombel' => $rombelXI_RPL, 'status' => 'aktif', 'rfid' => 'UID20250103',
                'skenario' => 'presensi_manual_lupa_kartu'
            ],
            [
                'nis' => '20250104', 'nisn' => '0081234004', 'nama' => 'Kevin Sanjaya',
                'nama_ortu' => 'Robertus', 'no_hp_ortu' => '081369020004',
                'rombel' => $rombelXI_RPL, 'status' => 'aktif', 'rfid' => 'UID20250104',
                'skenario' => 'hadir_reguler'
            ],

            // ─── KELAS X RPL 1 (Ada Izin Ditolak & Siswa Baru) ───
            [
                'nis' => '20260101', 'nisn' => '0091234001', 'nama' => 'Muhammad Rizky',
                'nama_ortu' => 'Ahmad Yani', 'no_hp_ortu' => '081369030001',
                'rombel' => $rombelX_RPL, 'status' => 'aktif', 'rfid' => 'UID20260101',
                'skenario' => 'izin_ditolak'
            ],
            [
                'nis' => '20260102', 'nisn' => '0091234002', 'nama' => 'Nanda Pratama',
                'nama_ortu' => 'Darmawan', 'no_hp_ortu' => '081369030002',
                'rombel' => $rombelX_RPL, 'status' => 'aktif', 'rfid' => 'UID20260102',
                'skenario' => 'hadir_reguler'
            ],
            [
                'nis' => '20260103', 'nisn' => '0091234003', 'nama' => 'Putri Anggraini',
                'nama_ortu' => 'Heri Sunaryo', 'no_hp_ortu' => '081369030003',
                'rombel' => $rombelX_RPL, 'status' => 'aktif', 'rfid' => 'UID20260103',
                'skenario' => 'hadir_reguler'
            ],
            [
                'nis' => '20260104', 'nisn' => '0091234004', 'nama' => 'Reza Firmansyah',
                'nama_ortu' => 'Samsul Bahri', 'no_hp_ortu' => '081369030004',
                'rombel' => $rombelX_RPL, 'status' => 'aktif', 'rfid' => 'UID20260104',
                'skenario' => 'hadir_reguler'
            ],

            // ─── KELAS XI APHP 1 (Binaan Siti Aminah, S.TP. & Izin Menunggu) ───
            [
                'nis' => '20250201', 'nisn' => '0082234001', 'nama' => 'Nabila Putri',
                'nama_ortu' => 'Mansur', 'no_hp_ortu' => '081369040001',
                'rombel' => $rombelXI_APHP, 'status' => 'aktif', 'rfid' => 'UID20250201',
                'skenario' => 'izin_menunggu_piket'
            ],
            [
                'nis' => '20250202', 'nisn' => '0082234002', 'nama' => 'Tiara Maharani',
                'nama_ortu' => 'Wawan', 'no_hp_ortu' => '081369040002',
                'rombel' => $rombelXI_APHP, 'status' => 'aktif', 'rfid' => 'UID20250202',
                'skenario' => 'hadir_reguler'
            ],
            [
                'nis' => '20250203', 'nisn' => '0082234003', 'nama' => 'Wahyu Hidayat',
                'nama_ortu' => 'Syarifuddin', 'no_hp_ortu' => '081369040003',
                'rombel' => $rombelXI_APHP, 'status' => 'aktif', 'rfid' => 'UID20250203',
                'skenario' => 'hadir_reguler'
            ],
            [
                'nis' => '20250204', 'nisn' => '0082234004', 'nama' => 'Zulfa Khairunnisa',
                'nama_ortu' => 'Abdullah', 'no_hp_ortu' => '081369040004',
                'rombel' => $rombelXI_APHP, 'status' => 'aktif', 'rfid' => 'UID20250204',
                'skenario' => 'hadir_reguler'
            ],

            // ─── KELAS XII APHP 1 ───
            [
                'nis' => '20240201', 'nisn' => '0072234001', 'nama' => 'Aditya Bagus',
                'nama_ortu' => 'Rustam', 'no_hp_ortu' => '081369050001',
                'rombel' => $rombelXII_APHP, 'status' => 'aktif', 'rfid' => 'UID20240201',
                'skenario' => 'hadir_reguler'
            ],
            [
                'nis' => '20240202', 'nisn' => '0072234002', 'nama' => 'Bagas Kara',
                'nama_ortu' => 'Suroso', 'no_hp_ortu' => '081369050002',
                'rombel' => $rombelXII_APHP, 'status' => 'aktif', 'rfid' => 'UID20240202',
                'skenario' => 'hadir_reguler'
            ],

            // ─── KELAS X TSM 1 (Binaan Agus Prasetyo, S.T. & Kasus Bimbingan Ulang) ───
            [
                'nis' => '20260301', 'nisn' => '0093234001', 'nama' => 'Aldi Kurniawan',
                'nama_ortu' => 'Arifin', 'no_hp_ortu' => '081369060001',
                'rombel' => $rombelX_TSM, 'status' => 'aktif', 'rfid' => 'UID20260301',
                'skenario' => 'kasus_perlu_tindak_lanjut_ulang'
            ],
            [
                'nis' => '20260302', 'nisn' => '0093234002', 'nama' => 'Bima Sakti',
                'nama_ortu' => 'Subagio', 'no_hp_ortu' => '081369060002',
                'rombel' => $rombelX_TSM, 'status' => 'aktif', 'rfid' => 'UID20260302',
                'skenario' => 'peringatan_bolos'
            ],
            [
                'nis' => '20260303', 'nisn' => '0093234003', 'nama' => 'Candra Wijaya',
                'nama_ortu' => 'Marwan', 'no_hp_ortu' => '081369060003',
                'rombel' => $rombelX_TSM, 'status' => 'aktif', 'rfid' => 'UID20260303',
                'skenario' => 'hadir_reguler'
            ],
            [
                'nis' => '20260304', 'nisn' => '0093234004', 'nama' => 'Deni Saputra',
                'nama_ortu' => 'Kasto', 'no_hp_ortu' => '081369060004',
                'rombel' => $rombelX_TSM, 'status' => 'aktif', 'rfid' => 'UID20260304',
                'skenario' => 'hadir_reguler'
            ],

            // ─── KELAS XII TSM 1 ───
            [
                'nis' => '20240301', 'nisn' => '0073234001', 'nama' => 'Fajar Nugroho',
                'nama_ortu' => 'Sarjono', 'no_hp_ortu' => '081369070001',
                'rombel' => $rombelXII_TSM, 'status' => 'aktif', 'rfid' => 'UID20240301',
                'skenario' => 'hadir_reguler'
            ],
            [
                'nis' => '20240302', 'nisn' => '0073234002', 'nama' => 'Guntur Prakoso',
                'nama_ortu' => 'Warsono', 'no_hp_ortu' => '081369070002',
                'rombel' => $rombelXII_TSM, 'status' => 'aktif', 'rfid' => 'UID20240302',
                'skenario' => 'hadir_reguler'
            ],

            // ─── SISWA STATUS KHUSUS (ALUMNI, PINDAH, KELUAR) ───
            [
                'nis' => '20230101', 'nisn' => '0061234001', 'nama' => 'Rizky Firmansyah (Alumni)',
                'nama_ortu' => 'H. Mulyadi', 'no_hp_ortu' => '081369080001',
                'rombel' => $rombelAlumni_RPL, 'status' => 'lulus', 'rfid' => 'UIDALUMNI01',
                'skenario' => 'alumni_lulus', 'tgl_nonaktif' => '2026-06-25'
            ],
            [
                'nis' => '20240199', 'nisn' => '0071234099', 'nama' => 'Sandi Pratama (Pindah)',
                'nama_ortu' => 'Sukirno', 'no_hp_ortu' => '081369080002',
                'rombel' => $rombelXII_RPL, 'status' => 'pindah', 'rfid' => 'UIDPINDAH01',
                'skenario' => 'siswa_pindah', 'tgl_nonaktif' => '2026-07-15'
            ],
            [
                'nis' => '20240198', 'nisn' => '0071234098', 'nama' => 'Taufik Hidayat (Keluar)',
                'nama_ortu' => 'Darminto', 'no_hp_ortu' => '081369080003',
                'rombel' => $rombelXII_RPL, 'status' => 'keluar', 'rfid' => 'UIDKELUAR01',
                'skenario' => 'siswa_keluar', 'tgl_nonaktif' => '2026-08-01'
            ],
        ];

        $createdSiswaList = [];

        foreach ($daftarSiswa as $idx => $item) {
            $isAktif = in_array($item['status'], ['aktif', 'pkl']);
            $siswa = Siswa::create([
                'nis'                => $item['nis'],
                'nisn'               => $item['nisn'],
                'nama'               => $item['nama'],
                'nama_ortu'          => $item['nama_ortu'],
                'no_hp_ortu'         => $item['no_hp_ortu'],
                'status'             => $item['status'],
                'face_embedding'     => $isAktif ? $makeFaceEmbedding($idx + 10, 'siswa') : null,
                'face_registered_at' => $isAktif ? now() : null,
            ]);

            $isAktifMembership = in_array($item['status'], ['aktif', 'pkl']);
            $siswaRombel = SiswaRombel::create([
                'siswa_id'           => $siswa->id,
                'rombel_id'          => $item['rombel']->id,
                'tahun_ajaran_id'    => $item['rombel']->tahun_ajaran_id,
                'status_keanggotaan' => $isAktifMembership ? 'aktif' : $item['status'],
            ]);

            // Riwayat keanggotaan tahun lalu untuk kelas XII
            if ($item['rombel']->tingkat === 'XII' && $isAktifMembership) {
                SiswaRombel::create([
                    'siswa_id'           => $siswa->id,
                    'rombel_id'          => $rombelXI_RPL->id,
                    'tahun_ajaran_id'    => $taLalu->id,
                    'status_keanggotaan' => 'naik',
                ]);
            }

            $createdSiswaList[] = [
                'siswa'        => $siswa,
                'siswa_rombel' => $siswaRombel,
                'skenario'     => $item['skenario'],
            ];
        }

        // 12. Buat Histori Absensi Realistis Selama 30 Hari Kerja Terakhir (Senin - Jumat)
        $hariEfektifSekolah = [];
        $cursorDate = Carbon::today()->subDays(30);

        while ($cursorDate->lte(Carbon::today())) {
            // Lewati hari Sabtu dan Minggu
            if (!in_array($cursorDate->dayOfWeek, [0, 6])) {
                $hariEfektifSekolah[] = $cursorDate->copy();
            }
            $cursorDate->addDay();
        }

        // Generate Histori Absensi Siswa
        foreach ($createdSiswaList as $sData) {
            $siswa = $sData['siswa'];
            $sr    = $sData['siswa_rombel'];
            $skenario = $sData['skenario'];

            // Siswa status nonaktif & PKL tidak memiliki absensi fisik di kelas
            if (in_array($skenario, ['alumni_lulus', 'siswa_pindah', 'siswa_keluar', 'pkl_industri'])) {
                continue;
            }

            foreach ($hariEfektifSekolah as $idx => $tglObj) {
                $tglStr = $tglObj->toDateString();
                $isToday = ($tglStr === Carbon::today()->toDateString());

                switch ($skenario) {
                    case 'teladan_100':
                        // 100% Hadir Tepat Waktu (06:40 - 07:05) & Jam Pulang (15:35 - 16:00)
                        $menitMasuk = rand(40, 58);
                        Absensi::create([
                            'pemilik_type'    => 'siswa',
                            'pemilik_id'      => $siswa->id,
                            'siswa_rombel_id' => $sr->id,
                            'tanggal'         => $tglStr,
                            'jam_masuk'       => sprintf('06:%02d:00', $menitMasuk),
                            'jam_pulang'      => $isToday ? null : sprintf('15:%02d:00', rand(35, 55)),
                            'status'          => 'hadir',
                            'sumber_absen'    => 'rfid',
                        ]);
                        break;

                    case 'sering_terlambat':
                        // Sering terlambat (masuk 07:18 - 07:35) setiap beberapa hari sekali
                        $isLate = ($idx % 3 === 0);
                        if ($isLate) {
                            $menitLate = rand(18, 35);
                            Absensi::create([
                                'pemilik_type'    => 'siswa',
                                'pemilik_id'      => $siswa->id,
                                'siswa_rombel_id' => $sr->id,
                                'tanggal'         => $tglStr,
                                'jam_masuk'       => sprintf('07:%02d:00', $menitLate),
                                'jam_pulang'      => $isToday ? null : sprintf('15:%02d:00', rand(30, 45)),
                                'status'          => 'terlambat',
                                'sumber_absen'    => 'rfid',
                            ]);
                        } else {
                            Absensi::create([
                                'pemilik_type'    => 'siswa',
                                'pemilik_id'      => $siswa->id,
                                'siswa_rombel_id' => $sr->id,
                                'tanggal'         => $tglStr,
                                'jam_masuk'       => '07:05:00',
                                'jam_pulang'      => $isToday ? null : '15:30:00',
                                'status'          => 'hadir',
                                'sumber_absen'    => 'rfid',
                            ]);
                        }
                        break;

                    case 'kasus_3alpha_disahkan':
                        // Akumulasi 3x Alpha di hari-hari sebelumnya (hari ke-2, ke-7, ke-13)
                        $isAlpha = in_array($idx, [2, 7, 13]);
                        if ($isAlpha) {
                            Absensi::create([
                                'pemilik_type'    => 'siswa',
                                'pemilik_id'      => $siswa->id,
                                'siswa_rombel_id' => $sr->id,
                                'tanggal'         => $tglStr,
                                'jam_masuk'       => null,
                                'jam_pulang'      => null,
                                'status'          => 'alpha',
                                'sumber_absen'    => 'sistem',
                            ]);
                        } else {
                            Absensi::create([
                                'pemilik_type'    => 'siswa',
                                'pemilik_id'      => $siswa->id,
                                'siswa_rombel_id' => $sr->id,
                                'tanggal'         => $tglStr,
                                'jam_masuk'       => '06:55:00',
                                'jam_pulang'      => $isToday ? null : '15:40:00',
                                'status'          => 'hadir',
                                'sumber_absen'    => 'rfid',
                            ]);
                        }
                        break;

                    case 'kasus_3alpha_menunggu_kepsek':
                        // 3x Alpha (hari ke-5, ke-11, ke-17) & sedang menunggu validasi kepsek
                        $isAlpha = in_array($idx, [5, 11, 17]);
                        if ($isAlpha) {
                            Absensi::create([
                                'pemilik_type'    => 'siswa',
                                'pemilik_id'      => $siswa->id,
                                'siswa_rombel_id' => $sr->id,
                                'tanggal'         => $tglStr,
                                'jam_masuk'       => null,
                                'jam_pulang'      => null,
                                'status'          => 'alpha',
                                'sumber_absen'    => 'sistem',
                            ]);
                        } else {
                            Absensi::create([
                                'pemilik_type'    => 'siswa',
                                'pemilik_id'      => $siswa->id,
                                'siswa_rombel_id' => $sr->id,
                                'tanggal'         => $tglStr,
                                'jam_masuk'       => '07:02:00',
                                'jam_pulang'      => $isToday ? null : '15:30:00',
                                'status'          => 'hadir',
                                'sumber_absen'    => 'rfid',
                            ]);
                        }
                        break;

                    case 'kasus_perlu_tindak_lanjut_ulang':
                        // Siswa dengan 3x alpha di mana Kepsek menginstruksikan tindak lanjut ulang
                        $isAlpha = in_array($idx, [4, 9, 15]);
                        if ($isAlpha) {
                            Absensi::create([
                                'pemilik_type'    => 'siswa',
                                'pemilik_id'      => $siswa->id,
                                'siswa_rombel_id' => $sr->id,
                                'tanggal'         => $tglStr,
                                'jam_masuk'       => null,
                                'jam_pulang'      => null,
                                'status'          => 'alpha',
                                'sumber_absen'    => 'sistem',
                            ]);
                        } else {
                            Absensi::create([
                                'pemilik_type'    => 'siswa',
                                'pemilik_id'      => $siswa->id,
                                'siswa_rombel_id' => $sr->id,
                                'tanggal'         => $tglStr,
                                'jam_masuk'       => '07:08:00',
                                'jam_pulang'      => $isToday ? null : '15:30:00',
                                'status'          => 'hadir',
                                'sumber_absen'    => 'rfid',
                            ]);
                        }
                        break;

                    case 'izin_dan_sakit':
                        // Memiliki 1 Sakit (surat dokter) dan 1 Izin Keperluan Resmi
                        if ($idx === 3) {
                            IzinSiswa::create([
                                'siswa_id'       => $siswa->id,
                                'tanggal'        => $tglStr,
                                'jenis'          => 'sakit',
                                'status'         => 'disetujui',
                                'keterangan'     => 'Demam tinggi & flu (ada surat dokter)',
                                'disetujui_oleh' => $guruRpl->nama,
                            ]);
                            Absensi::create([
                                'pemilik_type'    => 'siswa',
                                'pemilik_id'      => $siswa->id,
                                'siswa_rombel_id' => $sr->id,
                                'tanggal'         => $tglStr,
                                'status'          => 'sakit',
                                'sumber_absen'    => 'manual',
                            ]);
                        } elseif ($idx === 10) {
                            IzinSiswa::create([
                                'siswa_id'       => $siswa->id,
                                'tanggal'        => $tglStr,
                                'jenis'          => 'izin',
                                'status'         => 'disetujui',
                                'keterangan'     => 'Menghadiri pernikahan keluarga di luar kota',
                                'disetujui_oleh' => $guruRpl->nama,
                            ]);
                            Absensi::create([
                                'pemilik_type'    => 'siswa',
                                'pemilik_id'      => $siswa->id,
                                'siswa_rombel_id' => $sr->id,
                                'tanggal'         => $tglStr,
                                'status'          => 'izin',
                                'sumber_absen'    => 'manual',
                            ]);
                        } else {
                            Absensi::create([
                                'pemilik_type'    => 'siswa',
                                'pemilik_id'      => $siswa->id,
                                'siswa_rombel_id' => $sr->id,
                                'tanggal'         => $tglStr,
                                'jam_masuk'       => '06:50:00',
                                'jam_pulang'      => $isToday ? null : '15:35:00',
                                'status'          => 'hadir',
                                'sumber_absen'    => 'rfid',
                            ]);
                        }
                        break;

                    case 'pulang_cepat_dispensasi':
                        // Memiliki riwayat izin dispensasi pulang lebih awal
                        if ($idx === 8) {
                            IzinSiswa::create([
                                'siswa_id'       => $siswa->id,
                                'tanggal'        => $tglStr,
                                'jenis'          => 'dispensasi',
                                'status'         => 'disetujui',
                                'keterangan'     => 'Dispensasi persiapan Lomba Kompetensi Siswa (LKS)',
                                'disetujui_oleh' => $guruRpl->nama,
                            ]);
                            Absensi::create([
                                'pemilik_type'    => 'siswa',
                                'pemilik_id'      => $siswa->id,
                                'siswa_rombel_id' => $sr->id,
                                'tanggal'         => $tglStr,
                                'jam_masuk'       => '06:48:00',
                                'jam_pulang'      => '12:30:00', // Pulang lebih awal
                                'status'          => 'hadir',
                                'sumber_absen'    => 'rfid',
                            ]);
                        } else {
                            Absensi::create([
                                'pemilik_type'    => 'siswa',
                                'pemilik_id'      => $siswa->id,
                                'siswa_rombel_id' => $sr->id,
                                'tanggal'         => $tglStr,
                                'jam_masuk'       => '06:52:00',
                                'jam_pulang'      => $isToday ? null : '15:35:00',
                                'status'          => 'hadir',
                                'sumber_absen'    => 'rfid',
                            ]);
                        }
                        break;

                    case 'presensi_manual_lupa_kartu':
                        // Memiliki presensi manual yang dicatat guru piket
                        if ($idx === 6) {
                            Absensi::create([
                                'pemilik_type'    => 'siswa',
                                'pemilik_id'      => $siswa->id,
                                'siswa_rombel_id' => $sr->id,
                                'tanggal'         => $tglStr,
                                'jam_masuk'       => '07:05:00',
                                'jam_pulang'      => '15:30:00',
                                'status'          => 'hadir',
                                'sumber_absen'    => 'manual',
                            ]);
                        } else {
                            Absensi::create([
                                'pemilik_type'    => 'siswa',
                                'pemilik_id'      => $siswa->id,
                                'siswa_rombel_id' => $sr->id,
                                'tanggal'         => $tglStr,
                                'jam_masuk'       => '06:58:00',
                                'jam_pulang'      => $isToday ? null : '15:30:00',
                                'status'          => 'hadir',
                                'sumber_absen'    => 'rfid',
                            ]);
                        }
                        break;

                    case 'izin_menunggu_piket':
                        // Hari ini memiliki permohonan izin dengan status MENUNGGU (pending)
                        if ($isToday) {
                            IzinSiswa::create([
                                'siswa_id'       => $siswa->id,
                                'tanggal'        => $tglStr,
                                'jenis'          => 'izin',
                                'status'         => 'pending',
                                'keterangan'     => 'Izin urusan keluarga mendesak ke luar kota',
                                'disetujui_oleh' => null,
                            ]);
                        } else {
                            Absensi::create([
                                'pemilik_type'    => 'siswa',
                                'pemilik_id'      => $siswa->id,
                                'siswa_rombel_id' => $sr->id,
                                'tanggal'         => $tglStr,
                                'jam_masuk'       => '07:00:00',
                                'jam_pulang'      => '15:30:00',
                                'status'          => 'hadir',
                                'sumber_absen'    => 'rfid',
                            ]);
                        }
                        break;

                    case 'izin_ditolak':
                        // Pernah mengajukan izin namun ditolak oleh guru piket
                        if ($idx === 5) {
                            IzinSiswa::create([
                                'siswa_id'       => $siswa->id,
                                'tanggal'        => $tglStr,
                                'jenis'          => 'izin',
                                'status'         => 'ditolak',
                                'keterangan'     => 'Izin jalan-jalan (alasan ditolak)',
                                'disetujui_oleh' => $guruRpl->nama,
                            ]);
                            Absensi::create([
                                'pemilik_type'    => 'siswa',
                                'pemilik_id'      => $siswa->id,
                                'siswa_rombel_id' => $sr->id,
                                'tanggal'         => $tglStr,
                                'jam_masuk'       => null,
                                'jam_pulang'      => null,
                                'status'          => 'alpha',
                                'sumber_absen'    => 'sistem',
                            ]);
                        } else {
                            Absensi::create([
                                'pemilik_type'    => 'siswa',
                                'pemilik_id'      => $siswa->id,
                                'siswa_rombel_id' => $sr->id,
                                'tanggal'         => $tglStr,
                                'jam_masuk'       => '07:04:00',
                                'jam_pulang'      => $isToday ? null : '15:30:00',
                                'status'          => 'hadir',
                                'sumber_absen'    => 'rfid',
                            ]);
                        }
                        break;

                    default:
                        // Hadir Reguler
                        Absensi::create([
                            'pemilik_type'    => 'siswa',
                            'pemilik_id'      => $siswa->id,
                            'siswa_rombel_id' => $sr->id,
                            'tanggal'         => $tglStr,
                            'jam_masuk'       => sprintf('06:%02d:00', rand(45, 59)),
                            'jam_pulang'      => $isToday ? null : '15:30:00',
                            'status'          => 'hadir',
                            'sumber_absen'    => 'rfid',
                        ]);
                        break;
                }
            }
        }

        // 13. Generate Histori Absensi Realistis untuk Guru & Pegawai
        $daftarGuruAbsen = [$guruKepsek, $guruRpl, $guruAphp, $guruTsm, $guruBk, $guruRian, $guruNurul, $guruHendra];
        foreach ($daftarGuruAbsen as $idxGuru => $guruItem) {
            foreach ($hariEfektifSekolah as $idxHari => $tglObj) {
                $tglStr = $tglObj->toDateString();
                $isToday = ($tglStr === Carbon::today()->toDateString());

                // Simulasi Variasi Guru:
                // Guru Hendra belum hadir hari ini untuk menguji metrik "Belum Hadir" di dashboard piket
                if ($isToday && $guruItem->id === $guruHendra->id) {
                    continue; // Belum tap hari ini
                }

                // Guru TSM pernah 1 hari izin dinas luar
                if ($guruItem->id === $guruTsm->id && $idxHari === 5) {
                    Absensi::create([
                        'pemilik_type'    => 'guru',
                        'pemilik_id'      => $guruItem->id,
                        'siswa_rombel_id' => null,
                        'tanggal'         => $tglStr,
                        'jam_masuk'       => null,
                        'jam_pulang'      => null,
                        'status'          => 'izin',
                        'sumber_absen'    => 'manual',
                    ]);
                    continue;
                }

                // Guru Nurul pernah terlambat (masuk 07:22)
                if ($guruItem->id === $guruNurul->id && $idxHari === 9) {
                    Absensi::create([
                        'pemilik_type'    => 'guru',
                        'pemilik_id'      => $guruItem->id,
                        'siswa_rombel_id' => null,
                        'tanggal'         => $tglStr,
                        'jam_masuk'       => '07:22:00',
                        'jam_pulang'      => '16:00:00',
                        'status'          => 'terlambat',
                        'sumber_absen'    => 'rfid',
                    ]);
                    continue;
                }

                // Hadir tepat waktu normal
                Absensi::create([
                    'pemilik_type'    => 'guru',
                    'pemilik_id'      => $guruItem->id,
                    'siswa_rombel_id' => null,
                    'tanggal'         => $tglStr,
                    'jam_masuk'       => sprintf('06:%02d:00', rand(30, 50)),
                    'jam_pulang'      => $isToday ? null : sprintf('16:%02d:00', rand(0, 30)),
                    'status'          => 'hadir',
                    'sumber_absen'    => 'rfid',
                ]);
            }
        }

        // 14. Data Draf Notifikasi & Berkas Kasus Pembinaan Lengkap (Demo Cetak Berita Acara & Pengesahan Kepsek)
        $siswaRangga = Siswa::where('nis', '20240103')->first();
        $siswaFajar  = Siswa::where('nis', '20240104')->first();
        $siswaDimas  = Siswa::where('nis', '20240102')->first();
        $siswaAldi   = Siswa::where('nis', '20260301')->first();
        $siswaBima   = Siswa::where('nis', '20260302')->first();
        $siswaNabila = Siswa::where('nis', '20250201')->first();

        if ($siswaRangga) {
            // Kasus 1: Panggilan Orang Tua 3x Alpha -> Sudah Dimusyawarahkan & DISAHKAN KEPSEK
            NotifikasiOrtu::create([
                'siswa_id'               => $siswaRangga->id,
                'kategori'               => 'panggilan_ortu',
                'tanggal'                => Carbon::today()->subDays(4),
                'no_tujuan'              => '6281369010003',
                'nama_ortu'              => 'Bpk. Slamet Riyadi',
                'judul'                  => 'SURAT PANGGILAN ORANG TUA / WALI SISWA',
                'pesan'                  => "🚨 *SURAT PANGGILAN ORANG TUA / WALI MURID*\n*SMK NEGERI 1 AIR NANINGAN*\n\nYth. Bapak Slamet Riyadi (Wali dari Rangga Aditya - XII RPL 1),\n\nBerdasarkan monitoring kehadiran, ananda telah mencapai akumulasi 3x ALPHA (tanpa keterangan). Kami mengundang kehadiran Bapak ke sekolah pada:\n• Hari/Tgl: " . Carbon::today()->subDays(2)->translatedFormat('l, d F Y') . "\n• Tempat: Ruang BK / Wali Kelas XII RPL 1\n• Agenda: Koordinasi & Pembinaan Kedisiplinan Belajar Siswa.\n\nTerima kasih atas kerja samanya.",
                'catatan_hasil_diskusi'  => "Telah dilaksanakan musyawarah pembinaan kedisiplinan antara Wali Kelas XII RPL 1 (Budi Santoso, S.Kom.), Guru BK (Dewi Lestari, S.Pd.), dan Orang Tua Siswa (Bpk. Slamet Riyadi).\n\nPoin Kesepakatan:\n1. Orang tua berkomitmen mengawasi keberangkatan ananda dari rumah setiap pagi.\n2. Ananda berjanji tidak akan mengulangi ketidakhadiran tanpa keterangan dan akan aktif mengikuti KBM.\n3. Pihak sekolah memberikan masa observasi pembinaan selama 30 hari ke depan.",
                'waktu_diskusi'          => Carbon::today()->subDays(2)->setHour(9)->setMinute(30),
                'nama_wali_hadir'        => 'Bpk. Slamet Riyadi (Ayah Kandung)',
                'status_pembinaan'       => 'selesai',
                'status_validasi_kepsek' => 'disahkan',
                'nama_kepsek_validasi'   => $guruKepsek->nama,
                'waktu_validasi_kepsek'  => Carbon::today()->subDays(2)->setHour(11)->setMinute(0),
                'catatan_kepala_sekolah' => 'Telah diperiksa dan disahkan. Teruskan pemantauan berkala oleh Wali Kelas dan Guru BK.',
                'status'                 => 'terkirim',
                'dibuat_oleh'            => 'Sistem Otomatis Kesiswaan',
                'diverifikasi_oleh'      => $guruRpl->nama,
                'waktu_verifikasi'       => Carbon::today()->subDays(4)->setHour(10)->setMinute(15),
                'waktu_kirim'            => Carbon::today()->subDays(4)->setHour(10)->setMinute(16),
            ]);
        }

        if ($siswaFajar) {
            // Kasus 2: Berkas Pembinaan Lengkap -> Status MENUNGGU VALIDASI KEPSEK (Uji Coba Pengesahan di Akun Kepsek)
            NotifikasiOrtu::create([
                'siswa_id'               => $siswaFajar->id,
                'kategori'               => 'panggilan_ortu',
                'tanggal'                => Carbon::today()->subDays(1),
                'no_tujuan'              => '6281369010004',
                'nama_ortu'              => 'Bpk. Hendra Gunawan',
                'judul'                  => 'SURAT PANGGILAN ORANG TUA - Fajar Ramadhan',
                'pesan'                  => "Panggilan orang tua terkait evaluasi kedisiplinan dan absensi siswa Fajar Ramadhan (XII RPL 1).",
                'catatan_hasil_diskusi'  => "Hasil musyawarah pembinaan orang tua ananda Fajar Ramadhan:\n1. Siswa berjanji tidak akan terlambat dan tidak alpa.\n2. Orang tua menyetujui komitmen pendampingan belajar di rumah.\n3. Berkas berita acara musyawarah telah ditandatangani.",
                'waktu_diskusi'          => Carbon::today()->subDays(1)->setHour(10)->setMinute(0),
                'nama_wali_hadir'        => 'Bpk. Hendra Gunawan (Orang Tua)',
                'status_pembinaan'       => 'selesai',
                'status_validasi_kepsek' => 'menunggu_validasi',
                'nama_kepsek_validasi'   => null,
                'waktu_validasi_kepsek'  => null,
                'catatan_kepala_sekolah' => null,
                'status'                 => 'terkirim',
                'dibuat_oleh'            => $guruRpl->nama,
                'diverifikasi_oleh'      => $guruRpl->nama,
                'waktu_verifikasi'       => Carbon::today()->subDays(1)->setHour(10)->setMinute(30),
                'waktu_kirim'            => Carbon::today()->subDays(1)->setHour(10)->setMinute(31),
            ]);
        }

        if ($siswaAldi) {
            // Kasus 3: Kasus Bimbingan X TSM 1 -> Status PERLU TINDAK LANJUT ULANG oleh Kepsek
            NotifikasiOrtu::create([
                'siswa_id'               => $siswaAldi->id,
                'kategori'               => 'pembinaan',
                'tanggal'                => Carbon::today()->subDays(2),
                'no_tujuan'              => '6281369060001',
                'nama_ortu'              => 'Bpk. Arifin',
                'judul'                  => 'Bimbingan Kedisiplinan Siswa - Aldi Kurniawan',
                'pesan'                  => "Pemberitahuan bimbingan kedisiplinan siswa Aldi Kurniawan kelas X TSM 1.",
                'catatan_hasil_diskusi'  => "Pertemuan awal dilakukan secara daring via telepon.",
                'waktu_diskusi'          => Carbon::today()->subDays(2)->setHour(13)->setMinute(0),
                'nama_wali_hadir'        => 'Ibu Siti (Ibu Kandung via Telp)',
                'status_pembinaan'       => 'proses',
                'status_validasi_kepsek' => 'perlu_tindak_lanjut_ulang',
                'nama_kepsek_validasi'   => $guruKepsek->nama,
                'waktu_validasi_kepsek'  => Carbon::today()->subDays(1)->setHour(14)->setMinute(0),
                'catatan_kepala_sekolah' => 'Perlu dilakukan pemanggilan fisik orang tua ke sekolah untuk penandatanganan komitmen resmi bermaterai.',
                'status'                 => 'terkirim',
                'dibuat_oleh'            => $guruTsm->nama,
                'diverifikasi_oleh'      => $guruTsm->nama,
                'waktu_verifikasi'       => Carbon::today()->subDays(2),
                'waktu_kirim'            => Carbon::today()->subDays(2),
            ]);
        }

        if ($siswaDimas) {
            // Notifikasi Keterlambatan Pending Hari Ini (Siap Diverifikasi Guru Piket)
            NotifikasiOrtu::create([
                'siswa_id'          => $siswaDimas->id,
                'kategori'          => 'terlambat',
                'tanggal'           => Carbon::today(),
                'no_tujuan'         => '6281369010002',
                'nama_ortu'         => 'Bpk. Joko Susilo',
                'judul'             => 'PEMBERITAHUAN KETERLAMBATAN SISWA',
                'pesan'             => "🔔 *PEMBERITAHUAN KEHADIRAN SISWA*\n*SMK NEGERI 1 AIR NANINGAN*\n\nYth. Bapak/Ibu Wali dari *Dimas Arya Pratama*,\nKami informasikan bahwa ananda telah tiba di sekolah:\n\n• Tanggal : " . Carbon::today()->translatedFormat('d F Y') . "\n• Waktu Masuk : 07:22 WIB\n• Status : ⚠️ *TERLAMBAT* (Batas: 07:15)\n• Rombel : XII RPL 1\n\nMohon bimbingan dan motivasinya agar ananda dapat hadir tepat waktu. Terima kasih.",
                'status'            => 'pending',
                'dibuat_oleh'       => 'Mesin Tap RFID Gerbang',
            ]);
        }

        if ($siswaBima) {
            // Notifikasi Peringatan Pulang Sebelum Waktu (Bolos)
            NotifikasiOrtu::create([
                'siswa_id'          => $siswaBima->id,
                'kategori'          => 'bolos',
                'tanggal'           => Carbon::today()->subDays(3),
                'no_tujuan'         => '6281369060002',
                'nama_ortu'         => 'Bpk. Subagio',
                'judul'             => 'PERINGATAN PULANG SEBELUM WAKTU (BOLOS)',
                'pesan'             => "🚫 *PERINGATAN PULANG TANPA IZIN (BOLOS)*\n*SMK NEGERI 1 AIR NANINGAN*\n\nYth. Bapak/Ibu Wali dari *Bima Sakti*,\nKami informasikan ananda terdeteksi mencoba tap kartu pulang sebelum jam pulang resmi tanpa izin Guru Piket pada pukul 10:45 WIB.\n\nMohon perhatian dan konfirmasi dari Bapak/Ibu. Terima kasih.",
                'status'            => 'terkirim',
                'dibuat_oleh'       => 'Mesin Tap RFID Gerbang',
                'diverifikasi_oleh' => $guruRian->nama,
                'waktu_verifikasi'  => Carbon::today()->subDays(3)->setHour(11)->setMinute(0),
                'waktu_kirim'       => Carbon::today()->subDays(3)->setHour(11)->setMinute(1),
            ]);
        }

        if ($siswaNabila) {
            // Notifikasi Izin Sakit Terverifikasi
            NotifikasiOrtu::create([
                'siswa_id'          => $siswaNabila->id,
                'kategori'          => 'sakit',
                'tanggal'           => Carbon::today()->subDays(2),
                'no_tujuan'         => '6281369040001',
                'nama_ortu'         => 'Bpk. Mansur',
                'judul'             => 'KONFIRMASI PERIZINAN SAKIT SISWA',
                'pesan'             => "🩺 *KONFIRMASI PERIZINAN SISWA*\n*SMK NEGERI 1 AIR NANINGAN*\n\nYth. Bapak/Ibu Wali Murid,\nSurat izin sakit ananda *Nabila Putri* (XI APHP 1) telah diverifikasi oleh Guru Piket:\n\n• Status : 🩺 *SAKIT*\n• Keterangan : Demam dan flu\n• Tanggal : " . Carbon::today()->subDays(2)->translatedFormat('d F Y') . "\n\nSemoga ananda lekas sembuh. Terima kasih.",
                'status'            => 'terkirim',
                'dibuat_oleh'       => 'Guru Piket',
                'diverifikasi_oleh' => $guruAphp->nama,
                'waktu_verifikasi'  => Carbon::today()->subDays(2)->setHour(8)->setMinute(0),
                'waktu_kirim'       => Carbon::today()->subDays(2)->setHour(8)->setMinute(1),
            ]);
        }
    }
}
