<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Piket Harian — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <style>
    /* ── Hero Meja Piket ── */
    .piket-hero {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-xl);
      padding: 22px 24px;
      margin-bottom: 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 16px;
      box-shadow: var(--shadow-sm);
    }
    .piket-clock-card {
      background: var(--bg-3);
      border: 1px solid var(--border-2);
      border-radius: var(--r-md);
      padding: 12px 18px;
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 4px;
      min-width: 180px;
    }
    .piket-clock-time {
      font-family: var(--font-mono);
      font-size: 24px;
      font-weight: 900;
      color: var(--text);
      letter-spacing: 1px;
    }

    /* ── KPI Stat Cards ── */
    .piket-stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 10px;
      margin-bottom: 8px;
    }
    .piket-stat-card {
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      border-radius: var(--r-sm);
      padding: 10px 14px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      gap: 3px;
      transition: all .15s ease;
      box-shadow: var(--shadow-sm);
    }
    .piket-stat-card:hover {
      border-color: #000000;
    }
    .piket-stat-head {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 10.5px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.4px;
      color: var(--text-3);
    }
    .piket-stat-val {
      font-size: 22px;
      font-weight: 900;
      line-height: 1.1;
      font-family: var(--font-mono);
      color: #000000;
      margin: 2px 0 1px;
    }

    /* ── Presensi Manual Quick Bar ── */
    .pm-panel {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-xl);
      padding: 22px 24px;
      margin-bottom: 24px;
      box-shadow: var(--shadow-sm);
    }
    .pm-panel:hover {
      border-color: var(--text);
    }
    .pm-search-dropdown {
      position: absolute;
      top: calc(100% + 4px);
      left: 0;
      right: 0;
      background: var(--bg-2);
      border: 1.5px solid var(--border-2);
      border-radius: var(--r-sm);
      box-shadow: var(--shadow-lg);
      z-index: 1050;
      overflow: hidden;
    }
    .pm-item {
      padding: 8px 12px;
      border-bottom: 1px solid var(--border);
      cursor: pointer;
      transition: background .15s ease;
      text-align: left;
    }
    .pm-item:last-child {
      border-bottom: none;
    }
    .pm-item:hover, .pm-item.active {
      background: var(--surface);
    }

    /* ── Tabs Styling ── */
    .piket-nav-tabs {
      display: flex;
      gap: 4px;
      border-bottom: 1.5px solid var(--border);
      margin-bottom: 12px;
      overflow-x: auto;
      padding-bottom: 0px;
      -webkit-overflow-scrolling: touch;
      scrollbar-width: none;
    }
    .piket-nav-tabs::-webkit-scrollbar {
      display: none;
    }
    .piket-tab-btn {
      background: none;
      border: none;
      border-bottom: 2.5px solid transparent;
      margin-bottom: -1.5px;
      padding: 6px 12px;
      font-size: 11.5px;
      font-weight: 700;
      color: var(--text-2);
      cursor: pointer;
      border-radius: var(--r-sm) var(--r-sm) 0 0;
      white-space: nowrap;
      flex-shrink: 0;
      transition: all .15s ease;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .piket-tab-btn:hover {
      color: #000000;
      background: var(--surface);
    }
    .piket-tab-btn.active {
      color: #000000;
      font-weight: 900;
      border-bottom-color: #000000;
      background: none;
    }
    .piket-tab-pane { display: none; }
    .piket-tab-pane.active { display: block; }

    /* ── Filter Pills ── */
    .filter-pills {
      display: flex;
      gap: 4px;
      align-items: center;
      flex-wrap: wrap;
      margin-bottom: 0;
    }
    .filter-pill {
      background: var(--bg-3);
      border: 1px solid var(--border-2);
      color: var(--text-2);
      padding: 3px 8px;
      border-radius: 5px;
      font-size: 11px;
      font-weight: 700;
      cursor: pointer;
      transition: all .15s;
      white-space: nowrap;
    }
    .filter-pill:hover {
      color: #000000;
      border-color: #000000;
    }
    .filter-pill.active {
      background: #000000;
      border-color: #000000;
      color: #FFFFFF;
      font-weight: 800;
    }

    @media (max-width: 768px) {
      .filter-pills {
        flex-wrap: nowrap !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        padding-bottom: 2px;
        width: 100%;
      }
      .filter-pills::-webkit-scrollbar {
        display: none;
      }
      .filter-pill {
        flex-shrink: 0 !important;
        padding: 4px 9px !important;
        font-size: 10.5px !important;
      }
      .piket-search-wrap {
        max-width: 100% !important;
        margin-top: 2px;
      }
    }

    /* ── Day Column Grid ── */
    .piket-day-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
      gap: 14px;
    }
    .piket-day-col {
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      border-radius: var(--r-md);
      padding: 14px;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    .piket-day-col.today {
      border-color: var(--text);
      background: var(--surface);
      box-shadow: var(--shadow-sm);
    }
    
    /* ── MINIMALIST & ELEGANT PIKET DESIGN ── */
    .piket-page-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      flex-wrap: wrap;
      gap: 14px;
      margin-bottom: 16px;
      padding-bottom: 14px;
      border-bottom: 1px solid var(--border);
    }
    .piket-header-title {
      font-size: 19px;
      font-weight: 800;
      letter-spacing: -0.02em;
      color: var(--text);
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .piket-header-subtitle {
      font-size: 12px;
      color: var(--text-2);
      margin-top: 4px;
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }
    .piket-header-subtitle strong {
      color: var(--text);
      font-family: var(--font-mono);
    }
    .piket-header-actions {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }
    .piket-hdr-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      height: 34px;
      padding: 0 13px;
      border-radius: 6px;
      font-size: 12px;
      font-weight: 600;
      font-family: var(--font);
      background: var(--bg-2);
      color: var(--text);
      border: 1px solid var(--border-2);
      box-shadow: var(--shadow-sm);
      cursor: pointer;
      text-decoration: none;
      white-space: nowrap;
      transition: all .15s ease;
      letter-spacing: -0.01em;
    }
    .piket-hdr-btn:hover {
      background: var(--bg-3);
      color: var(--text);
      border-color: var(--border);
      transform: translateY(-1px);
      box-shadow: var(--shadow-md);
    }
    .piket-hdr-btn:active {
      transform: translateY(0);
    }
    .piket-hdr-btn i {
      font-size: 13.5px;
      line-height: 1;
    }
    .piket-hdr-btn--wa {
      color: #16A34A;
      border-color: #86EFAC;
    }
    .piket-hdr-btn--wa:hover {
      background: rgba(22, 163, 74, 0.08);
      border-color: #16A34A;
      color: #15803D;
    }
    .piket-hdr-btn--wa i {
      color: #16A34A;
    }
    .piket-main-switcher {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 16px;
      flex-wrap: wrap;
    }
    .piket-main-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 9px 18px;
      border-radius: var(--r-sm);
      font-size: 13px;
      font-weight: 700;
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      color: var(--text-2);
      cursor: pointer;
      transition: all .18s ease;
      text-decoration: none;
      box-shadow: var(--shadow-sm);
    }
    .piket-main-btn:hover {
      background: var(--bg-3);
      color: var(--text);
      border-color: var(--border);
    }
    .piket-main-btn.active {
      background: #000000 !important;
      color: #FFFFFF !important;
      border-color: #000000 !important;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
    }
    .piket-main-btn.active i {
      color: #FFFFFF !important;
    }
    .piket-main-btn.active .piket-main-badge {
      background: rgba(255, 255, 255, 0.2);
      color: #FFFFFF;
      border-color: rgba(255, 255, 255, 0.3);
    }
    .piket-main-badge {
      font-size: 11px;
      font-weight: 800;
      padding: 1px 7px;
      border-radius: 10px;
      background: var(--bg-3);
      color: var(--text-2);
      border: 1px solid var(--border);
    }
    .piket-tab-pane {
      display: none;
    }
    .piket-tab-pane.active {
      display: block;
      animation: fadeIn .15s ease-in-out;
    }
  </style>
