<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $judulLaporan ?? 'Daftar Pendidik & Tenaga Kependidikan - SMKN 1 Air Naningan' }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <style>
    /* STANDAR CETAK DINAS A4 RESMI DENGAN MARGIN KONSISTEN DI SELURUH HALAMAN */
    @page {
      size: A4 {{ $orientasi ?? 'portrait' }};
      margin: 12mm 12mm 14mm 12mm;
    }
    *, *::before, *::after {
      box-sizing: border-box;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }
    body {
      background-color: #334155;
      font-family: 'Times New Roman', Times, serif;
      color: #000000;
      font-size: 10pt;
      line-height: 1.35;
      -webkit-font-smoothing: antialiased;
      margin: 0;
      padding: 20px 0 40px;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      overflow-x: auto;
    }
    .print-actions-bar {
      width: {{ ($orientasi ?? 'portrait') === 'landscape' ? '297mm' : '210mm' }};
      max-width: 95vw;
      background: #1E293B;
      border: 1px solid #334155;
      border-radius: 12px;
      padding: 12px 18px;
      margin-bottom: 16px;
      box-shadow: 0 10px 25px -5px rgba(0,0,0,0.4);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
      box-sizing: border-box;
    }
    .print-title-info {
      color: #FFFFFF;
      font-size: 13.5px;
      font-weight: 800;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .badge-a4 {
      background: rgba(234, 179, 8, 0.2);
      color: #FACC15;
      border: 1px solid rgba(234, 179, 8, 0.4);
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
    }
    .btn-action-group {
      display: flex;
      gap: 8px;
    }
    .btn-print {
      background: #000000;
      color: #FFFFFF;
      font-weight: 800;
      font-size: 13px;
      padding: 8px 18px;
      border-radius: 8px;
      border: 1px solid #000000;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
    }
    .btn-print:hover {
      background: #262626;
      border-color: #262626;
    }
    .btn-back {
      background: rgba(255,255,255,0.1);
      color: #FFFFFF;
      font-weight: 700;
      font-size: 13px;
      padding: 8px 14px;
      border-radius: 8px;
      border: 1px solid rgba(255,255,255,0.2);
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      text-decoration: none;
    }
    .print-sheet-wrapper {
      width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      display: flex;
      justify-content: center;
      padding-bottom: 30px;
    }

    /* KERTAS A4 (PORTRAIT / LANDSCAPE) - TAMPILAN SCREEN */
    .a4-sheet {
      width: {{ ($orientasi ?? 'portrait') === 'landscape' ? '297mm' : '210mm' }};
      min-width: {{ ($orientasi ?? 'portrait') === 'landscape' ? '297mm' : '210mm' }};
      min-height: {{ ($orientasi ?? 'portrait') === 'landscape' ? '210mm' : '297mm' }};
      background: #FFFFFF;
      padding: 12mm 12mm 14mm 12mm;
      box-shadow: 0 4px 25px rgba(0,0,0,0.35);
      box-sizing: border-box;
      margin: 0 auto;
    }

    @media (max-width: 820px) {
      body {
        padding: 10px 0 30px;
        align-items: stretch;
      }
      .print-actions-bar {
        width: calc(100% - 16px);
        margin: 0 8px 14px;
        position: sticky;
        top: 6px;
        z-index: 1000;
        padding: 10px 14px;
      }
      .print-title-info {
        font-size: 12px;
      }
      .btn-action-group {
        width: 100%;
        display: flex;
        gap: 8px;
      }
      .btn-back {
        flex: 1;
        justify-content: center;
        padding: 8px 10px;
        font-size: 12px;
      }
      .btn-print {
        flex: 1.5;
        justify-content: center;
        padding: 8px 12px;
        font-size: 12px;
      }
      .print-sheet-wrapper {
        justify-content: flex-start;
        padding: 0 8px 30px;
      }
    }

    /* KOP SURAT DINAS */
    .kop-container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      text-align: center;
      margin-bottom: 2px;
      width: 100%;
      page-break-inside: avoid;
      break-inside: avoid;
    }
    .kop-logo-left, .kop-logo-right {
      width: 68px;
      min-width: 68px;
      max-width: 68px;
      height: 68px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .kop-logo-left img, .kop-logo-right img {
      max-width: 68px;
      max-height: 68px;
      width: auto;
      height: auto;
      object-fit: contain;
      display: block;
      margin: 0 auto;
    }
    .kop-text {
      flex: 1;
      text-align: center;
      padding: 0 8px;
      margin: 0;
      min-width: 0;
    }
    .kop-instansi {
      font-size: 11pt;
      font-weight: 700;
      text-transform: uppercase;
      line-height: 1.15;
    }
    .kop-dinas {
      font-size: 12pt;
      font-weight: 700;
      text-transform: uppercase;
      line-height: 1.2;
    }
    .kop-sekolah {
      font-size: 15pt;
      font-weight: 700;
      text-transform: uppercase;
      line-height: 1.25;
      margin-top: 1px;
    }
    .kop-alamat {
      font-size: 8pt;
      font-family: 'Plus Jakarta Sans', sans-serif;
      color: #222222;
      line-height: 1.3;
      margin-top: 2px;
    }
    .kop-border {
      border-top: 2.5px solid #000000;
      border-bottom: 0.8px solid #000000;
      height: 3.5px;
      margin: 5px 0 12px;
    }

    /* JUDUL */
    .judul-laporan {
      text-align: center;
      margin-bottom: 12px;
      page-break-inside: avoid;
      break-inside: avoid;
    }
    .judul-laporan h2 {
      font-size: 12pt;
      font-weight: 800;
      text-transform: uppercase;
      text-decoration: underline;
      letter-spacing: 0.5px;
    }
    .judul-laporan .sub-judul {
      font-size: 9.5pt;
      font-weight: 600;
      margin-top: 2px;
    }

    /* TABEL DATA */
    .table-data {
      width: 100%;
      table-layout: {{ count($kolomTerpilih) > 7 ? 'auto' : 'fixed' }};
      border-collapse: collapse;
      font-size: {{ count($kolomTerpilih) > 9 ? '7.5pt' : (count($kolomTerpilih) > 6 ? '8pt' : '8.5pt') }};
      margin-bottom: 12px;
      page-break-inside: auto;
      break-inside: auto;
    }
    .table-data thead {
      display: table-header-group;
      page-break-inside: avoid;
      break-inside: avoid;
    }
    .table-data thead tr {
      page-break-inside: avoid;
      break-inside: avoid;
    }
    .table-data tbody {
      display: table-row-group;
    }
    .table-data tr {
      page-break-inside: avoid !important;
      break-inside: avoid !important;
    }
    .table-data th, .table-data td {
      border: 1px solid #000000;
      padding: {{ count($kolomTerpilih) > 9 ? '3.5px 4px' : (count($kolomTerpilih) > 6 ? '4px 5px' : '5px 6px') }};
      vertical-align: middle;
      page-break-inside: avoid !important;
      break-inside: avoid !important;
      overflow-wrap: break-word;
      word-break: normal;
    }
    .table-data th {
      background-color: #F1F5F9 !important;
      text-align: center;
      font-weight: 700;
      line-height: 1.25;
    }
    .table-data th.col-no,
    .table-data td.col-no {
      width: 1%;
      white-space: nowrap !important;
      text-align: center;
      padding: 4px 6px;
    }
    .table-data td.text-center {
      text-align: center;
    }
    .table-data td.text-end {
      text-align: right;
    }

    /* TANDA TANGAN */
    .ttd-container {
      width: 100%;
      margin-top: 16px;
      font-size: 9.5pt;
      page-break-inside: avoid !important;
      break-inside: avoid !important;
    }
    .ttd-table {
      width: 100%;
      border-collapse: collapse;
      text-align: center;
    }
    .ttd-table td {
      width: 50%;
      vertical-align: top;
      padding: 4px;
    }
    .ttd-space {
      height: 48px;
    }
    .ttd-name {
      font-weight: 700;
      text-decoration: underline;
    }

    @media print {
      @page {
        size: A4 {{ $orientasi ?? 'portrait' }};
        margin: 12mm 12mm 14mm 12mm;
      }
      html, body {
        background: #FFFFFF !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        height: auto !important;
      }
      .no-print, .print-actions-bar {
        display: none !important;
      }
      .print-sheet-wrapper {
        padding: 0 !important;
        margin: 0 !important;
        display: block !important;
        width: 100% !important;
      }
      .a4-sheet {
        box-shadow: none !important;
        margin: 0 !important;
        padding: 0 !important; /* Margin halaman diatur secara bersih oleh @page pada SEMUA halaman */
        width: 100% !important;
        min-width: 100% !important;
        max-width: 100% !important;
        border: none !important;
        box-sizing: border-box !important;
      }
    }
  </style>
</head>
<body>

  {{-- FLOATING ACTION TOOLBAR --}}
  <div class="print-actions-bar no-print">
    <div class="print-title-info">
      <i class="bi bi-file-earmark-pdf-fill" style="color:#FACC15; font-size:18px;"></i>
      <span>{{ $judulLaporan ?? 'Daftar Pendidik & Tenaga Kependidikan' }}</span>
      <span class="badge-a4">A4 {{ ucfirst($orientasi ?? 'portrait') }}</span>
      @if(!($withKop ?? true))
        <span class="badge-a4" style="background:rgba(59,130,246,0.2); color:#93C5FD; border-color:rgba(59,130,246,0.4);">Tanpa Kop (Kertas Pre-Printed)</span>
      @endif
      @if(($orientasi ?? 'portrait') === 'portrait')
        <a href="{{ request()->fullUrlWithQuery(['orientasi' => 'landscape']) }}" class="badge-a4" style="text-decoration:none; background:rgba(34,197,94,0.2); color:#86EFAC; border-color:rgba(34,197,94,0.4); display:inline-flex; align-items:center; gap:4px;" title="Ganti ke orientasi mendatar / landscape">
          <i class="bi bi-arrow-repeat"></i> Ganti ke Landscape
        </a>
      @else
        <a href="{{ request()->fullUrlWithQuery(['orientasi' => 'portrait']) }}" class="badge-a4" style="text-decoration:none; background:rgba(148,163,184,0.2); color:#CBD5E1; border-color:rgba(148,163,184,0.4); display:inline-flex; align-items:center; gap:4px;" title="Ganti ke orientasi tegak / portrait">
          <i class="bi bi-arrow-repeat"></i> Ganti ke Portrait
        </a>
      @endif
      <span style="font-size:11px; font-weight:normal; color:#94A3B8; margin-left:8px;">
        <i class="bi bi-info-circle"></i> Hilangkan centang <em>"Headers and footers"</em> di opsi cetak browser agar URL tidak tercetak.
      </span>
    </div>
    <div class="btn-action-group">
      <a href="{{ url('/guru') }}" class="btn-back">
        <i class="bi bi-arrow-left"></i> Kembali ke Data Guru
      </a>
      <button type="button" onclick="window.print()" class="btn-print">
        <i class="bi bi-printer-fill"></i> Cetak / Simpan PDF (A4)
      </button>
    </div>
  </div>

  {{-- WRAPPER LEMBAR A4 RESMI --}}
  <div class="print-sheet-wrapper">
    <div class="a4-sheet">

    {{-- KOP SURAT DINAS --}}
    @if($withKop ?? true)
      @include('partials.kop_surat')
    @else
      {{-- Spacer kosong jika dicetak pada kertas kop surat resmi fisik --}}
      <div style="height: 30mm;"></div>
    @endif

    {{-- JUDUL LAPORAN --}}
    <div class="judul-laporan">
      <h2>{{ $judulLaporan ?? 'DAFTAR PENDIDIK & TENAGA KEPENDIDIKAN' }}</h2>
      @if(!empty($subJudul))
        <div class="sub-judul">{{ $subJudul }}</div>
      @endif
    </div>

    {{-- TABEL DATA FLEKSIBEL SESUAI KOLOM TERPILIH --}}
    <table class="table-data">
      <thead>
        <tr>
          <th class="col-no">No</th>
          @foreach($kolomTerpilih as $colKey)
            @php $cfg = $kamusKolom[$colKey] ?? null; @endphp
            @if($cfg)
              <th style="{{ count($kolomTerpilih) <= 7 && isset($cfg['width']) ? 'width:'.$cfg['width'].';' : '' }}">
                {{ $cfg['label'] }}
              </th>
            @endif
          @endforeach
        </tr>
      </thead>
      <tbody>
        @forelse($gurus as $idx => $g)
          <tr>
            <td class="col-no">{{ $idx + 1 }}</td>
            @foreach($kolomTerpilih as $colKey)
              @php 
                $cfg = $kamusKolom[$colKey] ?? null; 
                $fn = $cfg['value'] ?? ($cfg['val'] ?? null);
                $val = $fn ? $fn($g) : '-';
                $alignClass = ($cfg['align'] ?? 'left') === 'center' ? 'text-center' : (($cfg['align'] ?? 'left') === 'right' ? 'text-end' : '');
                $isMono = !empty($cfg['mono']);
              @endphp
              <td class="{{ $alignClass }}" style="{{ $isMono ? "font-family:'JetBrains Mono', monospace; font-size:7.5pt;" : "" }}">
                @if($colKey === 'tanda_tangan')
                  <div style="height: 28px; width: 100%;"></div>
                @elseif($colKey === 'nama')
                  <strong>{{ $val }}</strong>
                @else
                  {{ $val }}
                @endif
            @endforeach
          </tr>
        @empty
          <tr>
            <td colspan="{{ count($kolomTerpilih) + 1 }}" class="text-center" style="padding:16px; color:#666;">
              Tidak ada data guru / pegawai terdaftar.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <div style="font-size:8.5pt; color:#333; margin-bottom:12px;">
      <em>* Total Data: {{ $gurus->count() }} Orang &middot; Keterangan Data: SITUAN SMKN 1 Air Naningan</em>
    </div>

    {{-- TANDA TANGAN --}}
    <div class="ttd-container">
      <table class="ttd-table">
        <tr>
          <td>
            Mengetahui,<br />
            Kepala SMK Negeri 1 Air Naningan
            <div class="ttd-space"></div>
            <div class="ttd-name">{{ $sekolah->nama_kepala_sekolah ?? 'Aprida, S.Si.' }}</div>
            <div>NIP. {{ $sekolah->nip_kepala_sekolah ?? '197904172008012019' }}</div>
          </td>
          <td>
            Air Naningan, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br />
            Kepala Tata Usaha / Kepegawaian
            <div class="ttd-space"></div>
            <div class="ttd-name">{{ auth()->user()->name ?? 'Staf Administrasi Kepegawaian' }}</div>
            <div>NIP/NUPTK. -</div>
          </td>
        </tr>
      </table>
    </div>

  </div>
  </div>

</body>
</html>
