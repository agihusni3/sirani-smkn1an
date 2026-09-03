<?php

namespace App\Services;

use App\Models\Guru;
use App\Models\KasusDisiplin;
use App\Models\NotifikasiOrtu;
use App\Models\PengaturanNotifikasi;
use App\Models\PengaturanSekolah;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DisiplinNotificationService
{
    /**
     * Kirim notifikasi WhatsApp otomatis saat kasus dibuat atau dieskalasi ke tahap berikutnya.
     */
    public static function kirimAlertEskalasi(KasusDisiplin $kasus, string $tahapBaru, ?string $catatan = null): void
    {
        $siswa = $kasus->siswa;
        if (!$siswa) return;

        $rombelAktif = $siswa->siswaRombels()->where('status_keanggotaan', 'aktif')->with(['rombel.waliKelas', 'rombel.jurusan'])->first();
        $rombel = $rombelAktif?->rombel;
        $waliKelas = $rombel?->waliKelas;
        $namaRombel = $rombel?->nama_rombel ?? '-';

        $waService = new WhatsAppNotificationService();
        $today = Carbon::today()->toDateString();

        // 1. Tentukan Pejabat Penerima berdasarkan Tahap Baru
        $targetPejabat = self::resolvePejabatByTahap($tahapBaru, $waliKelas);

        if ($targetPejabat && !empty($targetPejabat['no_hp'])) {
            $pesanPejabat = self::buildPesanEskalasiPejabat(
                $targetPejabat['nama'],
                $targetPejabat['jabatan'],
                $siswa->nama,
                $siswa->nisn ?: '-',
                $namaRombel,
                $tahapBaru,
                $kasus->poin_bersih,
                $kasus->total_alpha,
                $kasus->total_bolos,
                $catatan
            );

            $notifPejabat = NotifikasiOrtu::create([
                'siswa_id'       => $siswa->id,
                'no_tujuan'      => $targetPejabat['no_hp'],
                'nama_ortu'      => $targetPejabat['nama'] . ' (' . $targetPejabat['jabatan'] . ')',
                'judul'          => 'Eskalasi Kasus Disiplin - ' . $targetPejabat['jabatan'],
                'pesan'          => $pesanPejabat,
                'kategori'       => 'eskalasi_disiplin_internal',
                'tanggal'        => $today,
                'status'         => 'pending',
                'diverifikasi_oleh' => 'Sistem Disiplin',
                'catatan_petugas'   => "Notifikasi eskalasi otomatis ke {$targetPejabat['jabatan']}",
            ]);

            $waService->kirim($notifPejabat);
        }

        // 2. Kirim Pemberitahuan ke Orang Tua Siswa jika nomor HP tersedia
        $noHpOrtu = $siswa->no_hp_ortu ?: $siswa->nomor_hp_ortu;
        if (!empty($noHpOrtu)) {
            $pesanOrtu = self::buildPesanEskalasiOrtu(
                $siswa->nama_ortu ?: 'Bapak/Ibu Orang Tua Siswa',
                $siswa->nama,
                $namaRombel,
                $tahapBaru,
                $catatan
            );

            $notifOrtu = NotifikasiOrtu::create([
                'siswa_id'       => $siswa->id,
                'no_tujuan'      => $noHpOrtu,
                'nama_ortu'      => $siswa->nama_ortu ?: 'Orang Tua / Wali Siswa',
                'judul'          => 'Pemberitahuan Perkembangan Pembinaan Siswa',
                'pesan'          => $pesanOrtu,
                'kategori'       => 'pemberitahuan_disiplin_ortu',
                'tanggal'        => $today,
                'status'         => 'pending',
                'diverifikasi_oleh' => 'Sistem Disiplin',
            ]);

            $waService->kirim($notifOrtu);
        }
    }

    /**
     * Rutinitas Pengingat Harian Otomatis (1x Sehari) untuk Kasus yang Belum Ditangani Aktor Terkait.
     * Pengingat otomatis berhenti begitu aktor melakukan pembinaan di sistem.
     */
    public static function kirimPengingatHarianBelumDitangani(): int
    {
        $today = Carbon::today()->toDateString();
        $waService = new WhatsAppNotificationService();
        $countSent = 0;

        $kasusAktif = KasusDisiplin::where('is_active', true)
            ->where('status_tahap', '!=', 'selesai_pembinaan')
            ->with(['siswa.siswaRombels.rombel.waliKelas'])
            ->get();

        foreach ($kasusAktif as $kasus) {
            $siswa = $kasus->siswa;
            if (!$siswa) continue;

            $rombelAktif = $siswa->siswaRombels->where('status_keanggotaan', 'aktif')->first()?->rombel;
            $waliKelas = $rombelAktif?->waliKelas;
            $namaRombel = $rombelAktif?->nama_rombel ?? '-';

            // Cek apakah aktor saat ini sudah melakukan pembinaan
            $belumDitangani = false;
            $tahap = $kasus->status_tahap;

            if ($tahap === 'tahap_1_wali_kelas') {
                $belumDitangani = empty($kasus->tanggal_tindak_wali) || empty($kasus->catatan_wali_kelas);
            } elseif ($tahap === 'tahap_2_bk') {
                $belumDitangani = empty($kasus->tanggal_panggilan_bk) && empty($kasus->hasil_musyawarah_bk);
            } elseif ($tahap === 'tahap_3_wakasis') {
                $belumDitangani = empty($kasus->tanggal_sidang_wakasis) && empty($kasus->sanksi_wakasis);
            } elseif ($tahap === 'tahap_4_kepsek') {
                $belumDitangani = empty($kasus->tanggal_keputusan_kepsek) && empty($kasus->keputusan_kepsek);
            }

            // Jika sudah ditangani, hentikan pengingat otomatis!
            if (!$belumDitangani) {
                continue;
            }

            // Cek Anti-Spam: Pastikan pengingat belum dikirimkan hari ini untuk siswa ini pada tahap ini
            $alreadyRemindedToday = NotifikasiOrtu::where('siswa_id', $siswa->id)
                ->where('kategori', 'pengingat_disiplin_harian_' . $tahap)
                ->whereDate('tanggal', $today)
                ->exists();

            if ($alreadyRemindedToday) {
                continue;
            }

            // Dapatkan nomor HP pejabat penanggung jawab tahap aktif
            $targetPejabat = self::resolvePejabatByTahap($tahap, $waliKelas);
            if (!$targetPejabat || empty($targetPejabat['no_hp'])) {
                continue;
            }

            $tahapLabel = str_replace('_', ' ', strtoupper($tahap));
            $pesanPengingat = self::buildPesanPengingatHarian(
                $targetPejabat['nama'],
                $targetPejabat['jabatan'],
                $siswa->nama,
                $siswa->nisn ?: '-',
                $namaRombel,
                $tahapLabel,
                $kasus->poin_bersih
            );

            $notif = NotifikasiOrtu::create([
                'siswa_id'          => $siswa->id,
                'no_tujuan'         => $targetPejabat['no_hp'],
                'nama_ortu'         => $targetPejabat['nama'] . ' (' . $targetPejabat['jabatan'] . ')',
                'judul'             => "Pengingat Harian Pembinaan Kasus ({$targetPejabat['jabatan']})",
                'pesan'             => $pesanPengingat,
                'kategori'          => 'pengingat_disiplin_harian_' . $tahap,
                'tanggal'           => $today,
                'status'            => 'pending',
                'diverifikasi_oleh' => 'Sistem Disiplin Scheduler',
                'catatan_petugas'   => "Pengingat harian 1x sehari ke {$targetPejabat['jabatan']}",
            ]);

            $waService->kirim($notif);
            $countSent++;
        }

        return $countSent;
    }

    /**
     * Mencari nomor HP dan profil pejabat berdasarkan jenjang tahapan.
     * Strategi: (1) Cari via User model berdasarkan role — paling akurat.
     *           (2) Fallback: cari via jabatan teks di tabel guru.
     */
    private static function resolvePejabatByTahap(string $tahap, ?Guru $waliKelas): ?array
    {
        if ($tahap === 'tahap_1_wali_kelas') {
            if ($waliKelas && !empty($waliKelas->no_hp)) {
                return [
                    'nama'    => $waliKelas->nama,
                    'jabatan' => 'Wali Kelas',
                    'no_hp'   => $waliKelas->no_hp,
                ];
            }
        } elseif ($tahap === 'tahap_2_bk') {
            // Prioritas 1: Cari User dengan role guru_bk
            $userBk = User::where('role', 'guru_bk')
                ->whereHas('guru', fn($q) => $q->whereNotNull('no_hp'))
                ->with('guru')
                ->first();
            if ($userBk?->guru) {
                return [
                    'nama'    => $userBk->guru->nama,
                    'jabatan' => 'Guru BK',
                    'no_hp'   => $userBk->guru->no_hp,
                ];
            }

            // Fallback: cari berdasarkan jabatan di tabel guru (termasuk jabatan "BK", "Bimbingan", atau "Konseling")
            $guruBk = Guru::where('status', 'aktif')
                ->where(function ($q) {
                    $q->where('jabatan', 'like', '%BK%')
                      ->orWhere('jabatan', 'like', '%Bimbingan%')
                      ->orWhere('jabatan', 'like', '%Konseling%');
                })
                ->whereNotNull('no_hp')
                ->first();

            if ($guruBk) {
                return [
                    'nama'    => $guruBk->nama,
                    'jabatan' => 'Guru BK',
                    'no_hp'   => $guruBk->no_hp,
                ];
            }

            // Fallback ke User yang isGuruBk() berdasarkan jabatan
            $userGuruBk = User::with('guru')->get()->first(fn($u) => $u->isGuruBk() && !empty($u->guru?->no_hp));
            if ($userGuruBk?->guru) {
                return [
                    'nama'    => $userGuruBk->guru->nama,
                    'jabatan' => 'Guru BK',
                    'no_hp'   => $userGuruBk->guru->no_hp,
                ];
            }

        } elseif ($tahap === 'tahap_3_wakasis') {
            // Prioritas 1: Cari User dengan role waka_kesiswaan
            $userWakasis = User::where('role', 'waka_kesiswaan')
                ->whereHas('guru', fn($q) => $q->whereNotNull('no_hp'))
                ->with('guru')
                ->first();
            if ($userWakasis?->guru) {
                return [
                    'nama'    => $userWakasis->guru->nama,
                    'jabatan' => 'Waka Kesiswaan',
                    'no_hp'   => $userWakasis->guru->no_hp,
                ];
            }

            // Fallback 1: isWakaKesiswaan() via User model (dynamic jabatan check)
            $userViaMethod = User::with('guru')->get()->first(fn($u) => $u->isWakaKesiswaan() && !empty($u->guru?->no_hp));
            if ($userViaMethod?->guru) {
                return [
                    'nama'    => $userViaMethod->guru->nama,
                    'jabatan' => 'Waka Kesiswaan',
                    'no_hp'   => $userViaMethod->guru->no_hp,
                ];
            }

            // Fallback 2: cari berdasarkan jabatan di tabel guru
            $wakasis = Guru::where('status', 'aktif')
                ->where(function ($q) {
                    $q->where('jabatan', 'like', '%Kesiswaan%')
                      ->orWhere('jabatan', 'like', '%Wakasis%')
                      ->orWhere('jabatan', 'like', '%Waka%');
                })
                ->whereNotNull('no_hp')
                ->first();
            if ($wakasis) {
                return [
                    'nama'    => $wakasis->nama,
                    'jabatan' => 'Waka Kesiswaan',
                    'no_hp'   => $wakasis->no_hp,
                ];
            }

            // Fallback 3: Jika tidak ada Wakasis, eskalasi langsung ke Kepala Sekolah
            Log::warning('[SIRANI ESKALASI] Waka Kesiswaan tidak ditemukan di sistem. Notifikasi dialihkan ke Kepala Sekolah sebagai fallback.');
            $kepsekFallback = Guru::where('status', 'aktif')
                ->where('jabatan', 'like', '%Kepala Sekolah%')
                ->whereNotNull('no_hp')
                ->first();
            if ($kepsekFallback) {
                return [
                    'nama'    => $kepsekFallback->nama,
                    'jabatan' => 'Kepala Sekolah (Fallback Wakasis)',
                    'no_hp'   => $kepsekFallback->no_hp,
                ];
            }

        } elseif ($tahap === 'tahap_4_kepsek') {
            // Prioritas 1: Cari User dengan role kepala_sekolah
            $userKepsek = User::where('role', 'kepala_sekolah')
                ->whereHas('guru', fn($q) => $q->whereNotNull('no_hp'))
                ->with('guru')
                ->first();
            if ($userKepsek?->guru) {
                return [
                    'nama'    => $userKepsek->guru->nama,
                    'jabatan' => 'Kepala Sekolah',
                    'no_hp'   => $userKepsek->guru->no_hp,
                ];
            }

            // Fallback: cari berdasarkan jabatan
            $kepsek = Guru::where('status', 'aktif')
                ->where('jabatan', 'like', '%Kepala Sekolah%')
                ->whereNotNull('no_hp')
                ->first();
            if ($kepsek) {
                return [
                    'nama'    => $kepsek->nama,
                    'jabatan' => 'Kepala Sekolah',
                    'no_hp'   => $kepsek->no_hp,
                ];
            }
        }

        return null;
    }

    /**
     * Template pesan eskalasi ke pejabat sekolah.
     */
    private static function buildPesanEskalasiPejabat(
        string $namaPejabat,
        string $jabatan,
        string $namaSiswa,
        string $nisn,
        string $rombel,
        string $tahap,
        int $poin,
        int $alpha,
        int $bolos,
        ?string $catatan
    ): string {
        $tahapLabel = str_replace('_', ' ', strtoupper($tahap));

        return "🔔 *[NOTIFIKASI ESKALASI KASUS KESISWAAN - SIRANI]*\n"
             . "Yth. Bapak/Ibu *{$namaPejabat}* ({$jabatan})\n"
             . "SMK Negeri 1 Air Naningan\n\n"
             . "Diberitahukan bahwa terdapat kasus kedisiplinan siswa yang telah dilimpahkan ke *{$tahapLabel}*:\n\n"
             . "👤 *Identitas Siswa:*\n"
             . "• Nama : *{$namaSiswa}*\n"
             . "• NISN : {$nisn}\n"
             . "• Kelas: {$rombel}\n\n"
             . "📊 *Statistik Pelanggaran:*\n"
             . "• Akumulasi Poin : *{$poin} Poin*\n"
             . "• Total Alpha    : {$alpha} Hari\n"
             . "• Total Bolos    : {$bolos} Kali\n"
             . ($catatan ? "📝 *Catatan Tindakan:* {$catatan}\n\n" : "\n")
             . "Mohon Bapak/Ibu segera membuka sistem SIRANI pada menu *Buku Kasus (/disiplin)* untuk melakukan tindak lanjut pembinaan dan pemanggilan orang tua.\n\n"
             . "_Pesan otomatis sistem SIRANI SMKN 1 Air Naningan._";
    }

    /**
     * Template pesan pemberitahuan eskalasi ke orang tua siswa.
     */
    private static function buildPesanEskalasiOrtu(
        string $namaOrtu,
        string $namaSiswa,
        string $rombel,
        string $tahap,
        ?string $catatan
    ): string {
        $tahapLabel = match ($tahap) {
            'tahap_1_wali_kelas' => 'Pembinaan Awal Wali Kelas',
            'tahap_2_bk'         => 'Konseling & Pemanggilan Ruang BK',
            'tahap_3_wakasis'    => 'Sidang Kesiswaan (Waka Kesiswaan)',
            'tahap_4_kepsek'     => 'Evaluasi Akhir Kepala Sekolah',
            'selesai_pembinaan'  => 'Penyelesaian Pembinaan (Disiplin Pulih)',
            default              => 'Proses Pembinaan Kesiswaan',
        };

        return "📢 *[PEMBERITAHUAN KEDISIPLINAN SISWA SMKN 1 AIR NANINGAN]*\n"
             . "Yth. *{$namaOrtu}*,\n"
             . "Orang Tua/Wali dari Ananda *{$namaSiswa}* ({$rombel})\n\n"
             . "Kami menginformasikan bahwa status kedisiplinan ananda saat ini masuk ke tahap: *{$tahapLabel}*.\n\n"
             . ($catatan ? "📝 *Keterangan Sekolah:* {$catatan}\n\n" : "")
             . "Kami mengharapkan kerja sama Bapak/Ibu dalam mendampingi kedisiplinan belajar ananda di rumah.\n\n"
             . "Hormat kami,\n"
             . "*Tim Kesiswaan SMKN 1 Air Naningan*";
    }

    /**
     * Template pesan pengingat harian (1x sehari) ke pejabat yang belum menindaklanjuti.
     */
    private static function buildPesanPengingatHarian(
        string $namaPejabat,
        string $jabatan,
        string $namaSiswa,
        string $nisn,
        string $rombel,
        string $tahapLabel,
        int $poin
    ): string {
        return "⏰ *[PENGINGAT HARIAN KASUS KESISWAAN - SIRANI]*\n"
             . "Yth. Bapak/Ibu *{$namaPejabat}* ({$jabatan})\n"
             . "SMK Negeri 1 Air Naningan\n\n"
             . "Mengingatkan kembali bahwa kasus kedisiplinan siswa berikut *MASIH MENUNGGU TINDAK LANJUT*:\n\n"
             . "👤 *Siswa:* {$namaSiswa} (NISN: {$nisn})\n"
             . "🏛️ *Kelas:* {$rombel}\n"
             . "⚖️ *Tahap Saat Ini:* *{$tahapLabel}* (Akumulasi: {$poin} Poin)\n\n"
             . "Mohon dapat meluangkan waktu untuk mencatat hasil pembinaan/konseling melalui menu *Buku Kasus & Disiplin (/disiplin)* di sistem SIRANI.\n\n"
             . "_(Pesan pengingat ini otomatis berhenti setelah hasil pembinaan diinput di sistem)_";
    }
}