</head>
<body>
<div class="app-container">
  @include('partials.sidebar')

  <main class="main-content">
    
    {{-- FLASH MESSAGES --}}
    @if(session('success'))
      <div class="alert-success" style="margin-bottom:12px;">
        <i class="bi bi-check-circle-fill" style="margin-right:6px;"></i> {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="alert-error" style="margin-bottom:12px;">
        <i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i> {{ session('error') }}
      </div>
    @endif

    {{-- ══ MINIMALIST & ELEGANT PAGE HEADER ══ --}}
    <div class="piket-page-header no-print">
      <div>
        <div class="piket-header-title">
          <span>Meja Piket</span>
          <span style="font-size:12px; font-weight:600; color:var(--text-3); font-family:var(--font-mono); background:var(--bg-3); padding:2px 8px; border-radius:5px; border:1px solid var(--border-2);" id="livePiketClock">
            {{ now()->format('H:i:s') }} WIB
          </span>
          @if($isLibur)
            <span class="badge" style="background:rgba(239,68,68,0.12); color:#DC2626; font-size:10.5px; font-weight:700; padding:2px 8px; border-radius:4px;">
              HARI LIBUR
            </span>
          @endif
        </div>
        
        <div class="piket-header-subtitle">
          <span>{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
          <span>·</span>
          <span>Batas Masuk: <strong>{{ substr($jadwal->jam_masuk_toleransi ?? '07:15', 0, 5) }}</strong></span>
          <span>·</span>
          <span>Smart Gate: <strong style="color:{{ $jadwal->is_sesi_buka ? '#16A34A' : '#DC2626' }};">{{ $jadwal->is_sesi_buka ? 'Terbuka' : 'Ditutup' }}</strong></span>
          @if($guruPiketHariIni->isNotEmpty())
            <span>·</span>
            <span>Petugas: <span style="color:var(--text); font-weight:600;">{{ $guruPiketHariIni->map(fn($g) => $g->guru->nama ?? 'Guru')->join(', ') }}</span></span>
          @endif
        </div>
      </div>

      {{-- Action Buttons --}}
      <div class="piket-header-actions">
        {{-- Gate Toggle --}}
        @if($jadwal->is_sesi_buka)
          <form action="{{ route('piket.toggle-gerbang') }}" method="POST" style="margin:0;">
            @csrf
            <button type="submit" name="status" value="tutup" class="piket-hdr-btn" title="Tutup sesi gerbang">
              <i class="bi bi-door-closed"></i> Tutup Gerbang
            </button>
          </form>
        @else
          <form action="{{ route('piket.toggle-gerbang') }}" method="POST" style="margin:0;">
            @csrf
            <button type="submit" name="status" value="buka" class="piket-hdr-btn" title="Buka sesi gerbang">
              <i class="bi bi-door-open"></i> Buka Gerbang
            </button>
          </form>
        @endif

        {{-- WA Massal --}}
        @if(($canKoreksi ?? false) && ($siswaBelumHadirList->count() + $guruBelumHadirList->count() > 0))
          <form action="{{ route('piket.flagging-wa') }}" method="POST" style="margin:0;"
                onsubmit="return confirm('Kirim WA pengingat ke {{ $siswaBelumHadirList->count() }} wali murid & {{ $guruBelumHadirList->count() }} guru yang belum hadir?')">
            @csrf
            <button type="submit" class="piket-hdr-btn piket-hdr-btn--wa" title="Kirim WA Pengingat">
              <i class="bi bi-whatsapp"></i> Broadcast WA ({{ $siswaBelumHadirList->count() + $guruBelumHadirList->count() }})
            </button>
          </form>
        @endif

        {{-- Presensi Manual & Izin --}}
        <button type="button" class="piket-hdr-btn" onclick="openModal('modalPresensiManual')" title="Input presensi manual siswa / guru">
          <i class="bi bi-plus-lg"></i> Presensi Manual
        </button>
        <button type="button" class="piket-hdr-btn" onclick="openModal('modalCatatIzinPiket')" title="Catat perizinan atau sakit">
          <i class="bi bi-envelope-paper"></i> Catat Izin
        </button>
      </div>{{-- /.piket-header-actions --}}
    </div>{{-- /.piket-page-header --}}

    {{-- ══ 6 KPI CARDS RINGKASAN MEJA PIKET ══ --}}
    @php
      $isAfter1000 = now()->format('H:i') >= '10:00';
      $alphaCount = max(0, $totalSiswaAktif - ($hadirTepat + $terlambat + $izinCount + ($totalSiswaPkl ?? 0)));
    @endphp
    <div class="db-kpi-grid no-print" style="grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; margin-bottom: 18px;">
      <!-- Tingkat Kehadiran -->
      <div class="db-kpi-card" onclick="selectSiswaFilter('all')">
        <div class="db-kpi-head">
          <span class="db-kpi-title">Tingkat Kehadiran</span>
          <div class="db-kpi-icon"><i class="bi bi-pie-chart-fill"></i></div>
        </div>
        <div class="db-kpi-val">{{ $persenKehadiran }}%</div>
        <div class="db-kpi-sub">{{ $hadirTepat + $terlambat }} dari {{ $totalSiswaAktif }} siswa</div>
      </div>

      <!-- Siswa Hadir Tepat -->
      <div class="db-kpi-card" onclick="selectSiswaFilter('hadir')">
        <div class="db-kpi-head">
          <span class="db-kpi-title">Hadir Tepat Waktu</span>
          <div class="db-kpi-icon"><i class="bi bi-check2-circle"></i></div>
        </div>
        <div class="db-kpi-val">{{ $hadirTepat }}</div>
        <div class="db-kpi-sub">Scan sebelum batas</div>
      </div>

      <!-- Siswa Terlambat -->
      <div class="db-kpi-card" onclick="selectSiswaFilter('terlambat')">
        <div class="db-kpi-head">
          <span class="db-kpi-title">Terlambat Gerbang</span>
          <div class="db-kpi-icon"><i class="bi bi-clock-history"></i></div>
        </div>
        <div class="db-kpi-val">{{ $terlambat }}</div>
        <div class="db-kpi-sub">Masuk pos piket</div>
      </div>

      <!-- Siswa Izin / Sakit -->
      <div class="db-kpi-card" onclick="selectSiswaFilter('izin')">
        <div class="db-kpi-head">
          <span class="db-kpi-title">Izin / Sakit</span>
          <div class="db-kpi-icon"><i class="bi bi-envelope-paper-fill"></i></div>
        </div>
        <div class="db-kpi-val">{{ $izinCount }}</div>
        <div class="db-kpi-sub">Keterangan sah</div>
      </div>

      <!-- Siswa PKL -->
      <div class="db-kpi-card" onclick="selectSiswaFilter('pkl')">
        <div class="db-kpi-head">
          <span class="db-kpi-title">Siswa PKL</span>
          <div class="db-kpi-icon"><i class="bi bi-briefcase-fill"></i></div>
        </div>
        <div class="db-kpi-val">{{ $totalSiswaPkl ?? 0 }}</div>
        <div class="db-kpi-sub">Prakerin industri</div>
      </div>

      <!-- Siswa Alpha / Belum Scan -->
      <div class="db-kpi-card" onclick="selectSiswaFilter('belum_hadir')">
        <div class="db-kpi-head">
          <span class="db-kpi-title">Alpha / Belum Scan</span>
          <div class="db-kpi-icon"><i class="bi bi-exclamation-octagon-fill"></i></div>
        </div>
        <div class="db-kpi-val">{{ $alphaCount }}</div>
        <div class="db-kpi-sub">{{ $isAfter1000 ? 'Status terkunci' : 'Otomatis Alpha 10:00' }}</div>
      </div>

      <!-- Siswa Belum Scan Pulang → buka modal -->
      <div class="db-kpi-card" onclick="openModal('modalRekapPulangPiket')" style="border:{{ $sudahLewatJamTutup ? '2px solid #000000' : '1px solid var(--border)' }}; cursor:pointer; transition:all .15s;">
        <div class="db-kpi-head">
          <span class="db-kpi-title">{{ $sudahLewatJamTutup ? 'Dianggap Bolos' : 'Blm Scan Pulang' }}</span>
          <div class="db-kpi-icon"><i class="bi bi-door-open-fill"></i></div>
        </div>
        <div class="db-kpi-val">{{ $siswaBelumScanPulang }}</div>
        <div class="db-kpi-sub" style="font-weight:{{ $sudahLewatJamTutup ? '800' : '600' }}; color:{{ $sudahLewatJamTutup ? '#000000' : 'var(--text-3)' }};">
          {{ $sudahLewatJamTutup ? 'Lewat jam tutup' : 'Klik → lihat detail' }}
        </div>
      </div>
    </div>

    {{-- ══ MAIN VIEW SWITCHER (PRESENSI SISWA vs GURU) ══ --}}
    <div class="piket-main-switcher no-print">
      <button type="button" class="piket-main-btn active" id="btnViewSiswa" onclick="switchMainView('siswa', this)">
        <i class="bi bi-people-fill"></i> Presensi Siswa
        <span class="piket-main-badge">{{ $totalSiswaAktif }}</span>
      </button>
      <button type="button" class="piket-main-btn" id="btnViewGuru" onclick="switchMainView('guru', this)">
        <i class="bi bi-person-badge-fill"></i> Presensi Guru &amp; Pegawai
        <span class="piket-main-badge">{{ $guruBelumHadirList->count() + $absensiGuruHariIni->count() }}</span>
      </button>
    </div>

    {{-- ══ VIEW 1: TABEL PRESENSI SISWA (TERMASUK BELUM HADIR) ══ --}}
    <div id="view-siswa" class="piket-view-pane">
      <div class="panel" style="padding:0; overflow:hidden;">
        
        {{-- Toolbar Filter & Search Siswa --}}
        <div style="padding:10px 14px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; background:var(--surface);">
          <div class="filter-pills" style="margin-bottom:0;">
            <button type="button" class="filter-pill active" data-filter="all" onclick="filterSiswaTable('all', this)">Semua ({{ $totalSiswaAktif }})</button>
            <button type="button" class="filter-pill" data-filter="hadir" onclick="filterSiswaTable('hadir', this)">Hadir Tepat ({{ $hadirTepat }})</button>
            <button type="button" class="filter-pill" data-filter="terlambat" onclick="filterSiswaTable('terlambat', this)">Terlambat ({{ $terlambat }})</button>
            <button type="button" class="filter-pill" data-filter="belum_hadir" onclick="filterSiswaTable('belum_hadir', this)">
              Belum Hadir
              @if($siswaBelumHadirList->count() > 0)
                <span style="background:rgba(239,68,68,0.15); color:#DC2626; font-size:10px; font-weight:800; padding:1px 6px; border-radius:10px; margin-left:4px;">{{ $siswaBelumHadirList->count() }}</span>
              @else
                <span style="font-size:10px; font-weight:700; color:var(--text-3); margin-left:4px;">0</span>
              @endif
            </button>
            <button type="button" class="filter-pill" data-filter="izin" onclick="filterSiswaTable('izin', this)">Izin / Sakit ({{ $izinCount }})</button>
            <button type="button" class="filter-pill" data-filter="pkl" onclick="filterSiswaTable('pkl', this)">PKL ({{ $totalSiswaPkl ?? 0 }})</button>
            <button type="button" class="filter-pill" data-filter="bolos" onclick="filterSiswaTable('bolos', this)">Bolos ({{ $absensiHariIni->where('status', 'bolos')->count() }})</button>
            <button type="button" class="filter-pill" data-filter="pulang" onclick="filterSiswaTable('pulang', this)">Sudah Pulang ({{ $sudahPulang }})</button>
          </div>
          <div style="position:relative; width:100%; max-width:240px;" class="piket-search-wrap">
            <i class="bi bi-search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--text-3); font-size:11px;"></i>
            <input type="text" id="searchSiswaPiket" onkeyup="searchSiswaPiketTable()" placeholder="Cari nama, NIS, kelas..." style="width:100%; height:32px; padding-left:28px; font-size:11.5px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); color:var(--text);" />
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table" id="tableSiswaPiket">
            <thead>
              <tr>
                <th style="width:40px; text-align:center;">No</th>
                <th style="width:45px; text-align:center;">Foto</th>
                <th style="width:110px;">NISN / NIS</th>
                <th>Nama Peserta Didik</th>
                <th style="width:110px;">Kelas / Rombel</th>
                <th style="width:90px; text-align:center;">Jam Masuk</th>
                <th style="width:90px; text-align:center;">Jam Pulang</th>
                <th style="width:110px; text-align:center;">Status</th>
                <th style="width:150px;">Sumber Input</th>
                <th style="text-align:right; width:115px;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {{-- 1. Siswa yang sudah absen/scan hari ini --}}
              @foreach($absensiHariIni as $idx => $ab)
                @php
                  $rombel = $ab->siswaRombel?->rombel?->nama_rombel ?? '—';
                  $s = $ab->siswa;
                  $hp = $s?->no_hp_siswa ?: $s?->no_hp_ortu;
                  $hpClean = preg_replace('/[^0-9]/', '', $hp ?? '');
                  if (str_starts_with($hpClean, '0')) $hpClean = '62' . substr($hpClean, 1);
                @endphp
                <tr class="row-siswa-absen" data-status="{{ $ab->status }}" data-pulang="{{ $ab->jam_pulang ? 'pulang' : 'belum' }}">
                  <td style="text-align:center; font-weight:700; color:var(--text-3); font-size:12px;">{{ $idx + 1 }}</td>
                  <td style="text-align:center; vertical-align:middle;">
                    <img src="{{ $ab->siswa?->foto_url }}" alt="{{ $ab->siswa?->nama }}" style="width:34px; height:34px; border-radius:50%; object-fit:cover; border:1.5px solid var(--border-2);" />
                  </td>
                  <td style="font-family:var(--font-mono); font-weight:700; color:var(--text); font-size:12px;">{{ $ab->siswa?->nisn ?? ($ab->siswa?->nis ?? '-') }}</td>
                  <td>
                    <strong style="color:var(--text); font-size:13px;">{{ $ab->siswa?->nama ?? '—' }}</strong>
                    @if($hpClean)
                      <div style="margin-top:2px;">
                        <a href="https://wa.me/{{ $hpClean }}" target="_blank" style="font-size:11px; color:#16A34A; text-decoration:none; font-weight:600; display:inline-flex; align-items:center; gap:3px;">
                          <i class="bi bi-whatsapp"></i> <span style="font-family:var(--font-mono);">{{ $hp }}</span>
                        </a>
                      </div>
                    @endif
                  </td>
                  <td>
                    <span style="font-weight:800; color:var(--text); font-size:12px;">{{ $rombel }}</span>
                  </td>
                  <td style="font-family:var(--font-mono); font-size:12px; font-weight:800; color:var(--text); text-align:center;">
                    {{ $ab->jam_masuk ? substr($ab->jam_masuk, 0, 5) : '—' }}
                  </td>
                  <td style="font-family:var(--font-mono); font-size:12px; font-weight:700; color:var(--text); text-align:center;">
                    @if($ab->jam_pulang)
                      {{ substr($ab->jam_pulang, 0, 5) }}
                    @else
                      <span style="color:var(--text-3); font-style:italic; font-size:11px;">Belum Pulang</span>
                    @endif
                  </td>
                  <td style="text-align:center;">
                    @if($ab->status === 'hadir')
                      <span style="font-size:11.5px; font-weight:700; color:var(--text);">Hadir Tepat</span>
                    @elseif($ab->status === 'terlambat')
                      <span style="font-size:11.5px; font-weight:700; color:var(--text);">Terlambat</span>
                    @elseif($ab->status === 'bolos')
                      <span style="font-size:11.5px; font-weight:700; color:var(--text);">Bolos</span>
                    @elseif($ab->status === 'alpha')
                      <span style="font-size:11.5px; font-weight:700; color:var(--text);">Alpha</span>
                    @else
                      <span style="font-size:11.5px; font-weight:700; color:var(--text);">{{ ucfirst($ab->status) }}</span>
                    @endif
                  </td>
                  <td>
                    @php
                      $matchingIzin = $izinHariIni->firstWhere('siswa_id', $ab->pemilik_id ?: $ab->siswaRombel?->siswa_id);
                    @endphp
                    @if(in_array($ab->status, ['izin', 'sakit', 'dispen']) || $matchingIzin)
                      <span style="font-size:11.5px; font-weight:700; color:var(--text);">Izin Resmi</span>
                    @elseif(in_array($ab->sumber_absen, ['rfid', 'kios_rfid', 'barcode', 'scan_barcode', 'qr_code', 'kios_wajah', 'face_kiosk', 'kios_biometrik']) || empty($ab->sumber_absen))
                      <span style="font-size:11.5px; font-weight:700; color:var(--text);">Smart Gate</span>
                    @elseif($ab->sumber_absen === 'koreksi_piket_manual')
                      <span style="font-size:11.5px; font-weight:700; color:var(--text);">Koreksi Piket</span>
                    @elseif($ab->sumber_absen === 'manual_piket' || $ab->sumber_absen === 'manual_izin_piket')
                      <span style="font-size:11.5px; font-weight:700; color:var(--text);">Manual Piket</span>
                    @elseif($ab->sumber_absen === 'evaluasi_alpha')
                      <span style="font-size:11.5px; font-weight:700; color:var(--text);">Sistem Otomatis</span>
                    @else
                      <span style="font-size:11.5px; font-weight:700; color:var(--text);">{{ ucfirst(str_replace('_', ' ', $ab->sumber_absen)) }}</span>
                    @endif
                  </td>
                  <td style="text-align:right; white-space:nowrap;">
                    @if($canKoreksi ?? false)
                      <button type="button" class="btn btn-sm btn-outline" style="padding:3px 8px; font-size:11px; font-weight:700; color:#000000; border-color:rgba(0,0,0,0.25);" onclick="openKoreksiPiketModal({{ $ab->id }}, '{{ addslashes($ab->siswa?->nama ?? 'Siswa') }}', '{{ $ab->status }}', '{{ $ab->jam_masuk ? substr($ab->jam_masuk,0,5) : '' }}', '{{ $ab->jam_pulang ? substr($ab->jam_pulang,0,5) : '' }}', '{{ addslashes($ab->keterangan ?? '') }}')">
                        <i class="bi bi-pencil-square"></i> Koreksi
                      </button>
                    @else
                      <span style="color:var(--text-3); font-size:11px;">-</span>
                    @endif
                  </td>
                </tr>
              @endforeach

              {{-- 2. Siswa Belum Hadir / Scan --}}
              @foreach($siswaBelumHadirList as $sb)
                @php
                  $rombel = $sb->siswaRombels->where('status_keanggotaan', 'aktif')->first()?->rombel?->nama_rombel ?? '—';
                  $hp = $sb->no_hp_siswa ?: $sb->no_hp_ortu;
                  $hpClean = preg_replace('/[^0-9]/', '', $hp ?? '');
                  if (str_starts_with($hpClean, '0')) $hpClean = '62' . substr($hpClean, 1);
                @endphp
                <tr class="row-siswa-absen" data-status="belum_hadir" data-pulang="belum">
                  <td style="text-align:center; font-weight:700; color:var(--text-3); font-size:12px;">-</td>
                  <td style="text-align:center; vertical-align:middle;">
                    <img src="{{ $sb->foto_url }}" alt="{{ $sb->nama }}" style="width:34px; height:34px; border-radius:50%; object-fit:cover; border:1.5px solid var(--border-2); opacity:0.85;" />
                  </td>
                  <td style="font-family:var(--font-mono); font-weight:700; color:var(--text); font-size:12px;">{{ $sb->nisn ?? ($sb->nis ?? '-') }}</td>
                  <td>
                    <strong style="color:var(--text); font-size:13px;">{{ $sb->nama }}</strong>
                    @if($hpClean)
                      <div style="margin-top:2px;">
                        <a href="https://wa.me/{{ $hpClean }}" target="_blank" style="font-size:11px; color:#16A34A; text-decoration:none; font-weight:600; display:inline-flex; align-items:center; gap:3px;">
                          <i class="bi bi-whatsapp"></i> <span style="font-family:var(--font-mono);">{{ $hp }}</span>
                        </a>
                      </div>
                    @endif
                  </td>
                  <td>
                    <span style="font-weight:800; color:var(--text); font-size:12px;">{{ $rombel }}</span>
                  </td>
                  <td style="font-family:var(--font-mono); font-size:12px; color:var(--text-3); text-align:center;">—</td>
                  <td style="font-family:var(--font-mono); font-size:12px; color:var(--text-3); text-align:center;">—</td>
                  <td style="text-align:center;">
                    <span class="badge" style="background:rgba(239,68,68,0.1); color:#DC2626; font-weight:800; font-size:11px; display:inline-flex; align-items:center; gap:3px;">
                      <i class="bi bi-exclamation-circle-fill"></i> Belum Scan
                    </span>
                  </td>
                  <td>
                    <span style="color:var(--text-3); font-size:11.5px;">—</span>
                  </td>
                  <td style="text-align:right; white-space:nowrap;">
                    @if($canKoreksi ?? false)
                      <button type="button" class="btn btn-sm btn-outline" style="padding:3px 8px; font-size:11px; font-weight:700; color:var(--text); border-color:var(--border-2);" onclick="openValidasiSiswaModal({{ $sb->id }}, '{{ addslashes($sb->nama) }}', 'NISN: {{ $sb->nisn ?? '-' }}', '{{ addslashes($rombel) }}')" title="Catat Status (Izin, Sakit, Hadir Manual, Dispen)">
                        <i class="bi bi-pencil-square"></i> Set Status
                      </button>
                    @else
                      <span style="color:var(--text-3); font-size:11px;">-</span>
                    @endif
                  </td>
                </tr>
              @endforeach

              @if($absensiHariIni->count() + $siswaBelumHadirList->count() === 0)
                <tr class="empty-row">
                  <td colspan="10" style="text-align:center; padding:36px; color:var(--text-3);">
                    <i class="bi bi-inbox" style="font-size:32px; opacity:0.4; display:block; margin-bottom:6px;"></i>
                    Belum ada rekaman data siswa aktif hari ini.
                  </td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>

        {{-- Pagination Bar Siswa --}}
        <div class="piket-pagination-bar" id="paginationSiswaBar" style="padding:10px 14px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; background:var(--surface);">
          <div style="font-size:12px; color:var(--text-2); font-weight:600;" id="paginationSiswaInfo">
            Menampilkan 1 - 20 dari {{ $totalSiswaAktif }} data
          </div>
          <div class="custom-pagination">
            <div class="pagination-wrapper" id="paginationSiswaControls"></div>
          </div>
        </div>
      </div>
    </div>

    {{-- ══ VIEW 2: TABEL PRESENSI GURU & PEGAWAI (TERMASUK BELUM HADIR) ══ --}}
    <div id="view-guru" class="piket-view-pane" style="display:none;">
      <div class="panel" style="padding:0; overflow:hidden;">
        
        {{-- Toolbar Filter & Search Guru --}}
        <div style="padding:10px 14px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; background:var(--surface);">
          <div class="filter-pills" style="margin-bottom:0;">
            <button type="button" class="filter-pill active" data-filter="all" onclick="filterGuruTable('all', this)">Semua ({{ $absensiGuruHariIni->count() + $guruBelumHadirList->count() }})</button>
            <button type="button" class="filter-pill" data-filter="hadir" onclick="filterGuruTable('hadir', this)">Hadir Tepat ({{ $guruHadirTepat }})</button>
            <button type="button" class="filter-pill" data-filter="terlambat" onclick="filterGuruTable('terlambat', this)">Terlambat ({{ $guruTerlambat }})</button>
            <button type="button" class="filter-pill" data-filter="belum_hadir" onclick="filterGuruTable('belum_hadir', this)">
              Belum Hadir
              @if($guruBelumHadirList->count() > 0)
                <span style="background:rgba(239,68,68,0.15); color:#DC2626; font-size:10px; font-weight:800; padding:1px 6px; border-radius:10px; margin-left:4px;">{{ $guruBelumHadirList->count() }}</span>
              @else
                <span style="font-size:10px; font-weight:700; color:var(--text-3); margin-left:4px;">0</span>
              @endif
            </button>
            <button type="button" class="filter-pill" data-filter="izin" onclick="filterGuruTable('izin', this)">Izin / Dinas ({{ $guruIzinSakit }})</button>
            <button type="button" class="filter-pill" data-filter="pulang" onclick="filterGuruTable('pulang', this)">Sudah Pulang ({{ $guruSudahPulang }})</button>
          </div>
          <div style="position:relative; width:100%; max-width:240px;" class="piket-search-wrap">
            <i class="bi bi-search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--text-3); font-size:11px;"></i>
            <input type="text" id="searchGuruPiket" onkeyup="searchGuruPiketTable()" placeholder="Cari nama guru, NIP, jabatan..." style="width:100%; height:32px; padding-left:28px; font-size:11.5px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); color:var(--text);" />
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table" id="tableGuruPiket">
            <thead>
              <tr>
                <th style="width:40px; text-align:center;">No</th>
                <th style="width:45px; text-align:center;">Foto</th>
                <th style="width:120px;">NIP / ID</th>
                <th>Nama Guru / Pegawai</th>
                <th style="width:130px;">Jabatan</th>
                <th style="width:90px; text-align:center;">Jam Masuk</th>
                <th style="width:90px; text-align:center;">Jam Pulang</th>
                <th style="width:110px; text-align:center;">Status</th>
                <th style="width:150px;">Sumber Input</th>
                <th style="text-align:right; width:115px;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {{-- 1. Guru yang sudah absen/scan hari ini --}}
              @foreach($absensiGuruHariIni as $idx => $ag)
                @php
                  $hpGuru = preg_replace('/[^0-9]/', '', $ag->guru?->no_hp ?? '');
                  if (str_starts_with($hpGuru, '0')) $hpGuru = '62' . substr($hpGuru, 1);
                @endphp
                <tr class="row-guru-absen" data-status="{{ $ag->status }}" data-pulang="{{ $ag->jam_pulang ? 'pulang' : 'belum' }}">
                  <td style="text-align:center; font-weight:700; color:var(--text-3); font-size:12px;">{{ $idx + 1 }}</td>
                  <td style="text-align:center; vertical-align:middle;">
                    <img src="{{ $ag->guru?->foto_url }}" alt="{{ $ag->guru?->nama }}" style="width:34px; height:34px; border-radius:50%; object-fit:cover; border:1.5px solid var(--border-2);" />
                  </td>
                  <td style="font-family:var(--font-mono); font-weight:700; color:var(--text); font-size:12px;">{{ $ag->guru?->nip ?: '-' }}</td>
                  <td>
                    <strong style="color:var(--text); font-size:13px;">{{ $ag->guru?->nama ?? '—' }}</strong>
                    @if($hpGuru)
                      <div style="margin-top:2px;">
                        <a href="https://wa.me/{{ $hpGuru }}" target="_blank" style="font-size:11px; color:#16A34A; text-decoration:none; font-weight:600; display:inline-flex; align-items:center; gap:3px;">
                          <i class="bi bi-whatsapp"></i> <span style="font-family:var(--font-mono);">{{ $ag->guru?->no_hp }}</span>
                        </a>
                      </div>
                    @endif
                  </td>
                  <td style="color:var(--text-2); font-size:12px;">{{ $ag->guru?->jabatan ?? 'Guru' }}</td>
                  <td style="font-family:var(--font-mono); font-size:12px; font-weight:800; color:var(--text); text-align:center;">
                    {{ $ag->jam_masuk ? substr($ag->jam_masuk,0,5) : '—' }}
                  </td>
                  <td style="font-family:var(--font-mono); font-size:12px; font-weight:700; color:var(--text); text-align:center;">
                    @if($ag->jam_pulang)
                      {{ substr($ag->jam_pulang,0,5) }}
                    @else
                      <span style="color:var(--text-3); font-style:italic; font-size:11px;">Belum Pulang</span>
                    @endif
                  </td>
                  <td style="text-align:center;">
                    @if($ag->status === 'hadir')
                      <span style="font-size:11.5px; font-weight:700; color:var(--text);">Hadir Tepat</span>
                    @elseif($ag->status === 'terlambat')
                      <span style="font-size:11.5px; font-weight:700; color:var(--text);">Terlambat</span>
                    @elseif(in_array($ag->status, ['izin', 'sakit', 'cuti', 'dispen', 'dinas_luar']))
                      <span style="font-size:11.5px; font-weight:700; color:var(--text);">{{ ucfirst(str_replace('_', ' ', $ag->status)) }}</span>
                    @else
                      <span style="font-size:11.5px; font-weight:700; color:var(--text);">{{ ucfirst($ag->status) }}</span>
                    @endif
                  </td>
                  <td>
                    @if(in_array($ag->sumber_absen, ['rfid', 'kios_rfid', 'barcode', 'scan_barcode', 'qr_code', 'kios_wajah', 'face_kiosk', 'kios_biometrik']) || empty($ag->sumber_absen))
                      <span style="font-size:11.5px; font-weight:700; color:var(--text);">Smart Gate</span>
                    @elseif($ag->sumber_absen === 'manual_izin_piket' || in_array($ag->status, ['izin', 'sakit', 'cuti', 'dispen', 'dinas_luar']))
                      <span style="font-size:11.5px; font-weight:700; color:var(--text);">Izin Dinas / Sakit</span>
                    @elseif($ag->sumber_absen === 'koreksi_piket_manual')
                      <span style="font-size:11.5px; font-weight:700; color:var(--text);">Koreksi Piket</span>
                    @elseif($ag->sumber_absen === 'manual_piket')
                      <span style="font-size:11.5px; font-weight:700; color:var(--text);">Manual Piket</span>
                    @else
                      <span style="font-size:11.5px; font-weight:700; color:var(--text);">{{ ucfirst(str_replace('_', ' ', $ag->sumber_absen)) }}</span>
                    @endif
                  </td>
                  <td style="text-align:right; white-space:nowrap;">
                    @if($canKoreksi ?? false)
                      <button type="button" class="btn btn-sm btn-outline" style="padding:3px 8px; font-size:11px; font-weight:700; color:#000000; border-color:rgba(0,0,0,0.25);" onclick="openKoreksiPiketModal({{ $ag->id }}, '{{ addslashes($ag->guru?->nama ?? 'Guru') }}', '{{ $ag->status }}', '{{ $ag->jam_masuk ? substr($ag->jam_masuk,0,5) : '' }}', '{{ $ag->jam_pulang ? substr($ag->jam_pulang,0,5) : '' }}', '{{ addslashes($ag->keterangan ?? '') }}')">
                        <i class="bi bi-pencil-square"></i> Koreksi
                      </button>
                    @else
                      <span style="color:var(--text-3); font-size:11px;">-</span>
                    @endif
                  </td>
                </tr>
              @endforeach

              {{-- 2. Guru Belum Hadir / Scan --}}
              @foreach($guruBelumHadirList as $gb)
                @php
                  $hpG = preg_replace('/[^0-9]/', '', $gb->no_hp ?? '');
                  if (str_starts_with($hpG, '0')) $hpG = '62' . substr($hpG, 1);
                @endphp
                <tr class="row-guru-absen" data-status="belum_hadir" data-pulang="belum">
                  <td style="text-align:center; font-weight:700; color:var(--text-3); font-size:12px;">-</td>
                  <td style="text-align:center; vertical-align:middle;">
                    <img src="{{ $gb->foto_url }}" alt="{{ $gb->nama }}" style="width:34px; height:34px; border-radius:50%; object-fit:cover; border:1.5px solid var(--border-2); opacity:0.85;" />
                  </td>
                  <td style="font-family:var(--font-mono); font-weight:700; color:var(--text); font-size:12px;">{{ $gb->nip ?: '-' }}</td>
                  <td>
                    <strong style="color:var(--text); font-size:13px;">{{ $gb->nama }}</strong>
                    @if($hpG)
                      <div style="margin-top:2px;">
                        <a href="https://wa.me/{{ $hpG }}" target="_blank" style="font-size:11px; color:#16A34A; text-decoration:none; font-weight:600; display:inline-flex; align-items:center; gap:3px;">
                          <i class="bi bi-whatsapp"></i> <span style="font-family:var(--font-mono);">{{ $gb->no_hp }}</span>
                        </a>
                      </div>
                    @endif
                  </td>
                  <td style="color:var(--text-2); font-size:12px;">{{ $gb->jabatan ?: 'Guru' }}</td>
                  <td style="font-family:var(--font-mono); font-size:12px; color:var(--text-3); text-align:center;">—</td>
                  <td style="font-family:var(--font-mono); font-size:12px; color:var(--text-3); text-align:center;">—</td>
                  <td style="text-align:center;">
                    <span class="badge" style="background:rgba(239,68,68,0.1); color:#DC2626; font-weight:800; font-size:11px; display:inline-flex; align-items:center; gap:3px;">
                      <i class="bi bi-exclamation-circle-fill"></i> Belum Scan
                    </span>
                  </td>
                  <td>
                    <span style="color:var(--text-3); font-size:11.5px;">—</span>
                  </td>
                  <td style="text-align:right; white-space:nowrap;">
                    @if($canKoreksi ?? false)
                      <button type="button" class="btn btn-sm btn-outline" style="padding:3px 8px; font-size:11px; font-weight:700; color:var(--text); border-color:var(--border-2);" onclick="openSetStatusGuruModal({{ $gb->id }}, '{{ addslashes($gb->nama) }}', '{{ addslashes($gb->jabatan ?: 'Guru') }}')" title="Catat Hadir Manual / Izin / Sakit / Dinas Luar">
                        <i class="bi bi-pencil-square"></i> Set Status
                      </button>
                    @else
                      <span style="color:var(--text-3); font-size:11px;">-</span>
                    @endif
                  </td>
                </tr>
              @endforeach

              @if($absensiGuruHariIni->count() + $guruBelumHadirList->count() === 0)
                <tr class="empty-row">
                  <td colspan="10" style="text-align:center; padding:36px; color:var(--text-3);">
                    <i class="bi bi-person-badge" style="font-size:32px; opacity:0.4; display:block; margin-bottom:6px;"></i>
                    Belum ada data dewan guru &amp; pegawai aktif hari ini.
                  </td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>

        {{-- Pagination Bar Guru --}}
        <div class="piket-pagination-bar" id="paginationGuruBar" style="padding:10px 14px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; background:var(--surface);">
          <div style="font-size:12px; color:var(--text-2); font-weight:600;" id="paginationGuruInfo">
            Menampilkan 1 - 20 dari {{ $guruBelumHadirList->count() + $absensiGuruHariIni->count() }} data
          </div>
          <div class="custom-pagination">
            <div class="pagination-wrapper" id="paginationGuruControls"></div>
          </div>
        </div>
      </div>
    </div>

  </main>
</div>

{{-- SCRIPT INTERAKTIF MEJA PIKET --}}
<script>
  // ── Clock Digital ──
  function updateLiveClock() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    const s = String(now.getSeconds()).padStart(2, '0');
    const el = document.getElementById('livePiketClock');
    if (el) el.textContent = `${h}:${m}:${s}`;
  }
  setInterval(updateLiveClock, 1000);

  // ── Main View Switcher (Siswa vs Guru) ──
  function switchMainView(view, btn) {
    // Toggle views
    const siswaPane = document.getElementById('view-siswa');
    const guruPane  = document.getElementById('view-guru');
    if (siswaPane) siswaPane.style.display = view === 'siswa' ? '' : 'none';
    if (guruPane)  guruPane.style.display  = view === 'guru'  ? '' : 'none';

    // Update button active state
    document.querySelectorAll('.piket-main-btn').forEach(el => el.classList.remove('active'));
    if (btn) btn.classList.add('active');

    // Trigger paginator render for the shown table
    if (view === 'siswa' && tableSiswaPiket_paginator) tableSiswaPiket_paginator.render();
    if (view === 'guru'  && tableGuruPiket_paginator)  tableGuruPiket_paginator.render();
  }

  // ── KPI Card Quick Filter (activates Siswa view + sets filter pill) ──
  function selectSiswaFilter(status) {
    // Switch to siswa view
    switchMainView('siswa', document.getElementById('btnViewSiswa'));

    // Find and click the matching filter pill
    const pills = document.querySelectorAll('#view-siswa .filter-pill');
    pills.forEach(pill => {
      pill.classList.remove('active');
      if (pill.getAttribute('data-filter') === status) {
        pill.classList.add('active');
      }
    });
    if (tableSiswaPiket_paginator) tableSiswaPiket_paginator.setFilter(status);
  }

  // ── Presensi Manual Live Search Logic ──
  function onPmKategoriChange() {
    const kat = document.getElementById('pmKategori').value;
    const label = document.getElementById('pmLabelNama');
    const input = document.getElementById('pmSearchInput');
    
    if (kat === 'siswa') {
      label.innerHTML = 'PILIH SISWA <span style="color:var(--red);">*</span>';
      input.placeholder = 'Ketik nama, NIS, atau kelas...';
    } else {
      label.innerHTML = 'PILIH GURU <span style="color:var(--red);">*</span>';
      input.placeholder = 'Ketik nama, NIP, atau jabatan...';
    }

    clearPmSelection();
  }

  function filterPmSearch(val) {
    const kat = document.getElementById('pmKategori').value;
    const q = (val || '').toLowerCase().trim();
    const dropdown = document.getElementById('pmDropdownMenu');
    
    // Jangan munculkan dropdown jika kolom input kosong / belum mengetik
    if (!q || q.length === 0) {
      dropdown.style.display = 'none';
      return;
    }

    const items = document.querySelectorAll('.pm-search-item');
    let visibleCount = 0;

    items.forEach(el => {
      const itemType = el.getAttribute('data-type');
      if (itemType !== kat) {
        el.style.display = 'none';
        return;
      }

      const name = (el.getAttribute('data-name') || '').toLowerCase();
      const sub = (el.getAttribute('data-sub') || '').toLowerCase();
      const match = name.includes(q) || sub.includes(q);

      el.style.display = match ? 'block' : 'none';
      if (match) visibleCount++;
    });

    const emptyMsg = document.getElementById('pmEmptyMsg');
    if (emptyMsg) emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
    
    dropdown.style.display = 'block';
  }

  function selectPmItem(type, id, name, sub) {
    document.getElementById('pmPemilikId').value = id;
    document.getElementById('pmSearchInput').value = name + ' (' + sub + ')';
    document.getElementById('pmClearBtn').style.display = 'block';
    const dropdown = document.getElementById('pmDropdownMenu');
    if (dropdown) dropdown.style.display = 'none';
  }

  function clearPmSelection() {
    document.getElementById('pmPemilikId').value = '';
    document.getElementById('pmSearchInput').value = '';
    document.getElementById('pmClearBtn').style.display = 'none';
    const dropdown = document.getElementById('pmDropdownMenu');
    if (dropdown) dropdown.style.display = 'none';
  }

  // Close dropdown when clicking outside
  document.addEventListener('click', function(e) {
    const wrap = document.getElementById('pmSearchWrap');
    if (wrap && !wrap.contains(e.target)) {
      const menu = document.getElementById('pmDropdownMenu');
      if (menu) menu.style.display = 'none';
    }
  });

  document.addEventListener('DOMContentLoaded', () => {
    onPmKategoriChange();
  });

  // ── REUSABLE CLIENT-SIDE TABLE PAGINATOR (20 BARIS / HALAMAN) ──
  class TablePaginator {
    constructor(tableId, infoId, controlsId, pageSize = 20, onRowFiltered = null) {
      this.tableId = tableId;
      this.infoId = infoId;
      this.controlsId = controlsId;
      this.pageSize = pageSize;
      this.currentPage = 1;
      this.onRowFiltered = onRowFiltered;
      this.filterStatus = 'all';
      this.searchQuery = '';
    }

    getRows() {
      const table = document.getElementById(this.tableId);
      if (!table) return [];
      return Array.from(table.querySelectorAll('tbody tr:not(.empty-row)'));
    }

    getMatchingRows() {
      const rows = this.getRows();
      return rows.filter(row => {
        let matchFilter = true;
        if (this.onRowFiltered) {
          matchFilter = this.onRowFiltered(row, this.filterStatus);
        }
        
        let matchSearch = true;
        if (this.searchQuery) {
          const text = row.innerText.toLowerCase();
          matchSearch = text.includes(this.searchQuery);
        }

        return matchFilter && matchSearch;
      });
    }

    setFilter(status) {
      this.filterStatus = status;
      this.currentPage = 1;
      this.render();
    }

    setSearch(query) {
      this.searchQuery = (query || '').toLowerCase().trim();
      this.currentPage = 1;
      this.render();
    }

    setPage(page) {
      this.currentPage = page;
      this.render();
    }

    render() {
      const allRows = this.getRows();
      const matchingRows = this.getMatchingRows();
      const totalItems = matchingRows.length;
      const totalPages = Math.max(1, Math.ceil(totalItems / this.pageSize));

      if (this.currentPage > totalPages) this.currentPage = totalPages;
      if (this.currentPage < 1) this.currentPage = 1;

      const startIndex = (this.currentPage - 1) * this.pageSize;
      const endIndex = startIndex + this.pageSize;

      // Hide all rows first
      allRows.forEach(row => { row.style.display = 'none'; });

      // Show matching rows in the current page slice
      matchingRows.forEach((row, idx) => {
        if (idx >= startIndex && idx < endIndex) {
          row.style.display = '';
          const firstTd = row.querySelector('td:first-child');
          if (firstTd && !isNaN(parseInt(firstTd.innerText))) {
            firstTd.innerText = idx + 1;
          }
        }
      });

      // Update Info Text
      const infoEl = document.getElementById(this.infoId);
      if (infoEl) {
        if (totalItems === 0) {
          infoEl.innerHTML = '<span style="color:var(--text-3);">Tidak ada data yang sesuai</span>';
        } else {
          const showStart = startIndex + 1;
          const showEnd = Math.min(endIndex, totalItems);
          infoEl.innerHTML = `Menampilkan <strong style="color:var(--text); font-family:var(--font-mono);">${showStart} - ${showEnd}</strong> dari <strong style="color:var(--text); font-family:var(--font-mono);">${totalItems}</strong> data`;
        }
      }

      // Render Pagination Buttons
      const controlsEl = document.getElementById(this.controlsId);
      if (controlsEl) {
        const paginatorVarName = `${this.tableId}_paginator`;
        let html = '';

        // Prev Button
        if (this.currentPage > 1) {
          html += `<button type="button" class="page-btn" onclick="${paginatorVarName}.setPage(${this.currentPage - 1})"><i class="bi bi-chevron-left"></i></button>`;
        } else {
          html += `<button type="button" class="page-btn disabled" disabled><i class="bi bi-chevron-left"></i></button>`;
        }

        // Page Numbers with Smart Ellipsis
        for (let p = 1; p <= totalPages; p++) {
          if (p === 1 || p === totalPages || (p >= this.currentPage - 1 && p <= this.currentPage + 1)) {
            if (p === this.currentPage) {
              html += `<button type="button" class="page-btn active">${p}</button>`;
            } else {
              html += `<button type="button" class="page-btn" onclick="${paginatorVarName}.setPage(${p})">${p}</button>`;
            }
          } else if (p === this.currentPage - 2 || p === this.currentPage + 2) {
            html += `<span style="padding:0 4px; color:var(--text-3); font-size:11px; align-self:center;">...</span>`;
          }
        }

        // Next Button
        if (this.currentPage < totalPages) {
          html += `<button type="button" class="page-btn" onclick="${paginatorVarName}.setPage(${this.currentPage + 1})"><i class="bi bi-chevron-right"></i></button>`;
        } else {
          html += `<button type="button" class="page-btn disabled" disabled><i class="bi bi-chevron-right"></i></button>`;
        }

        controlsEl.innerHTML = html;
      }
    }
  }

  // ── INSTANTIATE PAGINATORS ──
  let tableSiswaPiket_paginator = null;
  let tableGuruPiket_paginator = null;

  document.addEventListener('DOMContentLoaded', () => {
    // 1. Paginator Siswa (termasuk baris Belum Hadir)
    tableSiswaPiket_paginator = new TablePaginator(
      'tableSiswaPiket',
      'paginationSiswaInfo',
      'paginationSiswaControls',
      20,
      (row, status) => {
        const rowStatus = row.dataset.status || '';
        const rowPulang = row.dataset.pulang || '';
        if (status === 'all') return true;
        if (status === 'hadir') return rowStatus === 'hadir';
        if (status === 'terlambat') return rowStatus === 'terlambat';
        if (status === 'belum_hadir') return rowStatus === 'belum_hadir';
        if (status === 'izin') return ['izin', 'sakit', 'dispen'].includes(rowStatus);
        if (status === 'pkl') return rowStatus === 'pkl';
        if (status === 'bolos') return rowStatus === 'bolos';
        if (status === 'pulang') return rowPulang === 'pulang';
        return true;
      }
    );
    tableSiswaPiket_paginator.render();

    // 2. Paginator Guru (termasuk baris Belum Hadir)
    tableGuruPiket_paginator = new TablePaginator(
      'tableGuruPiket',
      'paginationGuruInfo',
      'paginationGuruControls',
      20,
      (row, status) => {
        const rowStatus = row.dataset.status || '';
        const rowPulang = row.dataset.pulang || '';
        if (status === 'all') return true;
        if (status === 'hadir') return rowStatus === 'hadir';
        if (status === 'terlambat') return rowStatus === 'terlambat';
        if (status === 'belum_hadir') return rowStatus === 'belum_hadir';
        if (status === 'izin') return ['izin', 'sakit', 'cuti', 'dispen', 'dinas_luar'].includes(rowStatus);
        if (status === 'pulang') return rowPulang === 'pulang';
        return true;
      }
    );
    tableGuruPiket_paginator.render();
  });

  // ── Filter & Search Handlers ──
  function filterSiswaTable(status, btn) {
    if (btn) {
      btn.parentElement.querySelectorAll('.filter-pill').forEach(el => el.classList.remove('active'));
      btn.classList.add('active');
    }
    if (tableSiswaPiket_paginator) tableSiswaPiket_paginator.setFilter(status);
  }

  function searchSiswaPiketTable() {
    const q = document.getElementById('searchSiswaPiket')?.value || '';
    if (tableSiswaPiket_paginator) tableSiswaPiket_paginator.setSearch(q);
  }

  function filterGuruTable(status, btn) {
    if (btn) {
      btn.parentElement.querySelectorAll('.filter-pill').forEach(el => el.classList.remove('active'));
      btn.classList.add('active');
    }
    if (tableGuruPiket_paginator) tableGuruPiket_paginator.setFilter(status);
  }

  function searchGuruPiketTable() {
    const q = document.getElementById('searchGuruPiket')?.value || '';
    if (tableGuruPiket_paginator) tableGuruPiket_paginator.setSearch(q);
  }

  // ── Modal Helper Global Meja Piket ──
  function openModal(id) {
    const el = document.getElementById(id);
    if (el) {
      el.classList.add('active');
      el.style.display = 'flex';
    }
  }

  function closeModal(id) {
    const el = document.getElementById(id);
    if (el) {
      el.classList.remove('active');
      el.style.display = 'none';
    }
  }

  // ── Modal Koreksi Presensi Meja Piket ──
  function openKoreksiPiketModal(id, nama, status, jamMasuk, jamPulang, keterangan) {
    const form = document.getElementById('formKoreksiPiket');
    if (form) form.action = '/piket/absensi/' + id;
    
    const elNama = document.getElementById('koreksiPiketNama');
    if (elNama) elNama.value = nama || '';

    const elStatus = document.getElementById('koreksiPiketStatus');
    if (elStatus) elStatus.value = status || 'hadir';

    const elMasuk = document.getElementById('koreksiPiketJamMasuk');
    if (elMasuk) elMasuk.value = jamMasuk ? jamMasuk.substring(0, 5) : '';

    const elPulang = document.getElementById('koreksiPiketJamPulang');
    if (elPulang) elPulang.value = jamPulang ? jamPulang.substring(0, 5) : '';

    const elKet = document.getElementById('koreksiPiketKeterangan');
    if (elKet) elKet.value = keterangan || '';

    openModal('modalKoreksiPiket');
  }

  // ── Modal Validasi Siswa Belum Hadir Meja Piket ──
  function openValidasiSiswaModal(id, nama, nis, rombel) {
    const elId = document.getElementById('validasiSiswaId');
    if (elId) elId.value = id;

    const elNama = document.getElementById('validasiSiswaNama');
    if (elNama) elNama.value = nama;

    const elSub = document.getElementById('validasiSiswaSub');
    if (elSub) elSub.textContent = nis + ' · ' + rombel;

    const elStatus = document.getElementById('validasiSiswaStatus');
    if (elStatus) elStatus.value = 'izin';

    const elKet = document.getElementById('validasiSiswaKeterangan');
    if (elKet) elKet.value = '';

    onValidasiStatusChange('izin');
    openModal('modalValidasiSiswa');
  }

  function onValidasiStatusChange(val) {
    const wrapJam = document.getElementById('validasiWrapJamMasuk');
    const jamInput = document.getElementById('validasiSiswaJamMasuk');
    const wrapSurat = document.getElementById('validasiWrapSurat');

    if (val === 'hadir' || val === 'terlambat') {
      if (wrapJam) wrapJam.style.display = 'block';
      if (wrapSurat) wrapSurat.style.display = 'none';
      if (jamInput && !jamInput.value) {
        const now = new Date();
        jamInput.value = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
      }
    } else if (val === 'izin' || val === 'sakit' || val === 'dispen') {
      if (wrapJam) wrapJam.style.display = 'none';
      if (wrapSurat) wrapSurat.style.display = 'block';
      if (jamInput) jamInput.value = '';
    } else {
      if (wrapJam) wrapJam.style.display = 'none';
      if (wrapSurat) wrapSurat.style.display = 'none';
      if (jamInput) jamInput.value = '';
    }
  }

  // Close modal when clicking on backdrop
  document.addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('modal-overlay')) {
      closeModal(e.target.id);
    }
  });

  document.addEventListener('DOMContentLoaded', () => {
    updatePmSelect();
  });
