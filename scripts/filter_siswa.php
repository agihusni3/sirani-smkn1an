<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Siswa;
use App\Models\Rombel;

$inputPath = storage_path('app/import_temp/raw_input.csv');
$outUnimportedPath = storage_path('app/import_temp/siswa_belum_masuk.csv');
$outAllCleanPath = storage_path('app/import_temp/siswa_semua_bersih.csv');

if (!file_exists($inputPath)) {
    die("File raw input tidak ditemukan: $inputPath\n");
}

$content = file_get_contents($inputPath);
if (str_starts_with($content, "\xEF\xBB\xBF")) {
    $content = substr($content, 3);
}
$lines = preg_split('/\r\n|\r|\n/', trim($content));

// Header: Timestamp,NIS,NISN,Nama Lengkap Siswa,No WhatsApp Siswa,Nama Orang tua,No WhatsApp Ortu,Kelas,Status
$header = str_getcsv(array_shift($lines), ',');

function cleanPhone($phone) {
    if (empty($phone)) return '';
    $phone = trim($phone, " \t\n\r\0\x0B\"'");
    if (preg_match('/(ga ada|nggak|nggk|tidak ada|belum|none|hapal)/i', $phone)) return '';
    $digits = preg_replace('/[^0-9]/', '', $phone);
    if (empty($digits) || strlen($digits) < 7) return '';
    if (str_starts_with($digits, '62')) {
        $digits = '0' . substr($digits, 2);
    } elseif (str_starts_with($digits, '8')) {
        $digits = '0' . $digits;
    }
    return $digits;
}

function cleanNisn($nisn, $nis) {
    $nisn = trim((string)$nisn, " \t\n\r\0\x0B\"'=");
    $digits = preg_replace('/[^0-9]/', '', $nisn);
    if (empty($digits) || empty(ltrim($digits, '0')) || strlen($digits) < 6) {
        $nisDigits = preg_replace('/[^0-9]/', '', (string)$nis);
        if (!empty($nisDigits) && !empty(ltrim($nisDigits, '0')) && strlen($nisDigits) >= 8 && strlen($nisDigits) <= 12) {
            return $nisDigits;
        }
        return '';
    }
    return $digits;
}

// Ambil semua siswa dari DB
$dbSiswas = Siswa::with('siswaRombel.rombel')->get();
$dbNisnMap = [];
$dbNamaMap = [];

foreach ($dbSiswas as $s) {
    $cleanDbNisn = preg_replace('/[^0-9]/', '', (string)$s->nisn);
    if (!empty($cleanDbNisn)) {
        $dbNisnMap[$cleanDbNisn] = $s;
        $dbNisnMap[ltrim($cleanDbNisn, '0')] = $s;
    }
    $cleanDbNama = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$s->nama));
    if (!empty($cleanDbNama)) {
        $dbNamaMap[$cleanDbNama] = $s;
    }
}

// Tahap 1: Kumpulkan dan deduplikasi baris input (jika siswa isi 2x, utamakan yang punya NISN valid & timestamp terbaru)
$parsedRows = [];
foreach ($lines as $lineIdx => $line) {
    if (empty(trim($line))) continue;
    $row = str_getcsv($line, ',');
    if (count($row) < 8) continue;

    $timestamp   = trim($row[0] ?? '');
    $nis         = trim($row[1] ?? '');
    $rawNisn     = trim($row[2] ?? '');
    $nama        = trim($row[3] ?? '');
    $noHpSiswa   = cleanPhone($row[4] ?? '');
    $namaOrtu    = trim($row[5] ?? '');
    $noHpOrtu    = cleanPhone($row[6] ?? '');
    $kelas       = trim($row[7] ?? '');
    $status      = 'Aktif';

    // Bersihkan nama orang tua jika nomor HP
    if (preg_match('/^[0-9\-\+\s]+$/', $namaOrtu)) {
        $noHpOrtu = cleanPhone($namaOrtu);
        $namaOrtu = 'Orang Tua / Wali';
    }

    $nisn = cleanNisn($rawNisn, $nis);

    // Kunci unik berdasarkan nama yang dinormalisasi
    $normNama = strtolower(preg_replace('/[^a-zA-Z]/', '', $nama));
    
    // Penanganan khusus Rahma Irawan -> Rama Irawan (sama orang, salah ketik h)
    if ($normNama === 'rahmairawan') {
        $normNama = 'ramairawan';
    }

    if (!isset($parsedRows[$normNama])) {
        $parsedRows[$normNama] = [
            'nisn' => $nisn,
            'nama' => $nama,
            'nama_ortu' => $namaOrtu ?: 'Wali Siswa',
            'no_hp_ortu' => $noHpOrtu,
            'no_hp_siswa' => $noHpSiswa,
            'rombel' => $kelas,
            'status' => $status,
            'line_idx' => $lineIdx + 1,
        ];
    } else {
        // Jika sudah ada sebelumnya, tapi yang baru punya NISN valid sedangkan yang lama kosong: update!
        if (empty($parsedRows[$normNama]['nisn']) && !empty($nisn)) {
            $parsedRows[$normNama]['nisn'] = $nisn;
            $parsedRows[$normNama]['nama'] = $nama;
        }
        if (empty($parsedRows[$normNama]['no_hp_ortu']) && !empty($noHpOrtu)) {
            $parsedRows[$normNama]['no_hp_ortu'] = $noHpOrtu;
        }
        if (empty($parsedRows[$normNama]['no_hp_siswa']) && !empty($noHpSiswa)) {
            $parsedRows[$normNama]['no_hp_siswa'] = $noHpSiswa;
        }
    }
}

