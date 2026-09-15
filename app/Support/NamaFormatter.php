<?php

namespace App\Support;

class NamaFormatter
{
    /**
     * Peta gelar akademik umum Indonesia untuk standarisasi penulisan.
     */
    protected static array $gelarMap = [
        'S.PD'    => 'S.Pd.',
        'S.PD.'   => 'S.Pd.',
        'SPD'     => 'S.Pd.',
        'M.PD'    => 'M.Pd.',
        'M.PD.'   => 'M.Pd.',
        'MPD'     => 'M.Pd.',
        'S.SI'    => 'S.Si.',
        'S.SI.'   => 'S.Si.',
        'SSI'     => 'S.Si.',
        'M.SI'    => 'M.Si.',
        'M.SI.'   => 'M.Si.',
        'S.KOM'   => 'S.Kom.',
        'S.KOM.'  => 'S.Kom.',
        'SKOM'    => 'S.Kom.',
        'M.KOM'   => 'M.Kom.',
        'M.KOM.'  => 'M.Kom.',
        'S.T'     => 'S.T.',
        'S.T.'    => 'S.T.',
        'ST'      => 'S.T.',
        'M.T'     => 'M.T.',
        'M.T.'    => 'M.T.',
        'MT'      => 'M.T.',
        'S.TR.P'  => 'S.Tr.P.',
        'S.TR.P.' => 'S.Tr.P.',
        'S.TR.T'  => 'S.Tr.T.',
        'S.TR.T.' => 'S.Tr.T.',
        'S.E'     => 'S.E.',
        'S.E.'    => 'S.E.',
        'SE'      => 'S.E.',
        'M.M'     => 'M.M.',
        'M.M.'    => 'M.M.',
        'MM'      => 'M.M.',
        'S.AG'    => 'S.Ag.',
        'S.AG.'   => 'S.Ag.',
        'M.AG'    => 'M.Ag.',
        'M.AG.'   => 'M.Ag.',
        'S.SOS'   => 'S.Sos.',
        'S.SOS.'  => 'S.Sos.',
        'M.SOS'   => 'M.Sos.',
        'S.PSI'   => 'S.Psi.',
        'S.PSI.'  => 'S.Psi.',
        'S.H'     => 'S.H.',
        'S.H.'    => 'S.H.',
        'SH'      => 'S.H.',
        'M.H'     => 'M.H.',
        'M.H.'    => 'M.H.',
        'DRS'     => 'Drs.',
        'DRS.'    => 'Drs.',
        'DRA'     => 'Dra.',
        'DRA.'    => 'Dra.',
        'PROF'    => 'Prof.',
        'PROF.'   => 'Prof.',
        'DR'      => 'Dr.',
        'DR.'     => 'Dr.',
        'H'       => 'H.',
        'H.'      => 'H.',
        'HJ'      => 'Hj.',
        'HJ.'     => 'Hj.',
    ];

    /**
     * Format nama personel/siswa/guru apapun format aslinya menjadi Title Case rapi:
     * Contoh: "AGI HUSNI WIDODO" => "Agi Husni Widodo"
     *         "agi husni widodo" => "Agi Husni Widodo"
     *         "APRIDA,S.SI."     => "Aprida, S.Si."
     */
    public static function format(?string $nama): string
    {
        if ($nama === null || trim($nama) === '') {
            return '';
        }

        $raw = trim($nama);

        // Jika ada pemisah koma (gelar belakang)
        if (str_contains($raw, ',')) {
            $parts = explode(',', $raw);
            $baseName = array_shift($parts);
            $formattedBase = static::titleCaseWords($baseName);

            $formattedGelar = [];
            foreach ($parts as $g) {
                $trimmed = trim($g);
                if ($trimmed !== '') {
                    $formattedGelar[] = static::standardizeGelar($trimmed);
                }
            }

            if (!empty($formattedGelar)) {
                return $formattedBase . ', ' . implode(', ', $formattedGelar);
            }

            return $formattedBase;
        }

        return static::titleCaseWords($raw);
    }

    /**
     * Konversi kata-kata nama ke Title Case yang rapi.
     */
    protected static function titleCaseWords(string $text): string
    {
        // Standarisasi spasi ganda
        $text = preg_replace('/\s+/', ' ', trim($text));

        // Pisahkan token berdasarkan spasi
        $words = explode(' ', $text);
        $result = [];

        foreach ($words as $word) {
            $word = trim($word);
            if ($word === '') continue;

            // Cek apakah kata ini merupakan gelar depan yang diketahui (Drs., Dra., Prof., Dr., H., Hj.)
            $upperClean = strtoupper(rtrim($word, '.'));
            if (isset(static::$gelarMap[$upperClean])) {
                $result[] = static::$gelarMap[$upperClean];
                continue;
            }

            // Tangani kata dengan tanda hubung misal: "Al-Fatih"
            if (str_contains($word, '-')) {
                $subParts = explode('-', $word);
                $formattedSub = array_map(fn($p) => mb_convert_case(mb_strtolower($p, 'UTF-8'), MB_CASE_TITLE, 'UTF-8'), $subParts);
                $result[] = implode('-', $formattedSub);
                continue;
            }

            // Tangani apostrof misal: "D'Angelo"
            if (str_contains($word, "'")) {
                $subParts = explode("'", $word);
                $formattedSub = array_map(fn($p) => mb_convert_case(mb_strtolower($p, 'UTF-8'), MB_CASE_TITLE, 'UTF-8'), $subParts);
                $result[] = implode("'", $formattedSub);
                continue;
            }

            // Standar kata: ubah ke Title Case
            $result[] = mb_convert_case(mb_strtolower($word, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
        }

        return implode(' ', $result);
    }

    /**
     * Standarisasi penulisan gelar akademik.
     */
    protected static function standardizeGelar(string $gelar): string
    {
        $gelarClean = trim($gelar);
        $upper = strtoupper($gelarClean);

        if (isset(static::$gelarMap[$upper])) {
            return static::$gelarMap[$upper];
        }

        // Jika pola gelar seperti "S.Pd" atau "M.Kom"
        if (preg_match('/^([a-z]+)\.([a-z]+)(\.([a-z]+))*\.?$/i', $gelarClean)) {
            $tokens = explode('.', rtrim($gelarClean, '.'));
            $casedTokens = array_map(function($t) {
                if (strlen($t) <= 1) return strtoupper($t);
                return ucfirst(strtolower($t));
            }, $tokens);
            return implode('.', $casedTokens) . '.';
        }

        return $gelarClean;
    }
}