</script>

{{-- MODAL PRESENSI MANUAL MEJA PIKET --}}
<div class="modal-overlay" id="modalPresensiManual">
  <div class="modal-card" style="max-width:540px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:36px; height:36px; border-radius:8px; background:rgba(0,0,0,0.06); color:#000000; display:flex; align-items:center; justify-content:center; font-size:18px;">
          <i class="bi bi-clipboard-plus-fill"></i>
        </div>
        <div>
          <h3 style="font-size:16px; font-weight:900; color:var(--text); margin:0;">Presensi Manual Piket</h3>
          <div style="font-size:11.5px; color:var(--text-3);">Catat kehadiran jika kartu tertinggal atau verifikasi manual</div>
        </div>
      </div>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('modalPresensiManual')"><i class="bi bi-x-lg"></i></button>
    </div>

    <form method="POST" action="{{ route('piket.presensi-manual.store') }}">
      @csrf
      <div style="display:flex; flex-direction:column; gap:14px;">
        
        {{-- Kategori --}}
        <div class="form-group" style="margin-bottom:0;">
          <label style="font-size:11.5px; font-weight:700; text-transform:uppercase; color:var(--text-2); margin-bottom:4px; display:block;">Kategori <span style="color:var(--red);">*</span></label>
          <select name="kategori" id="pmKategori" onchange="onPmKategoriChange()" required style="width:100%; height:40px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px; color:var(--text); font-size:13px; font-weight:700;">
            <option value="siswa">Peserta Didik (Siswa)</option>
            <option value="guru">Guru / Tenaga Pendidik</option>
          </select>
        </div>

        {{-- Pilih Siswa / Guru (Live Search Input) --}}
        <div class="form-group" style="margin-bottom:0; position:relative;">
          <label id="pmLabelNama" style="font-size:11.5px; font-weight:700; text-transform:uppercase; color:var(--text-2); margin-bottom:4px; display:block;">PILIH SISWA <span style="color:var(--red);">*</span></label>
          <div style="position:relative;" id="pmSearchWrap">
            <i class="bi bi-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-3); font-size:13px; pointer-events:none;"></i>
            <input 
              type="text" 
              id="pmSearchInput" 
              placeholder="Ketik nama, NIS, atau kelas..." 
              autocomplete="off"
              oninput="filterPmSearch(this.value)"
              required
              style="width:100%; height:40px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 34px 0 34px; color:var(--text); font-size:13px; font-weight:600;" 
            />
            <button 
              type="button" 
              id="pmClearBtn" 
              onclick="clearPmSelection()" 
              style="display:none; position:absolute; right:8px; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--text-3); cursor:pointer; padding:4px 6px; font-size:14px;"
              title="Hapus pilihan"
            >
              <i class="bi bi-x-circle-fill"></i>
            </button>

            <input type="hidden" name="pemilik_id" id="pmPemilikId" required />

            {{-- Live Search Dropdown Panel --}}
            <div id="pmDropdownMenu" class="pm-search-dropdown" style="display:none; position:absolute; top:calc(100% + 4px); left:0; right:0; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-md); box-shadow:var(--shadow-lg); z-index:1100;">
              <div id="pmItemsContainer" style="max-height:220px; overflow-y:auto;">
                {{-- Siswa Items --}}
                @foreach($semuaSiswa as $s)
                  @php 
                    $rombel = $s->siswaRombels->where('status_keanggotaan', 'aktif')->first()?->rombel?->nama_rombel ?? 'Tanpa Rombel'; 
                  @endphp
                  <div 
                    class="pm-search-item" 
                    data-type="siswa"
                    data-id="{{ $s->id }}"
                    data-name="{{ $s->nama }}"
                    data-sub="NISN: {{ $s->nisn ?? '-' }} · {{ $rombel }}"
                    onclick="selectPmItem('siswa', '{{ $s->id }}', '{{ addslashes($s->nama) }}', 'NISN: {{ $s->nisn ?? '-' }} · {{ addslashes($rombel) }}')"
                  >
                    <div style="font-weight:700; color:var(--text);">{{ $s->nama }}</div>
                    <div style="font-size:11.5px; color:var(--text-3); font-family:var(--font-mono);">NISN: {{ $s->nisn ?? '-' }} · {{ $rombel }}</div>
                  </div>
                @endforeach

                {{-- Guru Items --}}
                @foreach($semuaGuru as $g)
                  <div 
                    class="pm-search-item" 
                    data-type="guru"
                    data-id="{{ $g->id }}"
                    data-name="{{ $g->nama }}"
                    data-sub="{{ $g->jabatan ?? 'Guru' }} · NIP: {{ $g->nip ?: '-' }}"
                    style="display:none;"
                    onclick="selectPmItem('guru', '{{ $g->id }}', '{{ addslashes($g->nama) }}', '{{ addslashes($g->jabatan ?? 'Guru') }} · NIP: {{ $g->nip ?: '-' }}')"
                  >
                    <div style="font-weight:700; color:var(--text);">{{ $g->nama }}</div>
                    <div style="font-size:11.5px; color:var(--text-3); font-family:var(--font-mono);">{{ $g->jabatan ?? 'Guru' }} · NIP: {{ $g->nip ?: '-' }}</div>
                  </div>
                @endforeach

                <div id="pmEmptyMsg" style="display:none; padding:12px; text-align:center; color:var(--text-3); font-size:12.5px;">
                  <i class="bi bi-search" style="margin-right:4px;"></i> Data tidak ditemukan
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Sesi --}}
        <div class="form-group" style="margin-bottom:0;">
          <label style="font-size:11.5px; font-weight:700; text-transform:uppercase; color:var(--text-2); margin-bottom:4px; display:block;">Sesi Presensi <span style="color:var(--red);">*</span></label>
          <select name="sesi" required style="width:100%; height:40px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px; color:var(--text); font-size:13px; font-weight:700;">
            <option value="masuk">Sesi Masuk Pagi</option>
            <option value="pulang">Sesi Pulang Siang/Sore</option>
          </select>
        </div>

        {{-- Keterangan --}}
        <div class="form-group" style="margin-bottom:0;">
          <label style="font-size:11.5px; font-weight:700; text-transform:uppercase; color:var(--text-2); margin-bottom:4px; display:block;">Keterangan</label>
          <input type="text" name="keterangan" placeholder="Contoh: Lupa Kartu / Izin Lisan" style="width:100%; height:40px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px; color:var(--text); font-size:13px;" />
        </div>

        {{-- Action Buttons --}}
        <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:8px;">
          <button type="button" class="btn btn-outline" onclick="closeModal('modalPresensiManual')">Batal</button>
          <button type="submit" class="btn btn-gold" style="font-weight:800; display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-check2-circle"></i> Catat Presensi
          </button>
        </div>

      </div>
    </form>
  </div>
