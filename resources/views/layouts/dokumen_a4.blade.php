<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Dokumen Resmi — ' . config('app.name', 'SMKN 1 Air Naningan'))</title>
  
  {{-- Google Fonts: Serif Resmi & Sans-Serif untuk Antarmuka Layar --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    /* ═══════════════════════════════════════════════════════════════════
       STANDAR KERTAS A4 & SISTEM CETAK DINAS (PRINT ENGINE)
       ═══════════════════════════════════════════════════════════════════ */
    @page {
      size: {{ $paperSize ?? 'A4 portrait' }};
      margin: {{ $pageMargin ?? '12mm 15mm 15mm 15mm' }};
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }

    body {
      background-color: #334155;
      font-family: 'Tinos', 'Times New Roman', Times, serif;
      color: #000000;
      font-size: {{ $fontSize ?? '11pt' }};
      line-height: {{ $lineHeight ?? '1.4' }};
      margin: 0;
      padding: 20px 0 40px;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      -webkit-font-smoothing: antialiased;
    }

    /* ═══════════════════════════════════════════════════════════════════
       FLOATING ACTION TOOLBAR (HANYA TAMPIL DI BROWSER / SCREEN)
       ═══════════════════════════════════════════════════════════════════ */
    .screen-toolbar {
      width: 210mm;
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
      font-family: 'Plus Jakarta Sans', sans-serif;
      z-index: 1000;
    }

    .toolbar-info {
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
      letter-spacing: 0.5px;
    }

    .toolbar-actions {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }

    .btn-tool-back {
      background: rgba(255,255,255,0.1);
      color: #FFFFFF;
      font-weight: 700;
      font-size: 12.5px;
      padding: 7px 14px;
      border-radius: 8px;
      border: 1px solid rgba(255,255,255,0.2);
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      text-decoration: none;
      transition: all .15s ease;
    }
    .btn-tool-back:hover {
      background: rgba(255,255,255,0.2);
      color: #FFFFFF;
    }

    .btn-tool-print {
      background: #0284c7;
      color: #FFFFFF;
      font-weight: 800;
      font-size: 12.5px;
      padding: 7px 16px;
      border-radius: 8px;
      border: 1px solid #0284c7;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      box-shadow: 0 2px 8px rgba(2, 132, 199, 0.35);
      transition: all .15s ease;
    }
    .btn-tool-print:hover {
      background: #0369a1;
      border-color: #0369a1;
      transform: translateY(-1px);
    }

    /* ═══════════════════════════════════════════════════════════════════
       LEMBAR KERTAS A4 (CANVAS DOKUMEN RESMI)
       ═══════════════════════════════════════════════════════════════════ */
    .sheet-a4-wrapper {
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .sheet-a4 {
      width: 210mm;
      min-height: 297mm;
      background: #FFFFFF;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
      padding: {{ $sheetPadding ?? '15mm 20mm 15mm 20mm' }};
      position: relative;
      box-sizing: border-box;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
    }

    .sheet-body {
      flex: 1;
      width: 100%;
    }

    /* ═══════════════════════════════════════════════════════════════════
       PRINT MEDIA QUERY (OTOMATIS BERSIH DARI TAMPILAN SCREEN)
       ═══════════════════════════════════════════════════════════════════ */
    @media print {
      body {
        background: #FFFFFF !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
      }
      .no-print {
        display: none !important;
      }
      .sheet-a4-wrapper {
        width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
      }
      .sheet-a4 {
        width: 100% !important;
        min-height: auto !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
      }
    }
  </style>

  @stack('styles')
</head>
<body>

  {{-- FLOATING TOOLBAR (HANYA LAYAR) --}}
  <div class="screen-toolbar no-print">
    <div class="toolbar-info">
      <i class="bi bi-file-earmark-text-fill" style="color:#FACC15; font-size:18px;"></i>
      <span>@yield('toolbar_title', $docTitle ?? 'Dokumen Kedinasan Resmi')</span>
      <span class="badge-a4">{{ $paperBadge ?? 'A4 Portrait' }}</span>
    </div>

    <div class="toolbar-actions">
      @yield('toolbar_actions')

      @if(isset($backUrl) || !empty($backUrl))
        <a href="{{ $backUrl }}" class="btn-tool-back">
          <i class="bi bi-arrow-left"></i> {{ $backLabel ?? 'Kembali' }}
        </a>
      @else
        <button type="button" onclick="window.history.length > 1 ? window.history.back() : window.close()" class="btn-tool-back">
          <i class="bi bi-arrow-left"></i> {{ $backLabel ?? 'Kembali' }}
        </button>
      @endif

      <button type="button" onclick="window.print()" class="btn-tool-print">
        <i class="bi bi-printer-fill"></i> Cetak / Simpan PDF (A4)
      </button>
    </div>
  </div>

  {{-- LEMBAR UTAMA A4 --}}
  <div class="sheet-a4-wrapper">
    <div class="sheet-a4">

      {{-- 1. KOP SURAT DINAS RESMI --}}
      @if(!isset($withKop) || $withKop !== false)
        @include('partials.kop_surat')
      @endif

      {{-- 2. ISI DOKUMEN / SURAT --}}
      <div class="sheet-body">
        @yield('content')
      </div>

      {{-- 3. AREA TANDA TANGAN --}}
      @hasSection('ttd')
        <div class="sheet-ttd" style="margin-top:20px; page-break-inside:avoid;">
          @yield('ttd')
        </div>
      @endif

      {{-- 4. CATATAN / FOOTER DOKUMEN --}}
      @hasSection('footer')
        <div class="sheet-footer" style="margin-top:16px; font-size:8.5pt; color:#475569; border-top:1px solid #e2e8f0; padding-top:6px; page-break-inside:avoid;">
          @yield('footer')
        </div>
      @endif

    </div>
  </div>

  @stack('scripts')
</body>
</html>
