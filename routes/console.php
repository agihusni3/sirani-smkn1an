<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Alur Kerja Guru Piket ──────────────────────────────────────────────────

// 07:30 — Flagging belum hadir: kirim WA pengingat ke orang tua siswa & guru yang belum scan
Schedule::command('piket:flagging-belum-hadir')->dailyAt('07:30')->weekdays();

// 10:00 — Kunci Status Alpha: siswa/guru yang masih belum hadir & tanpa keterangan → Alpha
Schedule::command('piket:kunci-alpha')->dailyAt('10:00')->weekdays();

// ── Evaluasi Sore (Deteksi Bolos & Alpha Siswa Tidak Pulang) ──────────────

// 17:00 — Evaluasi Alpha & Deteksi Bolos Otomatis setiap hari kerja WIB
Schedule::command('absensi:evaluasi-alpha')->dailyAt('17:00')->weekdays();

// ── Backup & Maintenance ──────────────────────────────────────────────────

// Pencadangan Otomatis Database (Auto-Backup) setiap malam pukul 23:00 WIB dengan retensi 14 hari
Schedule::command('db:auto-backup --keep=14')->dailyAt('23:00');