</div>

{{-- MODAL KOREKSI PRESENSI MEJA PIKET --}}
<div class="modal-overlay" id="modalKoreksiPiket">
  <div class="modal-card" style="max-width:500px; padding:24px; border-radius:14px; background:var(--bg-2); border:1px solid var(--border); box-shadow:var(--shadow-lg);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--border); padding-bottom:12px;">
      <h3 style="font-size:16.5px; font-weight:900; color:var(--text); margin:0; display:flex; align-items:center; gap:8px;">
        <i class="bi bi-pencil-square" style="color:var(--text); font-size:18px;"></i> Koreksi Presensi Piket
      </h3>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('modalKoreksiPiket')" style="width:34px; height:34px; padding:0; display:flex; align-items:center; justify-content:center; border-radius:8px; border:1px solid var(--border-2); color:var(--text);"><i class="bi bi-x-lg" style="font-size:14px;"></i></button>
    </div>

    <form id="formKoreksiPiket" method="POST">
      @csrf
      @method('PUT')

      {{-- Nama Target --}}
      <div class="form-group" style="margin-bottom:14px;">
        <label style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.03em; color:var(--text-2); margin-bottom:5px; display:block;">NAMA PESERTA / GURU</label>
        <input type="text" id="koreksiPiketNama" readonly style="width:100%; height:42px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:8px; padding:0 14px; font-weight:800; color:var(--text); font-size:13.5px;" />
      </div>

      {{-- Status Presensi --}}
      <div class="form-group" style="margin-bottom:12px;">
        <label style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.03em; color:var(--text-2); margin-bottom:5px; display:block;">STATUS KEHADIRAN <span style="color:var(--red);">*</span></label>
        <select name="status" id="koreksiPiketStatus" required style="width:100%; height:42px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:8px; padding:0 12px; font-weight:800; color:var(--text); font-size:13px;" onchange="handleKoreksiStatusChange(this.value)">
          <option value="hadir">Hadir Tepat Waktu</option>
          <option value="terlambat">Datang Terlambat</option>
          <option value="alpha">Alpha / Belum Scan (Kembalikan Status)</option>
          <option value="titip_kartu">🚫 Dibatalkan — Terindikasi Titip Kartu (Alpha)</option>
          <option value="bolos">Bolos Kelas</option>
          <option value="izin">Izin</option>
          <option value="sakit">Sakit</option>
          <option value="dispen">Dispensasi</option>
        </select>
      </div>

      {{-- Quick Action Intervensi Chips --}}
      <div style="margin-bottom:14px; display:flex; flex-wrap:wrap; gap:6px;">
        <button type="button" onclick="setPresetKoreksi('titip_kartu')" class="btn btn-sm btn-outline" style="font-size:11px; font-weight:800; border-radius:6px; padding:4px 10px; color:#dc2626; border-color:rgba(220,38,38,0.3); background:rgba(220,38,38,0.06);">
          <i class="bi bi-slash-circle"></i> 🚫 Intervensi Titip Kartu
        </button>
        <button type="button" onclick="setPresetKoreksi('alpha')" class="btn btn-sm btn-outline" style="font-size:11px; font-weight:800; border-radius:6px; padding:4px 10px; color:var(--text-2); border-color:var(--border-2);">
          <i class="bi bi-arrow-counterclockwise"></i> Reset ke Alpha
        </button>
        <button type="button" onclick="setPresetKoreksi('bolos')" class="btn btn-sm btn-outline" style="font-size:11px; font-weight:800; border-radius:6px; padding:4px 10px; color:#d97706; border-color:rgba(217,119,6,0.3); background:rgba(217,119,6,0.06);">
          <i class="bi bi-exclamation-triangle"></i> Bolos Kelas
        </button>
      </div>

      {{-- Waktu Masuk & Pulang --}}
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
        <div>
          <label style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.03em; color:var(--text-2); margin-bottom:5px; display:block;">Jam Masuk</label>
          <input type="time" name="jam_masuk" id="koreksiPiketJamMasuk" style="width:100%; height:40px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:8px; padding:0 12px; color:var(--text); font-size:13px; font-family:var(--font-mono); font-weight:700;" />
        </div>
        <div>
          <label style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.03em; color:var(--text-2); margin-bottom:5px; display:block;">Jam Pulang</label>
          <input type="time" name="jam_pulang" id="koreksiPiketJamPulang" style="width:100%; height:40px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:8px; padding:0 12px; color:var(--text); font-size:13px; font-family:var(--font-mono); font-weight:700;" />
        </div>
      </div>

      {{-- Keterangan Alasan Koreksi --}}
      <div class="form-group" style="margin-bottom:20px;">
        <label style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.03em; color:var(--text-2); margin-bottom:5px; display:block;">ALASAN / CATATAN KOREKSI PIKET</label>
        <input type="text" name="keterangan" id="koreksiPiketKeterangan" placeholder="Contoh: Validasi Guru Piket (Administrator)" style="width:100%; height:42px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:8px; padding:0 14px; color:var(--text); font-size:13px; font-weight:600;" />
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid var(--border); padding-top:14px;">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalKoreksiPiket')" style="height:40px; padding:0 18px; font-weight:800; border-radius:8px;">Batal</button>
        <button type="submit" class="btn" style="background:#000000; color:#FFFFFF; border:1px solid #000000; font-weight:800; display:inline-flex; align-items:center; gap:6px; padding:0 20px; height:40px; border-radius:8px; cursor:pointer;">
          <i class="bi bi-check2"></i> Simpan Koreksi
        </button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL VALIDASI PRESENSI SISWA BELUM HADIR --}}
