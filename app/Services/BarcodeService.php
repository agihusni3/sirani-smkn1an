<?php

namespace App\Services;

class BarcodeService
{
    /**
     * Pola lebar garis (Bar/Space) standar Code 128 (ISO/IEC 15417).
     * Masing-masing terdiri dari 6 digit (Bar, Space, Bar, Space, Bar, Space),
     * kecuali Stop (106) yang terdiri dari 7 digit.
     */
    private static array $patterns = [
        '212222', '222122', '222221', '121223', '121322', '131222', '122213', '122312', '132212', '221213', // 0-9
        '221312', '231212', '112232', '122132', '122231', '113222', '123122', '123221', '223211', '221132', // 10-19
        '221231', '213212', '223112', '312131', '311222', '321122', '321221', '312212', '322112', '322211', // 20-29
        '212123', '212321', '232121', '111323', '131123', '131321', '112313', '132113', '132311', '211313', // 30-39
        '231113', '231311', '112133', '112331', '132131', '113123', '113321', '133121', '313121', '211331', // 40-49
        '231131', '213113', '213311', '213131', '311123', '311321', '331121', '312113', '312311', '332111', // 50-59
        '314111', '221411', '431111', '111224', '111422', '121124', '121421', '141122', '141221', '112214', // 60-69
        '112412', '122114', '122411', '142112', '142211', '241211', '221114', '413111', '241112', '134111', // 70-79
        '111242', '121142', '121241', '114212', '124112', '124211', '411212', '421112', '421211', '212141', // 80-89
        '214121', '412121', '111143', '111341', '131141', '114113', '114311', '411113', '411311', '113141', // 90-99
        '114131', '311141', '411131', '211412', '211214', '211232', '2331112'                                 // 100-106
    ];

    /**
     * Generate barcode SVG murni standar Code 128 (Auto B / C).
     *
     * @param string $code Teks/angka yang akan dijadikan barcode (misal NISN)
     * @param int $height Tinggi garis barcode (px)
     * @param int $moduleWidth Lebar 1 modul bar (px)
     * @param bool $showText Tampilkan teks di bawah barcode
     * @return string SVG string
     */
    public static function getBarcodeSvg(string $code, int $height = 42, int $moduleWidth = 2, bool $showText = true): string
    {
        $code = trim($code);
        if ($code === '') {
            return '';
        }

        // Cek apakah string hanya berisi digit genap >= 4 karakter untuk optimasi Code 128 Set C
        $isDigits = ctype_digit($code);
        $len = strlen($code);

        $codes = [];
        $checksum = 0;

        if ($isDigits && $len >= 4 && $len % 2 === 0) {
            // Mode Set C (Pasangan 2 digit -> 1 karakter barcode, sangat efisien dan ringkas untuk NISN)
            $startCode = 105; // START_C
            $codes[] = $startCode;
            $checksum = $startCode;

            $weight = 1;
            for ($i = 0; $i < $len; $i += 2) {
                $pair = (int) substr($code, $i, 2);
                $codes[] = $pair;
                $checksum += ($pair * $weight);
                $weight++;
            }
        } else {
            // Mode Set B (Standar ASCII 32 - 127)
            $startCode = 104; // START_B
            $codes[] = $startCode;
            $checksum = $startCode;

            for ($i = 0; $i < $len; $i++) {
                $ascii = ord($code[$i]);
                $val = $ascii - 32;
                if ($val < 0 || $val > 95) {
                    $val = 0; // fallback spasi
                }
                $codes[] = $val;
                $checksum += ($val * ($i + 1));
            }
        }

        // Checksum modulo 103
        $checkDigit = $checksum % 103;
        $codes[] = $checkDigit;
        $codes[] = 106; // STOP_CODE

        // Susun bar sequence (array boolean: true = bar hitam, false = space putih)
        $bars = [];
        foreach ($codes as $cIdx) {
            $pat = self::$patterns[$cIdx] ?? self::$patterns[0];
            $patLen = strlen($pat);
            $isBar = true;
            for ($p = 0; $p < $patLen; $p++) {
                $width = (int) $pat[$p];
                for ($w = 0; $w < $width; $w++) {
                    $bars[] = $isBar;
                }
                $isBar = !$isBar;
            }
        }

        // Hitung total lebar dengan margin tenang (quiet zone) 10 modul kiri dan kanan
        $quietZone = 10;
        $totalModules = count($bars) + ($quietZone * 2);
        $svgWidth = $totalModules * $moduleWidth;
        $textHeight = $showText ? 15 : 0;
        $svgHeight = $height + $textHeight;

        $rects = '';
        $currentX = $quietZone * $moduleWidth;

        $inBar = false;
        $barStartX = 0;

        foreach ($bars as $bar) {
            if ($bar && !$inBar) {
                $inBar = true;
                $barStartX = $currentX;
            } elseif (!$bar && $inBar) {
                $inBar = false;
                $barW = $currentX - $barStartX;
                $rects .= sprintf('<rect x="%d" y="0" width="%d" height="%d" fill="#000000" />', $barStartX, $barW, $height);
            }
            $currentX += $moduleWidth;
        }

        if ($inBar) {
            $barW = $currentX - $barStartX;
            $rects .= sprintf('<rect x="%d" y="0" width="%d" height="%d" fill="#000000" />', $barStartX, $barW, $height);
        }

        $textElement = '';
        if ($showText) {
            $textY = $height + 12;
            $centerX = $svgWidth / 2;
            $escapedText = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');
            $textElement = sprintf(
                '<text x="%.1f" y="%d" text-anchor="middle" font-family="monospace, sans-serif" font-size="11" font-weight="700" fill="#000000" letter-spacing="1">%s</text>',
                $centerX,
                $textY,
                $escapedText
            );
        }

        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %d %d" width="%d" height="%d" style="display:inline-block; vertical-align:middle; background:#ffffff;">%s%s</svg>',
            $svgWidth,
            $svgHeight,
            $svgWidth,
            $svgHeight,
            $rects,
            $textElement
        );
    }
}
