<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laporan &amp; Rekapitulasi Presensi — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <style>
    /* Segmented Control Switcher */
    .segmented-control {
      display: inline-flex;
      background: var(--bg-3);
      padding: 4px;
      border-radius: var(--r-md);
      gap: 4px;
      border: 1px solid var(--border);
      margin-bottom: 20px;
    }
    .segmented-btn {
      padding: 8px 18px;
      border-radius: var(--r-sm);
      font-size: 13px;
      font-weight: 800;
      border: none;
      background: transparent;
      color: var(--text-2);
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all .2s ease;
      text-decoration: none;
    }
    .segmented-btn:hover { color: var(--text); }
    .segmented-btn.active {
      background: var(--text) !important;
      color: var(--bg) !important;
      font-weight: 800;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.25);
    }
    .segmented-btn.active i {
      color: var(--bg) !important;
    }

    /* Executive Filter Panel */
    .lp-filter-panel {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-xl);
      padding: 20px 22px;
      margin-bottom: 20px;
      box-shadow: var(--shadow-sm);
    }
    .period-chip {
      padding: 7px 16px;
      border-radius: var(--r-sm);
      font-size: 12.5px;
      font-weight: 700;
      cursor: pointer;
      background: var(--bg-3);
      border: 1px solid var(--border);
      color: var(--text-2);
      transition: all .15s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .period-chip:hover { color: var(--text); background: var(--surface); border-color: var(--border-2); }
    .period-chip.active { 
      background: var(--text) !important; 
      color: var(--bg) !important; 
      border-color: var(--text) !important; 
      font-weight: 800; 
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25); 
    }
    .period-chip.active i {
      color: var(--bg) !important;
    }

    /* Executive KPI Grid */
    .lp-kpi-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
      gap: 8px;
      margin-bottom: 12px;
    }
    .lp-kpi-card {
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      border-radius: var(--r-sm);
      padding: 10px 12px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      gap: 2px;
      transition: all .15s ease;
      box-shadow: var(--shadow-sm);
    }
    .lp-kpi-card:hover {
      border-color: #000000;
    }
    .lp-kpi-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 2px;
    }
    .lp-kpi-title {
      font-size: 10px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.4px;
      color: var(--text-3);
    }
    .lp-kpi-icon {
      width: 26px;
      height: 26px;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      background: var(--bg-3);
      color: #000000;
      border: 1px solid var(--border-2);
      flex-shrink: 0;
    }
    .lp-kpi-val {
      font-size: 20px;
      font-weight: 900;
      font-family: var(--font-mono);
      line-height: 1.1;
      color: #000000;
    }
      font-family: var(--font-mono);
      color: var(--text);
      line-height: 1.1;
    }

    @media (max-width: 1024px) {
      .segmented-control {
        display: flex !important;
        width: 100% !important;
        margin-bottom: 14px !important;
      }
      .segmented-btn {
        flex: 1 !important;
        justify-content: center !important;
        text-align: center !important;
        padding: 9px 8px !important;
        font-size: 12px !important;
      }
      .lp-kpi-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 10px !important;
        margin-bottom: 16px !important;
      }
      .lp-kpi-card {
        padding: 12px 14px !important;
      }
      .lp-kpi-val {
        font-size: 22px !important;
      }
    }

    /* ── Searchable Combobox for Laporan Individu ── */
    .lp-picker-wrap { position: relative; width: 100%; }
    .lp-picker-trigger {
      width: 100%;
      height: 42px;
      background: var(--bg-3);
      border: 1px solid var(--border-2);
      border-radius: var(--r-sm);
      padding: 0 12px;
      display: flex;
      align-items: center;
      cursor: pointer;
      transition: all .2s ease;
      user-select: none;
    }
    .lp-picker-trigger:hover, .lp-picker-trigger.focused {
      border-color: var(--text);
      box-shadow: 0 0 0 2px var(--border-2);
    }
    .btn-clear-lp {
      background: transparent;
      border: none;
      color: var(--text-3);
      font-size: 15px;
      cursor: pointer;
      padding: 2px 4px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: color .15s;
    }
    .btn-clear-lp:hover { color: var(--red); }
    .lp-dropdown-panel {
      display: none;
      position: absolute;
      top: calc(100% + 6px);
      left: 0;
      right: 0;
      background: var(--bg-2);
      border: 1.5px solid var(--border-2);
      border-radius: var(--r-sm);
      box-shadow: var(--shadow-lg);
      z-index: 1050;
      overflow: hidden;
    }
    .lp-dropdown-panel.open {
      display: block;
      animation: modalFadeIn .15s ease;
    }
    .lp-picker-item {
      padding: 9px 12px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid var(--border);
      cursor: pointer;
      transition: background .15s;
      gap: 10px;
    }
    .lp-picker-item:last-child { border-bottom: none; }
    .lp-picker-item:hover { background: var(--surface); }

    /* ── STYLING KOP & DOKUMEN CETAK (PRINT / PDF) ── */
    .print-only { display: none; }

    @media print {
      @page {
        size: A4 portrait;
        margin: 12mm 15mm 15mm 15mm;
      }
      body {
        background: #fff !important;
        color: #000 !important;
        font-family: 'Times New Roman', Times, serif, sans-serif !important;
        font-size: 10pt !important;
        line-height: 1.3 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
      .no-print, .sidebar, .header, .tab-container, .panel-filter, .stats-grid, .btn-action,
      #themeToggleBtn, .acct-wrap, .autorefresh-widget, .greeting-badge, .alert-success, .alert-error, .modal-overlay, .custom-pagination {
        display: none !important;
      }
      .main-content {
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
      }
      .panel {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
        padding: 0 !important;
        margin: 0 !important;
      }
      .panel-title {
        display: none !important;
      }
      .print-only {
        display: block !important;
      }

      /* KOP SURAT FORMAL */
      .kop-container {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        margin-bottom: 2px !important;
        padding-bottom: 6px !important;
        width: 100% !important;
      }
      .kop-logo-left, .kop-logo-right {
        width: 68px !important;
        min-width: 68px !important;
        max-width: 68px !important;
        height: 68px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
      }
      .kop-logo-left img, .kop-logo-right img {
        max-width: 68px !important;
        max-height: 68px !important;
        width: auto !important;
        height: auto !important;
        object-fit: contain !important;
        display: block !important;
        margin: 0 auto !important;
      }
      .kop-text {
        text-align: center !important;
        flex: 1 !important;
        padding: 0 6px !important;
        margin: 0 !important;
        min-width: 0 !important;
      }
      .kop-text h2 {
        font-size: 11pt !important;
        font-weight: 700 !important;
        margin: 0 0 1px 0 !important;
        color: #000 !important;
        letter-spacing: 0.5px !important;
      }
      .kop-text h3 {
        font-size: 12pt !important;
        font-weight: 700 !important;
        margin: 0 0 1px 0 !important;
        color: #000 !important;
      }
      .kop-text h1 {
        font-size: 15pt !important;
        font-weight: 800 !important;
        margin: 0 0 2px 0 !important;
        color: #000 !important;
        letter-spacing: 0.5px !important;
      }
      .kop-text p {
        font-size: 8pt !important;
        margin: 2px 0 0 0 !important;
        color: #000 !important;
        line-height: 1.3 !important;
      }
      .kop-line-double {
        border-top: 2.5px solid #000 !important;
        border-bottom: 1px solid #000 !important;
        height: 3px !important;
        margin-bottom: 14px !important;
      }

      /* JUDUL DOKUMEN */
      .doc-title-box {
        text-align: center !important;
        margin-bottom: 14px !important;
      }
      .doc-title-box h4 {
        font-size: 12pt !important;
        font-weight: 800 !important;
        text-decoration: underline !important;
        margin: 0 0 4px 0 !important;
        color: #000 !important;
        text-transform: uppercase !important;
      }
      .doc-title-box p {
        font-size: 9.5pt !important;
        margin: 0 !important;
        color: #000 !important;
      }

      /* TABEL CETAK */
      table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin-bottom: 20px !important;
      }
      th, td {
        border: 1px solid #000 !important;
        padding: 5px 6px !important;
        font-size: 8.5pt !important;
        color: #000 !important;
      }
      th {
        background-color: #f0f0f0 !important;
        font-weight: bold !important;
        text-align: center !important;
      }

      /* TANDA TANGAN */
      .signature-row {
        display: flex !important;
        justify-content: space-between !important;
        margin-top: 25px !important;
        page-break-inside: avoid !important;
      }
      .signature-box {
        width: 42% !important;
        text-align: center !important;
        font-size: 9.5pt !important;
      }
      .signature-box p {
        margin: 0 0 2px 0 !important;
      }
      .signature-space {
        height: 60px !important;
      }
      .signature-name {
        font-weight: bold !important;
        text-decoration: underline !important;
      }
    }
  </style>
</head>
<body>