<div class="modal-overlay" id="modalValidasiSiswa">
  <div class="modal-card" style="max-width:480px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
      <div>
        <h3 style="font-size:16px; font-weight:900; color:var(--text); margin:0;">Catat Keterangan Kehadiran Siswa</h3>
        <div style="font-size:11.5px; color:var(--text-3); margin-top:2px;">Simpan konfirmasi izin, sakit, dispensasi, atau hadir manual</div>
      </div>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('modalValidasiSiswa')"><i class="bi bi-x-lg"></i></button>
    </div>

    <form method="POST" action="{{ route('piket.validasi-siswa') }}" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="siswa_id" id="validasiSiswaId" required />

      <div style="display:flex; flex-direction:column; gap:14px;">
        {{-- Siswa Info --}}
        <div>
          <label style="font-size:11.5px; font-weight:700; color:var(--text-2); margin-bottom:4px; display:block;">PESERTA DIDIK</label>
          <input type="text" id="validasiSiswaNama" readonly style="width:100%; height:38px; background:var(--bg-2); border:1px solid var(--border); border-radius:var(--r-sm); padding:0 12px; font-weight:800; color:var(--text); font-size:13px;" />
          <div id="validasiSiswaSub" style="font-size:11.5px; color:var(--text-3); font-family:var(--font-mono); margin-top:3px;"></div>
        </div>

        {{-- Status Kehadiran --}}
        <div>
          <label style="font-size:11.5px; font-weight:700; color:var(--text-2); margin-bottom:4px; display:block;">STATUS KEHADIRAN <span style="color:var(--red);">*</span></label>
          <select name="status" id="validasiSiswaStatus" required onchange="onValidasiStatusChange(this.value)" style="width:100%; height:40px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px; font-weight:700; color:var(--text); font-size:13px;">
            <option value="izin">Izin (I) — Ada Surat / Keperluan</option>
            <option value="sakit">Sakit (S) — Keterangan Sakit</option>
            <option value="hadir">Hadir Tepat Waktu (H) — Hadir Manual</option>
            <option value="terlambat">Terlambat (T) — Tiba Siang</option>
            <option value="dispen">Dispensasi (D) — Tugas / Lomba</option>
            <option value="alpha">Alpha (A) — Tanpa Keterangan</option>
            <option value="bolos">Bolos (B) — Tidak Masuk Kelas</option>
          </select>
        </div>

        {{-- Unggah Surat Bukti (Muncul Saat Status Izin / Sakit / Dispen) --}}
        <div id="validasiWrapSurat" style="display:block;">
          <label style="font-size:11.5px; font-weight:700; color:var(--text-2); margin-bottom:4px; display:block;">
            Foto / Berkas Surat Bukti (Surat Dokter / Surat Izin)
          </label>
          <input type="file" name="file_pendukung" id="validasiFilePendukung" accept=".pdf,.jpg,.jpeg,.png,.webp" style="width:100%; height:38px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:6px 10px; font-size:12px; color:var(--text);" />
          <div style="font-size:11px; color:var(--text-3); margin-top:2px;">Format: PDF, JPG, PNG (Opsional / Maks 10MB)</div>
        </div>

        {{-- Jam Masuk --}}
        <div id="validasiWrapJamMasuk" style="display:none;">
          <label style="font-size:11.5px; font-weight:700; color:var(--text-2); margin-bottom:4px; display:block;">JAM MASUK</label>
          <input type="time" name="jam_masuk" id="validasiSiswaJamMasuk" style="width:100%; height:38px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 10px; color:var(--text); font-size:13px;" />
        </div>

        {{-- Catatan / Keterangan --}}
        <div>
          <label style="font-size:11.5px; font-weight:700; color:var(--text-2); margin-bottom:4px; display:block;">ALASAN / CATATAN GURU PIKET</label>
          <input type="text" name="keterangan" id="validasiSiswaKeterangan" placeholder="Contoh: Orang tua konfirmasi via WA / Surat dokter" style="width:100%; height:38px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px; color:var(--text); font-size:13px;" />
        </div>

        <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:6px;">
          <button type="button" class="btn btn-outline" onclick="closeModal('modalValidasiSiswa')">Batal</button>
          <button type="submit" class="btn" style="background:#000000; color:#FFFFFF; border:1px solid #000000; font-weight:800; display:inline-flex; align-items:center; gap:6px; padding:0 16px; height:38px; border-radius:var(--r-sm); cursor:pointer;">
            <i class="bi bi-check2-circle"></i> Simpan Status
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- MODAL CATAT IZIN OPERASIONAL PIKET (SISWA & GURU) --}}
<div class="modal-overlay" id="modalCatatIzinPiket">
  <div class="modal-card" style="max-width:540px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--border); padding-bottom:10px;">
      <div>
        <h3 style="font-size:16px; font-weight:900; color:var(--text); margin:0; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-envelope-paper-fill" style="color:#000000;"></i> Catat Surat Izin / Keterangan Tidak Hadir
        </h3>
        <div style="font-size:11px; color:var(--text-3); margin-top:2px;">Perekaman izin langsung di meja piket harian</div>
      </div>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('modalCatatIzinPiket')"><i class="bi bi-x-lg"></i></button>
    </div>

    {{-- Kategori Switcher: Siswa vs Guru --}}
    <div style="display:flex; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:3px; gap:4px; margin-bottom:14px;">
      <button type="button" id="tabIzinSiswaBtn" onclick="switchIzinPiketTab('siswa')" style="flex:1; height:30px; font-size:12px; font-weight:800; border-radius:4px; background:#000000; color:#FFFFFF; border:none; cursor:pointer;">
        Izin Siswa
      </button>
      <button type="button" id="tabIzinGuruBtn" onclick="switchIzinPiketTab('guru')" style="flex:1; height:30px; font-size:12px; font-weight:800; border-radius:4px; background:transparent; color:var(--text-2); border:none; cursor:pointer;">
        Izin Guru / Pegawai
      </button>
    </div>

    <form method="POST" action="{{ route('izin-siswa.store') }}" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="kategori" id="izinPiketKategori" value="siswa" />

      {{-- Pilih Siswa / Guru (Live Search Input) --}}
      <div class="form-group" style="margin-bottom:14px; position:relative;">
        <label id="izinLabelNama" style="font-size:11.5px; font-weight:700; text-transform:uppercase; color:var(--text-2); margin-bottom:4px; display:block;">
          PILIH SISWA <span style="color:var(--red);">*</span>
        </label>

        <div style="position:relative;" id="izinSearchWrap">
          <i class="bi bi-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-3); font-size:13px; pointer-events:none;"></i>
          <input 
            type="text" 
            id="izinSearchInput" 
            placeholder="Ketik nama siswa, NIS, atau rombel..." 
            autocomplete="off"
            oninput="filterIzinSearch(this.value)"
            onfocus="filterIzinSearch(this.value)"
            required
            style="width:100%; height:40px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 34px 0 34px; color:var(--text); font-size:13px; font-weight:600;" 
          />
          <button 
            type="button" 
            id="izinClearBtn" 
            onclick="clearIzinSelection()" 
            style="display:none; position:absolute; right:8px; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--text-3); cursor:pointer; padding:4px 6px; font-size:14px;"
            title="Hapus pilihan"
          >
            <i class="bi bi-x-circle-fill"></i>
          </button>

          <input type="hidden" name="siswa_id" id="izinSiswaIdInput" required />
          <input type="hidden" name="guru_id" id="izinGuruIdInput" />

          {{-- Live Search Dropdown Panel --}}
          <div id="izinDropdownMenu" class="pm-search-dropdown" style="display:none; position:absolute; top:calc(100% + 4px); left:0; right:0; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-md); box-shadow:var(--shadow-lg); z-index:1100;">
            <div id="izinItemsContainer" style="max-height:220px; overflow-y:auto;">
              {{-- Siswa Items --}}
              @foreach($semuaSiswa as $s)
                @php 
                  $rombel = $s->siswaRombels->where('status_keanggotaan', 'aktif')->first()?->rombel?->nama_rombel ?? 'Tanpa Rombel'; 
                @endphp
                <div 
                  class="pm-search-item izin-search-item" 
                  data-type="siswa"
                  data-id="{{ $s->id }}"
                  data-name="{{ $s->nama }}"
                  data-sub="NISN: {{ $s->nisn ?? '-' }} · {{ $rombel }}"
                  onclick="selectIzinItem('siswa', '{{ $s->id }}', '{{ addslashes($s->nama) }}', 'NISN: {{ $s->nisn ?? '-' }} · {{ addslashes($rombel) }}')"
                >
                  <div style="font-weight:700; color:var(--text);">{{ $s->nama }}</div>
                  <div style="font-size:11.5px; color:var(--text-3); font-family:var(--font-mono);">NISN: {{ $s->nisn ?? '-' }} · {{ $rombel }}</div>
                </div>
              @endforeach

              {{-- Guru Items --}}
              @foreach($semuaGuru as $g)
                <div 
                  class="pm-search-item izin-search-item" 
                  data-type="guru"
                  data-id="{{ $g->id }}"
                  data-name="{{ $g->nama }}"
                  data-sub="{{ $g->jabatan ?? 'Guru' }} · NIP: {{ $g->nip ?: '-' }}"
                  style="display:none;"
                  onclick="selectIzinItem('guru', '{{ $g->id }}', '{{ addslashes($g->nama) }}', '{{ addslashes($g->jabatan ?? 'Guru') }} · NIP: {{ $g->nip ?: '-' }}')"
                >
                  <div style="font-weight:700; color:var(--text);">{{ $g->nama }}</div>
                  <div style="font-size:11.5px; color:var(--text-3); font-family:var(--font-mono);">{{ $g->jabatan ?? 'Guru' }} · NIP: {{ $g->nip ?: '-' }}</div>
                </div>
              @endforeach

              <div id="izinEmptyMsg" style="display:none; padding:12px; text-align:center; color:var(--text-3); font-size:12.5px;">
                <i class="bi bi-search" style="margin-right:4px;"></i> Data tidak ditemukan
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Tanggal & Jenis Izin --}}
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
        <div>
          <label style="font-size:11.5px; font-weight:700; color:var(--text-2); margin-bottom:4px; display:block;">
            TANGGAL <span style="color:var(--red);">*</span>
          </label>
          <input type="date" name="tanggal" value="{{ $today }}" required class="input-field" style="width:100%; height:38px; font-family:var(--font-mono); font-size:12.5px;" />
        </div>
        <div>
          <label style="font-size:11.5px; font-weight:700; color:var(--text-2); margin-bottom:4px; display:block;">
            JENIS KETERANGAN <span style="color:var(--red);">*</span>
          </label>
          <select name="jenis" id="selectIzinJenis" class="input-field" style="width:100%; height:38px; font-weight:700; font-size:12.5px;" required>
            <option value="izin">Izin (Keperluan Keluarga / Acara)</option>
            <option value="sakit">Sakit (Keterangan Dokter / Orang Tua)</option>
            <option value="dispen">Dispensasi (Lomba / Tugas Sekolah)</option>
            <option value="pulang_cepat">Pulang Cepat (Izin Khusus Piket)</option>
            <option value="dinas_luar" id="optDinasLuar" style="display:none;">Dinas Luar / Pelatihan</option>
            <option value="cuti" id="optCuti" style="display:none;">Cuti Resmi</option>
          </select>
        </div>
      </div>

      {{-- Alasan / Keterangan --}}
      <div style="margin-bottom:12px;">
        <label style="font-size:11.5px; font-weight:700; color:var(--text-2); margin-bottom:4px; display:block;">
          KETERANGAN / ALASAN <span style="color:var(--red);">*</span>
        </label>
        <textarea name="keterangan" rows="2" placeholder="Tuliskan keterangan izin atau nomor surat..." required class="input-field" style="width:100%; font-size:12.5px;"></textarea>
      </div>

      {{-- Unggah Surat Bukti --}}
      <div style="margin-bottom:18px;">
        <label style="font-size:11.5px; font-weight:700; color:var(--text-2); margin-bottom:4px; display:block;">
          SURAT BUKTI / FOTO DOKUMEN (OPSIONAL)
        </label>
        <input type="file" name="file_pendukung" accept=".pdf,.jpg,.jpeg,.png,.webp" class="input-field" style="width:100%; height:38px; padding:6px 10px; font-size:12px;" />
        <div style="font-size:10.5px; color:var(--text-3); margin-top:2px;">Format: PDF, JPG, PNG (Maks 5MB)</div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalCatatIzinPiket')">Batal</button>
        <button type="submit" class="btn btn-gold" style="background:#000000; color:#FFFFFF; border:none; font-weight:800; height:38px; padding:0 16px;">
          Simpan Izin ke Presensi
        </button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL JADWAL PIKET & JAM OPERASIONAL SEKOLAH --}}
