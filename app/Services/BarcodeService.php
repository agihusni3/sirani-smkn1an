<?php

namespace App\Services;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class BarcodeService
{
    /**
     * Generate Barcode 2D (QR Code) dalam format SVG murni.
     * Sangat tajam untuk cetak resolusi tinggi (A4/PDF) dan browser.
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
     * Generate Barcode 2D (QR Code) dalam format Data URI PNG (Base64).
     * Sangat kompatibel saat dimasukkan ke dalam sel tabel Microsoft Excel (.xls).
     *
     * @param string $code Data barcode (misal NISN siswa)
     * @param int $scale Skala pixel per modul
     * @return string data:image/png;base64,...
     */
    public static function getBarcode2DDataUri(string $code, int $scale = 3): string
    {
        $code = trim($code);
        if ($code === '') {
            return '';
        }

        $options = new QROptions([
            'outputType'    => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'      => QRCode::ECC_M,
            'scale'         => $scale,
            'addQuietzone'  => true,
            'quietzoneSize' => 1,
            'imageBase64'   => true,
        ]);

        // Suppress PHP 8.5 imagedestroy notice from internal GD library
        return (string) @(new QRCode($options))->render($code);
    }

    /**
     * Alias kompatibilitas: mengembalikan Barcode 2D SVG
     */
    public static function getBarcodeSvg(string $code, int $size = 64, int $moduleWidth = 2, bool $showText = true): string
    {
        return self::getBarcode2DSvg($code, $size);
    }
}
