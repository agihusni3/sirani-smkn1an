<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KARTU BUKTI PENDAFTARAN & JADWAL SELEKSI — {{ $pendaftar->no_pendaftaran ?? $pendaftar->nomor_pendaftaran }}</title>
    <style>
        /* Standar Halaman A4: 210mm x 297mm */
        @page {
            size: A4 portrait;
            margin: 0;
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
            line-height: 1.35;
        }

        /* Lembar Kertas A4 */
        .page-a4 {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 14mm 16mm;
            box-shadow: 0 4px 18px rgba(0,0,0,0.15);
            position: relative;
        }

        /* Toolbar Layar (Tidak Tercetak) */
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
            transition: all 0.15s ease;
        }

        .btn-back:hover {
            background: #f8fafc;
            border-color: #94a3b8;
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
            box-shadow: 0 2px 6px rgba(2,132,199,0.3);
            transition: all 0.15s ease;
        }

        .btn-print:hover {
            background: #0369a1;
        }

        /* ─── KOP SURAT RESMI ─── */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .kop-table td {
            vertical-align: middle;
            padding: 0;
        }

        .kop-logo-col {
            width: 75px;
            text-align: center;
        }

        .kop-logo {
            max-width: 68px;
            max-height: 80px;
            width: auto;
            height: auto;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        .kop-text-col {
            text-align: center;
            padding: 0 10px !important;
        }

        .kop-text-1 {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            line-height: 1.25;
        }

        .kop-text-2 {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            line-height: 1.25;
        }

        .kop-nama-sekolah {
            font-size: 15.5pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 2px 0;
            line-height: 1.2;
        }

        .kop-alamat {
            font-size: 8.5pt;
            margin: 2px 0 0 0;
            line-height: 1.35;
        }

        .kop-kontak {
            font-size: 8pt;
            margin: 1px 0 0 0;
            letter-spacing: 0.2px;
        }

        /* Garis Ganda Pemisah Kop Surat */
        .kop-double-line {
            border-top: 2.5px solid #000000;
            border-bottom: 0.75px solid #000000;
            height: 2px;
            margin: 7px 0 14px 0;
        }

        /* ─── JUDUL KARTU ─── */
        .judul-area {
            text-align: center;
            margin-bottom: 12px;
        }

        .judul-teks {
            font-size: 12.5pt;
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
            letter-spacing: 0.3px;
        }

        /* ─── STATUS & NOMOR REGISTRASI BAR ─── */
        .status-bar {
            width: 100%;
            border: 1.5px solid #000000;
            background: #f8fafc;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 9pt;
        }

        .status-bar td {
            padding: 6px 10px;
            vertical-align: middle;
        }

        .reg-number {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12.5pt;
            font-weight: 900;
            color: #000000;
            letter-spacing: 0.5px;
        }

        .status-pill {
            display: inline-block;
            padding: 2px 10px;
            border: 1px solid #000000;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5pt;
            background: #ffffff;
        }

        /* ─── PANEL JADWAL SELEKSI CBT & WAWANCARA ─── */
        .jadwal-panel {
            width: 100%;
            border: 1.5px solid #000000;
            margin-bottom: 12px;
            border-collapse: collapse;
            font-size: 8.5pt;
            background: #fbfcfd;
        }

        .jadwal-panel-head {
            background: #f1f5f9;
            border-bottom: 1px solid #000000;
            font-weight: 900;
            font-size: 9pt;
            padding: 5px 10px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .jadwal-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }

        .jadwal-table td {
            padding: 4px 8px;
            vertical-align: top;
        }

        .jadwal-table td.label-col {
            width: 110px;
            color: #333333;
        }

        .jadwal-table td.val-col {
            font-weight: bold;
            color: #000000;
        }

        /* ─── BIODATA & FOTO AREA ─── */
        .biodata-wrapper {
            display: flex;
            gap: 16px;
            margin-bottom: 12px;
        }

        .side-col {
            width: 130px;
            flex-shrink: 0;
            text-align: center;
        }

        .pas-foto-box {
            width: 30mm;
            height: 40mm;
            border: 1.5px solid #000000;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            overflow: hidden;
            position: relative;
        }

        .pas-foto-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .qr-verifikasi-box {
            margin-top: 10px;
            padding: 6px;
            border: 1px dashed #64748b;
            background: #ffffff;
            display: inline-block;
            text-align: center;
        }

        .qr-verifikasi-box img {
            width: 78px;
            height: 78px;
            display: block;
            margin: 0 auto;
        }

        .qr-caption {
            font-size: 6.5pt;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #475569;
            margin-top: 3px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Tabel Biodata Siswa */
        .table-biodata {
            flex: 1;
            border-collapse: collapse;
            font-size: 9pt;
            width: 100%;
        }

        .table-biodata td {
            padding: 3.5px 6px;
            vertical-align: top;
            line-height: 1.3;
        }

        .table-biodata td.lbl {
            width: 160px;
            color: #000000;
        }

        .table-biodata td.sep {
            width: 10px;
            text-align: center;
        }

        .table-biodata td.val {
            font-weight: normal;
        }

        .table-biodata td.val strong {
            font-weight: bold;
        }

        /* ─── TATA TERTIB & CATATAN ─── */
        .catatan-box {
            border: 1px solid #000000;
            background: #fafafa;
            padding: 6px 12px;
            margin-bottom: 14px;
            font-size: 8pt;
            line-height: 1.35;
        }

        .catatan-box strong {
            display: block;
            font-size: 8.5pt;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .catatan-box ol {
            margin: 0;
            padding-left: 16px;
        }

        .catatan-box li {
            margin-bottom: 1.5px;
        }

        /* ─── TANDA TANGAN (PENGESAHAN) ─── */
        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-top: 4px;
        }

        .ttd-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 20px;
        }

        .ttd-space {
            height: 52px;
        }

        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
            display: block;
        }

        /* PRINT STYLES */
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
                padding: 10mm 14mm !important;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

    {{-- TOOLBAR ATAS (HANYA MUNCUL DI LAYAR BROWSER) --}}
    <div class="screen-toolbar">
        <a href="{{ route('ppdb.status', ['keyword' => $pendaftar->no_pendaftaran ?? $pendaftar->nomor_pendaftaran]) }}" class="btn-back">
            &larr; Kembali ke Status Pendaftaran
        </a>
        <div style="display:flex; gap:10px; align-items:center;">
            <span style="font-size: 12px; color: #64748b;">Ukuran: <strong>Kertas A4</strong> (Pas 1 Lembar)</span>
            <button onclick="window.print()" class="btn-print">
                <span style="font-size:16px;">🖨️</span>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    {{-- LEMBAR UTAMA KARTU A4 --}}
    <div class="page-a4">

        {{-- 1. KOP SURAT RESMI PEMERINTAH PROVINSI LAMPUNG & SEKOLAH --}}
        @include('partials.kop_surat')

        {{-- 2. JUDUL DOKUMEN --}}
        <div class="judul-area">
            @if($pendaftar->status == 'diterima')
                <h2 class="judul-teks">SURAT BUKTI KELULUSAN &amp; PENERIMAAN SISWA BARU</h2>
                <div class="sub-judul-teks">PENERIMAAN PESERTA DIDIK BARU (PPDB) TAHUN PELAJARAN {{ $pendaftar->tahun_ajaran ?? '2026/2027' }}</div>
            @else
                <h2 class="judul-teks">KARTU TANDA BUKTI PENDAFTARAN &amp; JADWAL SELEKSI</h2>
                <div class="sub-judul-teks">PENERIMAAN PESERTA DIDIK BARU (PPDB) TAHUN PELAJARAN {{ $pendaftar->tahun_ajaran ?? '2026/2027' }}</div>
            @endif
        </div>

        {{-- 3. STATUS BAR & NOMOR PENDAFTARAN --}}
        <table class="status-bar">
            <tr>
                <td style="width: 48%;">
                    <span style="font-size: 8pt; color: #475569; display: block; text-transform: uppercase;">Nomor Pendaftaran:</span>
                    <span class="reg-number">{{ $pendaftar->no_pendaftaran ?? $pendaftar->nomor_pendaftaran }}</span>
                </td>
                <td style="width: 26%;">
                    <span style="font-size: 8pt; color: #475569; display: block; text-transform: uppercase;">Jalur Seleksi:</span>
                    <strong style="font-size: 9.5pt; text-transform: uppercase;">Jalur {{ $pendaftar->jalur_pendaftaran }}</strong>
                </td>
                <td style="width: 26%; text-align: right;">
                    <span style="font-size: 8pt; color: #475569; display: block; text-transform: uppercase;">Status Berkas:</span>
                    <span class="status-pill">
                        @if($pendaftar->status == 'diterima')
                            RESMI DITERIMA
                        @elseif($pendaftar->status == 'cadangan')
                            CADANGAN
                        @elseif(in_array($pendaftar->status, ['terverifikasi', 'berkas_valid', 'siap_tes']))
                            TERVERIFIKASI
                        @elseif($pendaftar->status == 'ditolak')
                            DITOLAK
                        @else
                            MENUNGGU VERIFIKASI
                        @endif
                    </span>
                </td>
            </tr>
        </table>

        {{-- 4. PANEL KHUSUS: STATUS DITERIMA ATAU JADWAL UJIAN SELEKSI CBT --}}
        @if($pendaftar->status == 'diterima')
            <div style="border: 1.5px solid #000000; background: #f0fdf4; padding: 7px 12px; margin-bottom: 12px; font-size: 8.5pt;">
                <div style="font-size: 8pt; color: #047857; font-weight: bold; text-transform: uppercase;">Dinyatakan Lulus &amp; Diterima Pada Program Keahlian:</div>
                <div style="font-size: 11.5pt; font-weight: 900; color: #065f46; margin: 1px 0;">
                    {{ strtoupper($pendaftar->jurusanDiterima->nama_jurusan ?? ($pendaftar->jurusanPilihan1->nama_jurusan ?? '-')) }}
                </div>
                <div style="font-size: 8pt; color: #047857;">
                    Peringkat: <strong>#{{ $pendaftar->peringkat_jurusan ?: '1' }}</strong> &bull; 
                    Total Nilai Akhir: <strong>{{ number_format($pendaftar->nilai_akhir ?? 0, 2) }}</strong> &bull; 
                    Tanggal Registrasi: {{ \Carbon\Carbon::parse($pendaftar->created_at)->translatedFormat('d F Y') }}
                </div>
            </div>
        @else
            <div class="jadwal-panel">
                <div class="jadwal-panel-head">
                    Jadwal Pelaksanaan Tes Seleksi CBT &amp; Wawancara Kejuruan
                </div>
                <table class="jadwal-table">
                    <tr>
                        <td class="label-col">Hari / Tanggal</td>
                        <td style="width: 8px;">:</td>
                        <td class="val-col">
                            {{ \Carbon\Carbon::parse($pendaftar->jadwal_tanggal_resmi)->translatedFormat('l, d F Y') }}
                        </td>
                        <td class="label-col">Ruang / Tempat</td>
                        <td style="width: 8px;">:</td>
                        <td class="val-col">
                            {{ $pendaftar->jadwal_ruang_resmi }}
                        </td>
                    </tr>
                    <tr>
                        <td class="label-col">Waktu Ujian</td>
                        <td>:</td>
                        <td class="val-col">
                            {{ $pendaftar->jadwal_sesi_resmi }}
                        </td>
                        <td class="label-col">Pelaksanaan</td>
                        <td>:</td>
                        <td class="val-col">
                            1x Gelombang (Sesuai Juknis Resmi PPDB)
                        </td>
                    </tr>
                </table>
            </div>
        @endif

        {{-- 5. DETAIL BIODATA SISWA & PAS FOTO 3X4 --}}
        <div class="biodata-wrapper">
            {{-- Kolom Kiri: Pas Foto & QR Code --}}
            <div class="side-col">
                <div class="pas-foto-box">
                    @if($pendaftar->pas_foto && file_exists(public_path('storage/' . $pendaftar->pas_foto)))
                        <img src="{{ asset('storage/' . $pendaftar->pas_foto) }}" alt="Pas Foto">
                    @else
                        <div style="text-align: center; color: #475569; font-size: 8pt; font-family: -apple-system, BlinkMacSystemFont, sans-serif;">
                            <strong>PAS FOTO</strong><br>
                            3 &times; 4 cm<br>
                            <span style="font-size: 6.5pt; color: #64748b;">(Warna)</span>
                        </div>
                    @endif
                </div>

                {{-- 2D Barcode (QR Code) Resmi Presensi Ujian CBT & Verifikasi --}}
                <div class="qr-verifikasi-box" style="border: 1.5px solid #000000; padding: 6px; background: #ffffff; border-radius: 4px; display: inline-block; text-align: center;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($pendaftar->no_pendaftaran ?? $pendaftar->nomor_pendaftaran) }}" alt="2D Barcode Presensi" style="width: 86px; height: 86px; display: block; margin: 0 auto;">
                    <div class="qr-caption" style="font-weight: 900; font-size: 7pt; color: #000000; margin-top: 4px; letter-spacing: 0.5px;">2D BARCODE UJIAN</div>
                    <div style="font-size: 6.5pt; font-weight: bold; color: #1e293b; font-family: 'Courier New', monospace;">{{ $pendaftar->no_pendaftaran ?? $pendaftar->nomor_pendaftaran }}</div>
                </div>
            </div>

            {{-- Kolom Kanan: Rincian Data Pribadi --}}
            <table class="table-biodata">
                <tr>
                    <td class="lbl">Nomor Induk Siswa Nasional (NISN)</td>
                    <td class="sep">:</td>
                    <td class="val"><strong>{{ $pendaftar->nisn }}</strong></td>
                </tr>
                <tr>
                    <td class="lbl">Nomor Induk Kependudukan (NIK)</td>
                    <td class="sep">:</td>
                    <td class="val">{{ $pendaftar->nik ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="lbl">Nama Lengkap Calon Siswa</td>
                    <td class="sep">:</td>
                    <td class="val"><strong>{{ strtoupper($pendaftar->nama_lengkap) }}</strong></td>
                </tr>
                <tr>
                    <td class="lbl">Jenis Kelamin</td>
                    <td class="sep">:</td>
                    <td class="val">{{ $pendaftar->jenis_kelamin == 'L' ? 'Laki-laki (L)' : 'Perempuan (P)' }}</td>
                </tr>
                <tr>
                    <td class="lbl">Tempat, Tanggal Lahir</td>
                    <td class="sep">:</td>
                    <td class="val">{{ $pendaftar->tempat_lahir }}, {{ \Carbon\Carbon::parse($pendaftar->tanggal_lahir)->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="lbl">Asal Sekolah (SMP / MTs)</td>
                    <td class="sep">:</td>
                    <td class="val">{{ $pendaftar->asal_sekolah }} (Tahun Lulus: {{ $pendaftar->tahun_lulus }})</td>
                </tr>
                <tr>
                    <td class="lbl">Pilihan Keahlian 1 (Utama)</td>
                    <td class="sep">:</td>
                    <td class="val"><strong>{{ $pendaftar->jurusanPilihan1->nama_jurusan ?? '-' }} ({{ $pendaftar->jurusanPilihan1->kode_jurusan ?? $pendaftar->jurusanPilihan1->kode ?? '' }})</strong></td>
                </tr>
                <tr>
                    <td class="lbl">Pilihan Keahlian 2 (Alternatif)</td>
                    <td class="sep">:</td>
                    <td class="val">{{ $pendaftar->jurusanPilihan2->nama_jurusan ?? '-' }} ({{ $pendaftar->jurusanPilihan2->kode_jurusan ?? $pendaftar->jurusanPilihan2->kode ?? '' }})</td>
                </tr>
                <tr>
                    <td class="lbl">Nama Orang Tua / Wali</td>
                    <td class="sep">:</td>
                    <td class="val">Ibu: {{ $pendaftar->nama_ibu ?: '-' }} &bull; Ayah: {{ $pendaftar->nama_ayah ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="lbl">No. Kontak / WhatsApp</td>
                    <td class="sep">:</td>
                    <td class="val">{{ $pendaftar->no_hp_siswa ?: ($pendaftar->no_hp_ortu ?: '-') }}</td>
                </tr>
                <tr>
                    <td class="lbl">Alamat Tempat Tinggal</td>
                    <td class="sep">:</td>
                    <td class="val">{{ $pendaftar->alamat_lengkap }}</td>
                </tr>
            </table>
        </div>

        {{-- 6. TATA TERTIB & PETUNJUK PESERTA --}}
        <div class="catatan-box">
            @if($pendaftar->status == 'diterima')
                <strong>Ketentuan Daftar Ulang Calon Siswa Diterima:</strong>
                <ol>
                    <li>Bawa Surat Keterangan Lulus ini beserta kelengkapan berkas fisik ke Sekretariat PPDB SMKN 1 Air Naningan pada jadwal yang ditentukan.</li>
                    <li>Menyerahkan fotokopi legalisir: Ijazah/SKL SMP (2 lbr), Kartu Keluarga (2 lbr), Akta Kelahiran (2 lbr), dan Pas Foto 3x4 (4 lembar).</li>
                    <li>Mengisi dan menandatangani surat pernyataan kesanggupan mematuhi tata tertib dan disiplin sekolah bermaterai.</li>
                </ol>
            @else
                <strong>Tata Tertib &amp; Petunjuk Pelaksanaan Ujian Seleksi:</strong>
                <ol>
                    <li>Kartu Tanda Peserta ini <strong>wajib dicetak pada kertas HVS A4</strong> dan dibawa saat verifikasi berkas dan pelaksanaan tes seleksi di sekolah.</li>
                    <li>Peserta wajib hadir di lokasi <strong>30 menit sebelum ujian dimulai</strong> dengan mengenakan seragam sekolah asal rapi, lengkap, dan bersepatu.</li>
                    <li>Bagi pelaksanaan ujian CBT online, peserta dapat mengakses menu <code>{{ url('/ppdb/ujian') }}</code> menggunakan Nomor Pendaftaran di atas.</li>
                    <li>Hasil seleksi dan pemeringkatan kelulusan akan diumumkan secara resmi melalui laman: <code>{{ url('/ppdb/status') }}</code></li>
                </ol>
            @endif
        </div>

        {{-- 7. PENGESAHAN / TANDA TANGAN (2 KOLOM SEJAJAR) --}}
        <table class="ttd-table">
            <tr>
                <td>
                    Calon Peserta Didik Baru,
                    <div class="ttd-space"></div>
                    <span class="ttd-nama">{{ strtoupper($pendaftar->nama_lengkap) }}</span>
                    <span style="font-size: 8pt; color: #333;">NISN. {{ $pendaftar->nisn }}</span>
                </td>
                <td>
                    Air Naningan, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    Panitia PPDB SMKN 1 Air Naningan,
                    <div class="ttd-space"></div>
                    <span class="ttd-nama">( Tim Verifikator PPDB )</span>
                    <span style="font-size: 8pt; color: #333;">NPSN: {{ $sekolah->npsn ?? '70011825' }}</span>
                </td>
            </tr>
        </table>

        {{-- FOOTER KETERANGAN DATA PPDB --}}
        <div style="margin-top:10px; font-size:7.5pt; color:#475569; border-top:1px solid #cbd5e1; padding-top:3px; display:flex; justify-content:space-between; align-items:center; font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
            <span>Dokumen Resmi Penerimaan Peserta Didik Baru · <strong>PPDB SMKN 1 Air Naningan</strong></span>
            <span>Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</span>
        </div>

    </div>

</body>
</html>
