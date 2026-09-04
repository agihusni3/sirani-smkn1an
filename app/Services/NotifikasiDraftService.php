<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\NotifikasiOrtu;
use App\Models\PengaturanNotifikasi;
use App\Models\Siswa;
use Carbon\Carbon;

class NotifikasiDraftService
{
    /**
     * Buat draf notifikasi baru untuk verifikasi (Anti-Spam 1 notifikasi per kategori per siswa per hari).
     */
    public static function buatDraft(Siswa $siswa, string $kategori, array $params = [], string $dibuatOleh = 'sistem'): ?NotifikasiOrtu
    {
        $setting = PengaturanNotifikasi::getPengaturan();

        // 0. Cek apakah kategori ini diaktifkan di Pengaturan (cegah spam masuk/pulang normal)
        if (!$setting->isKategoriAktif($kategori)) {
            return null;
        }

        $tanggal = $params['tanggal'] ?? Carbon::today()->toDateString();

        // 1. Anti-Duplikasi: Cek apakah sudah ada notifikasi kategori yang sama untuk siswa hari ini
        $existing = NotifikasiOrtu::where('siswa_id', $siswa->id)
            ->where('kategori', $kategori)
            ->whereDate('tanggal', $tanggal)
            ->whereIn('status', ['pending', 'diverifikasi', 'terkirim'])
            ->first();

        if ($existing) {
            return $existing; // Sudah ada, tidak diduplikasi
        }

        // 2. Ambil data rombel & jurusan aktif
        $rombelAktif = $siswa->siswaRombels()
            ->where('status_keanggotaan', 'aktif')
            ->with(['rombel.waliKelas', 'rombel.jurusan'])
            ->first();

        $rombel = $rombelAktif?->rombel;
        $waliKelas = $rombel?->waliKelas;
        $namaRombel = $rombel ? $rombel->nama_rombel : '-';
        $namaJurusan = $rombel && $rombel->jurusan ? $rombel->jurusan->nama_jurusan : '-';
        $namaWali = $waliKelas ? $waliKelas->nama : '-';

        // 3. Hitung akumulasi & rincian pelanggaran dinamis siswa
        $totalAlpha = Absensi::where('pemilik_type', 'siswa')->where('pemilik_id', $siswa->id)->where('status', 'alpha')->count();
        $totalBolos = Absensi::where('pemilik_type', 'siswa')->where('pemilik_id', $siswa->id)->where('status', 'bolos')->count();
        $totalTerlambat = Absensi::where('pemilik_type', 'siswa')->where('pemilik_id', $siswa->id)->where('status', 'terlambat')->count();
        $totalPelanggaran = $params['total_pelanggaran'] ?? ($totalAlpha + $totalBolos);

        $riwayatPelanggaran = Absensi::where('pemilik_type', 'siswa')
            ->where('pemilik_id', $siswa->id)
            ->whereIn('status', ['alpha', 'bolos'])
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get();

        $rincianList = [];
        foreach ($riwayatPelanggaran as $p) {
            $tglFormat = Carbon::parse($p->tanggal)->translatedFormat('d/m/Y (l)');
            $statusStr = $p->status === 'alpha' ? '❌ Alpha (Tanpa Keterangan)' : '🚫 Bolos (Pulang Sebelum Waktu)';
            $rincianList[] = "• {$tglFormat} : {$statusStr}";
        }
        $rincianPelanggaran = !empty($rincianList) ? implode("\n", $rincianList) : "• Tanggal {$tanggal} : Pelanggaran tercatat sistem";

        // Tingkat urgensi & rekomendasi tindakan dinamis
        if ($totalPelanggaran >= 5) {
            $tingkatUrgensi = '🔴 KRITIS (SP-2 / Peringatan Keras Kesiswaan)';
            $rekomendasiTindakan = 'Wajib Terbitkan Surat Panggilan Orang Tua Tahap 2 & Konseling Khusus BK';
        } elseif ($totalPelanggaran >= 3) {
            $tingkatUrgensi = '⚠️ WASPADA (SP-1 / Panggilan Orang Tua Pertama)';
            $rekomendasiTindakan = 'Terbitkan Surat Panggilan Orang Tua & Pembinaan Wali Kelas';
        } else {
            $tingkatUrgensi = '🟡 PERINGATAN AWAL';
            $rekomendasiTindakan = 'Berikan teguran lisan & pantau kedisiplinan harian';
        }

        // 4. Ambil template pesan
        $setting = PengaturanNotifikasi::getPengaturan();
        $template = match ($kategori) {
            'terlambat'      => $setting->template_terlambat,
            'alpha'          => $setting->template_alpha,
            'izin'           => $setting->template_izin,
            'sakit'          => $setting->template_sakit,
            'bolos'          => $setting->template_bolos,
            'panggilan_ortu' => "🚨 *SURAT PANGGILAN ORANG TUA / WALI SISWA*\n*SMK NEGERI 1 AIR NANINGAN*\n\nYth. Bapak/Ibu Orang Tua / Wali dari *{nama_siswa}* ({kelas}),\n\nBerdasarkan catatan evaluasi sistem presensi, ananda telah mencapai *akumulasi {total_pelanggaran}x ketidakhadiran tanpa keterangan (Alpha/Bolos)*:\n\n📋 *Rincian Catatan Ketidakhadiran:*\n{rincian_pelanggaran}\n\nSesuai tata tertib kedisiplinan sekolah, kami mengharapkan kehadiran Bapak/Ibu ke sekolah guna koordinasi dan bimbingan bersama Wali Kelas (*{nama_wali_kelas}*) dan Guru BK:\n\n• Agenda : Pembinaan Kedisiplinan & Presensi Siswa\n• Tempat : Ruang Bimbingan & Konseling (BK) SMKN 1 Air Naningan\n• Rekap Presensi : {link_portal}\n\nMohon konfirmasi kehadiran. Terima kasih.\n_Wali Kelas & Kesiswaan SMKN 1 Air Naningan_",
            default          => "Pemberitahuan kehadiran siswa {nama_siswa} ({kelas}): Status {kategori} pada {tanggal}.",
        };

        // 5. Parse placeholders dinamis
        $tglIndo = Carbon::parse($tanggal)->translatedFormat('d F Y');
        $jam = $params['jam'] ?? Carbon::now()->format('H:i:s');
        $batasJam = $params['batas_jam'] ?? '07:15';
        $keterangan = $params['keterangan'] ?? '-';
        $linkPortal = url('/portal-siswa/' . ($siswa->nisn ?: $siswa->id));
        $linkCetakSurat = url('/surat/cetak?siswa_id=' . $siswa->id . '&kategori=panggilan_ortu');
        $linkDasborWali = url('/wali-kelas/dashboard');

        $replacements = [
            '{nama_siswa}'           => $siswa->nama,
            '{nis}'                  => $siswa->nisn ?: '-',
            '{nisn}'                 => $siswa->nisn ?: '-',
            '{kelas}'                => $namaRombel,
            '{rombel}'               => $namaRombel,
            '{jurusan}'              => $namaJurusan,
            '{nama_wali_kelas}'      => $namaWali,
            '{tanggal}'              => $tglIndo,
            '{jam}'                  => $jam,
            '{waktu}'                => $jam,
            '{batas_jam}'            => $batasJam,
            '{keterangan}'           => $keterangan,
            '{total_alpha}'          => (string) $totalAlpha,
            '{total_bolos}'          => (string) $totalBolos,
            '{total_terlambat}'      => (string) $totalTerlambat,
            '{total_pelanggaran}'    => (string) $totalPelanggaran,
            '{rincian_pelanggaran}'  => $rincianPelanggaran,
            '{tingkat_urgensi}'      => $tingkatUrgensi,
            '{rekomendasi_tindakan}' => $rekomendasiTindakan,
            '{link_portal}'          => $linkPortal,
            '{link_portal_ortu}'     => $linkPortal,
            '{link_cetak_surat}'     => $linkCetakSurat,
            '{link_dasbor_wali}'     => $linkDasborWali,
            '{kategori}'             => strtoupper(str_replace('_', ' ', $kategori)),
            '{nama_ortu}'            => $siswa->nama_ortu ?: 'Bapak/Ibu Orang Tua/Wali',
            '{no_hp_ortu}'           => $siswa->no_hp_ortu ?: 'Belum ada kontak',
        ];

        $pesanParsed = str_replace(array_keys($replacements), array_values($replacements), $template);

        $judul = match ($kategori) {
            'terlambat'      => "Keterlambatan: {$siswa->nama} ({$namaRombel})",
            'alpha'          => "Ketidakhadiran Alpha: {$siswa->nama} ({$namaRombel})",
            'izin'           => "Perizinan Siswa: {$siswa->nama} ({$namaRombel})",
            'sakit'          => "Izin Sakit: {$siswa->nama} ({$namaRombel})",
            'bolos'          => "Peringatan Bolos: {$siswa->nama} ({$namaRombel})",
            'panggilan_ortu' => "🚨 Panggilan Ortu (Akumulasi {$totalPelanggaran}x Pelanggaran): {$siswa->nama} ({$namaRombel})",
            default          => "Notifikasi Siswa: {$siswa->nama}",
        };

        // 6. Simpan draf notifikasi berstatus 'pending'
        return NotifikasiOrtu::create([
            'siswa_id'    => $siswa->id,
            'kategori'    => $kategori,
            'tanggal'     => $tanggal,
            'no_tujuan'   => $siswa->no_hp_ortu ?: '',
            'nama_ortu'   => $siswa->nama_ortu,
            'judul'       => $judul,
            'pesan'       => $pesanParsed,
            'status'      => 'pending',
            'dibuat_oleh' => $dibuatOleh,
        ]);
    }

