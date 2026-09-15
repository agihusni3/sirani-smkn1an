<?php

use App\Support\NamaFormatter;

if (!function_exists('format_nama')) {
    /**
     * Standarisasi format nama personel/siswa/guru menjadi Title Case resmi (Agi Husni Widodo).
     */
    function format_nama(?string $nama): string
    {
        return NamaFormatter::format($nama);
    }
}