<div class="modal-overlay" id="modalJadwalPiketView">
  <div class="modal-card" style="max-width:680px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--border); padding-bottom:10px;">
      <div>
        <h3 style="font-size:16px; font-weight:900; color:var(--text); margin:0; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-calendar-week-fill" style="color:#000000;"></i> Jadwal Piket Harian &amp; Jam Sesi Gerbang
        </h3>
        <div style="font-size:11px; color:var(--text-3); margin-top:2px;">Informasi penugasan guru piket dan batas waktu sekolah</div>
      </div>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('modalJadwalPiketView')"><i class="bi bi-x-lg"></i></button>
    </div>

    {{-- Jam Operasional Hari Ini --}}
    <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:12px 14px; margin-bottom:16px;">
      <div style="font-size:11px; font-weight:800; text-transform:uppercase; color:#000000; margin-bottom:8px; display:flex; align-items:center; gap:6px;">
        <i class="bi bi-clock-fill"></i> Jam Operasional Hari Ini ({{ $hariHariIni }}, {{ \Carbon\Carbon::parse($today)->translatedFormat('d F Y') }}):
      </div>
      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(130px, 1fr)); gap:10px;">
        <div style="background:var(--bg-2); padding:8px 10px; border-radius:6px; border:1px solid var(--border-2);">
          <div style="font-size:10px; color:var(--text-3); font-weight:700; text-transform:uppercase;">Batas Masuk Tepat</div>
          <div style="font-size:14px; font-weight:900; font-family:var(--font-mono); color:#000000;">{{ substr($jadwal->jam_masuk_mulai ?? '07:00', 0, 5) }} - {{ substr($jadwal->jam_masuk_toleransi ?? '07:15', 0, 5) }}</div>
        </div>
        <div style="background:var(--bg-2); padding:8px 10px; border-radius:6px; border:1px solid var(--border-2);">
          <div style="font-size:10px; color:var(--text-3); font-weight:700; text-transform:uppercase;">Toleransi Terlambat</div>
          <div style="font-size:14px; font-weight:900; font-family:var(--font-mono); color:#000000;">&gt; {{ substr($jadwal->jam_masuk_toleransi ?? '07:15', 0, 5) }} WIB</div>
        </div>
        <div style="background:var(--bg-2); padding:8px 10px; border-radius:6px; border:1px solid var(--border-2);">
          <div style="font-size:10px; color:var(--text-3); font-weight:700; text-transform:uppercase;">Mulai Jam Pulang</div>
          <div style="font-size:14px; font-weight:900; font-family:var(--font-mono); color:#000000;">{{ substr($jadwal->jam_pulang_mulai ?? '15:30', 0, 5) }} WIB</div>
        </div>
        <div style="background:var(--bg-2); padding:8px 10px; border-radius:6px; border:1px solid var(--border-2);">
          <div style="font-size:10px; color:var(--text-3); font-weight:700; text-transform:uppercase;">Tutup Gerbang</div>
          <div style="font-size:14px; font-weight:900; font-family:var(--font-mono); color:#000000;">{{ substr($jadwal->jam_tutup_gerbang ?? '18:00', 0, 5) }} WIB</div>
        </div>
      </div>
    </div>

    {{-- Tabel Jadwal Piket Seminggu --}}
    <div style="font-size:11px; font-weight:800; text-transform:uppercase; color:var(--text); margin-bottom:8px;">
      Jadwal Petugas Piket Mingguan:
    </div>

    <div style="border:1px solid var(--border); border-radius:var(--r-sm); overflow:hidden; max-height:280px; overflow-y:auto; margin-bottom:16px;">
      <table style="width:100%; border-collapse:collapse; font-size:12px;">
        <thead>
          <tr style="background:var(--bg-3); border-bottom:1px solid var(--border);">
            <th style="padding:8px 12px; text-align:left; font-weight:800; width:100px;">Hari</th>
            <th style="padding:8px 12px; text-align:left; font-weight:800;">Daftar Petugas Guru Piket</th>
          </tr>
        </thead>
        <tbody>
          @foreach($hariList as $h)
            @php
              $isHariIni = ($h === $hariHariIni);
              $piketsHari = $jadwalPiketSeminggu->get($h) ?? collect();
            @endphp
            <tr style="border-bottom:1px solid var(--border); {{ $isHariIni ? 'background:rgba(0,0,0,0.04);' : '' }}">
              <td style="padding:10px 12px; font-weight:800; color:{{ $isHariIni ? '#000000' : 'var(--text)' }};">
                {{ $h }}
                @if($isHariIni)
                  <span class="badge" style="background:#000000; color:#FFFFFF; font-size:9.5px; font-weight:800; margin-left:4px; padding:1px 5px;">HARI INI</span>
                @endif
              </td>
              <td style="padding:10px 12px;">
                @if($piketsHari->isNotEmpty())
                  <div style="display:flex; flex-wrap:wrap; gap:6px;">
                    @foreach($piketsHari as $jp)
                      <span class="badge" style="background:var(--bg-2); border:1px solid var(--border-2); color:var(--text); font-size:11px; font-weight:700; padding:3px 8px;">
                        {{ $jp->guru->nama ?? 'Guru' }}
                        <span style="color:var(--text-3); font-size:9.5px; margin-left:3px;">({{ ucfirst($jp->peran ?? 'Anggota') }})</span>
                      </span>
                    @endforeach
                  </div>
                @else
                  <span style="color:var(--text-3); font-size:11.5px; font-style:italic;">Belum ada jadwal petugas</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
      @if(auth()->user()?->isAdmin() || auth()->user()?->isKepalaSekolah() || auth()->user()?->isWakaKurikulum())
        <a href="{{ route('jadwal-piket.index') }}" class="btn btn-sm btn-outline" style="font-size:11.5px; font-weight:800; text-decoration:none;">
          <i class="bi bi-gear"></i> Kelola Jadwal Piket Lengkap &rarr;
        </a>
      @else
        <div></div>
      @endif
      <button type="button" class="btn btn-outline" onclick="closeModal('modalJadwalPiketView')">Tutup</button>
    </div>
  </div>
