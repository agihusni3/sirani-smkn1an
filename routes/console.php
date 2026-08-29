<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Evaluasi Alpha & Deteksi Bolos Otomatis setiap hari kerja pukul 17:00 WIB
Schedule::command('absensi:evaluasi-alpha')->dailyAt('17:00')->weekdays();

// Pencadangan Otomatis Database (Auto-Backup) setiap malam pukul 23:00 WIB dengan retensi 14 hari
Schedule::command('db:auto-backup --keep=14')->dailyAt('23:00');
