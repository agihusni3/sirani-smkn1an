<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Export Barcode 2D Siswa - SMKN 1 Air Naningan</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    @page {
      size: A4 portrait;
      margin: 10mm 12mm 12mm 12mm;
    }

    *, *::before, *::after {
      box-sizing: border-box;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }

    body {
      background-color: #1e293b;
      font-family: 'Times New Roman', Times, serif;
      color: #000000;
      font-size: 10.5pt;
      line-height: 1.3;
      margin: 0;
      padding: 16px 0 40px;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      overflow-x: auto;
    }

    /* ─── TOOLBAR KONTROL DI LAYAR (NO-PRINT) ─── */
    .print-actions-bar {
      width: 210mm;
      max-width: 96vw;
      background: #0f172a;
      border: 1px solid #334155;
      border-radius: 12px;
      padding: 12px 18px;
      margin-bottom: 16px;
      box-shadow: 0 10px 25px -5px rgba(0,0,0,0.5);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
      font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .toolbar-left {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }

    .toolbar-title {
      color: #ffffff;
      font-size: 14px;
      font-weight: 800;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .select-filter {
      background: #1e293b;
      color: #ffffff;
      border: 1px solid #475569;
      border-radius: 6px;
      padding: 6px 10px;
      font-size: 12px;
      font-weight: 700;
      outline: none;
      cursor: pointer;
    }

    .toolbar-right {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }

    .btn-tool {
      background: rgba(255, 255, 255, 0.1);
      color: #ffffff;
      border: 1px solid rgba(255, 255, 255, 0.2);
      padding: 7px 14px;
      border-radius: 6px;
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all .2s;
    }

    .btn-tool:hover {
      background: rgba(255, 255, 255, 0.2);
      color: #ffffff;
    }

    .btn-tool.active {
      background: #0284c7;
      border-color: #0284c7;
    }

    .btn-print {
      background: #ffffff;
      color: #0f172a;
      border-color: #ffffff;
      font-weight: 800;
    }

    .btn-print:hover {
      background: #f1f5f9;
      color: #0f172a;
    }

    /* ─── KERTAS DOKUMEN CETAK A4 ─── */
    .a4-sheet {
      width: 210mm;
      min-height: 297mm;
      background: #ffffff;
      padding: 12mm 15mm 15mm 15mm;
      box-shadow: 0 8px 30px rgba(0,0,0,0.3);
      position: relative;
      box-sizing: border-box;
    }

    /* ─── KOP SURAT DINAS RESMI ─── */
    .kop-wrapper {
      display: flex;
      align-items: center;
      border-bottom: 3px double #000000;
      padding-bottom: 8px;
      margin-bottom: 12px;
      position: relative;
    }

    .kop-logo {
      width: 74px;
      height: 74px;
      object-fit: contain;
      flex-shrink: 0;
      margin-right: 14px;
    }

    .kop-text {
      flex: 1;
      text-align: center;
      color: #000000;
    }

    .kop-text .instansi-prov {
      font-size: 12pt;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin: 0;
      line-height: 1.2;
    }

    .kop-text .instansi-dinas {
      font-size: 12pt;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin: 0;
      line-height: 1.2;
    }

    .kop-text .instansi-sekolah {
      font-size: 15pt;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin: 2px 0;
      line-height: 1.2;
    }

    .kop-text .instansi-alamat {
      font-size: 8pt;
      font-style: italic;
      margin: 0;
      line-height: 1.25;
    }

    /* ─── JUDUL DOKUMEN ─── */
    .doc-header {
      text-align: center;
      margin-bottom: 12px;
    }

    .doc-title {
      font-size: 13pt;
      font-weight: bold;
      text-transform: uppercase;
      text-decoration: underline;
      margin: 0 0 4px 0;
      letter-spacing: 0.5px;
    }

    .doc-meta {
      font-size: 9.5pt;
      margin: 0;
      color: #111827;
      display: flex;
      justify-content: center;
      gap: 16px;
      flex-wrap: wrap;
    }

    /* ─── TABEL FORMAT: NISN | NAMA | BARCODE 2D ─── */
    .barcode-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 8px;
      font-size: 9pt;
      color: #000000;
    }

    .barcode-table thead {
      display: table-header-group;
    }

    .barcode-table th {
      border: 1.5px solid #000000;
      background: #f1f5f9;
      padding: 6px 8px;
      text-align: center;
      font-weight: bold;
      text-transform: uppercase;
      font-size: 8.5pt;
      letter-spacing: 0.3px;
    }

    .barcode-table td {
      border: 1px solid #000000;
      padding: 5px 8px;
      vertical-align: middle;
    }

    .barcode-table tbody tr {
      page-break-inside: avoid;
    }

    .col-no {
      width: 32px;
      text-align: center;
      font-weight: bold;
    }

    .col-nisn {
      width: 110px;
      text-align: center;
      font-family: 'JetBrains Mono', monospace;
      font-size: 9pt;
      font-weight: bold;
      letter-spacing: 0.5px;
      white-space: nowrap;
    }

    .col-nama {
      text-align: left;
      font-weight: bold;
      font-size: 9pt;
      text-transform: uppercase;
    }

    .col-rombel {
      width: 90px;
      text-align: center;
      font-size: 8.5pt;
      white-space: nowrap;
    }

    .col-barcode {
      width: 120px;
      text-align: center;
      padding: 5px 4px;
    }

    .barcode-2d-box {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background: #ffffff;
      padding: 2px;
    }

    .barcode-2d-box svg {
      width: 60px;
      height: 60px;
    }

    .barcode-text-num {
      font-family: 'JetBrains Mono', monospace;
      font-size: 7.5pt;
      font-weight: 800;
      letter-spacing: 0.5px;
      color: #000000;
      margin-top: 2px;
    }

    /* ─── MODE GRID STIKER / LABEL 2D (ALTERNATIF LAYOUT) ─── */
    .barcode-grid-wrap {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 10px;
      margin-top: 10px;
    }

    .stiker-card {
      border: 1.5px dashed #000000;
      border-radius: 6px;
      padding: 10px;
      text-align: center;
      background: #ffffff;
      page-break-inside: avoid;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .stiker-school {
      font-size: 7pt;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 2px;
      color: #334155;
    }

    .stiker-nama {
      font-size: 8.5pt;
      font-weight: 800;
      text-transform: uppercase;
      margin-bottom: 2px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 100%;
    }

    .stiker-meta {
      font-size: 7.5pt;
      color: #475569;
      margin-bottom: 6px;
      font-weight: 600;
    }

    .stiker-barcode-2d svg {
      width: 72px;
      height: 72px;
    }

    /* ─── TANDA TANGAN / PENGESAHAN ─── */
    .doc-footer-sign {
      margin-top: 24px;
      display: flex;
      justify-content: space-between;
      page-break-inside: avoid;
      font-size: 9.5pt;
    }

    .sign-box {
      text-align: center;
      width: 220px;
    }

    .sign-space {
      height: 52px;
    }

    .sign-name {
      font-weight: bold;
      text-decoration: underline;
    }

    /* ─── PRINT MEDIA OPTIMIZATION ─── */
    @media print {
      body {
        background: #ffffff !important;
        padding: 0 !important;
      }

      .no-print, .print-actions-bar {
        display: none !important;
      }

      .a4-sheet {
        box-shadow: none !important;
        padding: 0 !important;
        width: 100% !important;
        min-height: auto !important;
      }
    }
  </style>
</head>
<body>

  {{-- TOOLBAR ATAS (HANYA DITAMPILKAN DI LAYAR) --}}
  <div class="print-actions-bar no-print">
    <div class="toolbar-left">
      <div class="toolbar-title">
        <i class="bi bi-qr-code" style="color: #38bdf8; font-size: 16px;"></i>
        Export Barcode 2D (QR Code) Siswa
      </div>

      {{-- Filter Rombel Cepat --}}
      <form action="{{ route('siswa.export-barcode') }}" method="GET" style="display:inline-flex; align-items:center; gap:6px; margin:0;">
        @if(!empty($layout))<input type="hidden" name="layout" value="{{ $layout }}" />@endif
        <select name="rombel_id" class="select-filter" onchange="this.form.submit()">
          <option value="">-- Semua Rombel / Kelas --</option>
          @foreach($rombels as $r)
            <option value="{{ $r->id }}" {{ (!empty($rombelId) && $rombelId == $r->id) ? 'selected' : '' }}>
              {{ $r->nama_rombel }}
            </option>
          @endforeach
        </select>
      </form>
    </div>

    <div class="toolbar-right">
      {{-- Switch Layout: Tabel vs Stiker --}}
      <a href="{{ request()->fullUrlWithQuery(['layout' => 'tabel']) }}" class="btn-tool {{ ($layout ?? 'tabel') === 'tabel' ? 'active' : '' }}" title="Mode Tabel Dokumen Resmi">
        <i class="bi bi-table"></i> Format Tabel
      </a>
      <a href="{{ request()->fullUrlWithQuery(['layout' => 'grid']) }}" class="btn-tool {{ ($layout ?? 'tabel') === 'grid' ? 'active' : '' }}" title="Mode Lembar Label Stiker">
        <i class="bi bi-grid-3x3-gap-fill"></i> Label Stiker
      </a>
      <a href="{{ request()->fullUrlWithQuery(['download' => 'excel']) }}" class="btn-tool" title="Download Excel (.xls) dengan Barcode 2D tertanam di tabel">
        <i class="bi bi-file-earmark-excel-fill" style="color:#22c55e;"></i> Excel (.xls)
      </a>
      <a href="{{ request()->fullUrlWithQuery(['download' => 'csv']) }}" class="btn-tool" title="Unduh CSV Data Siswa & Barcode">
        <i class="bi bi-file-earmark-spreadsheet"></i> CSV
      </a>
      <button type="button" onclick="window.print()" class="btn-tool btn-print" title="Cetak atau Simpan sebagai PDF">
        <i class="bi bi-printer-fill"></i> Cetak / Print PDF
      </button>
      <a href="{{ route('siswa.index') }}" class="btn-tool" title="Kembali ke Data Siswa">
        <i class="bi bi-arrow-left"></i> Kembali
      </a>
    </div>
  </div>

  {{-- LEMBAR CETAK DOKUMEN A4 --}}
  <div class="a4-sheet">

    {{-- KOP SURAT RESMI DINAS PENDIDIKAN --}}
    <div class="kop-wrapper">
      <img src="{{ !empty($sekolah->logo_url) ? $sekolah->logo_url : asset('logo.png') }}" alt="Logo Sekolah" class="kop-logo" onerror="this.style.display='none'" />
      <div class="kop-text">
        <p class="instansi-prov">PEMERINTAH PROVINSI LAMPUNG</p>
        <p class="instansi-dinas">DINAS PENDIDIKAN DAN KEBUDAYAAN</p>
        <h1 class="instansi-sekolah">{{ !empty($sekolah->nama_sekolah) ? strtoupper($sekolah->nama_sekolah) : 'SMK NEGERI 1 AIR NANINGAN' }}</h1>
        <p class="instansi-alamat">
          {{ $sekolah->alamat ?? 'Jl. Raya Air Naningan, Kec. Air Naningan, Kab. Tanggamus, Lampung 35379' }}
          @if(!empty($sekolah->email)) · Email: {{ $sekolah->email }} @endif
          @if(!empty($sekolah->website)) · Website: {{ $sekolah->website }} @endif
        </p>
      </div>
    </div>

    {{-- JUDUL DOKUMEN --}}
    <div class="doc-header">
      <h2 class="doc-title">DAFTAR BARCODE 2D (QR CODE) PRESENSI SISWA</h2>
      <div class="doc-meta">
        <span>Rombel / Kelas: <strong>{{ $rombel ? $rombel->nama_rombel : 'Semua Rombel Terpilih' }}</strong></span>
        <span>Total: <strong>{{ number_format(count($siswas)) }} Siswa</strong></span>
        <span>Tanggal Cetak: <strong>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</strong></span>
      </div>
    </div>

    @if(($layout ?? 'tabel') === 'grid')
      {{-- ═══════════════════════════════════════════════════════════════════ --}}
      {{-- MODE 2: LEMBAR LABEL / STIKER BARCODE 2D                        --}}
      {{-- ═══════════════════════════════════════════════════════════════════ --}}
      <div class="barcode-grid-wrap">
        @forelse($siswas as $idx => $s)
          @php
            $nisnClean = !empty($s->nisn) ? trim($s->nisn) : (!empty($s->nis) ? trim($s->nis) : (string)$s->id);
            $rombelNama = $s->siswaRombels->first()?->rombel?->nama_rombel ?? '-';
            $barcode2dSvg = \App\Services\BarcodeService::getBarcode2DSvg($nisnClean, 72);
          @endphp
          <div class="stiker-card">
            <div class="stiker-school">{{ !empty($sekolah->nama_sekolah) ? $sekolah->nama_sekolah : 'SMKN 1 AIR NANINGAN' }}</div>
            <div class="stiker-nama" title="{{ $s->nama }}">{{ $s->nama }}</div>
            <div class="stiker-meta">NISN: {{ $s->nisn ?: '-' }} · {{ $rombelNama }}</div>
            <div class="stiker-barcode-2d">
              {!! $barcode2dSvg !!}
            </div>
            <div class="barcode-text-num">{{ $nisnClean }}</div>
          </div>
        @empty
          <div style="grid-column: 1 / -1; text-align:center; padding: 40px; color:#64748b;">
            Tidak ada data siswa ditemukan.
          </div>
        @endforelse
      </div>
    @else
      {{-- ═══════════════════════════════════════════════════════════════════ --}}
      {{-- MODE 1: TABEL RESMI FORMAT: NISN | NAMA | BARCODE 2D (DEFAULT)   --}}
      {{-- ═══════════════════════════════════════════════════════════════════ --}}
      <table class="barcode-table">
        <thead>
          <tr>
            <th class="col-no">No</th>
            <th class="col-nisn">NISN</th>
            <th class="col-nama">Nama Lengkap Siswa</th>
            @if(empty($rombelId))
              <th class="col-rombel">Rombel</th>
            @endif
            <th class="col-barcode">Barcode 2D</th>
          </tr>
        </thead>
        <tbody>
          @forelse($siswas as $idx => $s)
            @php
              $nisnClean = !empty($s->nisn) ? trim($s->nisn) : (!empty($s->nis) ? trim($s->nis) : (string)$s->id);
              $rombelNama = $s->siswaRombels->first()?->rombel?->nama_rombel ?? '-';
              $barcode2dSvg = \App\Services\BarcodeService::getBarcode2DSvg($nisnClean, 60);
            @endphp
            <tr>
              <td class="col-no">{{ $idx + 1 }}</td>
              <td class="col-nisn">{{ $s->nisn ?: '-' }}</td>
              <td class="col-nama">
                {{ $s->nama }}
                @if(!empty($s->jenis_kelamin))
                  <span style="font-size: 7.5pt; font-weight: normal; color: #4b5563;">({{ strtoupper($s->jenis_kelamin) }})</span>
                @endif
              </td>
              @if(empty($rombelId))
                <td class="col-rombel">{{ $rombelNama }}</td>
              @endif
              <td class="col-barcode">
                <div class="barcode-2d-box">
                  {!! $barcode2dSvg !!}
                  <div class="barcode-text-num">{{ $nisnClean }}</div>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="{{ empty($rombelId) ? 5 : 4 }}" style="text-align:center; padding:30px; color:#64748b;">
                Tidak ada data siswa ditemukan untuk rombel yang dipilih.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    @endif

    {{-- TANDA TANGAN / PENGESAHAN DOKUMEN --}}
    <div class="doc-footer-sign">
      <div class="sign-box">
        <p style="margin:0 0 2px 0;">Mengetahui,</p>
        <p style="margin:0; font-weight:bold;">Kepala Sekolah</p>
        <div class="sign-space"></div>
        <p class="sign-name">{{ !empty($sekolah->kepala_sekolah) ? $sekolah->kepala_sekolah : '...................................................' }}</p>
        <p style="margin:2px 0 0 0; font-size:8.5pt;">NIP. {{ !empty($sekolah->nip_kepala_sekolah) ? $sekolah->nip_kepala_sekolah : '........................................' }}</p>
      </div>

      <div class="sign-box">
        <p style="margin:0 0 2px 0;">Air Naningan, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
        <p style="margin:0; font-weight:bold;">Petugas TU / Operator Kios</p>
        <div class="sign-space"></div>
        <p class="sign-name">{{ auth()->user()?->name ?? '...................................................' }}</p>
        <p style="margin:2px 0 0 0; font-size:8.5pt;">NIP / NUPTK. -</p>
      </div>
    </div>

  </div>

</body>
</html>