</div>

<script>
  let currentIzinType = 'siswa';

  function switchIzinPiketTab(type) {
    currentIzinType = type;
    const btnSiswa = document.getElementById('tabIzinSiswaBtn');
    const btnGuru = document.getElementById('tabIzinGuruBtn');
    const labelNama = document.getElementById('izinLabelNama');
    const inputSearch = document.getElementById('izinSearchInput');
    const inputKat = document.getElementById('izinPiketKategori');
    const optDinas = document.getElementById('optDinasLuar');
    const optCuti = document.getElementById('optCuti');

    inputKat.value = type;
    clearIzinSelection();

    if (type === 'siswa') {
      btnSiswa.style.background = '#000000';
      btnSiswa.style.color = '#FFFFFF';
      btnGuru.style.background = 'transparent';
      btnGuru.style.color = 'var(--text-2)';
      labelNama.innerHTML = 'PILIH SISWA <span style="color:var(--red);">*</span>';
      inputSearch.placeholder = 'Ketik nama siswa, NIS, atau rombel...';
      if (optDinas) optDinas.style.display = 'none';
      if (optCuti) optCuti.style.display = 'none';
    } else {
      btnGuru.style.background = '#000000';
      btnGuru.style.color = '#FFFFFF';
      btnSiswa.style.background = 'transparent';
      btnSiswa.style.color = 'var(--text-2)';
      labelNama.innerHTML = 'PILIH GURU / PEGAWAI <span style="color:var(--red);">*</span>';
      inputSearch.placeholder = 'Ketik nama guru, NIP, atau jabatan...';
      if (optDinas) optDinas.style.display = 'block';
      if (optCuti) optCuti.style.display = 'block';
    }
  }

  function filterIzinSearch(val) {
    const dropdown = document.getElementById('izinDropdownMenu');
    const items = document.querySelectorAll('.izin-search-item');
    const emptyMsg = document.getElementById('izinEmptyMsg');
    const q = (val || '').toLowerCase().trim();

    dropdown.style.display = 'block';
    let count = 0;

    items.forEach(item => {
      if (item.getAttribute('data-type') === currentIzinType) {
        const name = (item.getAttribute('data-name') || '').toLowerCase();
        const sub = (item.getAttribute('data-sub') || '').toLowerCase();
        if (!q || name.includes(q) || sub.includes(q)) {
          item.style.display = 'block';
          count++;
        } else {
          item.style.display = 'none';
        }
      } else {
        item.style.display = 'none';
      }
    });

    if (emptyMsg) emptyMsg.style.display = count === 0 ? 'block' : 'none';
  }

  function selectIzinItem(type, id, name, sub) {
    const input = document.getElementById('izinSearchInput');
    const clearBtn = document.getElementById('izinClearBtn');
    const dropdown = document.getElementById('izinDropdownMenu');
    const siswaIdInput = document.getElementById('izinSiswaIdInput');
    const guruIdInput = document.getElementById('izinGuruIdInput');

    input.value = name + ' (' + sub + ')';
    input.style.borderColor = '#000000';
    if (clearBtn) clearBtn.style.display = 'block';
    if (dropdown) dropdown.style.display = 'none';

    if (type === 'siswa') {
      siswaIdInput.value = id;
      guruIdInput.value = '';
      siswaIdInput.setAttribute('required', 'required');
      guruIdInput.removeAttribute('required');
    } else {
      guruIdInput.value = id;
      siswaIdInput.value = '';
      guruIdInput.setAttribute('required', 'required');
      siswaIdInput.removeAttribute('required');
    }
  }

  function clearIzinSelection() {
    const input = document.getElementById('izinSearchInput');
    const clearBtn = document.getElementById('izinClearBtn');
    const dropdown = document.getElementById('izinDropdownMenu');
    const siswaIdInput = document.getElementById('izinSiswaIdInput');
    const guruIdInput = document.getElementById('izinGuruIdInput');

    if (input) {
      input.value = '';
      input.style.borderColor = 'var(--border-2)';
    }
    if (clearBtn) clearBtn.style.display = 'none';
    if (dropdown) dropdown.style.display = 'none';
    if (siswaIdInput) siswaIdInput.value = '';
    if (guruIdInput) guruIdInput.value = '';
  }

  // Close search dropdown on click outside
  document.addEventListener('click', function(e) {
    const izinWrap = document.getElementById('izinSearchWrap');
    const izinDropdown = document.getElementById('izinDropdownMenu');
    if (izinWrap && !izinWrap.contains(e.target) && izinDropdown) {
      izinDropdown.style.display = 'none';
    }
  });

  function setPresetKoreksi(preset) {
    const statusSelect = document.getElementById('koreksiPiketStatus');
    const jamMasuk = document.getElementById('koreksiPiketJamMasuk');
    const jamPulang = document.getElementById('koreksiPiketJamPulang');
    const keterangan = document.getElementById('koreksiPiketKeterangan');

    if (preset === 'titip_kartu') {
      statusSelect.value = 'titip_kartu';
      if (jamMasuk) jamMasuk.value = '';
      if (jamPulang) jamPulang.value = '';
      if (keterangan) keterangan.value = 'Dibatalkan oleh Guru Piket — Terindikasi Titip Kartu Presensi';
    } else if (preset === 'alpha') {
      statusSelect.value = 'alpha';
      if (jamMasuk) jamMasuk.value = '';
      if (jamPulang) jamPulang.value = '';
      if (keterangan) keterangan.value = 'Intervensi Piket: Dikembalikan ke Status Alpha / Belum Scan';
    } else if (preset === 'bolos') {
      statusSelect.value = 'bolos';
      if (jamPulang) jamPulang.value = '';
      if (keterangan) keterangan.value = 'Intervensi Piket: Siswa Bolos Kelas / Meninggalkan Sekolah';
    }
  }

  function handleKoreksiStatusChange(val) {
    const jamMasuk = document.getElementById('koreksiPiketJamMasuk');
    const jamPulang = document.getElementById('koreksiPiketJamPulang');
    const keterangan = document.getElementById('koreksiPiketKeterangan');

    if (val === 'titip_kartu' || val === 'alpha') {
      if (jamMasuk) jamMasuk.value = '';
      if (jamPulang) jamPulang.value = '';
      if (val === 'titip_kartu' && keterangan && !keterangan.value) {
        keterangan.value = 'Dibatalkan oleh Guru Piket — Terindikasi Titip Kartu Presensi';
      }
    }
  }

  // ── MODAL REKAP SISWA BELUM SCAN PULANG ──
  function filterRombelModal(rombelSlug, btn) {
    const cards = document.querySelectorAll('.rombel-modal-card');
    const pills = document.querySelectorAll('.chip-filter-modal');
    pills.forEach(p => {
      p.style.background = '#FFFFFF';
      p.style.color = 'var(--text)';
      p.style.borderColor = 'var(--border-2)';
    });
    if (btn) {
      btn.style.background = '#000000';
      btn.style.color = '#FFFFFF';
      btn.style.borderColor = '#000000';
    }
    cards.forEach(card => {
      if (rombelSlug === 'all' || card.getAttribute('data-rombel') === rombelSlug) {
        card.style.display = 'block';
      } else {
        card.style.display = 'none';
      }
    });
  }
