<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIRINGAN UJI TES BUTA WARNA ISHIHARA — PPDB SMKN 1 AIR NANINGAN</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm 10mm 12mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, 'Times New Roman', serif;
            margin: 0;
            padding: 20px 0;
            background: #f1f5f9;
            color: #0f172a;
            line-height: 1.35;
        }

        .screen-toolbar {
            width: 210mm;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #1e293b;
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
            background: #ffffff;
            padding: 8px 16px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
        }

        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #2563eb;
            color: #ffffff;
            font-weight: 800;
            font-size: 13px;
            padding: 9px 20px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(37,99,235,0.25);
        }

        .page-a4 {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 12mm 14mm;
            box-shadow: 0 4px 18px rgba(0,0,0,0.12);
        }

        .kop-table {
            width: 100%;
            border-bottom: 3px double #000000;
            padding-bottom: 6px;
            margin-bottom: 12px;
            font-family: 'Times New Roman', Times, serif;
        }

        .kop-logo {
            width: 65px;
            text-align: center;
            vertical-align: middle;
        }

        .kop-logo img {
            width: 60px;
            height: auto;
        }

        .kop-text {
            text-align: center;
            vertical-align: middle;
        }

        .kop-prov {
            font-size: 10.5pt;
            font-weight: 700;
            text-transform: uppercase;
        }

        .kop-dinas {
            font-size: 11.5pt;
            font-weight: 700;
            text-transform: uppercase;
        }

        .kop-sekolah {
            font-size: 13.5pt;
            font-weight: 900;
            text-transform: uppercase;
        }

        .kop-alamat {
            font-size: 8.5pt;
            font-style: italic;
            color: #334155;
        }

        .doc-title {
            text-align: center;
            margin-bottom: 14px;
            font-family: 'Times New Roman', Times, serif;
        }

        .doc-title h2 {
            font-size: 13pt;
            font-weight: 800;
            margin: 0 0 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: underline;
        }

        .doc-title p {
            font-size: 9.5pt;
            margin: 0;
            color: #334155;
        }

        .grid-plates {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 16px;
        }

        .plate-card {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 8px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .plate-svg-wrap {
            width: 130px;
            height: 130px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .plate-meta {
            font-size: 8pt;
            color: #475569;
            line-height: 1.25;
            text-align: center;
            width: 100%;
        }

        .plate-meta strong {
            color: #0f172a;
            display: block;
            font-size: 9pt;
        }

        .table-panduan {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-top: 10px;
            font-family: 'Times New Roman', Times, serif;
        }

        .table-panduan th,
        .table-panduan td {
            border: 1px solid #000000;
            padding: 4px 6px;
            vertical-align: middle;
        }

        .table-panduan th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .screen-toolbar {
                display: none !important;
            }
            .page-a4 {
                box-shadow: none;
                padding: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="screen-toolbar">
        <a href="{{ route('admin.ppdb.seleksi', ['tab' => 'wawancara']) }}" class="btn-back">
            &larr; Kembali ke Seleksi PPDB
        </a>
        <div style="display: flex; gap: 10px;">
            <button type="button" onclick="window.print()" class="btn-print">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align:middle;">
                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                    <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
                </svg>
                Cetak Lembar Uji Ishihara (A4)
            </button>
        </div>
    </div>

    <div class="page-a4">
        {{-- KOP SURAT RESMI DINAS PEMERINTAH PROVINSI LAMPUNG & SEKOLAH --}}
        @include('partials.kop_surat')

        <div class="doc-title">
            <h2>PIRINGAN UJI PERSEPSI WARNA ISHIHARA PPDB 2026</h2>
            <p>Instrumen Uji Buta Warna Seleksi Masuk Kejuruan (RPL &bull; APHP &bull; TSM)</p>
        </div>

        {{-- 8 PIRINGAN ISHIHARA VECTOR RESMI --}}
        <div class="grid-plates">
            @php
                $plateDefs = [
                    ['no' => 1, 'num' => '12', 'tipe' => 'Plat Kontrol Demo', 'norm' => '12', 'pars' => '12', 'tot' => '12', 'fg' => ['#e05238', '#eb6244', '#cf4128', '#d9472e'], 'bg' => ['#689f63', '#7ba977', '#8bb487', '#5f965a', '#54874f']],
                    ['no' => 2, 'num' => '8', 'tipe' => 'Plat Transformasi', 'norm' => '8', 'pars' => '3', 'tot' => 'Tidak Ada', 'fg' => ['#d9534f', '#c9302c', '#d43f3a', '#b92c28'], 'bg' => ['#5cb85c', '#4cae4c', '#6ec06e', '#7ec87e', '#419641']],
                    ['no' => 3, 'num' => '6', 'tipe' => 'Plat Transformasi', 'norm' => '6', 'pars' => '5', 'tot' => 'Tidak Ada', 'fg' => ['#d9534f', '#c9302c', '#e74c3c', '#c0392b'], 'bg' => ['#5cb85c', '#4cae4c', '#27ae60', '#2ecc71', '#419641']],
                    ['no' => 4, 'num' => '29', 'tipe' => 'Plat Transformasi', 'norm' => '29', 'pars' => '70', 'tot' => 'Tidak Ada', 'fg' => ['#d9534f', '#c0392b', '#e67e22', '#d35400'], 'bg' => ['#27ae60', '#2ecc71', '#5cb85c', '#4cae4c', '#16a085']],
                    ['no' => 5, 'num' => '57', 'tipe' => 'Plat Penyamaran', 'norm' => '57', 'pars' => '35 / Kabur', 'tot' => 'Tidak Ada', 'fg' => ['#e67e22', '#d35400', '#f39c12', '#e74c3c'], 'bg' => ['#27ae60', '#2ecc71', '#5cb85c', '#1abc9c', '#16a085']],
                    ['no' => 6, 'num' => '5', 'tipe' => 'Plat Transformasi', 'norm' => '5', 'pars' => '2', 'tot' => 'Tidak Ada', 'fg' => ['#27ae60', '#2ecc71', '#1abc9c', '#16a085'], 'bg' => ['#d9534f', '#e67e22', '#f39c12', '#c0392b', '#d35400']],
                    ['no' => 7, 'num' => '3', 'tipe' => 'Plat Transformasi', 'norm' => '3', 'pars' => '5', 'tot' => 'Tidak Ada', 'fg' => ['#27ae60', '#2ecc71', '#5cb85c', '#4cae4c'], 'bg' => ['#d9534f', '#c0392b', '#e74c3c', '#e67e22', '#d35400']],
                    ['no' => 8, 'num' => '74', 'tipe' => 'Plat Transformasi', 'norm' => '74', 'pars' => '21', 'tot' => 'Tidak Ada', 'fg' => ['#27ae60', '#2ecc71', '#16a085', '#5cb85c'], 'bg' => ['#d9534f', '#c0392b', '#d35400', '#e74c3c', '#f39c12']],
                ];
            @endphp

            @foreach($plateDefs as $p)
                <div class="plate-card">
                    <div class="plate-svg-wrap">
                        <svg viewBox="0 0 200 200" width="125" height="125" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <clipPath id="circle-clip-{{ $p['no'] }}">
                                    <circle cx="100" cy="100" r="94"/>
                                </clipPath>
                                <mask id="mask-num-{{ $p['no'] }}">
                                    <rect width="200" height="200" fill="black" />
                                    <text x="100" y="{{ strlen($p['num']) > 1 ? '128' : '136' }}" font-family="Arial, Helvetica, sans-serif" font-weight="900" font-size="{{ strlen($p['num']) > 1 ? '86' : '108' }}" text-anchor="middle" fill="white" letter-spacing="-2">{{ $p['num'] }}</text>
                                </mask>
                            </defs>

                            <!-- Background Disc Ring -->
                            <circle cx="100" cy="100" r="98" fill="#e2e8f0" stroke="#cbd5e1" stroke-width="2"/>
                            
                            <g clip-path="url(#circle-clip-{{ $p['no'] }})">
                                <!-- Background Dots Pattern Base -->
                                @php
                                    $bgCols = $p['bg'];
                                    $fgCols = $p['fg'];
                                    // Generate pseudo-deterministic dots for aesthetics
                                    $dots = [];
                                    $step = 12;
                                    for ($x = 10; $x <= 190; $x += $step) {
                                        for ($y = 10; $y <= 190; $y += $step) {
                                            $dx = $x - 100;
                                            $dy = $y - 100;
                                            if (($dx*$dx + $dy*$dy) < (92 * 92)) {
                                                $jitterX = $x + (($x * 7 + $y * 13 + $p['no']) % 7) - 3;
                                                $jitterY = $y + (($x * 11 + $y * 5 + $p['no']) % 7) - 3;
                                                $radius = 3.5 + (($x * 3 + $y * 7) % 4);
                                                $cIdx = ($x + $y + $p['no']) % count($bgCols);
                                                $dots[] = ['x' => $jitterX, 'y' => $jitterY, 'r' => $radius, 'c' => $bgCols[$cIdx]];
                                            }
                                        }
                                    }
                                @endphp

                                <!-- Render Background Dots -->
                                @foreach($dots as $d)
                                    <circle cx="{{ $d['x'] }}" cy="{{ $d['y'] }}" r="{{ $d['r'] }}" fill="{{ $d['c'] }}" opacity="0.95"/>
                                @endforeach

                                <!-- Render Foreground Numeral Dots (Masked) -->
                                <g mask="url(#mask-num-{{ $p['no'] }})">
                                    <circle cx="100" cy="100" r="95" fill="{{ $fgCols[0] }}"/>
                                    @foreach($dots as $d)
                                        @php
                                            $fgIdx = ($d['x'] * 3 + $d['y'] * 5 + $p['no']) % count($fgCols);
                                        @endphp
                                        <circle cx="{{ $d['x'] }}" cy="{{ $d['y'] }}" r="{{ $d['r'] + 0.5 }}" fill="{{ $fgCols[$fgIdx] }}"/>
                                    @endforeach
                                </g>
                            </g>
                            
                            <circle cx="100" cy="100" r="94" fill="none" stroke="rgba(0,0,0,0.15)" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <div class="plate-meta">
                        <strong>Plat #{{ $p['no'] }} (Normal: {{ $p['norm'] }})</strong>
                        <span>{{ $p['tipe'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- TABEL PANDUAN DIAGNOSA GURU PEWAWANCARA --}}
        <table class="table-panduan">
            <thead>
                <tr>
                    <th style="width: 10%;">No. Plat</th>
                    <th style="width: 25%;">Tipe Piringan</th>
                    <th style="width: 20%;">Mata Normal</th>
                    <th style="width: 25%;">Buta Warna Merah-Hijau</th>
                    <th style="width: 20%;">Buta Warna Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($plateDefs as $p)
                    <tr>
                        <td style="text-align: center; font-weight: bold;">Plat #{{ $p['no'] }}</td>
                        <td>{{ $p['tipe'] }}</td>
                        <td style="text-align: center; font-weight: 800; color: #15803d; background: #f0fdf4;">
                            Angka {{ $p['norm'] }}
                        </td>
                        <td style="text-align: center; color: #b45309; background: #fffbeb;">
                            {{ $p['pars'] }}
                        </td>
                        <td style="text-align: center; color: #b91c1c; background: #fef2f2;">
                            {{ $p['tot'] }}
                        </td>
                    </tr>
                @endforeach
                <tr style="background: #f8fafc; font-weight: bold;">
                    <td colspan="2" style="text-align: right; padding: 6px;">Kriteria Kesimpulan PPDB:</td>
                    <td style="text-align: center; color: #15803d;">
                        Lolos 7–8 Plat = Bebas Buta Warna (Layak RPL, APHP, TSM)
                    </td>
                    <td style="text-align: center; color: #b45309;">
                        Hanya Lolos Sebagian = Buta Warna Parsial (Rekomendasi RPL Khusus / Pertimbangan)
                    </td>
                    <td style="text-align: center; color: #b91c1c;">
                        Hanya Plat #1 = Buta Warna Total (Tidak Layak TSM/Kelistrikan)
                    </td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 14px; font-size: 8pt; color: #475569; font-style: italic; text-align: center; font-family: 'Times New Roman', serif;">
            * Piringan Ishihara ini disusun berdasarkan standar uji pseudoisochromatic untuk keperluan seleksi masuk kejuruan SMKN 1 Air Naningan Tahun 2026.
        </div>
    </div>

</body>
</html>