<div class="app-container">
  @include('partials.sidebar')

  <main class="main-content">
    @php
      $userLogin = auth()->user();
      $todayDate = \Carbon\Carbon::today()->toDateString();
      $isPiketBertugasHariIni = $userLogin && (
          $userLogin->isAdmin() || 
          ($userLogin->guru && \App\Models\JadwalPiket::isGuruPiketHariIni($userLogin->guru->id, $todayDate))
      );
    @endphp

    {{-- ULTRA COMPACT SLIM HEADER & SWITCHER BAR --}}
    <div class="panel no-print" style="background:var(--bg-2); border:1px solid var(--border); padding:10px 16px; margin-bottom:12px; border-radius:var(--r-md); box-shadow:var(--shadow-sm);">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          <h1 style="margin:0; font-size:16px; font-weight:900; color:var(--text); display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-file-earmark-bar-graph-fill" style="color:#000000; font-size:16px;"></i> Rekap Presensi &amp; Laporan
          </h1>
          <span style="color:var(--border-2); font-weight:300;">|</span>
          <span style="font-size:11.5px; color:var(--text-3);">
            Periode: <strong style="color:#000000;">{{ $periodeText }}</strong>
          </span>
        </div>

        <div style="display:flex; align-items:center; gap:8px;">
          <div class="segmented-control no-print" style="margin-bottom:0; background:var(--bg-3); border:1px solid var(--border-2); border-radius:6px; padding:2px; gap:2px;">
            <a href="/laporan?kategori=siswa&periode={{ $periode }}" class="segmented-btn {{ $kategori === 'siswa' ? 'active' : '' }}" style="height:28px; font-size:11px; font-weight:800; padding:0 12px; border-radius:4px;">
              <i class="bi bi-people-fill"></i> Siswa
            </a>
            @if($canAccessGuru ?? false)
              <a href="/laporan?kategori=guru&periode={{ $periode }}" class="segmented-btn {{ $kategori === 'guru' ? 'active' : '' }}" style="height:28px; font-size:11px; font-weight:800; padding:0 12px; border-radius:4px;">
                <i class="bi bi-person-badge-fill"></i> Guru &amp; Pegawai
              </a>
            @endif
          </div>
          @include('partials.header_actions')
        </div>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success no-print" style="margin-bottom: 12px;">
        <i class="bi bi-check-circle-fill"></i>
        <span>{{ session('success') }}</span>
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-error no-print" style="margin-bottom: 12px;">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span>{{ session('error') }}</span>
      </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- KOP SURAT DINAS RESMI (PRINT / PDF ONLY) -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="print-only">
      <div class="kop-container">
        <div class="kop-logo-left">
          <img src="/img/logo_prov_lampung.png" alt="Logo Provinsi" onerror="this.onerror=null; this.src='/img/logo_prov_lampung.svg'" />
        </div>
        <div class="kop-text">
          <h2>{{ $sekolah->nama_instansi_atas ?? 'PEMERINTAH PROVINSI LAMPUNG' }}</h2>
          <h3>{{ $sekolah->nama_dinas ?? 'DINAS PENDIDIKAN DAN KEBUDAYAAN' }}</h3>
          <h1>{{ $sekolah->nama_sekolah ?? 'SMK NEGERI 1 AIR NANINGAN' }}</h1>
          <p>{{ $sekolah->alamat_lengkap ?? $sekolah->alamat ?? 'Jl. Raya Air Naningan, Kec. Air Naningan, Kab. Tanggamus, Lampung 35379' }}</p>
          <p>Email: {{ $sekolah->email ?? 'info@smkn1airnaningan.sch.id' }} · Website: {{ $sekolah->website ?? 'smkn1airnaningan.sch.id' }}</p>
        </div>
        <div class="kop-logo-right">
          <img src="/img/logo.png" alt="Logo Sekolah" onerror="this.onerror=null; this.src='/logo.png'" />
        </div>
      </div>
      <div class="kop-line-double"></div>

      <div class="doc-title-box">
        <h4>LAPORAN REKAPITULASI KEHADIRAN {{ strtoupper($kategori) }}</h4>
        <p>Periode: <strong>{{ $periodeText }}</strong> @if(isset($rombel) && $rombel) | Rombel: <strong>{{ $rombel->nama_rombel }}</strong> @endif</p>
      </div>
    </div>

    @if($isWaliKelas ?? false)
      <div class="no-print" style="margin-bottom: 16px; background: rgba(0,0,0,0.04); border: 1px solid var(--border); border-radius: var(--r-sm); padding: 10px 14px; color: var(--text); display: flex; align-items: center; gap: 10px; font-size: 13px;">
        <div>
          <strong>Akses Wali Kelas:</strong> Menampilkan rekapitulasi data khusus untuk rombel binaan Anda: <strong style="color:#000000; font-weight:800;">{{ $waliRombelNama ?? 'Kelas Anda' }}</strong>.
        </div>
      </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- 1. EXECUTIVE KPI STAT CARDS (NO-PRINT) — DI ATAS FILTER -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="lp-kpi-grid no-print">
      {{-- Total Rekap --}}
      <div class="lp-kpi-card">
        <div class="lp-kpi-head">
          <span class="lp-kpi-title">Total Rekap</span>
          <div class="lp-kpi-icon" style="background:var(--bg-3); color:#000000; border:1px solid var(--border-2);">
            <i class="bi bi-folder-symlink-fill"></i>
          </div>
        </div>
        <div class="lp-kpi-val" style="color:#000000;">{{ $totalRecord }}</div>
      </div>

      {{-- Hadir Tepat --}}
      <div class="lp-kpi-card">
        <div class="lp-kpi-head">
          <span class="lp-kpi-title">Hadir Tepat</span>
          <div class="lp-kpi-icon" style="background:var(--bg-3); color:#000000; border:1px solid var(--border-2);">
            <i class="bi bi-check-circle-fill"></i>
          </div>
        </div>
        <div class="lp-kpi-val" style="color:#000000;">{{ $totalHadir }}</div>
      </div>

      {{-- Terlambat --}}
      <div class="lp-kpi-card">
        <div class="lp-kpi-head">
          <span class="lp-kpi-title">Terlambat</span>
          <div class="lp-kpi-icon" style="background:var(--bg-3); color:#000000; border:1px solid var(--border-2);">
            <i class="bi bi-clock-history"></i>
          </div>
        </div>
        <div class="lp-kpi-val" style="color:#000000;">{{ $totalTerlambat }}</div>
      </div>

      {{-- Izin / Sakit --}}
      <div class="lp-kpi-card">
        <div class="lp-kpi-head">
          <span class="lp-kpi-title">Izin / Sakit</span>
          <div class="lp-kpi-icon" style="background:var(--bg-3); color:#000000; border:1px solid var(--border-2);">
            <i class="bi bi-envelope-open-fill"></i>
          </div>
        </div>
        <div class="lp-kpi-val" style="color:#000000;">{{ $totalIzin }}</div>
      </div>

      {{-- Alpha --}}
      <div class="lp-kpi-card">
        <div class="lp-kpi-head">
          <span class="lp-kpi-title">Alpha</span>
          <div class="lp-kpi-icon" style="background:var(--bg-3); color:#000000; border:1px solid var(--border-2);">
            <i class="bi bi-exclamation-triangle-fill"></i>
          </div>
        </div>
        <div class="lp-kpi-val" style="color:#000000;">{{ $totalAlpha }}</div>
      </div>

      {{-- Bolos --}}
      <div class="lp-kpi-card">
        <div class="lp-kpi-head">
          <span class="lp-kpi-title">Bolos</span>
          <div class="lp-kpi-icon" style="background:var(--bg-3); color:#000000; border:1px solid var(--border-2);">
            <i class="bi bi-x-octagon-fill"></i>
          </div>
        </div>
        <div class="lp-kpi-val" style="color:#000000;">{{ $totalBolos }}</div>
      </div>

      {{-- Kehadiran % --}}
      <div class="lp-kpi-card">
        <div class="lp-kpi-head">
          <span class="lp-kpi-title">Kehadiran</span>
          <div class="lp-kpi-icon" style="background:var(--bg-3); color:#000000; border:1px solid var(--border-2);">
            <i class="bi bi-pie-chart-fill"></i>
          </div>
        </div>
        <div class="lp-kpi-val" style="color:#000000;">{{ $persentase }}%</div>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- 2. PANEL FILTER & KONTROL PERIODE (NO-PRINT) — DI BAWAH KPI -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="lp-filter-panel no-print">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:18px; padding-bottom:14px; border-bottom:1px solid var(--border);">
        <div style="font-weight:800; font-size:12.5px; text-transform:uppercase; letter-spacing:0.8px; color:var(--text); display:flex; align-items:center; gap:8px;">
          <i class="bi bi-sliders" style="color:#000000; font-size:15px;"></i> Parameter Rentang Waktu &amp; Filter Data
        </div>

        <!-- Periode Chip Buttons -->
        <div style="display:flex; gap:6px; flex-wrap:wrap;">
          <a href="/laporan?kategori={{ $kategori }}&periode=harian" class="period-chip {{ $periode === 'harian' ? 'active' : '' }}">
            <i class="bi bi-calendar-day"></i> Harian
          </a>
          <a href="/laporan?kategori={{ $kategori }}&periode=mingguan" class="period-chip {{ $periode === 'mingguan' ? 'active' : '' }}">
            <i class="bi bi-calendar-week"></i> Mingguan
          </a>
          <a href="/laporan?kategori={{ $kategori }}&periode=bulanan" class="period-chip {{ $periode === 'bulanan' ? 'active' : '' }}">
            <i class="bi bi-calendar-month"></i> Bulanan
          </a>
          <a href="/laporan?kategori={{ $kategori }}&periode=tahunan" class="period-chip {{ $periode === 'tahunan' ? 'active' : '' }}">
            <i class="bi bi-calendar3"></i> Tahunan
          </a>
          <a href="/laporan?kategori={{ $kategori }}&periode=individu" class="period-chip {{ $periode === 'individu' ? 'active' : '' }}">
            <i class="bi bi-person-lines-fill"></i> Per Individu
          </a>
        </div>
      </div>

      <form action="/laporan" method="GET">
        <input type="hidden" name="kategori" value="{{ $kategori }}" />
        <input type="hidden" name="periode" value="{{ $periode }}" />

        <div class="form-row" style="align-items:flex-end; gap:16px;">
          @if($periode === 'harian')
            <div class="form-group" style="flex:1; min-width:200px; margin-bottom:0;">
              <label style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-2); margin-bottom:6px; display:block;">Pilih Tanggal <span style="color:var(--red);">*</span></label>
              <input type="date" name="tanggal" value="{{ $tanggal }}" style="width:100%; height:42px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px; color:var(--text); font-size:13px; font-weight:700; font-family:var(--font-mono);" />
            </div>
          @elseif($periode === 'mingguan' || $periode === 'individu')
            <div class="form-group" style="flex:1; min-width:180px; margin-bottom:0;">
              <label style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-2); margin-bottom:6px; display:block;">Tanggal Mulai <span style="color:var(--red);">*</span></label>
              <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" style="width:100%; height:42px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px; color:var(--text); font-size:13px; font-weight:700; font-family:var(--font-mono);" />
            </div>
            <div class="form-group" style="flex:1; min-width:180px; margin-bottom:0;">
              <label style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-2); margin-bottom:6px; display:block;">Tanggal Selesai <span style="color:var(--red);">*</span></label>
              <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}" style="width:100%; height:42px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px; color:var(--text); font-size:13px; font-weight:700; font-family:var(--font-mono);" />
            </div>
          @elseif($periode === 'bulanan')
            <div class="form-group" style="flex:1; min-width:200px; margin-bottom:0;">
              <label style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-2); margin-bottom:6px; display:block;">Pilih Bulan &amp; Tahun <span style="color:var(--red);">*</span></label>
              <input type="month" name="bulan" value="{{ $bulan }}" style="width:100%; height:42px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px; color:var(--text); font-size:13px; font-weight:700; font-family:var(--font-mono);" />
            </div>
          @elseif($periode === 'tahunan')
            <div class="form-group" style="flex:1; min-width:200px; margin-bottom:0;">
              <label style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-2); margin-bottom:6px; display:block;">Pilih Tahun <span style="color:var(--red);">*</span></label>
              <select name="tahun" style="width:100%; height:42px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px; color:var(--text); font-size:13px; font-weight:700; font-family:var(--font-mono);">
                @for($y = date('Y') + 1; $y >= 2024; $y--)
                  <option value="{{ $y }}" @if($tahun == $y) selected @endif>Tahun {{ $y }}</option>
                @endfor
              </select>
            </div>
          @endif

          <!-- Filter Spesifik Siswa / Rombel -->
          @if($kategori === 'siswa')
            @if($periode !== 'individu')
              <div class="form-group" style="flex:1; min-width:220px; margin-bottom:0;">
                <label style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-2); margin-bottom:6px; display:block;">Filter Rombel Kelas</label>
                @if($isWaliKelas ?? false)
                  <select name="rombel_id" style="width:100%; height:42px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px; color:var(--text); font-size:13px; font-weight:700; cursor:not-allowed; opacity:0.85;" disabled title="Terkunci untuk Wali Kelas">
                    <option value="{{ $waliRombelId }}">{{ $waliRombelNama ?? 'Rombel Anda' }} (Terkunci)</option>
                  </select>
                  <input type="hidden" name="rombel_id" value="{{ $waliRombelId }}" />
                @else
                  <select name="rombel_id" style="width:100%; height:42px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px; color:var(--text); font-size:13px; font-weight:700;">
                    <option value="">-- Semua Rombel Kelas --</option>
                    @foreach($rombels as $r)
                      <option value="{{ $r->id }}" @if($rombelId == $r->id) selected @endif>{{ $r->nama_rombel }}</option>
                    @endforeach
                  </select>
                @endif
              </div>
            @else
              @php
                $selectedSiswa = $siswaId ? $siswas->firstWhere('id', $siswaId) : null;
                $selectedSiswaRombel = ($selectedSiswa && $selectedSiswa->siswaRombels && $selectedSiswa->siswaRombels->first() && $selectedSiswa->siswaRombels->first()->rombel) ? $selectedSiswa->siswaRombels->first()->rombel->nama_rombel : '';
              @endphp
              <div class="form-group lp-picker-wrap" style="flex:1.5; min-width:260px; margin-bottom:0;">
                <label style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-2); margin-bottom:6px; display:block;">PILIH SISWA SPESIFIK <span style="color:var(--red);">*</span></label>
                <input type="hidden" name="siswa_id" id="inputLpSiswaId" value="{{ $siswaId }}" required />
                
                <div id="lpSiswaTrigger" class="lp-picker-trigger" onclick="toggleLpSiswaDropdown()">
                  <div id="lpSiswaSelectedView" style="{{ $selectedSiswa ? 'display:flex;' : 'display:none;' }} align-items:center; justify-content:space-between; width:100%;">
                    <div style="display:flex; align-items:center; gap:8px; overflow:hidden;">
                      <img id="lpSiswaFoto" src="{{ $selectedSiswa ? $selectedSiswa->foto_url : '/img/user-default.png' }}" alt="Foto" style="width:26px; height:26px; border-radius:50%; object-fit:cover; border:1.5px solid rgba(0,0,0,0.15); flex-shrink:0;" />
                      <div style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        <strong id="lpSiswaNama" style="color:var(--text); font-size:13px;">{{ $selectedSiswa ? $selectedSiswa->nama : '' }}</strong>
                        <span id="lpSiswaMeta" style="font-size:11px; color:#000000; font-family:var(--font-mono); margin-left:6px; font-weight:700;">{{ $selectedSiswa ? 'NISN: ' . ($selectedSiswa->nisn ?: '-') . ($selectedSiswaRombel ? ' · ' . $selectedSiswaRombel : '') : '' }}</span>
                      </div>
                    </div>
                    <button type="button" class="btn-clear-lp" onclick="clearLpSiswa(event)" title="Ganti Siswa">
                      <i class="bi bi-x-circle-fill"></i>
                    </button>
                  </div>
                  <div id="lpSiswaPlaceholder" style="{{ $selectedSiswa ? 'display:none;' : 'display:flex;' }} align-items:center; gap:8px; color:var(--text-3); font-size:12.5px;">
                    <i class="bi bi-search" style="color:#000000;"></i>
                    <span>Ketik nama, NISN, atau kelas...</span>
                  </div>
                </div>

                <!-- Dropdown List Panel -->
                <div id="lpSiswaDropdown" class="lp-dropdown-panel">
                  <div style="padding:8px 10px; border-bottom:1px solid var(--border-2); background:var(--bg-3);">
                    <div style="position:relative;">
                      <i class="bi bi-search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#000000; font-size:12px;"></i>
                      <input type="text" id="lpSiswaSearchBox" placeholder="Ketik nama, NISN, kelas..." oninput="filterLpSiswaList(this.value)" style="width:100%; padding-left:32px; height:34px; font-size:12px; border-radius:var(--r-sm);" autocomplete="off" />
                    </div>
                  </div>
                  <div id="lpSiswaListContainer" style="max-height:260px; overflow-y:auto;">
                    @foreach($siswas as $s)
                      @php
                        $rombelNama = ($s->siswaRombels && $s->siswaRombels->first() && $s->siswaRombels->first()->rombel) ? $s->siswaRombels->first()->rombel->nama_rombel : '';
                      @endphp
                      <div class="lp-picker-item lp-item-siswa" 
                           data-id="{{ $s->id }}" 
                           data-nama="{{ strtolower($s->nama) }}" 
                           data-nisn="{{ strtolower($s->nisn ?? '') }}" 
                           data-rombel="{{ strtolower($rombelNama) }}"
                           onclick="selectLpSiswa('{{ $s->id }}', '{{ addslashes($s->nama) }}', '{{ $s->nisn ?: '-' }}', '{{ $rombelNama }}', '{{ $s->foto_url }}')">
                        <div style="display:flex; align-items:center; gap:8px;">
                          <div class="avatar-circle avatar-sm gold-border">
                            <img src="{{ $s->foto_url }}" alt="{{ $s->nama }}" class="avatar-img" />
                          </div>
                          <div>
                            <div style="font-weight:700; font-size:12.5px; color:var(--text);">{{ $s->nama }}</div>
                            <div style="font-size:11px; color:var(--text-3); font-family:var(--font-mono);">NISN: {{ $s->nisn ?: '-' }}</div>
                          </div>
                        </div>
                        @if($rombelNama)
                          <span class="badge" style="background:rgba(0,0,0,0.06); color:#000000; border:1px solid rgba(0,0,0,0.12); font-size:10.5px; padding:1px 6px; font-weight:800;">
                            {{ $rombelNama }}
                          </span>
                        @endif
                      </div>
                    @endforeach
                    <div id="lpSiswaEmptyMsg" style="display:none; padding:16px; text-align:center; color:var(--text-3); font-size:12px;">
                      <i class="bi bi-search" style="margin-right:4px;"></i> Siswa tidak ditemukan
                    </div>
                  </div>
                </div>
              </div>
            @endif
          @else
            <!-- Filter Spesifik Guru -->
            @if($periode === 'individu')
              @php
                $selectedGuru = $guruId ? $gurus->firstWhere('id', $guruId) : null;
              @endphp
              <div class="form-group lp-picker-wrap" style="flex:1.5; min-width:260px; margin-bottom:0;">
                <label style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-2); margin-bottom:6px; display:block;">PILIH GURU SPESIFIK <span style="color:var(--red);">*</span></label>
                <input type="hidden" name="guru_id" id="inputLpGuruId" value="{{ $guruId }}" required />
                
                <div id="lpGuruTrigger" class="lp-picker-trigger" onclick="toggleLpGuruDropdown()">
                  <div id="lpGuruSelectedView" style="{{ $selectedGuru ? 'display:flex;' : 'display:none;' }} align-items:center; justify-content:space-between; width:100%;">
                    <div style="display:flex; align-items:center; gap:8px; overflow:hidden;">
                      <img id="lpGuruFoto" src="{{ $selectedGuru ? ($selectedGuru->foto_url ?? '/img/user-default.png') : '/img/user-default.png' }}" alt="Foto" style="width:26px; height:26px; border-radius:50%; object-fit:cover; border:1.5px solid rgba(0,0,0,0.15); flex-shrink:0;" />
                      <div style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        <strong id="lpGuruNama" style="color:var(--text); font-size:13px;">{{ $selectedGuru ? $selectedGuru->nama : '' }}</strong>
                        <span id="lpGuruMeta" style="font-size:11px; color:#000000; font-family:var(--font-mono); margin-left:6px; font-weight:700;">{{ $selectedGuru ? ($selectedGuru->nip ? 'NIP: ' . $selectedGuru->nip : ($selectedGuru->jabatan ?? 'Guru')) : '' }}</span>
                      </div>
                    </div>
                    <button type="button" class="btn-clear-lp" onclick="clearLpGuru(event)" title="Ganti Guru">
                      <i class="bi bi-x-circle-fill"></i>
                    </button>
                  </div>
                  <div id="lpGuruPlaceholder" style="{{ $selectedGuru ? 'display:none;' : 'display:flex;' }} align-items:center; gap:8px; color:var(--text-3); font-size:12.5px;">
                    <i class="bi bi-search" style="color:#000000;"></i>
                    <span>Ketik nama guru, NIP, atau jabatan...</span>
                  </div>
                </div>

                <!-- Dropdown List Panel -->
                <div id="lpGuruDropdown" class="lp-dropdown-panel">
                  <div style="padding:8px 10px; border-bottom:1px solid var(--border-2); background:var(--bg-3);">
                    <div style="position:relative;">
                      <i class="bi bi-search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#000000; font-size:12px;"></i>
                      <input type="text" id="lpGuruSearchBox" placeholder="Ketik nama guru, NIP, jabatan..." oninput="filterLpGuruList(this.value)" style="width:100%; padding-left:32px; height:34px; font-size:12px; border-radius:var(--r-sm);" autocomplete="off" />
                    </div>
                  </div>
                  <div id="lpGuruListContainer" style="max-height:260px; overflow-y:auto;">
                    @foreach($gurus as $g)
                      <div class="lp-picker-item lp-item-guru" 
                           data-id="{{ $g->id }}" 
                           data-nama="{{ strtolower($g->nama) }}" 
                           data-nip="{{ strtolower($g->nip ?? '') }}" 
                           data-jabatan="{{ strtolower($g->jabatan ?? '') }}"
                           onclick="selectLpGuru('{{ $g->id }}', '{{ addslashes($g->nama) }}', '{{ $g->nip ?? '-' }}', '{{ $g->jabatan ?? 'Guru' }}', '{{ $g->foto_url ?? '/img/user-default.png' }}')">
                        <div style="display:flex; align-items:center; gap:8px;">
                          <div class="avatar-circle avatar-sm gold-border">
                            <img src="{{ $g->foto_url ?? '/img/user-default.png' }}" alt="{{ $g->nama }}" class="avatar-img" />
                          </div>
                          <div>
                            <div style="font-weight:700; font-size:12.5px; color:var(--text);">{{ $g->nama }}</div>
                            <div style="font-size:11px; color:var(--text-3); font-family:var(--font-mono);">NIP: {{ $g->nip ?? 'Non-NIP' }}</div>
                          </div>
                        </div>
                        <span class="badge" style="background:var(--green-dim); color:var(--green); border:1px solid rgba(22,163,74,0.2); font-size:10.5px; padding:1px 6px;">
                          {{ $g->jabatan ?? 'Guru' }}
                        </span>
                      </div>
                    @endforeach
                    <div id="lpGuruEmptyMsg" style="display:none; padding:16px; text-align:center; color:var(--text-3); font-size:12px;">
                      <i class="bi bi-search" style="margin-right:4px;"></i> Guru tidak ditemukan
                    </div>
                  </div>
                </div>
              </div>
            @endif
          @endif

          <div style="display:flex; gap:8px; align-items:flex-end;">
            <button type="submit" class="btn btn-gold" style="height:42px; padding:0 20px; font-weight:800; display:inline-flex; align-items:center; justify-content:center; gap:8px;">
              <i class="bi bi-funnel-fill"></i> Tampilkan Data
            </button>
            <a href="/laporan?kategori={{ $kategori }}&periode={{ $periode }}" class="btn btn-outline" style="height:42px; padding:0 14px; display:inline-flex; align-items:center; justify-content:center; font-size:12.5px; font-weight:700;" title="Reset Filter" data-tooltip="Reset Filter">
              <i class="bi bi-arrow-counterclockwise"></i> Reset
            </a>
          </div>
        </div>
      </form>
    </div>

    @if(in_array($periode, ['mingguan', 'bulanan', 'tahunan']))
      <!-- ═══════════════════════════════════════════════════════════════════ -->
      <!-- TABEL REKAP AGREGAT PERIODE (MINGGUAN / BULANAN / TAHUNAN) -->
      <!-- ═══════════════════════════════════════════════════════════════════ -->
      <div class="panel" style="padding:0; overflow:hidden; border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-sm); background:var(--bg-2); margin-bottom:24px;">
        <div class="panel-title no-print" style="padding:14px 18px; margin:0; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
          <div style="font-weight:800; font-size:15px; color:var(--text); display:flex; align-items:center; gap:8px;">
            <i class="bi bi-file-earmark-spreadsheet-fill" style="color:#000000;"></i>
            <span>Rekapitulasi {{ ucfirst($kategori) }} ({{ ucfirst($periode) }})</span>
          </div>
          <div>
            <a href="{{ route('laporan.cetak-pdf', request()->query()) }}" target="_blank" class="btn btn-outline" style="height:36px; font-size:12px; font-weight:800; color:#000000; border:1.5px solid #000000; background:var(--bg-2); display:inline-flex; align-items:center; gap:6px;" title="Buka Dokumen PDF Resmi">
              <i class="bi bi-file-earmark-pdf-fill" style="color:#000000;"></i> Cetak PDF
            </a>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th style="width:36px; text-align:center;">No</th>
                @if($kategori === 'siswa')
                  <th style="width:110px; text-align:center;">NISN</th>
                  <th style="text-align:left;">Nama Siswa</th>
                  <th style="width:120px; text-align:center;">Kelas</th>
                @else
                  <th style="width:110px; text-align:center;">NIP</th>
                  <th style="text-align:left;">Nama Guru / Pegawai</th>
                  <th style="width:140px; text-align:left;">Jabatan</th>
                @endif
                <th style="width:60px; text-align:center;">Hadir</th>
                <th style="width:60px; text-align:center;">Telat</th>
                <th style="width:60px; text-align:center;">Sakit</th>
                <th style="width:60px; text-align:center;">Izin</th>
                <th style="width:60px; text-align:center;">Alpha</th>
                <th style="width:60px; text-align:center;">Bolos</th>
                <th style="width:70px; text-align:center;">Total</th>
                <th style="width:80px; text-align:center;">% Hadir</th>
                <th style="width:85px; text-align:center;" class="no-print">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($rekapData as $i => $item)
                @php
                  $totalH = $item->total_hadir + $item->total_telat;
                  $pct = $item->total_hari > 0 ? round(($totalH / $item->total_hari) * 100, 1) : 0;
                @endphp
                <tr>
                  <td style="text-align:center; font-weight:700; color:var(--text-3); font-size:12px;">
                    {{ $rekapData->firstItem() + $i }}
                  </td>
                  @if($kategori === 'siswa')
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:700; color:#000000; font-size:12px;">
                      {{ $item->nisn ?? '-' }}
                    </td>
                    <td>
                      <strong style="color:var(--text); font-size:13.5px;">{{ $item->nama }}</strong>
                    </td>
                    <td style="text-align:center; font-weight:800; color:var(--text); font-size:13px;">
                      {{ $item->rombel }}
                    </td>
                  @else
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:700; color:#000000; font-size:12px;">
                      {{ $item->nip ?: '-' }}
                    </td>
                    <td>
                      <strong style="color:var(--text); font-size:13.5px;">{{ $item->nama }}</strong>
                      @if(isset($item->jenis_kepegawaian) && $item->jenis_kepegawaian === 'honor')
                        <div style="margin-top:3px;">
                          <span style="color:#CA8A04; font-size:10.5px; font-weight:700; display:inline-flex; align-items:center; gap:4px;">
                            <i class="bi bi-clock-history"></i> GTT / Honor ({{ !empty($item->hari_mengajar) ? implode(', ', $item->hari_mengajar) : 'Senin-Jumat' }})
                          </span>
                        </div>
                      @endif
                    </td>
                    <td style="font-size:12px; color:var(--text-2);">{{ $item->jabatan }}</td>
                  @endif
                  <td style="text-align:center; font-weight:700; color:var(--text); font-family:var(--font-mono); font-size:12.5px;">{{ $item->total_hadir }}</td>
                  <td style="text-align:center; font-weight:700; color:var(--text); font-family:var(--font-mono); font-size:12.5px;">{{ $item->total_telat }}</td>
                  <td style="text-align:center; font-weight:700; color:var(--text); font-family:var(--font-mono); font-size:12.5px;">{{ $item->total_sakit ?? 0 }}</td>
                  <td style="text-align:center; font-weight:700; color:var(--text); font-family:var(--font-mono); font-size:12.5px;">{{ $item->total_izin }}</td>
                  <td style="text-align:center; font-weight:700; color:var(--text); font-family:var(--font-mono); font-size:12.5px;">{{ $item->total_alpha }}</td>
                  <td style="text-align:center; font-weight:700; color:var(--text); font-family:var(--font-mono); font-size:12.5px;">{{ $item->total_bolos }}</td>
                  <td style="text-align:center; font-weight:800; font-size:13px; color:var(--text); font-family:var(--font-mono);">{{ $item->total_hari }}</td>
                  <td style="text-align:center; font-weight:800; font-size:13px; color:var(--text); font-family:var(--font-mono);">
                    {{ $pct }}%
                  </td>
                  <td style="text-align:center;" class="no-print">
                    <div style="display:flex; gap:4px; justify-content:center;">
                      @if($kategori === 'siswa')
                        <a href="/laporan?kategori=siswa&periode=individu&siswa_id={{ $item->id }}" class="btn-icon btn-icon-edit" data-tooltip="Buka Rekap Presensi Lengkap" title="Buka Rekap Presensi Lengkap">
                          <i class="bi bi-person-lines-fill"></i>
                        </a>
                        <a href="/portal-siswa/{{ $item->nisn ?: $item->id }}" target="_blank" class="btn-icon btn-icon-edit" data-tooltip="Lihat Portal Mandiri Siswa" title="Lihat Portal Mandiri Siswa">
                          <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                      @else
                        <a href="/laporan?kategori=guru&periode=individu&guru_id={{ $item->id }}" class="btn-icon btn-icon-edit" data-tooltip="Buka Rekap Guru Lengkap" title="Buka Rekap Guru Lengkap">
                          <i class="bi bi-person-lines-fill"></i>
                        </a>
                      @endif
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="12" style="text-align:center; padding:30px; color:var(--text-3);">
                    <i class="bi bi-inbox" style="font-size:24px; display:block; margin-bottom:6px;"></i>
                    Tidak ada data presensi ditemukan untuk filter dan periode ini.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($rekapData->hasPages())
          <div style="padding:14px 18px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <div style="font-size:12.5px; color:var(--text-3);">
              Menampilkan {{ $rekapData->firstItem() }} s/d {{ $rekapData->lastItem() }} dari {{ $rekapData->total() }} data
            </div>
            <div>
              {{ $rekapData->links() }}
            </div>
          </div>
        @endif
      </div>

    @elseif($periode === 'individu')
      <!-- ═══════════════════════════════════════════════════════════════════ -->
      <!-- TABEL RINCIAN DETAIL INDIVIDU -->
      <!-- ═══════════════════════════════════════════════════════════════════ -->
      @if($selectedIndividu)
        <!-- Profil Web -->
        <div class="panel no-print" style="margin-bottom: 20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;">
          <div style="display:flex; align-items:center; gap:14px;">
            <div class="avatar-circle avatar-lg gold-border">
              <img src="{{ $selectedIndividu->foto_url ?? '/img/logo.png' }}" alt="Foto" class="avatar-img" />
            </div>
            <div>
              <div style="font-size:16px; font-weight:800; color:var(--text);">{{ $selectedIndividu->nama }}</div>
              <div style="font-size:12px; color:var(--text-3); font-family:var(--font-mono); margin-top:2px;">
                {{ $kategori === 'siswa' ? 'NISN: ' . ($selectedIndividu->nisn ?: '-') . ' · Kelas: ' . ($selectedIndividu->siswaRombels->first()?->rombel?->nama_rombel ?? '-') : 'NIP: ' . ($selectedIndividu->nip ?? '-') . ' · Jabatan: ' . ($selectedIndividu->jabatan ?? '-') }}
              </div>
            </div>
          </div>
          <div style="display:flex; gap:12px; font-family:var(--font-mono); font-size:12px; background:var(--surface); padding:8px 16px; border-radius:var(--r-sm); border:1px solid var(--border);">
            <span style="color:var(--text); font-weight:800;">Hadir: {{ $totalHadir }}</span>
            <span style="color:var(--text); font-weight:800;">Telat: {{ $totalTerlambat }}</span>
            <span style="color:var(--text); font-weight:800;">Izin: {{ $totalIzin }}</span>
            <span style="color:var(--text); font-weight:800;">Alpha: {{ $totalAlpha }}</span>
            <span style="color:var(--text); font-weight:800;">Bolos: {{ $totalBolos }}</span>
          </div>
        </div>

        <!-- Profil Ringkas Print -->
        <div class="print-only" style="margin-bottom: 10px; font-size: 9.5pt;">
          <table style="width: 100%; border: none !important; margin-bottom: 8px;">
            <tr style="border: none !important;">
              <td style="border: none !important; width: 14%; font-weight: 700; padding: 2px 0;">Nama {{ ucfirst($kategori) }}</td>
              <td style="border: none !important; width: 36%; padding: 2px 0;">: {{ $selectedIndividu->nama }}</td>
              <td style="border: none !important; width: 14%; font-weight: 700; padding: 2px 0;">{{ $kategori === 'siswa' ? 'Kelas' : 'Jabatan' }}</td>
              <td style="border: none !important; width: 36%; padding: 2px 0;">: {{ $kategori === 'siswa' ? ($selectedIndividu->siswaRombels->first()?->rombel?->nama_rombel ?? '-') : ($selectedIndividu->jabatan ?? '-') }}</td>
            </tr>
            <tr style="border: none !important;">
              <td style="border: none !important; font-weight: 700; padding: 2px 0;">{{ $kategori === 'siswa' ? 'NISN' : 'NIP' }}</td>
              <td style="border: none !important; padding: 2px 0;">: {{ $kategori === 'siswa' ? ($selectedIndividu->nisn ?: '-') : ($selectedIndividu->nip ?? '-') }}</td>
              <td style="border: none !important; font-weight: 700; padding: 2px 0;">Total Kehadiran</td>
              <td style="border: none !important; padding: 2px 0;">: Hadir: {{ $totalHadir }}, Telat: {{ $totalTerlambat }}, Izin: {{ $totalIzin }}, Alpha: {{ $totalAlpha }}, Bolos: {{ $totalBolos }}</td>
            </tr>
          </table>
        </div>
      @endif

      <div class="panel" style="padding:0; overflow:hidden;">
        <div class="panel-title no-print" style="padding:14px 18px; margin:0; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
          <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
            <span style="font-weight:800; font-size:14px; color:var(--text); display:inline-flex; align-items:center; gap:8px;">
              <i class="bi bi-journal-text" style="color:#000000;"></i> Rincian Riwayat Presensi Individu
            </span>
          </div>
          <div>
            <a href="{{ route('laporan.cetak-pdf', request()->query()) }}" target="_blank" class="btn btn-outline" style="height:36px; font-size:12px; font-weight:800; color:#000000; border:1.5px solid #000000; background:var(--bg-2); display:inline-flex; align-items:center; gap:6px;" title="Buka Dokumen PDF Resmi">
              <i class="bi bi-file-earmark-pdf-fill" style="color:#000000;"></i> Cetak PDF
            </a>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th style="width:36px; text-align:center;">No</th>
                <th style="width:115px; min-width:115px; text-align:center; white-space:nowrap;">Tanggal</th>
                <th style="width:75px; text-align:center;">Hari</th>
                <th style="width:65px; text-align:center;">Masuk</th>
                <th style="width:65px; text-align:center;">Pulang</th>
                <th style="width:90px; text-align:center;">Status</th>
                <th style="text-align:left;">Keterangan / Alasan</th>
                <th style="width:120px; text-align:left;" class="no-print">Sumber Log</th>
                <th style="width:80px; text-align:center;" class="no-print">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($laporans as $i => $lap)
                @php
                  $carbonTgl = \Illuminate\Support\Carbon::parse($lap->tanggal);
                  $izin = $izinMap->get($lap->tanggal);
                  $nama = $selectedIndividu->nama ?? 'Anggota';
                @endphp
                <tr>
                  <td style="text-align:center; font-weight:700; color:var(--text-3); font-size:12px;">
                    {{ $laporans->firstItem() + $i }}
                  </td>
                  <td style="text-align:center; font-family:var(--font-mono); font-size:12px; font-weight:700; white-space:nowrap;">{{ $lap->tanggal }}</td>
                  <td style="text-align:center; font-size:12px; color:var(--text-2);">{{ $carbonTgl->translatedFormat('l') }}</td>
                  <td style="text-align:center; font-family:var(--font-mono); font-size:12px; font-weight:700; color:var(--text);">
                    {{ $lap->jam_masuk ?? '-' }}
                  </td>
                  <td style="text-align:center; font-family:var(--font-mono); font-size:12px; font-weight:700; color:var(--text);">
                    {{ $lap->jam_pulang ?? '-' }}
                  </td>
                  <td style="text-align:center; white-space:nowrap;">
                    @if($lap->status === 'hadir')
                      <span class="table-status-pill hadir"><i class="bi bi-check-circle-fill"></i> Hadir</span>
                    @elseif($lap->status === 'terlambat')
                      <span class="table-status-pill terlambat"><i class="bi bi-clock-fill"></i> Terlambat</span>
                    @elseif(in_array($lap->status, ['izin', 'sakit', 'cuti', 'dispen', 'dispensasi']))
                      <span class="table-status-pill izin"><i class="bi bi-file-earmark-text-fill"></i> {{ ucfirst($lap->status) }}</span>
                    @elseif($lap->status === 'pkl')
                      <span class="table-status-pill pkl"><i class="bi bi-building"></i> PKL</span>
                    @elseif($lap->status === 'bolos')
                      <span class="table-status-pill bolos"><i class="bi bi-door-open-fill"></i> Bolos</span>
                    @else
                      <span class="table-status-pill belum"><i class="bi bi-x-circle-fill"></i> {{ ucfirst($lap->status) }}</span>
                    @endif
                  </td>
                  <td style="text-align:left; font-size:12px;">
                    @if($lap->keterangan)
                      <span style="font-weight:600; color:var(--text);">{{ $lap->keterangan }}</span>
                    @elseif($izin && $izin->keterangan)
                      <span>{{ $izin->keterangan }}</span>
                      @if($izin->disetujui_oleh)<small style="display:block; color:var(--text-3); font-size:10.5px;">(Disetujui: {{ $izin->disetujui_oleh }})</small>@endif
                    @elseif($lap->status === 'bolos')
                      <span style="color:#991B1B; font-weight:600;">Scan masuk tanpa tap pulang</span>
                    @elseif($lap->status === 'alpha')
                      <span style="color:#DC2626; font-weight:600;">Tanpa keterangan</span>
                    @else
                      <span style="color:var(--text-3);">-</span>
                    @endif
                  </td>
                  <td class="no-print" style="text-align:left; white-space:nowrap;">
                    @php
                      $isManual = in_array($lap->sumber_absen, ['manual_piket', 'manual_izin_piket', 'koreksi_piket_manual']);
                      $sumberLabel = $lap->sumber_absen_label ?? ($isManual ? 'Manual Piket' : 'Smart Gate');
                    @endphp
                    <span style="font-size:12px; font-weight:700; color:var(--text-2);">
                      {{ $sumberLabel }}
                    </span>
                  </td>
                  <td class="no-print" style="text-align:center;">
                    @php
                      $canKoreksi = $isPiketBertugasHariIni && ($lap->tanggal === $todayDate);
                    @endphp
                    @if($canKoreksi)
                      <div style="display:flex; gap:4px; justify-content:center;">
                        <button type="button" 
                          class="btn-icon btn-icon-edit btn-koreksi-modal" 
                          data-id="{{ $lap->id }}"
                          data-nama="{{ $nama }}"
                          data-tanggal="{{ $lap->tanggal }}"
                          data-status="{{ $lap->status }}"
                          data-masuk="{{ $lap->jam_masuk ?? '' }}"
                          data-pulang="{{ $lap->jam_pulang ?? '' }}"
                          data-sumber="{{ $lap->sumber_absen }}"
                          data-keterangan="{{ $lap->keterangan ?? ($izin->keterangan ?? '') }}"
                          data-tooltip="Koreksi Presensi" 
                          title="Koreksi Presensi">
                          <i class="bi bi-pencil-square"></i>
                        </button>
                        <form action="/laporan/{{ $lap->id }}" method="POST" onsubmit="return confirm('Hapus catatan presensi tanggal {{ $lap->tanggal }}?')" style="display:inline;">
                          @csrf @method('DELETE')
                          <button type="submit" class="btn-icon btn-icon-danger" data-tooltip="Hapus Presensi" title="Hapus Presensi">
                            <i class="bi bi-trash3-fill"></i>
                          </button>
                        </form>
                      </div>
                    @else
                      <span style="color:var(--text-3); font-size:11px;">-</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" style="text-align:center; padding:30px; color:var(--text-3);">
                    <i class="bi bi-inbox" style="font-size:24px; display:block; margin-bottom:6px;"></i>
                    Belum ada riwayat kehadiran individu pada rentang tanggal ini.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($laporans->hasPages())
          <div style="padding:14px 18px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <div style="font-size:12.5px; color:var(--text-3);">
              Menampilkan {{ $laporans->firstItem() }} s/d {{ $laporans->lastItem() }} dari {{ $laporans->total() }} data
            </div>
            <div>
              {{ $laporans->links() }}
            </div>
          </div>
        @endif
      </div>

    @else
      <!-- ═══════════════════════════════════════════════════════════════════ -->
      <!-- TABEL HARIAN -->
      <!-- ═══════════════════════════════════════════════════════════════════ -->
      <div class="panel" style="padding:0; overflow:hidden; border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-sm); background:var(--bg-2); margin-bottom:24px;">
        <div class="panel-title no-print" style="padding:14px 18px; margin:0; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
          <div style="font-weight:800; font-size:15px; color:var(--text); display:flex; align-items:center; gap:8px;">
            <i class="bi bi-table" style="color:#000000;"></i>
            <span>Rincian Presensi Harian</span>
          </div>
          <div>
            <a href="{{ route('laporan.cetak-pdf', request()->query()) }}" target="_blank" class="btn btn-outline" style="height:36px; font-size:12px; font-weight:800; color:#000000; border:1.5px solid #000000; background:var(--bg-2); display:inline-flex; align-items:center; gap:6px;" title="Buka Dokumen PDF Resmi">
              <i class="bi bi-file-earmark-pdf-fill" style="color:#000000;"></i> Cetak PDF
            </a>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th style="width:36px; text-align:center;">No</th>
                <th style="width:65px; text-align:center;">Masuk</th>
                <th style="width:65px; text-align:center;">Pulang</th>
                <th style="text-align:left;">Nama {{ ucfirst($kategori) }}</th>
                @if($kategori === 'siswa')
                  <th style="width:105px; text-align:center;">Kelas</th>
                @else
                  <th style="width:130px; text-align:left;">Jabatan</th>
                @endif
                <th style="width:95px; text-align:center;">Status</th>
                <th style="min-width:180px; text-align:left;">Alasan / Keterangan</th>
                <th style="width:115px; text-align:left;" class="no-print">Sumber Audit</th>
                <th style="width:85px; text-align:center;" class="no-print">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($laporans as $i => $lap)
                @php
                  $nama = $lap->pemilik_type === 'siswa'
                    ? ($lap->siswa->nama ?? ($lap->siswaRombel->siswa->nama ?? 'Siswa'))
                    : ($lap->guru->nama ?? (($guruMap->get($lap->pemilik_id))->nama ?? 'Guru'));
                  $nisn = $lap->pemilik_type === 'siswa'
                    ? ($lap->siswa->nisn ?? ($lap->siswaRombel->siswa->nisn ?? ''))
                    : '';
                  $rombelNama = $lap->pemilik_type === 'siswa'
                    ? ($lap->siswaRombel->rombel->nama_rombel ?? ($lap->siswa->siswaRombels->first()?->rombel?->nama_rombel ?? '-'))
                    : ($lap->guru->jabatan ?? (($guruMap->get($lap->pemilik_id))->jabatan ?? 'Pegawai'));
                  
                  $izin = $lap->pemilik_type === 'siswa' ? $izinMap->get($lap->pemilik_id) : null;
                  $keteranganText = $lap->keterangan ?: ($izin ? $izin->keterangan : null);
                @endphp
                <tr>
                  <td style="text-align:center; font-weight:700; color:var(--text-3); font-size:12px;">
                    {{ $laporans->firstItem() + $i }}
                  </td>
                  <td style="text-align:center; font-family:var(--font-mono); font-size:12px; font-weight:700; color:var(--text);">
                    {{ $lap->jam_masuk ?? '-' }}
                  </td>
                  <td style="text-align:center; font-family:var(--font-mono); font-size:12px; font-weight:700; color:var(--text);">
                    {{ $lap->jam_pulang ?? '-' }}
                  </td>
                  <td style="text-align:left;">
                    <strong style="color:var(--text); font-size:13.5px;">{{ $nama }}</strong>
                    @if($nisn)
                      <div style="font-size:11px; color:var(--text-3); font-family:var(--font-mono);">NISN: {{ $nisn }}</div>
                    @endif
                  </td>
                  <td style="{{ $lap->pemilik_type === 'siswa' ? 'text-align:center;' : 'text-align:left;' }}">
                    @if($lap->pemilik_type === 'siswa')
                      <span style="font-weight:800; color:var(--text); font-size:12.5px;">
                        {{ $rombelNama }}
                      </span>
                    @else
                      <span style="font-size:12px; color:var(--text-2);">{{ $rombelNama }}</span>
                    @endif
                  </td>
                  <td style="text-align:center; white-space:nowrap;">
                    @if($lap->status === 'hadir')
                      <span class="table-status-pill hadir"><i class="bi bi-check-circle-fill"></i> Hadir</span>
                    @elseif($lap->status === 'terlambat')
                      <span class="table-status-pill terlambat"><i class="bi bi-clock-fill"></i> Terlambat</span>
                    @elseif(in_array($lap->status, ['izin', 'sakit', 'cuti', 'dispen', 'dispensasi']))
                      <span class="table-status-pill izin"><i class="bi bi-file-earmark-text-fill"></i> {{ ucfirst($lap->status) }}</span>
                    @elseif($lap->status === 'pkl')
                      <span class="table-status-pill pkl"><i class="bi bi-building"></i> PKL</span>
                    @elseif($lap->status === 'bolos')
                      <span class="table-status-pill bolos"><i class="bi bi-door-open-fill"></i> Bolos</span>
                    @else
                      <span class="table-status-pill belum"><i class="bi bi-x-circle-fill"></i> {{ ucfirst($lap->status) }}</span>
                    @endif
                  </td>
                  <td style="text-align:left; font-size:12px;">
                    @if($keteranganText)
                      <div style="font-weight:600; color:var(--text);">
                        <span>{{ $keteranganText }}</span>
                      </div>
                      @if($izin && $izin->disetujui_oleh)
                        <small style="display:block; color:var(--text-3); font-size:10.5px; margin-top:2px;">(Piket: {{ $izin->disetujui_oleh }})</small>
                      @endif
                    @elseif($lap->status === 'bolos')
                      <span style="color:#991B1B; font-weight:600; font-size:11.5px;">Tanpa tap pulang</span>
                    @elseif($lap->status === 'alpha')
                      <span style="color:#DC2626; font-weight:600; font-size:11.5px;">Tanpa keterangan</span>
                    @else
                      <span style="color:var(--text-3); font-size:11.5px;">-</span>
                    @endif
                  </td>
                  <td class="no-print" style="text-align:left; white-space:nowrap;">
                    @php
                      $isManual = in_array($lap->sumber_absen, ['manual_piket', 'manual_izin_piket', 'koreksi_piket_manual']);
                      $sumberLabel = $lap->sumber_absen_label ?? ($isManual ? 'Manual Piket' : 'Smart Gate');
                    @endphp
                    <span style="font-size:12px; font-weight:700; color:var(--text-2);">
                      {{ $sumberLabel }}
                    </span>
                  </td>
                  <td class="no-print" style="text-align:center;">
                    @php
                      $canKoreksi = $isPiketBertugasHariIni && ($lap->tanggal === $todayDate);
                    @endphp
                    <div style="display:flex; gap:4px; justify-content:center;">
                      @if($canKoreksi)
                        <button type="button" 
                          class="btn-icon btn-icon-edit btn-koreksi-modal" 
                          data-id="{{ $lap->id }}"
                          data-nama="{{ $nama }}"
                          data-tanggal="{{ $lap->tanggal }}"
                          data-status="{{ $lap->status }}"
                          data-masuk="{{ $lap->jam_masuk ?? '' }}"
                          data-pulang="{{ $lap->jam_pulang ?? '' }}"
                          data-sumber="{{ $lap->sumber_absen }}"
                          data-keterangan="{{ $keteranganText ?? '' }}"
                          data-tooltip="Koreksi Presensi" 
                          title="Koreksi Presensi">
                          <i class="bi bi-pencil-square"></i>
                        </button>
                      @endif
                      @if($lap->pemilik_type === 'siswa')
                        <a href="/portal-siswa/{{ $nisn ?: $lap->pemilik_id }}" target="_blank" class="btn-icon btn-icon-edit" data-tooltip="Portal Rekap Siswa" title="Portal Rekap Siswa">
                          <i class="bi bi-person-lines-fill"></i>
                        </a>
                      @endif
                      @if($canKoreksi)
                        <form action="/laporan/{{ $lap->id }}" method="POST" onsubmit="return confirm('Hapus catatan presensi {{ addslashes($nama) }}?')" style="display:inline;">
                          @csrf @method('DELETE')
                          <button type="submit" class="btn-icon btn-icon-danger" data-tooltip="Hapus Presensi" title="Hapus Presensi">
                            <i class="bi bi-trash3-fill"></i>
                          </button>
                        </form>
                      @endif
                      @if(!$canKoreksi && !($lap->pemilik_type === 'siswa'))
                        <span style="color:var(--text-3); font-size:11px;">-</span>
                      @endif
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" style="text-align:center; padding:30px; color:var(--text-3);">
                    <i class="bi bi-inbox" style="font-size:24px; display:block; margin-bottom:6px;"></i>
                    Tidak ada data presensi ditemukan untuk filter dan periode ini.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($laporans->hasPages())
          <div style="padding:14px 18px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <div style="font-size:12.5px; color:var(--text-3);">
              Menampilkan {{ $laporans->firstItem() }} s/d {{ $laporans->lastItem() }} dari {{ $laporans->total() }} data
            </div>
            <div>
              {{ $laporans->links() }}
            </div>
          </div>
        @endif
      </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- TANDA TANGAN PENGESAHAN CETAK (PRINT / PDF ONLY) -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="print-only">
      <div class="signature-row">
        <div class="signature-box">
          <p>Mengetahui,</p>
          <p style="font-weight:700;">{{ $kategori === 'siswa' ? 'Guru Piket / Wali Kelas' : 'Kepala Tata Usaha' }}</p>
          <div class="signature-space"></div>
          <p class="signature-name">( ..................................................... )</p>
          <p>NIP. .....................................................</p>
        </div>

        <div class="signature-box">
          <p>Air Naningan, {{ \Illuminate\Support\Carbon::today()->translatedFormat('d F Y') }}</p>
          <p style="font-weight:700;">Kepala SMK Negeri 1 Air Naningan</p>
          <div class="signature-space"></div>
          <p class="signature-name">( ..................................................... )</p>
          <p>NIP. .....................................................</p>
        </div>
      </div>
    </div>

  </main>
