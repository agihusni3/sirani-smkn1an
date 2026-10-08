<?php

namespace App\Services;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class BarcodeService
{
    /**
     * Generate Barcode 2D (QR Code) dalam format SVG murni.
     * Sangat tajam untuk cetak resolusi tinggi (A4/PDF) dan browser tanpa ketergantungan GD.
     *
     * @param string $code Data barcode (misal NISN siswa)
     * @param int $size Ukuran pixel (lebar & tinggi)
     * @return string String elemen SVG
     */
    public static function getBarcode2DSvg(string $code, int $size = 64): string
    {
        $code = trim($code);
        if ($code === '') {
            return '';
        }

        $options = new QROptions([
            'outputType'    => QRCode::OUTPUT_MARKUP_SVG,
            'eccLevel'      => QRCode::ECC_M,
            'addQuietzone'  => false,
            'imageBase64'   => false,
        ]);

        $svg = (new QRCode($options))->render($code);

        // Hapus style bawaan dan inject ukuran presisi
        $svg = preg_replace('/<svg([^>]*)style="[^"]*"/i', '<svg$1', $svg);
        $svg = str_replace(
            '<svg ',
            sprintf('<svg width="%d" height="%d" style="display:block; margin:0 auto;" ', $size, $size),
            $svg
        );

        return $svg;
    }

    /**
     * Generate Barcode 2D (QR Code) dalam format binary PNG murni tanpa ekstensi GD.
     * Sangat handal dan kompatibel di semua server Ubuntu / Linux walaupun tanpa php-gd.
     *
     * @param string $code Data barcode (misal NISN siswa)
     * @param int $scale Skala pixel per modul QR (default 3)
     * @param int $quietZone Margin padding modul (default 1)
     * @return string Binary PNG string
     */
    public static function getBarcode2DPngBinary(string $code, int $scale = 3, int $quietZone = 1): string
    {
        $code = trim($code);
        if ($code === '') {
            return '';
        }

        try {
            $options = new QROptions([
                'version'  => -1,
                'eccLevel' => QRCode::ECC_M,
            ]);
            $matrix = (new QRCode($options))->getMatrix($code);
            $matrixSize = $matrix->size();

            $totalModules = $matrixSize + ($quietZone * 2);
            $w = $totalModules * $scale;
            $h = $w;

            // Susun scanline mentah format 8-bit Grayscale (0 = hitam, 255 = putih)
            $scanlines = '';
            for ($y = 0; $y < $totalModules; $y++) {
                $moduleY = $y - $quietZone;
                $rowBytes = '';
                for ($x = 0; $x < $totalModules; $x++) {
                    $moduleX = $x - $quietZone;
                    $isDark = ($moduleY >= 0 && $moduleY < $matrixSize && $moduleX >= 0 && $moduleX < $matrixSize)
                        ? $matrix->check($moduleX, $moduleY)
                        : false;
                    $pixel = $isDark ? "\x00" : "\xFF";
                    $rowBytes .= str_repeat($pixel, $scale);
                }
                $line = "\x00" . $rowBytes; // Filter type 0: None
                for ($s = 0; $s < $scale; $s++) {
                    $scanlines .= $line;
                }
            }

            // Chunk IHDR: width, height, 8 bit depth, color type 0 (Grayscale)
            $ihdrData = pack('NNCCCCC', $w, $h, 8, 0, 0, 0, 0);
            $ihdrChunk = pack('N', 13) . 'IHDR' . $ihdrData . pack('N', crc32('IHDR' . $ihdrData));

            // Chunk IDAT: kompresi zlib
            $idatCompressed = gzcompress($scanlines, 9);
            $idatChunk = pack('N', strlen($idatCompressed)) . 'IDAT' . $idatCompressed . pack('N', crc32('IDAT' . $idatCompressed));

            // Chunk IEND
            $iendChunk = pack('N', 0) . 'IEND' . pack('N', crc32('IEND'));

            return "\x89PNG\r\n\x1a\n" . $ihdrChunk . $idatChunk . $iendChunk;
        } catch (\Throwable $e) {
            \Log::warning('BarcodeService PNG generation error: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Generate Barcode 2D (QR Code) dalam format Data URI PNG (Base64).
     *
     * @param string $code Data barcode (misal NISN siswa)
     * @param int $scale Skala pixel per modul
     * @return string data:image/png;base64,...
     */
    public static function getBarcode2DDataUri(string $code, int $scale = 3): string
    {
        $png = self::getBarcode2DPngBinary($code, $scale);
        if ($png === '') {
            return '';
        }

        return 'data:image/png;base64,' . base64_encode($png);
    }

    /**
     * Alias kompatibilitas: mengembalikan Barcode 2D SVG
     */
    public static function getBarcodeSvg(string $code, int $size = 64, int $moduleWidth = 2, bool $showText = true): string
    {
        return self::getBarcode2DSvg($code, $size);
    }
}
