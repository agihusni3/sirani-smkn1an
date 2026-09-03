<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\HariLibur;
use App\Models\Siswa;
use App\Services\WhatsAppNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class FlaggingBelumHadirCommand extends Command
{
    protected $signature = 'piket:flagging-belum-hadir {tanggal?}';
    protected $description = 'Pukul 07:30 — kirim WA pengingat ke orang tua siswa & guru yang belum hadir';

    public function handle()
    {
        $tanggal = $this->argument('tanggal') ?? Carbon::today()->toDateString();

        if (HariLibur::isLibur($tanggal)) {
            $this->info("Tanggal {$tanggal} adalah hari libur. Flagging dilewati.");
            return 0;
        }

        $this->info("Menjalankan flagging belum hadir untuk: {$tanggal}");
        $waService = app(WhatsAppNotificationService::class);
        $todayStr  = Carbon::parse($tanggal)->locale('id')->isoFormat('dddd, D MMMM Y');

        // ── Siswa Belum Hadir ─────────────────────────────────────────────────
        $absensiSiswaIds = Absensi::where('pemilik_type', 'siswa')
            ->where('tanggal', $tanggal)
            ->pluck('pemilik_id');

        $siswaBelumHadir = Siswa::where('status', 'aktif')
            ->whereNotIn('id', $absensiSiswaIds)
            ->get();

        $countSiswaSent = 0;
        foreach ($siswaBelumHadir as $siswa) {
            $noHp = $siswa->no_hp_siswa ?: $siswa->no_hp_ortu;
            if (empty($noHp)) continue;

            $pesan = "Yth. Bapak/Ibu Wali dari ananda *{$siswa->nama}*,\n\n"
                . "Kami dari Petugas Piket SMKN 1 Air Naningan menginformasikan bahwa pada hari ini ({$todayStr}), "
                . "ananda *belum tercatat hadir* di sekolah hingga pukul 07.30 WIB.\n\n"
                . "Mohon konfirmasi jika ananda sakit, izin, atau ada keperluan lain.\n\n"
                . "_Terima kasih,_\n*Meja Piket SMKN 1 Air Naningan*";

            $res = $waService->kirimDirect($noHp, $pesan, 'PENGINGAT KEHADIRAN SISWA');
            if (!empty($res['success'])) $countSiswaSent++;

            // Delay antar pesan agar tidak diblokir gateway
            if ($countSiswaSent > 0) sleep(rand(3, 5));
        }

        // ── Guru Belum Hadir ──────────────────────────────────────────────────
        $absensiGuruIds = Absensi::where('pemilik_type', 'guru')
            ->where('tanggal', $tanggal)
            ->pluck('pemilik_id');

        $hariIniIndo = Carbon::parse($tanggal)->locale('id')->isoFormat('dddd');
        $guruBelumHadir = Guru::where('status', 'aktif')
            ->whereNotIn('id', $absensiGuruIds)
            ->get()
            ->filter(fn($g) => $g->isWajibHadirHari($hariIniIndo));

        $countGuruSent = 0;
        foreach ($guruBelumHadir as $guru) {
            if (empty($guru->no_hp)) continue;

            $pesan = "Yth. Bapak/Ibu *{$guru->nama}*,\n\n"
                . "Kami dari Petugas Guru Piket SMKN 1 Air Naningan mengingatkan bahwa pada hari ini ({$todayStr}), "
                . "Bapak/Ibu *belum melakukan pemindaian presensi masuk* di sekolah hingga pukul 07.30 WIB.\n\n"
                . "Mohon segera konfirmasi status kehadiran kepada Guru Piket. Terima kasih.\n\n"
                . "_Salam hormat,_\n*Petugas Piket SMKN 1 Air Naningan*";

            $res = $waService->kirimDirect($guru->no_hp, $pesan, 'PENGINGAT PRESENSI GURU');
            if (!empty($res['success'])) $countGuruSent++;

            if ($countGuruSent > 0) sleep(rand(3, 5));
        }

        $this->info("Flagging selesai: WA terkirim ke {$countSiswaSent} wali murid & {$countGuruSent} guru.");
        return 0;
    }
}