    /**
     * Parse template pesan untuk notifikasi tertentu sesuai template PengaturanNotifikasi.
     */
    public static function parsePesan(Siswa $siswa, string $kategori, array $params = []): string
    {
        $setting = PengaturanNotifikasi::getPengaturan();
        $template = match ($kategori) {
            'terlambat'      => $setting->template_terlambat,
            'alpha'          => $setting->template_alpha,
            'izin'           => $setting->template_izin,
            'sakit'          => $setting->template_sakit,
            'bolos'          => $setting->template_bolos,
            'panggilan_ortu' => "🚨 *SURAT PANGGILAN ORANG TUA / WALI SISWA*\n*SMK NEGERI 1 AIR NANINGAN*\n\nYth. Bapak/Ibu Orang Tua / Wali dari *{nama_siswa}* ({kelas}),\n\nBerdasarkan catatan evaluasi sistem presensi, ananda telah mencapai akumulasi ketidakhadiran tanpa keterangan.\nMohon kehadirannya ke sekolah guna koordinasi dan bimbingan bersama Wali Kelas dan Guru BK.",
            default          => "Pemberitahuan kehadiran siswa {nama_siswa} ({kelas}): Status {kategori} pada {tanggal}.",
        };

        $rombelAktif = $siswa->siswaRombels()->where('status_keanggotaan', 'aktif')->with(['rombel.waliKelas', 'rombel.jurusan'])->first();
        $rombel = $rombelAktif?->rombel;
        $namaRombel = $rombel?->nama_rombel ?? '-';
        $namaJurusan = $rombel?->jurusan?->nama_jurusan ?? '-';
        $namaWali = $rombel?->waliKelas?->nama ?? '-';

        $tanggal = $params['tanggal'] ?? Carbon::today()->toDateString();
        $tglIndo = Carbon::parse($tanggal)->translatedFormat('d F Y');
        $jam = $params['jam'] ?? Carbon::now()->format('H:i:s');
        $batasJam = $params['batas_jam'] ?? '07:15';
        $keterangan = $params['keterangan'] ?? '-';
        $linkPortal = url('/portal-siswa/' . ($siswa->nisn ?: $siswa->id));

        $replacements = [
            '{nama_siswa}'           => $siswa->nama,
            '{nis}'                  => $siswa->nisn ?: '-',
            '{nisn}'                 => $siswa->nisn ?: '-',
            '{kelas}'                => $namaRombel,
            '{rombel}'               => $namaRombel,
            '{jurusan}'              => $namaJurusan,
            '{nama_wali_kelas}'      => $namaWali,
            '{tanggal}'              => $tglIndo,
            '{jam}'                  => $jam,
            '{waktu}'                => $jam,
            '{batas_jam}'            => $batasJam,
            '{keterangan}'           => $keterangan,
            '{total_alpha}'          => '0',
            '{total_bolos}'          => '0',
            '{total_terlambat}'      => '0',
            '{total_pelanggaran}'    => '0',
            '{rincian_pelanggaran}'  => '-',
            '{tingkat_urgensi}'      => 'Pemberitahuan',
            '{rekomendasi_tindakan}' => 'Mohon pendampingan siswa di rumah',
            '{link_portal}'          => $linkPortal,
            '{link_portal_ortu}'     => $linkPortal,
            '{kategori}'             => strtoupper(str_replace('_', ' ', $kategori)),
            '{nama_ortu}'            => $siswa->nama_ortu ?: 'Bapak/Ibu Orang Tua/Wali',
            '{no_hp_ortu}'           => $siswa->no_hp_ortu ?: 'Belum ada kontak',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Sinkronkan otomatis draf pending yang masih memakai pesan hardcoded lama ke template resmi.
     */
    public static function sinkronkanDraftPending(): int
    {
        $drafts = NotifikasiOrtu::where('status', 'pending')
            ->where(function ($q) {
                $q->where('pesan', 'like', '%telah melakukan presensi%')
                  ->orWhere('pesan', 'like', '%Scan Barcode/RFID%');
            })
            ->with('siswa')
            ->get();

        $count = 0;
        foreach ($drafts as $d) {
            if ($d->siswa) {
                preg_match('/pukul\s+([0-9:]+)/i', $d->pesan, $m);
                $jam = $m[1] ?? Carbon::now()->format('H:i:s');
                $pesanBaru = self::parsePesan($d->siswa, $d->kategori, [
                    'tanggal'   => $d->tanggal,
                    'jam'       => $jam,
                    'batas_jam' => '07:15',
                ]);
                $d->update(['pesan' => $pesanBaru]);
                $count++;
            }
        }
        return $count;
    }

    /**
     * Evaluasi jika siswa telah mencapai akumulasi batas ketentuan pelanggaran
     * dan otomatis kirim notifikasi dinamis langsung ke Wali Kelas tanpa butuh verifikasi manual.
     */
    public static function cekAkumulasiAlphaDanBuatPanggilan(Siswa $siswa, string $dibuatOleh = 'sistem'): ?NotifikasiOrtu
    {
        $setting = PengaturanNotifikasi::getPengaturan();
        $ambangBatas = $setting->ambang_batas_alpha ?? 3;
        $hitungBolos = $setting->hitung_bolos_bersama_alpha ?? true;

        // Hitung total akumulasi pelanggaran
        $totalAlpha = Absensi::where('pemilik_type', 'siswa')
            ->where('pemilik_id', $siswa->id)
            ->where('status', 'alpha')
            ->count();

        $totalBolos = Absensi::where('pemilik_type', 'siswa')
            ->where('pemilik_id', $siswa->id)
            ->where('status', 'bolos')
            ->count();

        $totalTerlambat = Absensi::where('pemilik_type', 'siswa')
            ->where('pemilik_id', $siswa->id)
            ->where('status', 'terlambat')
            ->count();

        $totalPelanggaran = $hitungBolos ? ($totalAlpha + $totalBolos) : $totalAlpha;

        if ($totalPelanggaran < $ambangBatas) {
            return null;
        }

        // 1. Buat Draf Panggilan Orang Tua untuk Siswa (jika belum pernah dibuat dalam 14 hari)
        $fourteenDaysAgo = Carbon::today()->subDays(14)->toDateString();
        $recentPanggilan = NotifikasiOrtu::where('siswa_id', $siswa->id)
            ->where('kategori', 'panggilan_ortu')
            ->where('tanggal', '>=', $fourteenDaysAgo)
            ->first();

        $drafOrtu = $recentPanggilan ?: self::buatDraft($siswa, 'panggilan_ortu', [
            'tanggal'           => Carbon::today()->toDateString(),
            'jam'               => Carbon::now()->format('H:i'),
            'total_pelanggaran' => $totalPelanggaran,
        ], $dibuatOleh);

        // 2. OTOMATIS KIRIM NOTIFIKASI DINAMIS LANGSUNG KE WALI KELAS (TANPA PERLU VERIFIKASI ADMIN/KEPSEK)
        if ($setting->auto_notif_wali_kelas) {
            self::kirimNotifikasiOtomatisKeWaliKelas($siswa, $totalPelanggaran, $totalAlpha, $totalBolos, $totalTerlambat, $setting);
        }

        return $drafOrtu;
    }

    /**
     * Kirim notifikasi WhatsApp dinamis otomatis langsung ke Wali Kelas.
     */
    private static function kirimNotifikasiOtomatisKeWaliKelas(Siswa $siswa, int $totalPelanggaran, int $totalAlpha, int $totalBolos, int $totalTerlambat, PengaturanNotifikasi $setting): ?NotifikasiOrtu
    {
        $rombelAktif = $siswa->siswaRombels()
            ->where('status_keanggotaan', 'aktif')
            ->with(['rombel.waliKelas', 'rombel.jurusan'])
            ->first();

        $rombel = $rombelAktif?->rombel;
        $waliKelas = $rombel?->waliKelas;
        if (!$waliKelas) {
            return null;
        }

        // Cek anti-spam dalam 7 hari terakhir ke wali kelas untuk siswa yang sama
        $sevenDaysAgo = Carbon::today()->subDays(7)->toDateString();
        $recentWaliNotif = NotifikasiOrtu::where('siswa_id', $siswa->id)
            ->where('kategori', 'peringatan_wali_kelas')
            ->where('tanggal', '>=', $sevenDaysAgo)
            ->first();

        if ($recentWaliNotif) {
            return $recentWaliNotif; // Sudah pernah dikirim, hindari spam
        }

        $namaRombel = $rombel->nama_rombel ?? '-';
        $namaJurusan = $rombel->jurusan ? $rombel->jurusan->nama_jurusan : '-';

        // Rincian riwayat pelanggaran dinamis
        $riwayatPelanggaran = Absensi::where('pemilik_type', 'siswa')
            ->where('pemilik_id', $siswa->id)
            ->whereIn('status', ['alpha', 'bolos'])
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get();

        $rincianList = [];
        foreach ($riwayatPelanggaran as $p) {
            $tglFormat = Carbon::parse($p->tanggal)->translatedFormat('d/m/Y (l)');
            $statusStr = $p->status === 'alpha' ? '❌ Alpha' : '🚫 Bolos';
            $rincianList[] = "• {$tglFormat} : {$statusStr}";
        }
        $rincianPelanggaran = !empty($rincianList) ? implode("\n", $rincianList) : "• Pelanggaran akumulasi kehadiran";

        // Tingkat urgensi & rekomendasi tindakan dinamis
        if ($totalPelanggaran >= 5) {
            $tingkatUrgensi = '🔴 KRITIS (SP-2 / Panggilan Keras)';
            $rekomendasiTindakan = 'Wajib Terbitkan Surat Panggilan Orang Tua Tahap 2 & Konseling Khusus BK';
        } elseif ($totalPelanggaran >= 3) {
            $tingkatUrgensi = '⚠️ WASPADA (SP-1 / Panggilan Pertama)';
            $rekomendasiTindakan = 'Segera terbitkan Surat Panggilan Orang Tua fisik & koordinasi dengan BK';
        } else {
            $tingkatUrgensi = '🟡 PERINGATAN AWAL';
            $rekomendasiTindakan = 'Berikan teguran lisan & motivasi kedisiplinan';
        }

        $template = $setting->template_wali_kelas ?: "🚨 *PERINGATAN KESISWAAN SMKN 1 AIR NANINGAN*\nStatus: {tingkat_urgensi}\n\nYth. Bapak/Ibu Wali Kelas *{nama_wali_kelas}* ({kelas}),\n\nSiswa binaan Anda telah memenuhi ketentuan batas pelanggaran kehadiran:\n\n👤 *Data Siswa:*\n• Nama : *{nama_siswa}* (NISN: {nisn})\n• Kelas : {kelas} ({jurusan})\n• Kontak Ortu : {nama_ortu} ({no_hp_ortu})\n\n📊 *Akumulasi Pelanggaran: {total_pelanggaran}x Pelanggaran*\n• Alpha : {total_alpha}x\n• Bolos : {total_bolos}x\n• Terlambat : {total_terlambat}x\n\n📋 *Rincian Tanggal Ketidakhadiran:*\n{rincian_pelanggaran}\n\n⚠️ *Rekomendasi Tindakan:*\n{rekomendasi_tindakan}\n\n📄 Lembar Cetak Surat A4: {link_cetak_surat}\n📊 Dasbor Wali Kelas: {link_dasbor_wali}\n\n_Sistem Otomatis SIRANI SMKN 1 Air Naningan_";

        $replacements = [
            '{nama_wali_kelas}'      => $waliKelas->nama,
            '{nama_siswa}'           => $siswa->nama,
            '{nis}'                  => $siswa->nisn ?: '-',
            '{nisn}'                 => $siswa->nisn ?: '-',
            '{kelas}'                => $namaRombel,
            '{rombel}'               => $namaRombel,
            '{jurusan}'              => $namaJurusan,
            '{nama_ortu}'            => $siswa->nama_ortu ?: 'Orang Tua Siswa',
            '{no_hp_ortu}'           => $siswa->no_hp_ortu ?: 'Belum ada nomor',
            '{total_alpha}'          => (string) $totalAlpha,
            '{total_bolos}'          => (string) $totalBolos,
            '{total_terlambat}'      => (string) $totalTerlambat,
            '{total_pelanggaran}'    => (string) $totalPelanggaran,
            '{rincian_pelanggaran}'  => $rincianPelanggaran,
            '{tingkat_urgensi}'      => $tingkatUrgensi,
            '{rekomendasi_tindakan}' => $rekomendasiTindakan,
            '{link_cetak_surat}'     => url('/surat/cetak?siswa_id=' . $siswa->id . '&kategori=panggilan_ortu'),
            '{link_dasbor_wali}'     => url('/wali-kelas/dashboard'),
            '{link_portal_ortu}'     => url('/portal-siswa/' . ($siswa->nisn ?: $siswa->id)),
        ];

        $pesanParsed = str_replace(array_keys($replacements), array_values($replacements), $template);

        // Simpan notifikasi ke database berstatus LANGSUNG TERKIRIM (tanpa perlu verifikasi admin/kepsek)
        $noHpWali = $waliKelas->no_hp ?: '';
        $notifWali = NotifikasiOrtu::create([
            'siswa_id'          => $siswa->id,
            'kategori'          => 'peringatan_wali_kelas',
            'tanggal'           => Carbon::today()->toDateString(),
            'no_tujuan'         => $noHpWali,
            'nama_ortu'         => $waliKelas->nama . ' (Wali Kelas ' . $namaRombel . ')',
            'judul'             => "🚨 Peringatan Kesiswaan Otomatis ke Wali Kelas: {$siswa->nama} ({$totalPelanggaran}x Pelanggaran)",
            'pesan'             => $pesanParsed,
            'status'            => 'terkirim',
            'dibuat_oleh'       => 'sistem_otomatis',
            'diverifikasi_oleh' => 'Sistem Otomatis (Auto Sent)',
            'waktu_verifikasi'  => now(),
            'waktu_kirim'       => now(),
            'catatan_error'     => '[AUTO-SENT] Terkirim otomatis ke Wali Kelas tanpa perlu verifikasi admin.',
        ]);

        // Kirim via gateway WhatsApp
        if (!empty($noHpWali)) {
            try {
                app(WhatsAppNotificationService::class)->kirim($notifWali);
            } catch (\Throwable $e) {
                // ignore
            }
        }

        return $notifWali;
    }
}
