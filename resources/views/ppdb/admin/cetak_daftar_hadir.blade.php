<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DAFTAR HADIR &amp; BERITA ACARA UJIAN PPDB — {{ $tanggal }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 12mm 15mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 20px 0;
            background: #e2e8f0;
            color: #000000;
            line-height: 1.3;
        }

        .page-a4 {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 12mm 15mm;
            box-shadow: 0 4px 18px rgba(0,0,0,0.15);
            position: relative;
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
            gap: 8px;
            padding: 9px 24px;
            background: #0284c7;
            color: #ffffff;
            border: 1px solid #0369a1;
            border-radius: 6px;
            font-weight: 700;
            font-size: 13.5px;
            cursor: pointer;
        }

        .judul-area {
            text-align: center;
            margin-bottom: 12px;
        }

        .judul-teks {
            font-size: 12pt;
            font-weight: 900;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .sub-judul-teks {
            font-size: 9.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 2px 0 0 0;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 12px;
        }

        .meta-table td {
            padding: 3px 6px;
            vertical-align: top;
        }

        .table-data {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 14px;
        }

        .table-data th, .table-data td {
            border: 1px solid #000000;
            padding: 4px 6px;
            vertical-align: middle;
        }

        .table-data th {
            background: #f1f5f9;
            font-weight: 900;
            text-transform: uppercase;
            text-align: center;
            font-size: 8pt;
        }

        .ttd-box {
            font-size: 7.5pt;
            height: 24px;
            vertical-align: middle !important;
        }

        .berita-acara-card {
            border: 1px solid #000000;
            padding: 8px 12px;
            font-size: 8pt;
            margin-bottom: 14px;
            background: #fafafa;
        }

        .ttd-pengawas-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-top: 10px;
        }

        .ttd-pengawas-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 16px;
        }

        .ttd-space {
            height: 48px;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
                margin: 0;
            }
            .screen-toolbar {
                display: none !important;
            }
            .page-a4 {
                width: 100%;
                min-height: auto;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

    <div class="screen-toolbar">
        <a href="{{ route('admin.ppdb.presensi.kios', ['tanggal' => $tanggal, 'sesi' => $sesiFilter, 'ruang' => $ruangFilter]) }}" class="btn-back">
            &larr; Kembali ke Kios Presensi
        </a>
        <div style="display:flex; gap:10px; align-items:center;">
            <span style="font-size: 12px; color: #64748b;">Format: <strong>Kertas A4 Resmi</strong></span>
            <button onclick="window.print()" class="btn-print">
                🖨️ Cetak Berita Acara &amp; Daftar Hadir
            </button>
        </div>
    </div>

    <div class="page-a4">
        
        {{-- KOP SURAT RESMI PEMERINTAH PROVINSI & SMKN 1 AIR NANINGAN --}}
        @include('partials.kop_surat')

        {{-- JUDUL DOKUMEN --}}
        <div class="judul-area">
            <h2 class="judul-teks">DAFTAR HADIR &amp; BERITA ACARA PELAKSANAAN UJIAN SELEKSI</h2>
            <div class="sub-judul-teks">PENERIMAAN PESERTA DIDIK BARU (PPDB) TAHUN PELAJARAN 2026/2027</div>
        </div>

        {{-- METADATA PELAKSANAAN --}}
        <table class="meta-table">
            <tr>
                <td style="width: 110px;">Hari / Tanggal</td>
                <td style="width: 10px;">:</td>
                <td style="width: 40%; font-weight: bold;">
                    {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
                </td>
                <td style="width: 130px;">Ruang Ujian</td>
                <td style="width: 10px;">:</td>
                <td style="font-weight: bold;">{{ $ruangFilter ?: 'Semua Ruang Lab Komputer' }}</td>
            </tr>
            <tr>
                <td>Sesi Ujian</td>
                <td>:</td>
                <td style="font-weight: bold;">{{ $sesiFilter ?: 'Semua Sesi Ujian' }}</td>
                <td>Rekap Kehadiran</td>
                <td>:</td>
                <td>
                    Terjadwal: <strong>{{ $totalTerjadwal }}</strong> &bull; 
                    Hadir: <strong>{{ $totalHadir }}</strong> &bull; 
                    Tidak Hadir: <strong>{{ $totalTidakHadir }}</strong>
                </td>
            </tr>
        </table>

        {{-- TABEL DAFTAR HADIR PESERTA --}}
        <table class="table-data">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 95px;">No. Pendaftaran</th>
                    <th style="width: 70px;">NISN</th>
                    <th>Nama Calon Siswa</th>
                    <th style="width: 120px;">Asal Sekolah</th>
                    <th style="width: 60px;">Keahlian</th>
                    <th style="width: 65px;">Scan Hadir</th>
                    <th style="width: 90px;">Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pesertas as $index => $p)
                    @php
                        $hadir = $p->absensiUjian !== null;
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="font-family: monospace; font-weight: bold;">{{ $p->no_pendaftaran }}</td>
                        <td style="font-family: monospace; text-align: center;">{{ $p->nisn }}</td>
                        <td style="font-weight: bold;">{{ strtoupper($p->nama_lengkap) }}</td>
                        <td>{{ $p->asal_sekolah }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $p->jurusanPilihan1->kode_jurusan ?? '-' }}</td>
                        <td style="text-align: center; font-size: 7.5pt;">
                            @if($hadir)
                                <strong>{{ $p->absensiUjian->waktu_hadir->format('H:i') }}</strong>
                            @else
                                <span style="color: #666;">-</span>
                            @endif
                        </td>
                        <td class="ttd-box">
                            @if($index % 2 == 0)
                                <span style="font-size: 7pt; color: #444;">{{ $index + 1 }}. ............</span>
                            @else
                                <span style="font-size: 7pt; color: #444; margin-left: 28px;">{{ $index + 1 }}. ............</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 18px; color: #666;">
                            Tidak ada peserta terjadwal untuk filter tanggal/sesi/ruang ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- BERITA ACARA PELAKSANAAN UJIAN --}}
        <div class="berita-acara-card">
            <strong style="display: block; font-size: 8.5pt; margin-bottom: 3px; text-transform: uppercase;">
                Berita Acara Pelaksanaan Ujian Seleksi:
            </strong>
            <div>
                Pada hari ini, <strong>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</strong>, telah diselenggarakan Ujian Seleksi Masuk CBT PPDB TP. 2026/2027 di Ruang <strong>{{ $ruangFilter ?: 'Lab Komputer' }}</strong>.
                Dari jumlah calon peserta sebanyak <strong>{{ $totalTerjadwal }} orang</strong>, telah hadir dan memverifikasi barcode sejumlah <strong>{{ $totalHadir }} orang</strong>, serta tidak hadir sejumlah <strong>{{ $totalTidakHadir }} orang</strong>.
                Ujian terlaksana dengan tertib, lancar, dan berintegritas.
            </div>
            <div style="margin-top: 4px; font-style: italic; color: #333;">
                Catatan khusus pengawas: .....................................................................................................................................................................
            </div>
        </div>

        {{-- PENGESAHAN PENGAWAS RUANG & KETUA PANITIA --}}
        <table class="ttd-pengawas-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    Ketua Panitia PPDB 2026,
                    <div class="ttd-space"></div>
                    <strong style="text-decoration: underline; display: block;">( ............................................................ )</strong>
                    <span>NIP. .....................................................</span>
                </td>
                <td>
                    Air Naningan, {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}<br>
                    Pengawas Ruang Ujian,
                    <div class="ttd-space"></div>
                    <strong style="text-decoration: underline; display: block;">( {{ auth()->user()->name ?? 'Pengawas Ruang' }} )</strong>
                    <span>NIP/ID: {{ auth()->user()->guru?->nip ?? '.....................................................' }}</span>
                </td>
            </tr>
        </table>

    </div>

</body>
</html>
