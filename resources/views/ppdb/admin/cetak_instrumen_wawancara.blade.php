<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INSTRUMEN &amp; RUBRIK WAWANCARA PPDB — {{ $pendaftar ? $pendaftar->nama_lengkap : 'SMKN 1 AIR NANINGAN' }}</title>
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
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 20px 0;
            background: #e2e8f0;
            color: #000000;
            line-height: 1.25;
            font-size: 11.5pt;
        }

        .page-a4 {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 10mm 14mm;
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

        .kop-table {
            width: 100%;
            border-bottom: 3px double #000000;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }

        .kop-logo {
            width: 70px;
            text-align: center;
            vertical-align: middle;
        }

        .kop-logo img {
            width: 65px;
            height: auto;
        }

        .kop-text {
            text-align: center;
            vertical-align: middle;
        }

        .kop-prov {
            font-size: 11pt;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin: 0;
            text-transform: uppercase;
        }

        .kop-dinas {
            font-size: 12pt;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
        }

        .kop-sekolah {
            font-size: 14pt;
            font-weight: 900;
            letter-spacing: 0.5px;
            margin: 1px 0;
            text-transform: uppercase;
        }

        .kop-alamat {
            font-size: 8.5pt;
            font-style: italic;
            margin: 0;
            color: #1e293b;
        }

        .doc-title {
            text-align: center;
            margin-bottom: 10px;
        }

        .doc-title h2 {
            font-size: 12.5pt;
            font-weight: 800;
            margin: 0 0 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: underline;
        }

        .doc-title p {
            font-size: 9.5pt;
            margin: 0;
            font-weight: 700;
        }

        .table-identitas {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 10px;
        }

        .table-identitas td {
            padding: 2.5px 4px;
            vertical-align: top;
        }

        .table-rubrik {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 10px;
        }

        .table-rubrik th,
        .table-rubrik td {
            border: 1px solid #000000;
            padding: 4.5px 6px;
            vertical-align: top;
        }

        .table-rubrik th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: 800;
            text-transform: uppercase;
        }

        .score-box {
            font-family: monospace;
            font-weight: 800;
            font-size: 11pt;
            text-align: center;
        }

        .checklist-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 10px;
        }

        .checklist-table th,
        .checklist-table td {
            border: 1px solid #000000;
            padding: 4px 6px;
        }

        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-top: 14px;
        }

        .ttd-table td {
            vertical-align: top;
            text-align: center;
            width: 50%;
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
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('admin.ppdb.seleksi', ['tab' => 'wawancara']) }}" class="btn-back">
                &larr; Kembali ke Tab Wawancara
            </a>
            <a href="{{ route('admin.ppdb.seleksi.tes_buta_warna') }}" target="_blank" class="btn-back" style="color: #2563eb; border-color: #bfdbfe; background: #eff6ff;">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align:middle; margin-right:4px;">
                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                </svg>
                Buka Piringan Ishihara (Uji Buta Warna)
            </a>
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="button" onclick="window.print()" class="btn-print">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align:middle;">
                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                    <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
                </svg>
                Cetak Lembar Instrumen (A4)
            </button>
        </div>
    </div>

    <div class="page-a4">
        {{-- KOP RESMI DINAS PEMERINTAH PROVINSI LAMPUNG & SEKOLAH --}}
        @include('partials.kop_surat')

        {{-- JUDUL DOKUMEN --}}
        <div class="doc-title">
            <h2>Instrumen &amp; Rubrik Wawancara Minat Kejuruan PPDB 2026</h2>
            <p>Standar Penilaian Terstruktur Calon Siswa Baru SMK Negeri 1 Air Naningan</p>
        </div>

        {{-- IDENTITAS CALON SISWA --}}
        <table class="table-identitas">
            <tr>
                <td style="width: 17%; font-weight: bold;">No. Pendaftaran</td>
                <td style="width: 33%;">: <strong>{{ $pendaftar->no_pendaftaran ?? '____________________' }}</strong></td>
                <td style="width: 18%; font-weight: bold;">Pilihan Jurusan 1</td>
                <td style="width: 32%;">: <strong>{{ $pendaftar->jurusan1->nama_jurusan ?? ($pendaftar->jurusanPilihan1->nama_jurusan ?? 'Semua Peminatan') }}</strong></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Nama Lengkap</td>
                <td>: <strong>{{ $pendaftar->nama_lengkap ?? '__________________________________' }}</strong></td>
                <td style="font-weight: bold;">Pilihan Jurusan 2</td>
                <td>: {{ $pendaftar->jurusan2->nama_jurusan ?? ($pendaftar->jurusanPilihan2->nama_jurusan ?? '-') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Asal SMP / MTs</td>
                <td>: {{ $pendaftar->asal_sekolah ?? '__________________________________' }}</td>
                <td style="font-weight: bold;">Waktu / Ruang</td>
                <td>: {{ $pendaftar ? $pendaftar->jadwal_sesi_resmi : '08.00 - 10.00 WIB' }} / {{ $pendaftar ? $pendaftar->jadwal_ruang_resmi : 'Lab SMKN 1 AN' }}</td>
            </tr>
        </table>

        @php
            $mw = $setting ? $setting->materi_wawancara_aktif : \App\Models\PpdbUjianSetting::getDefaultMateriWawancara();
            $kodeJurusan = strtoupper($pendaftar->jurusan1->kode_jurusan ?? '');
        @endphp

        {{-- TABEL RUBRIK 4 KRITERIA --}}
        <table class="table-rubrik">
            <thead>
                <tr>
                    <th style="width: 26%;">Kriteria &amp; Bobot</th>
                    <th style="width: 36%;">Panduan Pertanyaan Pemandu (Pewawancara)</th>
                    <th style="width: 26%;">Pedoman Rubrik Penilaian</th>
                    <th style="width: 12%;">Skor (0-100)</th>
                </tr>
            </thead>
            <tbody>
                {{-- 1. MOTIVASI --}}
                <tr>
                    <td>
                        <strong>1. {{ $mw['motivasi']['judul'] ?? 'Motivasi & Minat Belajar' }}</strong><br>
                        <span style="font-size: 7.5pt; color: #334155;">Bobot: <strong>{{ $mw['motivasi']['bobot'] ?? 25 }}%</strong></span>
                        <div style="font-size: 7.5pt; color: #475569; margin-top: 3px;">
                            {{ $mw['motivasi']['tujuan'] ?? 'Menilai kemauan murni belajar dan target karir.' }}
                        </div>
                    </td>
                    <td>
                        @foreach((array)($mw['motivasi']['pertanyaan'] ?? []) as $q)
                            &bull; <em>"{{ $q }}"</em><br>
                        @endforeach
                    </td>
                    <td style="font-size: 7.5pt;">
                        @foreach((array)($mw['motivasi']['rubrik'] ?? []) as $k => $v)
                            <strong>{{ $k }}</strong>: {{ $v }}<br>
                        @endforeach
                    </td>
                    <td class="score-box">
                        {{ $pendaftar && $pendaftar->nilai_wawancara_motivasi !== null ? number_format($pendaftar->nilai_wawancara_motivasi, 0) : '' }}
                    </td>
                </tr>

                {{-- 2. KARAKTER & DISIPLIN --}}
                <tr>
                    <td>
                        <strong>2. {{ $mw['karakter']['judul'] ?? 'Karakter, Sikap & Disiplin' }}</strong><br>
                        <span style="font-size: 7.5pt; color: #334155;">Bobot: <strong>{{ $mw['karakter']['bobot'] ?? 25 }}%</strong></span>
                        <div style="font-size: 7.5pt; color: #475569; margin-top: 3px;">
                            {{ $mw['karakter']['tujuan'] ?? 'Menilai etika, disiplin jam 07.00 WIB, dan tata tertib.' }}
                        </div>
                    </td>
                    <td>
                        @foreach((array)($mw['karakter']['pertanyaan'] ?? []) as $q)
                            &bull; <em>"{{ $q }}"</em><br>
                        @endforeach
                    </td>
                    <td style="font-size: 7.5pt;">
                        @foreach((array)($mw['karakter']['rubrik'] ?? []) as $k => $v)
                            <strong>{{ $k }}</strong>: {{ $v }}<br>
                        @endforeach
                    </td>
                    <td class="score-box">
                        {{ $pendaftar && $pendaftar->nilai_wawancara_karakter !== null ? number_format($pendaftar->nilai_wawancara_karakter, 0) : '' }}
                    </td>
                </tr>

                {{-- 3. KESIAPAN KEJURUAN & UJI KHUSUS --}}
                <tr>
                    <td>
                        <strong>3. Kesiapan Kejuruan &amp; Fisik</strong><br>
                        <span style="font-size: 7.5pt; color: #334155;">Bobot: <strong>30%</strong></span>
                        <div style="font-size: 7.5pt; color: #475569; margin-top: 3px;">
                            Diselaraskan dengan Jurusan Pilihan 1:
                            <strong>{{ $pendaftar->jurusan1->kode_jurusan ?? 'RPL / APHP / TSM' }}</strong>.
                        </div>
                    </td>
                    <td>
                        @if(str_contains($kodeJurusan, 'RPL'))
                            <strong>[RPL]</strong>:<br>
                            @foreach((array)($mw['kejuruan_rpl']['pertanyaan'] ?? []) as $q)
                                &bull; <em>"{{ $q }}"</em><br>
                            @endforeach
                            <div style="font-size: 7.5pt; color: #1e40af; margin-top:2px;">
                                <strong>Uji Fisik:</strong> {{ $mw['kejuruan_rpl']['uji_fisik'] ?? 'Cek Ishihara Bebas Buta Warna' }}
                            </div>
                        @elseif(str_contains($kodeJurusan, 'APHP'))
                            <strong>[APHP]</strong>:<br>
                            @foreach((array)($mw['kejuruan_aphp']['pertanyaan'] ?? []) as $q)
                                &bull; <em>"{{ $q }}"</em><br>
                            @endforeach
                            <div style="font-size: 7.5pt; color: #065f46; margin-top:2px;">
                                <strong>Uji Fisik:</strong> {{ $mw['kejuruan_aphp']['uji_fisik'] ?? 'Kebersihan kuku, alergi pangan, bebas buta warna' }}
                            </div>
                        @elseif(str_contains($kodeJurusan, 'TSM'))
                            <strong>[TSM]</strong>:<br>
                            @foreach((array)($mw['kejuruan_tsm']['pertanyaan'] ?? []) as $q)
                                &bull; <em>"{{ $q }}"</em><br>
                            @endforeach
                            <div style="font-size: 7.5pt; color: #b45309; margin-top:2px;">
                                <strong>Uji Fisik:</strong> {{ $mw['kejuruan_tsm']['uji_fisik'] ?? 'Cek Buta Warna MUTLAK kabel kelistrikan motor' }}
                            </div>
                        @else
                            &bull; <strong>RPL:</strong> {{ implode(' ', (array)($mw['kejuruan_rpl']['pertanyaan'] ?? [])) }}<br>
                            &bull; <strong>APHP:</strong> {{ implode(' ', (array)($mw['kejuruan_aphp']['pertanyaan'] ?? [])) }}<br>
                            &bull; <strong>TSM:</strong> {{ implode(' ', (array)($mw['kejuruan_tsm']['pertanyaan'] ?? [])) }}
                        @endif
                    </td>
                    <td style="font-size: 7.5pt;">
                        @if(str_contains($kodeJurusan, 'RPL'))
                            @foreach((array)($mw['kejuruan_rpl']['rubrik'] ?? []) as $k => $v)
                                <strong>{{ $k }}</strong>: {{ $v }}<br>
                            @endforeach
                        @elseif(str_contains($kodeJurusan, 'APHP'))
                            @foreach((array)($mw['kejuruan_aphp']['rubrik'] ?? []) as $k => $v)
                                <strong>{{ $k }}</strong>: {{ $v }}<br>
                            @endforeach
                        @elseif(str_contains($kodeJurusan, 'TSM'))
                            @foreach((array)($mw['kejuruan_tsm']['rubrik'] ?? []) as $k => $v)
                                <strong>{{ $k }}</strong>: {{ $v }}<br>
                            @endforeach
                        @else
                            <strong>85-100</strong>: Bebas buta warna, antusias tinggi kejuruan.<br>
                            <strong>70-84</strong>: Bebas buta warna, siap belajar.<br>
                            <strong>&lt;70</strong>: Buta warna total, ragu-ragu.
                        @endif
                    </td>
                    <td class="score-box">
                        {{ $pendaftar && $pendaftar->nilai_wawancara_kejuruan !== null ? number_format($pendaftar->nilai_wawancara_kejuruan, 0) : '' }}
                    </td>
                </tr>

                {{-- 4. DUKUNGAN ORANG TUA --}}
                <tr>
                    <td>
                        <strong>4. {{ $mw['ortu']['judul'] ?? 'Dukungan & Komitmen Orang Tua' }}</strong><br>
                        <span style="font-size: 7.5pt; color: #334155;">Bobot: <strong>{{ $mw['ortu']['bobot'] ?? 20 }}%</strong></span>
                        <div style="font-size: 7.5pt; color: #475569; margin-top: 3px;">
                            {{ $mw['ortu']['tujuan'] ?? 'Menilai komitmen keluarga dalam mendukung pembelajaran dan magang PKL industri.' }}
                        </div>
                    </td>
                    <td>
                        @foreach((array)($mw['ortu']['pertanyaan'] ?? []) as $q)
                            &bull; <em>"{{ $q }}"</em><br>
                        @endforeach
                    </td>
                    <td style="font-size: 7.5pt;">
                        @foreach((array)($mw['ortu']['rubrik'] ?? []) as $k => $v)
                            <strong>{{ $k }}</strong>: {{ $v }}<br>
                        @endforeach
                    </td>
                    <td class="score-box">
                        {{ $pendaftar && $pendaftar->nilai_wawancara_ortu !== null ? number_format($pendaftar->nilai_wawancara_ortu, 0) : '' }}
                    </td>
                </tr>

                {{-- TOTAL TERBOBOT --}}
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td colspan="3" style="text-align: right; font-size: 9pt; padding: 6px;">
                        TOTAL SKOR WAWANCARA TERBOBOT (25% M + 25% K + 30% J + 20% O):
                    </td>
                    <td class="score-box" style="font-size: 13pt; color: #1e40af; background: #eff6ff;">
                        {{ $pendaftar && $pendaftar->nilai_wawancara_total !== null ? number_format($pendaftar->nilai_wawancara_total, 1) : '' }}
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- PEMERIKSAAN FISIK & KESEHATAN AWAL --}}
        <table class="checklist-table">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th colspan="4" style="text-align: left; font-size: 8.5pt;">Pemeriksaan Fisik &amp; Kesiapan Kejuruan:</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 25%;">
                        [ &nbsp; ] Bebas Buta Warna (Ishihara)
                    </td>
                    <td style="width: 25%;">
                        [ &nbsp; ] Tidak Bertato
                    </td>
                    <td style="width: 25%;">
                        [ &nbsp; ] Tidak Bertindik (Putra)
                    </td>
                    <td style="width: 25%;">
                        [ &nbsp; ] Bebas Riwayat Penyakit Berat
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="font-size: 8pt; color: #1e293b;">
                        <strong>Catatan Khusus Penguji / Observasi:</strong><br>
                        <div style="min-height: 28px; margin-top: 2px;">
                            {{ $pendaftar->catatan_wawancara ?? '(Tuliskan catatan khusus terkait fisik, potensi minat, komitmen siswa/wali bila ada)' }}
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- TANDA TANGAN PEWAWANCARA & KETUA PANITIA --}}
        <table class="ttd-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    <strong>Ketua Panitia PPDB 2026</strong>
                    <div style="height: 48px;"></div>
                    <strong><u>Hendra Pratama, S.Pd.</u></strong><br>
                    NIP. 19850412 201101 1 008
                </td>
                <td>
                    Air Naningan, {{ $pendaftar && $pendaftar->diwawancara_pada ? $pendaftar->diwawancara_pada->translatedFormat('d F Y') : \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    <strong>Guru Pewawancara / Penguji</strong>
                    <div style="height: 48px;"></div>
                    <strong><u>{{ $pendaftar->pewawancara->name ?? '( ____________________________ )' }}</u></strong><br>
                    NIP. {{ $pendaftar->pewawancara->nip ?? '..................................................' }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