</script>

{{-- MODAL: REKAP SISWA BELUM SCAN PULANG --}}
<div class="modal-overlay" id="modalRekapPulangPiket" style="background:rgba(15,23,42,0.65); backdrop-filter:blur(8px); z-index:9999;">
  <div class="modal-card" style="max-width:780px; max-height:88vh; background:#FFFFFF !important; border-radius:16px; border:1px solid var(--border-2); box-shadow:0 25px 50px -12px rgba(0,0,0,0.35); overflow:hidden; display:flex; flex-direction:column; padding:0;">

    {{-- Header Modal --}}
    <div style="padding:18px 24px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; background:#FFFFFF; flex-shrink:0;">
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:40px; height:40px; border-radius:10px; background:#000000; color:#FFFFFF; display:flex; align-items:center; justify-content:center; font-size:19px; flex-shrink:0;">
          <i class="bi bi-door-open-fill"></i>
        </div>
        <div>
          <div style="font-size:16px; font-weight:900; color:var(--text); line-height:1.2;">Rekap Siswa Belum Scan Pulang</div>
          <div style="font-size:12px; color:var(--text-3); margin-top:2px;">
            Tanggal: <span style="font-family:var(--font-mono); font-weight:700; color:var(--text);">{{ now()->translatedFormat('d F Y') }}</span>
          </div>
        </div>
      </div>
      <div style="display:flex; align-items:center; gap:10px;">
        @if($siswaBelumScanPulang > 0)
          <span style="background:#000000; color:#FFFFFF; border-radius:20px; padding:5px 14px; font-size:12px; font-weight:800; font-family:var(--font-mono); display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-exclamation-circle-fill" style="font-size:11px;"></i>
            {{ $siswaBelumScanPulang }} Siswa
          </span>
        @else
          <span style="background:#F1F3F5; color:var(--text-3); border:1px solid var(--border-2); border-radius:20px; padding:5px 14px; font-size:12px; font-weight:700; display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-check-circle-fill" style="color:#16A34A;"></i>
            Semua Sudah Pulang
          </span>
        @endif
        <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('modalRekapPulangPiket')" style="width:34px; height:34px; padding:0; display:flex; align-items:center; justify-content:center; border-radius:8px; border:1px solid var(--border-2); color:var(--text);">
          <i class="bi bi-x-lg" style="font-size:14px;"></i>
        </button>
      </div>
    </div>

    {{-- Filter Chips per Rombel --}}
    @if($siswaBelumScanPulang > 0)
      <div style="padding:10px 24px; background:#F8F9FA; border-bottom:1px solid var(--border); display:flex; gap:8px; flex-wrap:wrap; align-items:center; flex-shrink:0;">
        <span style="font-size:11px; font-weight:800; text-transform:uppercase; color:var(--text-3); letter-spacing:0.5px; margin-right:4px;">Filter Kelas:</span>
        <button type="button" class="chip-filter-modal" onclick="filterRombelModal('all', this)"
          style="background:#000000; color:#FFFFFF; border:1px solid #000000; border-radius:20px; padding:4px 12px; font-size:11.5px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:all .15s;">
          <span>Semua</span>
          <span style="background:rgba(255,255,255,0.25); color:#FFFFFF; border-radius:10px; padding:1px 6px; font-size:10px; font-family:var(--font-mono);">{{ $siswaBelumScanPulang }}</span>
        </button>
        @foreach($siswaBelumScanPulangGrouped as $namaRombel => $items)
          <button type="button" class="chip-filter-modal" onclick="filterRombelModal('{{ Str::slug($namaRombel) }}', this)"
            style="background:#FFFFFF; color:var(--text); border:1px solid var(--border-2); border-radius:20px; padding:4px 12px; font-size:11.5px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:all .15s;">
            <span>{{ $namaRombel }}</span>
            <span style="background:#000000; color:#FFFFFF; border-radius:10px; padding:1px 6px; font-size:10px; font-family:var(--font-mono);">{{ $items->count() }}</span>
          </button>
        @endforeach
      </div>
    @endif

    {{-- Body Modal (Scrollable) --}}
    <div style="overflow-y:auto; flex:1; padding:20px 24px; background:#F8F9FA;">
      @if($siswaBelumScanPulang === 0)
        <div style="padding:48px 20px; text-align:center; color:var(--text-3); background:#FFFFFF; border:1px solid var(--border); border-radius:12px;">
          <i class="bi bi-patch-check-fill" style="font-size:52px; color:#16A34A; opacity:0.85;"></i>
          <div style="font-weight:800; margin-top:14px; font-size:15px; color:var(--text);">Semua Siswa Sudah Scan Pulang</div>
          <div style="font-size:12.5px; color:var(--text-3); margin-top:4px;">Tidak ada siswa yang tertinggal belum absen pulang hari ini.</div>
        </div>
      @else
        <div style="display:flex; flex-direction:column; gap:16px;">
          @foreach($siswaBelumScanPulangGrouped as $namaRombel => $items)
            <div class="rombel-modal-card" data-rombel="{{ Str::slug($namaRombel) }}" style="background:#FFFFFF; border:1px solid var(--border-2); border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
              {{-- Header Rombel --}}
              <div style="background:#F1F3F5; padding:10px 16px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border);">
                <div style="display:flex; align-items:center; gap:8px;">
                  <i class="bi bi-people-fill" style="color:#000000; font-size:14px;"></i>
                  <span style="font-weight:900; font-size:13.5px; color:var(--text);">{{ $namaRombel }}</span>
                </div>
                <span style="font-family:var(--font-mono); font-size:11.5px; font-weight:800; background:#FFFFFF; border:1px solid var(--border-2); border-radius:6px; padding:2px 10px; color:var(--text);">
                  {{ $items->count() }} siswa
                </span>
              </div>
              {{-- Tabel Siswa --}}
              <table style="width:100%; border-collapse:collapse; font-size:12.5px; background:#FFFFFF;">
                <thead>
                  <tr style="background:#FAFAFA;">
                    <th style="padding:8px 16px; text-align:center; font-weight:800; color:var(--text-3); font-size:10.5px; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid var(--border); width:40px;">No</th>
                    <th style="padding:8px 16px; text-align:left; font-weight:800; color:var(--text-3); font-size:10.5px; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid var(--border);">Nama Siswa</th>
                    <th style="padding:8px 16px; text-align:center; font-weight:800; color:var(--text-3); font-size:10.5px; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid var(--border); width:120px;">Jam Masuk</th>
                    <th style="padding:8px 16px; text-align:center; font-weight:800; color:var(--text-3); font-size:10.5px; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid var(--border); width:120px;">Status Masuk</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($items as $idx => $absen)
                    @php
                      $siswa = $absen->siswa ?? $absen->siswaRombel?->siswa ?? null;
                    @endphp
                    <tr style="border-bottom:1px solid var(--border); background:#FFFFFF; transition:background .1s;" onmouseover="this.style.background='#F8F9FA'" onmouseout="this.style.background='#FFFFFF'">
                      <td style="padding:10px 16px; text-align:center; font-weight:700; font-family:var(--font-mono); color:var(--text-3);">{{ $idx + 1 }}</td>
                      <td style="padding:10px 16px;">
                        <div style="display:flex; align-items:center; gap:10px;">
                          <div class="avatar-circle avatar-sm" style="width:32px; height:32px; font-size:12px; font-weight:800; background:#F1F3F5; color:#0F172A; border:1px solid var(--border-2); border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            @if($siswa && $siswa->foto_url)
                              <img src="{{ $siswa->foto_url }}" alt="{{ $siswa->nama ?? '-' }}" class="avatar-img" style="width:100%; height:100%; border-radius:50%; object-fit:cover;" />
                            @else
                              {{ strtoupper(substr($siswa->nama ?? 'S', 0, 2)) }}
                            @endif
                          </div>
                          <div>
                            <div style="font-weight:800; color:var(--text); font-size:13px;">{{ $siswa->nama ?? '-' }}</div>
                            <div style="font-size:11px; color:var(--text-3); font-family:var(--font-mono); margin-top:1px;">
                              {{ $siswa->nisn ? 'NISN: '.$siswa->nisn : 'Non-NISN' }}
                            </div>
                          </div>
                        </div>
                      </td>
                      <td style="padding:10px 16px; text-align:center;">
                        <span style="font-family:var(--font-mono); font-weight:800; font-size:12px; color:#0F172A; background:#F1F3F5; border:1px solid var(--border-2); padding:3px 8px; border-radius:6px;">
                          {{ $absen->jam_masuk ? substr($absen->jam_masuk, 0, 5) . ' WIB' : '-' }}
                        </span>
                      </td>
                      <td style="padding:10px 16px; text-align:center;">
                        @if($absen->status === 'terlambat')
                          <span style="background:rgba(245,158,11,0.12); color:#B45309; border:1px solid rgba(245,158,11,0.3); font-weight:800; font-size:10.5px; padding:3px 10px; border-radius:6px; text-transform:uppercase; letter-spacing:0.3px;">
                            TERLAMBAT
                          </span>
                        @else
                          <span style="background:rgba(22,163,74,0.1); color:#16A34A; border:1px solid rgba(22,163,74,0.25); font-weight:800; font-size:10.5px; padding:3px 10px; border-radius:6px; text-transform:uppercase; letter-spacing:0.3px;">
                            HADIR
                          </span>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endforeach
        </div>
      @endif
    </div>

    {{-- Footer Modal --}}
    <div style="padding:14px 24px; border-top:1px solid var(--border); background:#FFFFFF; display:flex; justify-content:space-between; align-items:center; flex-shrink:0;">
      <div style="font-size:12px; color:var(--text-3); font-weight:600; display:flex; align-items:center; gap:6px;">
        <i class="bi bi-info-circle-fill" style="color:var(--text-3);"></i>
        Data sementara hingga batas jam tutup gerbang sekolah.
      </div>
      <button type="button" onclick="closeModal('modalRekapPulangPiket')" class="btn btn-outline" style="height:38px; padding:0 22px; font-weight:800; font-size:13px; border-radius:8px;">
        Tutup
      </button>
    </div>
  </div>
</div>

</body>
</html>