</div>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- MODAL KOREKSI STATUS PRESENSI -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<div id="modalKoreksiPresensi" class="modal-overlay" onclick="closeModalOnBackdrop(event)">
  <div class="modal-card" style="max-width: 520px; padding: 0; overflow: hidden;" onclick="event.stopPropagation()">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: var(--bg-2);">
      <div style="display: flex; align-items: center; gap: 8px;">
        <i class="bi bi-pencil-square" style="color: #000000; font-size: 18px;"></i>
        <strong style="font-size: 15px; color: var(--text);">Koreksi Catatan Presensi</strong>
      </div>
      <button type="button" onclick="closeKoreksiModal()" class="btn-close" style="width: 32px; height: 32px; border-radius: var(--r-sm); border: 1px solid var(--border); background: var(--surface); color: var(--text-2); display: flex; align-items: center; justify-content: center; cursor: pointer;" data-tooltip="Tutup Modal" title="Tutup Modal">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

    <form id="formKoreksiPresensi" action="" method="POST" style="padding: 22px;">
      @csrf
      @method('PUT')

      <div style="background: var(--bg-3); padding: 12px 16px; border-radius: var(--r-sm); margin-bottom: 18px; border: 1px solid var(--border-2);">
        <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--text-3); letter-spacing: 0.5px;">Identitas Anggota</div>
        <div id="koreksi_nama" style="font-size: 15px; font-weight: 800; color: var(--text); margin-top: 3px;">-</div>
        <div style="display: flex; gap: 16px; font-size: 12px; color: var(--text-3); font-family: var(--font-mono); margin-top: 6px;">
          <span>Tanggal: <strong id="koreksi_tanggal" style="color: var(--text);">-</strong></span>
          <span>Sumber: <strong id="koreksi_sumber" style="color: #000000;">-</strong></span>
        </div>
      </div>

      <div class="form-group" style="margin-bottom: 16px;">
        <label style="font-weight: 700; font-size: 12px; text-transform: uppercase; color: var(--text-2); margin-bottom: 6px; display: block;">
          Pilih Status Presensi Baru <span style="color: var(--red);">*</span>
        </label>
        <select name="status" id="koreksi_status" required class="input-field" style="width: 100%; height: 42px; font-weight: 700; font-size: 13.5px;">
          <option value="hadir">✅ HADIR (Tepat Waktu)</option>
          <option value="terlambat">⏰ TERLAMBAT (Lewat Jam Toleransi)</option>
          <option value="izin">📩 IZIN (Dengan Keterangan)</option>
          <option value="sakit">🩺 SAKIT (Surat Dokter / Istirahat)</option>
          <option value="dispen">🏆 DISPEN (Tugas Dinas / Lomba)</option>
          <option value="alpha">❌ ALPHA (Tanpa Keterangan)</option>
          <option value="bolos">⚠️ BOLOS (Tidak Tap Pulang)</option>
        </select>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
        <div class="form-group" style="margin-bottom: 0;">
          <label style="font-weight: 700; font-size: 11.5px; text-transform: uppercase; color: var(--text-2); margin-bottom: 4px; display: block;">
            Jam Masuk (Opsional)
          </label>
          <input type="time" name="jam_masuk" id="koreksi_jam_masuk" class="input-field" style="width: 100%; height: 40px;" />
        </div>
        <div class="form-group" style="margin-bottom: 0;">
          <label style="font-weight: 700; font-size: 11.5px; text-transform: uppercase; color: var(--text-2); margin-bottom: 4px; display: block;">
            Jam Pulang (Opsional)
          </label>
          <input type="time" name="jam_pulang" id="koreksi_jam_pulang" class="input-field" style="width: 100%; height: 40px;" />
        </div>
      </div>

      <div class="form-group" style="margin-bottom: 18px;">
        <label style="font-weight: 700; font-size: 11.5px; text-transform: uppercase; color: var(--text-2); margin-bottom: 6px; display: block;">
          Alasan / Keterangan Koreksi
        </label>
        <textarea name="keterangan" id="koreksi_keterangan" class="input-field" rows="2" placeholder="Contoh: Siswa mengikuti lomba mewakili sekolah / Surat izin menyusul / Lupa scan..." style="width: 100%; height: auto; padding: 10px 12px; font-size: 13px; resize: vertical; line-height: 1.4;"></textarea>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid var(--border); padding-top: 18px;">
        <button type="button" onclick="closeKoreksiModal()" class="btn btn-outline" style="height: 40px;">Batal</button>
        <button type="submit" class="btn btn-gold" style="height: 40px; font-weight: 800; display: inline-flex; align-items: center; gap: 6px;">
          <i class="bi bi-check2-circle"></i> Simpan Koreksi Status
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  function openKoreksiModal(id, nama, tanggal, status, jamMasuk, jamPulang, sumber, keterangan) {
    const modal = document.getElementById('modalKoreksiPresensi');
    const form = document.getElementById('formKoreksiPresensi');
    
    form.action = '/laporan/' + id;
    document.getElementById('koreksi_nama').innerText = nama || '-';
    document.getElementById('koreksi_tanggal').innerText = tanggal || '-';
    document.getElementById('koreksi_sumber').innerText = sumber || 'manual';
    document.getElementById('koreksi_status').value = status || 'hadir';
    document.getElementById('koreksi_jam_masuk').value = jamMasuk ? jamMasuk.substring(0, 5) : '';
    document.getElementById('koreksi_jam_pulang').value = jamPulang ? jamPulang.substring(0, 5) : '';
    document.getElementById('koreksi_keterangan').value = keterangan || '';

    modal.classList.add('active');
    modal.classList.add('open');
    modal.style.display = 'flex';
    modal.style.opacity = '1';
  }

  function closeKoreksiModal() {
    const modal = document.getElementById('modalKoreksiPresensi');
    if (modal) {
      modal.classList.remove('active');
      modal.classList.remove('open');
      modal.style.display = 'none';
      modal.style.opacity = '0';
    }
  }

  function closeModalOnBackdrop(e) {
    if (e.target.id === 'modalKoreksiPresensi') {
      closeKoreksiModal();
    }
  }

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeKoreksiModal();
    }
  });

  // Event Delegation untuk membuka modal koreksi dengan data-attributes
  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-koreksi-modal');
    if (btn) {
      const id = btn.getAttribute('data-id');
      const nama = btn.getAttribute('data-nama');
      const tanggal = btn.getAttribute('data-tanggal');
      const status = btn.getAttribute('data-status');
      const masuk = btn.getAttribute('data-masuk');
      const pulang = btn.getAttribute('data-pulang');
      const sumber = btn.getAttribute('data-sumber');
      const keterangan = btn.getAttribute('data-keterangan');
      openKoreksiModal(id, nama, tanggal, status, masuk, pulang, sumber, keterangan);
    }
  });
  // ─── Searchable Combobox Siswa & Guru (Laporan Individu) ───
  function toggleLpSiswaDropdown() {
    const dd = document.getElementById('lpSiswaDropdown');
    const trigger = document.getElementById('lpSiswaTrigger');
    if (!dd) return;
    const isOpen = dd.classList.contains('open');
    closeAllLpDropdowns();
    if (!isOpen) {
      dd.classList.add('open');
      if (trigger) trigger.classList.add('focused');
      const searchBox = document.getElementById('lpSiswaSearchBox');
      if (searchBox) {
        searchBox.value = '';
        filterLpSiswaList('');
        setTimeout(() => searchBox.focus(), 50);
      }
    }
  }

  function closeLpSiswaDropdown() {
    const dd = document.getElementById('lpSiswaDropdown');
    const trigger = document.getElementById('lpSiswaTrigger');
    if (dd) dd.classList.remove('open');
    if (trigger) trigger.classList.remove('focused');
  }

  function filterLpSiswaList(keyword) {
    const term = keyword.toLowerCase().trim();
    const items = document.querySelectorAll('.lp-item-siswa');
    const emptyMsg = document.getElementById('lpSiswaEmptyMsg');
    let visibleCount = 0;
    items.forEach(item => {
      const nama = item.getAttribute('data-nama') || '';
      const nisn = item.getAttribute('data-nisn') || '';
      const rombel = item.getAttribute('data-rombel') || '';
      if (!term || nama.includes(term) || nisn.includes(term) || rombel.includes(term)) {
        item.style.display = 'flex';
        visibleCount++;
      } else {
        item.style.display = 'none';
      }
    });
    if (emptyMsg) {
      emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
    }
  }

  function selectLpSiswa(id, nama, nisn, rombel, fotoUrl) {
    const inputHidden = document.getElementById('inputLpSiswaId');
    if (inputHidden) inputHidden.value = id;

    const selectedView = document.getElementById('lpSiswaSelectedView');
    const placeholder = document.getElementById('lpSiswaPlaceholder');
    const namaEl = document.getElementById('lpSiswaNama');
    const metaEl = document.getElementById('lpSiswaMeta');
    const fotoEl = document.getElementById('lpSiswaFoto');

    if (namaEl) namaEl.innerText = nama;
    if (metaEl) metaEl.innerText = 'NISN: ' + nisn + (rombel ? ' · ' + rombel : '');
    if (fotoEl) fotoEl.src = fotoUrl || '/img/user-default.png';

    if (selectedView) selectedView.style.display = 'flex';
    if (placeholder) placeholder.style.display = 'none';

    closeLpSiswaDropdown();
  }

  function clearLpSiswa(e) {
    if (e) e.stopPropagation();
    const inputHidden = document.getElementById('inputLpSiswaId');
    if (inputHidden) inputHidden.value = '';

    const selectedView = document.getElementById('lpSiswaSelectedView');
    const placeholder = document.getElementById('lpSiswaPlaceholder');
    if (selectedView) selectedView.style.display = 'none';
    if (placeholder) placeholder.style.display = 'flex';

    closeLpSiswaDropdown();
  }

  function toggleLpGuruDropdown() {
    const dd = document.getElementById('lpGuruDropdown');
    const trigger = document.getElementById('lpGuruTrigger');
    if (!dd) return;
    const isOpen = dd.classList.contains('open');
    closeAllLpDropdowns();
    if (!isOpen) {
      dd.classList.add('open');
      if (trigger) trigger.classList.add('focused');
      const searchBox = document.getElementById('lpGuruSearchBox');
      if (searchBox) {
        searchBox.value = '';
        filterLpGuruList('');
        setTimeout(() => searchBox.focus(), 50);
      }
    }
  }

  function closeLpGuruDropdown() {
    const dd = document.getElementById('lpGuruDropdown');
    const trigger = document.getElementById('lpGuruTrigger');
    if (dd) dd.classList.remove('open');
    if (trigger) trigger.classList.remove('focused');
  }

  function filterLpGuruList(keyword) {
    const term = keyword.toLowerCase().trim();
    const items = document.querySelectorAll('.lp-item-guru');
    const emptyMsg = document.getElementById('lpGuruEmptyMsg');
    let visibleCount = 0;
    items.forEach(item => {
      const nama = item.getAttribute('data-nama') || '';
      const nip = item.getAttribute('data-nip') || '';
      const jabatan = item.getAttribute('data-jabatan') || '';
      if (!term || nama.includes(term) || nip.includes(term) || jabatan.includes(term)) {
        item.style.display = 'flex';
        visibleCount++;
      } else {
        item.style.display = 'none';
      }
    });
    if (emptyMsg) {
      emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
    }
  }

  function selectLpGuru(id, nama, nip, jabatan, fotoUrl) {
    const inputHidden = document.getElementById('inputLpGuruId');
    if (inputHidden) inputHidden.value = id;

    const selectedView = document.getElementById('lpGuruSelectedView');
    const placeholder = document.getElementById('lpGuruPlaceholder');
    const namaEl = document.getElementById('lpGuruNama');
    const metaEl = document.getElementById('lpGuruMeta');
    const fotoEl = document.getElementById('lpGuruFoto');

    if (namaEl) namaEl.innerText = nama;
    if (metaEl) metaEl.innerText = (nip && nip !== '-' ? 'NIP: ' + nip : jabatan);
    if (fotoEl) fotoEl.src = fotoUrl || '/img/user-default.png';

    if (selectedView) selectedView.style.display = 'flex';
    if (placeholder) placeholder.style.display = 'none';

    closeLpGuruDropdown();
  }

  function clearLpGuru(e) {
    if (e) e.stopPropagation();
    const inputHidden = document.getElementById('inputLpGuruId');
    if (inputHidden) inputHidden.value = '';

    const selectedView = document.getElementById('lpGuruSelectedView');
    const placeholder = document.getElementById('lpGuruPlaceholder');
    if (selectedView) selectedView.style.display = 'none';
    if (placeholder) placeholder.style.display = 'flex';

    closeLpGuruDropdown();
  }

  function closeAllLpDropdowns() {
    closeLpSiswaDropdown();
    closeLpGuruDropdown();
  }

  document.addEventListener('click', function(e) {
    if (!e.target.closest('.lp-picker-wrap')) {
      closeAllLpDropdowns();
    }
  });
</script>

</body>
</html>
