<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanNotifikasi extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_notifikasis';

    protected $fillable = [
        'wa_provider',
        'wa_api_token',
        'wa_endpoint_url',
        'is_active',
        'ambang_batas_alpha',
        'hitung_bolos_bersama_alpha',
        'auto_notif_wali_kelas',
        'template_terlambat',
        'template_alpha',
        'template_izin',
        'template_sakit',
        'template_bolos',
        'template_wali_kelas',
    ];

    protected $casts = [
        'is_active'                  => 'boolean',
        'ambang_batas_alpha'         => 'integer',
        'hitung_bolos_bersama_alpha' => 'boolean',
        'auto_notif_wali_kelas'      => 'boolean',
    ];

    /**
     * Dapatkan instance pengaturan singleton atau buat dengan template default.
     */
    public static function getPengaturan(): self
    {
        $setting = self::first();
        if (!$setting) {
            $setting = self::create([
                'wa_provider'                => 'simulasi',
                'wa_api_token'               => null,
                'wa_endpoint_url'            => 'https://api.fonnte.com/send',
                'is_active'                  => false,
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
        }

        return $setting;
    }
}
