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

if (!function_exists('format_narasi_kbm')) {
    /**
     * Format teks narasi KBM / Kurikulum Merdeka (Capaian Pembelajaran, Rasional, dsb):
     * - Menggabungkan soft-break (baris baru tunggal dari hasil copy-paste PDF/dokumen) menjadi spasi
     *   sehingga kalimat mengalir memenuhi lebar baris penuh sebelum turun ke bawah.
     * - Membagi paragraf berdasarkan double newline (\n\n) dengan <p> terpisah.
     * - Mendeteksi daftar berpoin / bernomor secara otomatis.
     * - Mengatur alignment rata kanan-kiri (text-align: justify) dengan text-justify: inter-word.
     */
    function format_narasi_kbm(?string $text, array $options = []): string
    {
        if (empty($text)) {
            return '';
        }

        $fontSize   = $options['font_size'] ?? 'inherit';
        $lineHeight = $options['line_height'] ?? '1.85';
        $marginBtm  = $options['margin_bottom'] ?? '12px';
        $color      = $options['color'] ?? 'inherit';

        // Normalisasi format newline
        $text = str_replace(["\r\n", "\r"], "\n", trim($text));

        // Pecah blok per 2 newline atau lebih (paragraf terpisah)
        $rawBlocks = preg_split("/\n{2,}/", $text);
        $blocks = array_values(array_filter(array_map('trim', $rawBlocks), fn($b) => $b !== ''));
        $output = '';

        foreach ($blocks as $idx => $block) {
            $isLast = ($idx === count($blocks) - 1);
            $mb = $isLast ? '0' : $marginBtm;
            $lines = explode("\n", $block);
            $hasListMarker = false;

            // Cek apakah ada penomoran / bullet di baris
            foreach ($lines as $line) {
                if (preg_match('/^(\d+[\.\)]|[a-zA-Z][\.\)]|[\-\*\•])\s+/', trim($line))) {
                    $hasListMarker = true;
                    break;
                }
            }

            if ($hasListMarker) {
                $isNumbered = preg_match('/^\d+[\.\)]\s+/', trim($lines[0] ?? ''));
                $tag = $isNumbered ? 'ol' : 'ul';
                $items = [];
                $currentItem = null;

                foreach ($lines as $line) {
                    $trimmed = trim($line);
                    if ($trimmed === '') {
                        continue;
                    }

                    if (preg_match('/^(\d+[\.\)]|[a-zA-Z][\.\)]|[\-\*\•])\s+(.*)$/', $trimmed, $m)) {
                        if ($currentItem !== null) {
                            $items[] = $currentItem;
                        }
                        $currentItem = $m[2];
                    } else {
                        if ($currentItem !== null) {
                            $currentItem .= ' ' . $trimmed;
                        } else {
                            $currentItem = $trimmed;
                        }
                    }
                }
                if ($currentItem !== null) {
                    $items[] = $currentItem;
                }

                if (!empty($items)) {
                    $output .= "<{$tag} style=\"margin:0 0 {$mb} 0; padding-left:22px; line-height:{$lineHeight}; font-size:{$fontSize}; color:{$color};\">";
                    foreach ($items as $item) {
                        $output .= "<li style=\"margin-bottom:6px; text-align:justify; text-justify:inter-word;\">" . e($item) . "</li>";
                    }
                    $output .= "</{$tag}>";
                }
            } else {
                // Paragraf biasa: gabungkan newline tunggal menjadi spasi
                // agar baris terisi penuh sebelum membungkus ke bawah
                $paragraph = preg_replace('/\s*\n\s*/', ' ', $block);
                $output .= "<p style=\"margin:0 0 {$mb} 0; text-align:justify; text-justify:inter-word; line-height:{$lineHeight}; font-size:{$fontSize}; color:{$color};\">" . e($paragraph) . "</p>";
            }
        }

        return $output;
    }
}

if (!function_exists('format_hari_indo')) {
    /**
     * Konversi hari ke Bahasa Indonesia (Senin, Selasa, Rabu, Kamis, Jumat, Sabtu, Minggu)
     */
    function format_hari_indo($date): string
    {
        if (!$date) return '—';
        $carbon = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);
        $days = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
        ];
        return $days[$carbon->format('l')] ?? $carbon->locale('id')->translatedFormat('l');
    }
}

if (!function_exists('format_tanggal_indo')) {
    /**
     * Format tanggal lengkap Bahasa Indonesia: "Kamis, 24 September 2026"
     */
    function format_tanggal_indo($date, bool $withDay = true): string
    {
        if (!$date) return '—';
        $carbon = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);
        $dayPart = $withDay ? format_hari_indo($carbon) . ', ' : '';
        return $dayPart . $carbon->locale('id')->translatedFormat('d F Y');
    }
}