$alreadyInDb = [];
$notInDb = [];

foreach ($parsedRows as $normNama => $item) {
    $nisn = $item['nisn'];
    $nama = $item['nama'];
    $cleanNamaKey = $normNama;
    $nisnKey = !empty($nisn) ? ltrim(preg_replace('/[^0-9]/', '', $nisn), '0') : '';

    $matchSiswa = null;
    if (!empty($nisnKey) && isset($dbNisnMap[$nisnKey])) {
        $matchSiswa = $dbNisnMap[$nisnKey];
    } elseif (!empty($nisn) && isset($dbNisnMap[$nisn])) {
        $matchSiswa = $dbNisnMap[$nisn];
    } elseif (!empty($cleanNamaKey) && isset($dbNamaMap[$cleanNamaKey])) {
        $matchSiswa = $dbNamaMap[$cleanNamaKey];
    }

    // Jika NISN masih kosong, berikan kode sementara
    if (empty($item['nisn'])) {
        $item['nisn'] = 'NISN' . str_pad($item['line_idx'], 6, '0', STR_PAD_LEFT);
    }

    if ($matchSiswa) {
        $item['db_id'] = $matchSiswa->id;
        $item['db_nisn'] = $matchSiswa->nisn;
        $item['db_nama'] = $matchSiswa->nama;
        $item['db_rombel'] = $matchSiswa->siswaRombel->first()?->rombel?->nama_rombel ?? '-';
        $alreadyInDb[] = $item;
    } else {
        $notInDb[] = $item;
    }
}

// 1. Tulis file CSV KHUSUS YANG BELUM MASUK (format standar import SIRANI)
$fpUnimported = fopen($outUnimportedPath, 'w');
fprintf($fpUnimported, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
fputcsv($fpUnimported, ['NISN', 'Nama Lengkap Siswa', 'Nama Orang Tua / Wali', 'No HP WhatsApp Ortu', 'No HP WhatsApp Siswa', 'Nama Kelas (Rombel)', 'Status (Aktif/PKL/Lulus)'], ';');

foreach ($notInDb as $item) {
    fputcsv($fpUnimported, [
        $item['nisn'],
        $item['nama'],
        $item['nama_ortu'],
        $item['no_hp_ortu'],
        $item['no_hp_siswa'],
        $item['rombel'],
        $item['status']
    ], ';');
}
fclose($fpUnimported);

// 2. Tulis file CSV SEMUA SISWA BERSIH & RAPI (format standar import SIRANI)
$fpAll = fopen($outAllCleanPath, 'w');
fprintf($fpAll, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
fputcsv($fpAll, ['NISN', 'Nama Lengkap Siswa', 'Nama Orang Tua / Wali', 'No HP WhatsApp Ortu', 'No HP WhatsApp Siswa', 'Nama Kelas (Rombel)', 'Status (Aktif/PKL/Lulus)'], ';');

$allSorted = array_merge($notInDb, $alreadyInDb);
foreach ($allSorted as $item) {
    fputcsv($fpAll, [
        $item['nisn'],
        $item['nama'],
        $item['nama_ortu'],
        $item['no_hp_ortu'],
        $item['no_hp_siswa'],
        $item['rombel'],
        $item['status']
    ], ';');
}
fclose($fpAll);

echo "========================================\n";
echo "       HASIL PEMERIKSAAN & SORTIR       \n";
echo "========================================\n";
echo "Total Siswa Unik di Google Form/CSV : " . count($parsedRows) . " siswa\n";
echo "Siswa yang SUDAH ADA di Database    : " . count($alreadyInDb) . " siswa\n";
echo "Siswa yang BELUM MASUK (Perlu Import): " . count($notInDb) . " siswa\n";
echo "========================================\n\n";

echo "DAFTAR SISWA YANG BELUM MASUK (" . count($notInDb) . " SISWA):\n";
echo str_repeat('-', 95) . "\n";
printf("%-4s | %-12s | %-28s | %-8s | %-14s | %-14s\n", "No", "NISN", "Nama Siswa", "Kelas", "No HP Siswa", "No HP Ortu");
echo str_repeat('-', 95) . "\n";
foreach ($notInDb as $idx => $s) {
    printf("%-4d | %-12s | %-28s | %-8s | %-14s | %-14s\n", 
        $idx + 1, 
        $s['nisn'], 
        mb_substr($s['nama'], 0, 28), 
        $s['rombel'], 
        $s['no_hp_siswa'] ?: '-', 
        $s['no_hp_ortu'] ?: '-'
    );
}
echo str_repeat('-', 95) . "\n";
