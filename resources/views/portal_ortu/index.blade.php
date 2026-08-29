<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>{{ $siswa ? 'Rekap Kehadiran '.$siswa->nama.' — Portal Wali Murid' : 'Portal Kehadiran Siswa & Orang Tua — SMKN 1 Air Naningan' }}</title>
  
  {{-- Google Fonts: Plus Jakarta Sans & JetBrains Mono --}}
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;600;700;800;900&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    :root {
      --bg: #F8FAFC;
      --bg-card: #FFFFFF;
      --bg-subtle: #F1F5F9;
      --border: #E2E8F0;
      --border-2: #CBD5E1;
      --text: #0F172A;
      --text-2: #475569;
      --text-3: #94A3B8;
      
      --gold: #CA8A04;
      --gold-dark: #A16207;
      --gold-light: #FEF08A;
      --gold-subtle: rgba(202, 138, 4, 0.08);
      --gold-border: rgba(202, 138, 4, 0.25);
      
      --green: #16A34A;
      --green-subtle: rgba(22, 163, 74, 0.1);
      
      --amber: #D97706;
      --amber-subtle: rgba(217, 119, 6, 0.1);
      
      --red: #DC2626;
      --red-subtle: rgba(220, 38, 38, 0.1);
      
      --blue: #2563EB;
      --blue-subtle: rgba(37, 99, 235, 0.1);

      --teal: #0D9488;
      --teal-subtle: rgba(13, 148, 136, 0.1);
      
      --r-sm: 8px;
      --r-md: 12px;
      --r-lg: 16px;
      --r-xl: 20px;
      --shadow-sm: 0 1px 3px rgba(0,0,0,0.04);
      --shadow-md: 0 4px 14px rgba(0,0,0,0.05);
      
      --font-main: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
      --font-mono: 'JetBrains Mono', monospace;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: var(--font-main);
      background-color: var(--bg);
      color: var(--text);
      line-height: 1.5;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      -webkit-font-smoothing: antialiased;
    }

    /* ─── Top Navigation ─── */
    .top-nav {
      background: var(--bg-card);
      border-bottom: 1px solid var(--border);
      position: sticky;
      top: 0;
      z-index: 50;
    }
    .top-nav-inner {
      max-width: 1020px;
      margin: 0 auto;
      padding: 12px 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
    }
    .brand-logo {
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
      color: var(--text);
    }
    .brand-icon {
      width: 38px;
      height: 38px;
      background: var(--gold-subtle);
      border: 1.5px solid var(--gold-border);
      border-radius: var(--r-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--gold);
      font-size: 18px;
    }
    .brand-text h1 {
      font-size: 15px;
      font-weight: 800;
      line-height: 1.2;
      color: var(--text);
      letter-spacing: -0.01em;
    }
    .brand-text p {
      font-size: 11.5px;
      color: var(--text-3);
      font-weight: 600;
    }

    /* ─── Container ─── */
    .container {
      max-width: 1020px;
      margin: 0 auto;
      padding: 24px 18px 48px;
      width: 100%;
      flex: 1;
    }

    /* ─── Institutional Search Console ─── */
    .search-console-card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: var(--r-xl);
      padding: 28px 24px;
      box-shadow: var(--shadow-sm);
      margin-bottom: 24px;
    }
    .search-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: var(--gold-subtle);
      border: 1px solid var(--gold-border);
      color: var(--gold);
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 800;
      margin-bottom: 12px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .search-title {
      font-size: 22px;
      font-weight: 900;
      line-height: 1.25;
      margin-bottom: 6px;
      letter-spacing: -0.02em;
      color: var(--text);
    }
    .search-desc {
      font-size: 13.5px;
      color: var(--text-2);
      max-width: 640px;
      line-height: 1.6;
      margin-bottom: 20px;
    }

    .search-form-box {
      max-width: 600px;
    }
    .search-input-wrap {
      display: flex;
      background: var(--bg-subtle);
      border: 1px solid var(--border-2);
      border-radius: var(--r-md);
      padding: 4px;
      transition: all .2s ease;
    }
    .search-input-wrap:focus-within {
      border-color: var(--gold);
      background: var(--bg-card);
      box-shadow: 0 0 0 3px var(--gold-subtle);
    }
    .search-input {
      flex: 1;
      border: none;
      outline: none;
      padding: 10px 14px;
      font-size: 14px;
      font-weight: 600;
      color: var(--text);
      background: transparent;
      font-family: var(--font-main);
    }
    .search-input::placeholder {
      color: var(--text-3);
      font-weight: 500;
    }
    .btn-search {
      background: var(--gold);
      color: #0F172A;
      font-weight: 800;
      font-size: 13px;
      border: none;
      border-radius: var(--r-sm);
      padding: 0 18px;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all .2s;
      font-family: var(--font-main);
    }
    .btn-search:hover {
      background: #EAB308;
      transform: translateY(-1px);
    }

    /* ─── Highlights Features Grid ─── */
    .portal-features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 14px;
      margin-top: 24px;
      padding-top: 20px;
      border-top: 1px solid var(--border);
    }
    .portal-feature-item {
      display: flex;
      align-items: flex-start;
      gap: 10px;
    }
    .portal-feature-icon {
      width: 32px;
      height: 32px;
      border-radius: 8px;
      background: var(--bg-subtle);
      border: 1px solid var(--border);
      color: var(--gold);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 15px;
      flex-shrink: 0;
    }
    .portal-feature-text strong {
      font-size: 12.5px;
      font-weight: 800;
      color: var(--text);
      display: block;
      line-height: 1.3;
    }
    .portal-feature-text span {
      font-size: 11.5px;
      color: var(--text-3);
      line-height: 1.4;
      display: block;
      margin-top: 2px;
    }

    /* ─── Compact Search Bar (When Student Found) ─── */
    .compact-search-card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: var(--r-md);
      padding: 10px 14px;
      margin-bottom: 18px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
      box-shadow: var(--shadow-sm);
    }

    /* ─── Student Profile & Live Gate Widget (Dossier) ─── */
    .dossier-card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: var(--r-xl);
      padding: 22px 24px;
      margin-bottom: 20px;
      box-shadow: var(--shadow-sm);
    }
    .dossier-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 18px;
      border-bottom: 1px solid var(--border);
      padding-bottom: 12px;
      flex-wrap: wrap;
      gap: 10px;
    }
    .dossier-body {
      display: grid;
      grid-template-columns: 1fr;
      gap: 20px;
    }
    @media (min-width: 768px) {
      .dossier-body {
        grid-template-columns: 1.25fr 1fr;
        align-items: center;
      }
    }

    .student-info-left {
      display: flex;
      align-items: center;
      gap: 16px;
    }
    .student-avatar {
      width: 72px;
      height: 72px;
      border-radius: 50%;
      background: var(--gold-subtle);
      border: 2px solid var(--gold-border);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 26px;
      font-weight: 900;
      color: var(--gold);
      flex-shrink: 0;
      overflow: hidden;
    }
    .student-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .student-name {
      font-size: 19px;
      font-weight: 900;
      color: var(--text);
      line-height: 1.25;
      margin-bottom: 4px;
      letter-spacing: -0.01em;
    }
    .student-meta-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      margin-top: 8px;
    }
    .tag-pill {
      font-size: 11.5px;
      font-weight: 700;
      padding: 3px 9px;
      border-radius: 6px;
      background: var(--bg-subtle);
      border: 1px solid var(--border);
      color: var(--text-2);
      display: inline-flex;
      align-items: center;
      gap: 5px;
      text-decoration: none;
    }
    .tag-pill.gold {
      background: var(--gold-subtle);
      border-color: var(--gold-border);
      color: var(--gold);
      font-weight: 800;
    }

    /* ─── Today's Live Attendance Widget ─── */
    .today-widget {
      background: var(--bg-subtle);
      border: 1px solid var(--border);
      border-radius: var(--r-lg);
      padding: 16px 18px;
    }
    .today-widget-title {
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--text-3);
      margin-bottom: 12px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .today-time-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
    }
    .today-time-box {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: var(--r-sm);
      padding: 10px;
      text-align: center;
    }
    .today-time-label {
      font-size: 10.5px;
      font-weight: 800;
      color: var(--text-3);
      text-transform: uppercase;
      letter-spacing: 0.4px;
    }
    .today-time-val {
      font-family: var(--font-mono);
      font-size: 16px;
      font-weight: 900;
      margin-top: 2px;
    }

    /* ─── Status Badges ─── */
    .status-badge {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-size: 10.5px;
      font-weight: 800;
      padding: 3px 9px;
      border-radius: 20px;
      text-transform: uppercase;
      letter-spacing: 0.4px;
      white-space: nowrap;
      flex-shrink: 0;
      line-height: 1.3;
    }
    .status-hadir { background: var(--green-subtle); color: var(--green); border: 1px solid rgba(22,163,74,0.25); }
    .status-terlambat { background: var(--amber-subtle); color: var(--amber); border: 1px solid rgba(217,119,6,0.25); }
    .status-izin, .status-sakit { background: var(--blue-subtle); color: var(--blue); border: 1px solid rgba(37,99,235,0.25); }
    .status-bolos { background: rgba(153,27,27,0.1); color: #991B1B; border: 1px solid rgba(153,27,27,0.25); }
    .status-alpha { background: var(--red-subtle); color: var(--red); border: 1px solid rgba(220,38,38,0.25); }
    .status-pkl { background: var(--teal-subtle); color: var(--teal); border: 1px solid rgba(13,148,136,0.25); }
    .status-none { background: var(--bg-card); color: var(--text-3); border: 1px solid var(--border); }

    /* ─── Discipline Score & 4 KPI Cards ─── */
    .stats-overview-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 12px;
      margin-bottom: 20px;
    }
    @media (min-width: 480px) and (max-width: 767px) {
      .stats-overview-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      .discipline-score-card {
        grid-column: 1 / -1;
      }
    }
    @media (min-width: 768px) {
      .stats-overview-grid {
        grid-template-columns: 1.35fr repeat(4, 1fr);
      }
    }

    .discipline-score-card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: var(--r-lg);
      padding: 16px 18px;
      color: var(--text);
      display: flex;
      flex-direction: column;
      justify-content: center;
      box-shadow: var(--shadow-sm);
    }
    .discipline-val {
      font-size: 30px;
      font-weight: 900;
      line-height: 1;
      font-family: var(--font-mono);
      color: var(--text);
    }
    .discipline-lbl {
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-top: 4px;
      color: var(--text-3);
    }
    .discipline-badge {
      display: inline-block;
      background: var(--gold-subtle);
      border: 1px solid var(--gold-border);
      color: var(--gold);
      padding: 2px 8px;
      border-radius: 12px;
      font-size: 11px;
      font-weight: 800;
      margin-top: 6px;
      width: fit-content;
    }

    .stat-metric-card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: var(--r-lg);
      padding: 14px 16px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      box-shadow: var(--shadow-sm);
      transition: transform .15s ease;
    }
    .stat-metric-card:hover {
      border-color: var(--border-2);
      transform: translateY(-2px);
    }
    .stat-metric-num {
      font-size: 24px;
      font-weight: 900;
      line-height: 1.1;
      font-family: var(--font-mono);
      color: var(--text);
      margin-top: 4px;
    }
    .stat-metric-title {
      font-size: 11px;
      font-weight: 700;
      color: var(--text-2);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    /* ─── Period Control ─── */
    .period-control-card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: var(--r-md);
      padding: 8px 12px;
      margin-bottom: 16px;
      box-shadow: var(--shadow-sm);
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      flex-wrap: wrap;
    }
    .period-tabs {
      display: inline-flex;
      background: var(--bg-subtle);
      padding: 3px;
      border-radius: 10px;
      gap: 3px;
      border: 1px solid var(--border);
      flex-wrap: wrap;
    }
    .period-btn {
      padding: 5px 12px;
      border-radius: 7px;
      font-size: 11.5px;
      font-weight: 700;
      border: none;
      background: transparent;
      color: var(--text-2);
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      transition: all .15s ease;
      white-space: nowrap;
    }
    .period-btn:hover {
      background: var(--gold-subtle);
      color: var(--gold-dark);
    }
    .period-btn.active {
      background: var(--gold);
      color: #0F172A;
      font-weight: 800;
    }

    .period-input-wrap {
      display: flex;
      align-items: center;
      gap: 6px;
      flex-wrap: wrap;
    }
    .form-control-pt {
      padding: 5px 10px;
      border-radius: 6px;
      border: 1px solid var(--border-2);
      background: var(--bg-subtle);
      font-family: var(--font-main);
      font-size: 12px;
      font-weight: 700;
      color: var(--text);
      outline: none;
      cursor: pointer;
    }
    .form-control-pt:focus {
      border-color: var(--gold);
      background: var(--bg-card);
    }

    /* ─── Table Panel ─── */
    .panel {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: var(--r-xl);
      padding: 0;
      margin-bottom: 24px;
      overflow: hidden;
      box-shadow: var(--shadow-sm);
    }
    .panel-title {
      padding: 16px 20px;
      border-bottom: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 14px;
      font-weight: 800;
      background: #FAFBFD;
    }
    .table-wrap {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      width: 100%;
    }
    table {
      width: 100%;
      min-width: 620px;
      border-collapse: collapse;
      font-size: 13px;
    }
    th {
      background: #F1F5F9;
      color: var(--text-2);
      font-weight: 800;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 12px 16px;
      text-align: left;
      border-bottom: 1px solid var(--border);
      white-space: nowrap;
    }
    td {
      padding: 12px 16px;
      border-bottom: 1px solid var(--border);
      color: var(--text);
      vertical-align: middle;
    }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: #FAFBFD; }

    /* ─── Mobile Scroll Hint ─── */
    .mobile-scroll-hint {
      display: none;
      padding: 8px 14px;
      background: var(--bg-subtle);
      border-bottom: 1px solid var(--border);
      font-size: 11px;
      color: var(--text-3);
      text-align: center;
    }
    @media (max-width: 768px) {
      .mobile-scroll-hint { display: block; }
    }

    /* ─── Footer ─── */
    .footer {
      text-align: center;
      padding: 24px 16px;
      font-size: 12px;
      color: var(--text-3);
      border-top: 1px solid var(--border);
      background: var(--bg-card);
      margin-top: auto;
    }
  </style>
</head>
<body>

  {{-- TOP NAVIGATION --}}
  <nav class="top-nav">
    <div class="top-nav-inner">
      <a href="/cek-presensi" class="brand-logo">
        <div class="brand-icon">
          <i class="bi bi-mortarboard-fill"></i>
        </div>
        <div class="brand-text">
          <h1>SIRANI · Portal Wali Murid</h1>
          <p>SMK Negeri 1 Air Naningan</p>
        </div>
      </a>

      <div class="nav-actions">
        <span style="font-size:11.5px; font-weight:700; color:var(--text-2); display:inline-flex; align-items:center; gap:6px; background:var(--bg-subtle); padding:6px 12px; border-radius:var(--r-sm); border:1px solid var(--border);">
          <i class="bi bi-shield-check" style="color:var(--gold);"></i> Akses Resmi Wali Murid
        </span>
      </div>
    </div>
  </nav>

  {{-- MAIN CONTAINER --}}
  <main class="container">

    @if(!$siswa)
      {{-- SEARCH CONSOLE (HERO SELAMAT DATANG) --}}
      <section class="search-console-card">
        <span class="search-badge">
          <i class="bi bi-shield-check"></i> Layanan Informasi Presensi Siswa
        </span>
        <h2 class="search-title">Pantau Kehadiran &amp; Ketertiban Putra/Putri Anda</h2>
        <p class="search-desc">
          Selamat datang di portal presensi mandiri SMKN 1 Air Naningan. Masukkan <strong>Nomor Induk Siswa (NIS)</strong> atau <strong>NISN</strong> ananda untuk memantau catatan absensi harian Smart Gate Face ID, riwayat izin, serta pembinaan kedisiplinan secara real-time.
        </p>

        <div class="search-form-box">
          <form method="GET" action="{{ route('portal.ortu.index') }}">
            <div class="search-input-wrap">
              <i class="bi bi-search" style="align-self:center; margin-left:12px; color:var(--text-3); font-size:15px;"></i>
              <input
                type="text"
                name="keyword"
                class="search-input"
                value="{{ $keyword }}"
                placeholder="Ketik NIS (contoh: 10245) atau NISN siswa..."
                autocomplete="off"
                required
                autofocus
              />
              <button type="submit" class="btn-search">
                <i class="bi bi-arrow-right-circle-fill"></i> Cek Presensi
              </button>
            </div>
          </form>
        </div>

        {{-- Highlight Layanan --}}
        <div class="portal-features-grid">
          <div class="portal-feature-item">
            <div class="portal-feature-icon">
              <i class="bi bi-camera-video-fill"></i>
            </div>
            <div class="portal-feature-text">
              <strong>Smart Gate Face ID</strong>
              <span>Pindaian otomatis di gerbang sekolah secara akurat &amp; real-time.</span>
            </div>
          </div>
          <div class="portal-feature-item">
            <div class="portal-feature-icon">
              <i class="bi bi-bar-chart-line-fill"></i>
            </div>
            <div class="portal-feature-text">
              <strong>Rekapitulasi Lengkap</strong>
              <span>Pantauan riwayat harian, mingguan, bulanan, dan tahunan.</span>
            </div>
          </div>
          <div class="portal-feature-item">
            <div class="portal-feature-icon">
              <i class="bi bi-journal-bookmark-fill"></i>
            </div>
            <div class="portal-feature-text">
              <strong>Portofolio Karakter</strong>
              <span>Transparansi poin apresiasi, prestasi, serta catatan pembinaan.</span>
            </div>
          </div>
        </div>
      </section>
    @else
      {{-- SEARCH BAR RAMPING SAAT SISWA SUDAH TERPILIH --}}
      <div class="compact-search-card">
        <div style="display:flex; align-items:center; gap:8px;">
          <span style="font-size:12px; font-weight:800; color:var(--text-3); text-transform:uppercase; letter-spacing:0.5px;">
            <i class="bi bi-person-check-fill" style="color:var(--gold); margin-right:4px;"></i> Siswa Terpilih:
          </span>
          <strong style="color:var(--text); font-size:13px;">{{ $siswa->nama }}</strong>
          <span style="font-size:11.5px; color:var(--text-3); font-family:var(--font-mono);">(NIS: {{ $siswa->nis }})</span>
        </div>

        <form method="GET" action="{{ route('portal.ortu.index') }}" style="display:flex; align-items:center; gap:6px; margin:0;">
          <input
            type="text"
            name="keyword"
            placeholder="Cari NIS lain..."
            class="form-control-pt"
            style="height:32px; padding:0 10px; width:140px; font-size:12px;"
          />
          <button type="submit" class="btn-search" style="padding:0 12px; height:32px; font-size:11.5px;">
            <i class="bi bi-search"></i> Ganti
          </button>
        </form>
      </div>
    @endif

    {{-- PENGUMUMAN RESMI SEKOLAH (JIKA ADA) --}}
    @if(isset($pengumumans) && $pengumumans->count() > 0)
      <div style="margin-bottom: 20px;">
        @foreach($pengumumans as $p)
          @php $badge = $p->kategori_badge; @endphp
          <div style="background: var(--bg-card); border: 1px solid var(--border); border-left: 4px solid var(--gold); border-radius: var(--r-md); padding: 14px 18px; margin-bottom: 10px; box-shadow: var(--shadow-sm); display: flex; gap: 12px; align-items: flex-start;">
            <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--gold-subtle); color: var(--gold); display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; margin-top: 2px;">
              <i class="bi {{ $badge['icon'] }}"></i>
            </div>
            <div style="flex: 1;">
              <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px; margin-bottom: 3px;">
                <span class="badge" style="background: var(--gold-subtle); color: var(--gold); font-weight: 800; font-size: 10px; text-transform: uppercase;">
                  {{ $badge['label'] }}
                </span>
                <span style="font-size: 11px; color: var(--text-3); font-weight: 600;">
                  <i class="bi bi-clock"></i> {{ $p->created_at->translatedFormat('d M Y') }}
                </span>
              </div>
              <h3 style="font-size: 14.5px; font-weight: 800; color: var(--text); margin: 0 0 4px;">{{ $p->judul }}</h3>
              <p style="font-size: 12.5px; color: var(--text-2); line-height: 1.5; margin: 0; white-space: pre-line;">{{ $p->isi_pesan }}</p>
              
              @if($p->banner_url)
                <div style="margin-top: 10px; border-radius: 8px; overflow: hidden; border: 1px solid var(--border); max-width: 100%;">
                  <a href="{{ $p->banner_url }}" target="_blank" title="Klik untuk memperbesar gambar">
                    <img src="{{ $p->banner_url }}" alt="{{ $p->judul }}" style="width: 100%; max-height: 380px; object-fit: contain; background: rgba(0,0,0,0.02); display: block;" />
                  </a>
                </div>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    @endif

    @if($keyword && !$siswa)
      {{-- SISWA TIDAK DITEMUKAN --}}
      <div style="background:var(--bg-card); border:1px solid var(--border); border-radius:var(--r-xl); padding:40px 20px; text-align:center; box-shadow:var(--shadow-sm);">
        <i class="bi bi-person-x-fill" style="font-size:44px; color:var(--text-3); margin-bottom:12px; display:inline-block;"></i>
        <h3 style="font-size:17px; font-weight:800; margin-bottom:6px; color:var(--text);">Data Siswa Tidak Ditemukan</h3>
        <p style="font-size:13px; color:var(--text-2); max-width:480px; margin:0 auto 20px;">
          Nomor Induk Siswa (NIS/NISN) <strong>"{{ $keyword }}"</strong> tidak terdaftar pada pangkalan data aktif sekolah. Pastikan nomor yang Anda masukkan sudah sesuai.
        </p>
        <a href="/cek-presensi" class="btn-search" style="text-decoration:none; display:inline-flex; padding:8px 18px;">
          <i class="bi bi-arrow-repeat"></i> Coba Nomor Lain
        </a>
      </div>

    @elseif($siswa)
      {{-- HASIL REKAP DATA SISWA --}}

      {{-- 1. IDENTITAS SISWA & STATUS HARI INI (DOSSIER DIGITAL) --}}
      <div class="dossier-card">
        <div class="dossier-header">
          <span style="font-size:11.5px; font-weight:800; color:var(--text-3); text-transform:uppercase; letter-spacing:0.5px;">
            <i class="bi bi-person-badge-fill" style="color:var(--gold);"></i> Profil Lengkap Siswa
          </span>
          
          <div style="display:flex; gap:6px;">
            <button type="button" onclick="copyLinkPresensi('{{ url('/presensi-siswa/'.$siswa->nis) }}')" id="btnCopyLink" style="background:var(--bg-subtle); border:1px solid var(--border); padding:4px 10px; border-radius:var(--r-sm); font-size:11.5px; font-weight:700; color:var(--text-2); cursor:pointer; display:inline-flex; align-items:center; gap:5px;">
              <i class="bi bi-link-45deg"></i> <span id="copyBtnText">Salin Tautan</span>
            </button>
            <button type="button" onclick="resetSiswaTersimpan()" style="background:var(--bg-subtle); border:1px solid var(--border); padding:4px 10px; border-radius:var(--r-sm); font-size:11.5px; font-weight:700; color:var(--text-2); cursor:pointer; display:inline-flex; align-items:center; gap:5px;">
              <i class="bi bi-arrow-repeat"></i> Ganti Siswa
            </button>
          </div>
        </div>

        <div class="dossier-body">
          {{-- Kiri: Biodata Siswa --}}
          <div class="student-info-left">
            <div class="student-avatar">
              @if($siswa->foto)
                <img src="{{ asset('storage/'.$siswa->foto) }}" alt="{{ $siswa->nama }}" />
              @else
                {{ strtoupper(substr($siswa->nama, 0, 1)) }}
              @endif
            </div>
            <div>
              <h2 class="student-name">{{ $siswa->nama }}</h2>
              <div style="font-size:12px; color:var(--text-2); display:flex; flex-wrap:wrap; gap:6px;">
                <span>NIS: <strong style="font-family:var(--font-mono); color:var(--text);">{{ $siswa->nis }}</strong></span>
                <span>•</span>
                <span>NISN: <strong style="font-family:var(--font-mono); color:var(--text);">{{ $siswa->nisn ?: '-' }}</strong></span>
              </div>
              <div class="student-meta-tags">
                <span class="tag-pill gold">
                  <i class="bi bi-building"></i> Kelas {{ $rombel->nama_rombel ?? 'Belum Ada Rombel' }}
                </span>
                <span class="tag-pill">
                  <i class="bi bi-book-half"></i> {{ $rombel->jurusan->nama_jurusan ?? '-' }}
                </span>
                @if($siswa->status === 'pkl')
                  <span class="tag-pill" style="background:var(--teal-subtle); color:var(--teal); border-color:rgba(13,148,136,0.3); font-weight:800;">
                    <i class="bi bi-briefcase-fill"></i> Sedang Praktik Kerja (PKL)
                  </span>
                @endif
                @if($waliKelas)
                  <span class="tag-pill">
                    <i class="bi bi-person-workspace"></i> Wali Kelas: {{ $waliKelas->nama }}
                  </span>
                  @if($waliKelas->no_hp)
                    @php
                      $hpWaliClean = preg_replace('/[^0-9]/', '', $waliKelas->no_hp);
                      if (str_starts_with($hpWaliClean, '0')) $hpWaliClean = '62' . substr($hpWaliClean, 1);
                      $pesanWaWali = rawurlencode("Halo Bapak/Ibu Wali Kelas {$waliKelas->nama}, saya orang tua dari {$siswa->nama} (Kelas " . ($rombel->nama_rombel ?? '-') . "). Ingin berkonsultasi mengenai kehadiran/perkembangan belajar ananda.");
                    @endphp
                    <a href="https://wa.me/{{ $hpWaliClean }}?text={{ $pesanWaWali }}" target="_blank" class="tag-pill" style="background:rgba(34,197,94,0.1); color:#16A34A; border-color:rgba(34,197,94,0.25); font-weight:700;" title="Konsultasi WhatsApp dengan Wali Kelas">
                      <i class="bi bi-whatsapp"></i> Hubungi Wali Kelas
                    </a>
                  @endif
                @endif
                @if($siswa->nama_ortu)
                  <span class="tag-pill">
                    <i class="bi bi-person-heart" style="color:var(--gold);"></i> Wali Murid: {{ $siswa->nama_ortu }}
                  </span>
                @endif
              </div>
            </div>
          </div>

          {{-- Kanan: Status Kehadiran Hari Ini --}}
          <div class="today-widget">
            <div class="today-widget-title">
              <span>Kehadiran Hari Ini ({{ \Carbon\Carbon::today()->translatedFormat('d M Y') }})</span>
              <div>
                @if($todayAbsensi)
                  @if($todayAbsensi->status === 'hadir')
                    <span class="status-badge status-hadir"><i class="bi bi-check-circle-fill"></i> Hadir Tepat Waktu</span>
                  @elseif($todayAbsensi->status === 'terlambat')
                    <span class="status-badge status-terlambat"><i class="bi bi-clock-fill"></i> Terlambat</span>
                  @elseif($todayAbsensi->status === 'izin')
                    <span class="status-badge status-izin"><i class="bi bi-file-earmark-medical-fill"></i> Izin</span>
                  @elseif($todayAbsensi->status === 'sakit')
                    <span class="status-badge status-sakit"><i class="bi bi-heart-pulse-fill"></i> Sakit</span>
                  @elseif($todayAbsensi->status === 'bolos')
                    <span class="status-badge status-bolos"><i class="bi bi-exclamation-triangle-fill"></i> Bolos</span>
                  @elseif($todayAbsensi->status === 'alpha')
                    <span class="status-badge status-alpha"><i class="bi bi-x-circle-fill"></i> Alpha</span>
                  @endif
                @elseif($siswa->status === 'pkl')
                  <span class="status-badge status-pkl"><i class="bi bi-briefcase-fill"></i> PKL</span>
                @else
                  <span class="status-badge status-none"><i class="bi bi-hourglass-split"></i> Belum Presensi</span>
                @endif
              </div>
            </div>

            <div class="today-time-grid">
              <div class="today-time-box">
                <div class="today-time-label"><i class="bi bi-box-arrow-in-right" style="color:var(--green);"></i> Masuk Gerbang</div>
                <div class="today-time-val" style="color:{{ $todayAbsensi && $todayAbsensi->jam_masuk ? 'var(--text)' : 'var(--text-3)' }};">
                  {{ $todayAbsensi && $todayAbsensi->jam_masuk ? substr($todayAbsensi->jam_masuk, 0, 5).' WIB' : '—' }}
                </div>
              </div>
              <div class="today-time-box">
                <div class="today-time-label"><i class="bi bi-box-arrow-right" style="color:var(--gold);"></i> Pulang Gerbang</div>
                <div class="today-time-val" style="color:{{ $todayAbsensi && $todayAbsensi->jam_pulang ? 'var(--text)' : 'var(--text-3)' }};">
                  {{ $todayAbsensi && $todayAbsensi->jam_pulang ? substr($todayAbsensi->jam_pulang, 0, 5).' WIB' : '—' }}
                </div>
              </div>
            </div>
            
            <div style="font-size:11px; color:var(--text-3); text-align:center; margin-top:8px;">
              {{ $todayAbsensi ? ($todayAbsensi->keterangan ?: 'Tervalidasi via Smart Gate Face ID SMKN 1 AN') : ($siswa->status === 'pkl' ? 'Siswa sedang melaksanakan PKL di Industri' : 'Belum ada rekaman Face ID pada gerbang hari ini') }}
            </div>
          </div>
        </div>
      </div>

      {{-- 2. REKAPITULASI & RIWAYAT KEHADIRAN TERPADU --}}
      <div id="riwayat-kehadiran" style="scroll-margin-top: 70px;">
        
        {{-- KARTU SKOR DISIPLIN & 4 METRIK KEHADIRAN (KPI) --}}
        <div class="stats-overview-grid">
          {{-- Skor Disiplin Utama --}}
          <div class="discipline-score-card">
            <div class="discipline-val">{{ $stats['persen'] }}%</div>
            <div class="discipline-lbl">Tingkat Kedisiplinan</div>
            <div class="discipline-badge">{{ $stats['predikat'] }} ({{ $stats['total'] }} Hari Aktif)</div>
          </div>

          {{-- 1. Hadir Tepat --}}
          <div class="stat-metric-card">
            <div class="stat-metric-title">
              <span>Hadir Tepat</span>
              <span class="badge" style="background:rgba(34,197,94,0.12); color:#16A34A; border:1px solid rgba(34,197,94,0.25); font-size:9.5px; font-weight:800;">Tepat</span>
            </div>
            <div class="stat-metric-num">{{ $stats['hadir'] }}</div>
          </div>

          {{-- 2. Terlambat --}}
          <div class="stat-metric-card">
            <div class="stat-metric-title">
              <span>Terlambat</span>
              <span class="badge" style="background:rgba(245,158,11,0.12); color:#D97706; border:1px solid rgba(245,158,11,0.25); font-size:9.5px; font-weight:800;">Telat</span>
            </div>
            <div class="stat-metric-num">{{ $stats['terlambat'] }}</div>
          </div>

          {{-- 3. Izin / Sakit --}}
          <div class="stat-metric-card">
            <div class="stat-metric-title">
              <span>Izin / Sakit</span>
              <span class="badge" style="background:rgba(59,130,246,0.12); color:#2563EB; border:1px solid rgba(59,130,246,0.25); font-size:9.5px; font-weight:800;">Izin</span>
            </div>
            <div class="stat-metric-num">{{ $stats['izin'] + $stats['sakit'] }}</div>
          </div>

          {{-- 4. Alpha / Bolos --}}
          <div class="stat-metric-card">
            <div class="stat-metric-title">
              <span>Alpha / Bolos</span>
              <span class="badge" style="background:rgba(239,68,68,0.12); color:#DC2626; border:1px solid rgba(239,68,68,0.25); font-size:9.5px; font-weight:800;">Alpha</span>
            </div>
            <div class="stat-metric-num">{{ $stats['alpha'] + $stats['bolos'] }}</div>
          </div>
        </div>

        {{-- KONTROL PILIHAN PERIODE (RINGKAS & RAMPING) --}}
        <div class="period-control-card">
          <div class="period-tabs">
            <a href="/cek-presensi?keyword={{ $siswa->nis }}&periode=harian#riwayat-kehadiran" class="period-btn {{ $periode === 'harian' ? 'active' : '' }}">
              <i class="bi bi-calendar-day"></i> Harian
            </a>
            <a href="/cek-presensi?keyword={{ $siswa->nis }}&periode=mingguan#riwayat-kehadiran" class="period-btn {{ $periode === 'mingguan' ? 'active' : '' }}">
              <i class="bi bi-calendar-week"></i> Mingguan
            </a>
            <a href="/cek-presensi?keyword={{ $siswa->nis }}&periode=bulanan#riwayat-kehadiran" class="period-btn {{ $periode === 'bulanan' ? 'active' : '' }}">
              <i class="bi bi-calendar-month"></i> Bulanan
            </a>
            <a href="/cek-presensi?keyword={{ $siswa->nis }}&periode=tahunan#riwayat-kehadiran" class="period-btn {{ $periode === 'tahunan' ? 'active' : '' }}">
              <i class="bi bi-calendar3"></i> Tahunan
            </a>
          </div>

          <form method="GET" action="{{ route('portal.ortu.index') }}#riwayat-kehadiran" class="period-input-wrap">
            <input type="hidden" name="keyword" value="{{ $siswa->nis }}" />
            <input type="hidden" name="periode" value="{{ $periode }}" />

            @if($periode === 'harian')
              <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control-pt" onchange="preserveScrollAndSubmit(this.form)" />
            @elseif($periode === 'mingguan')
              <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" class="form-control-pt" />
              <span style="font-size:11.5px; color:var(--text-3); font-weight:700;">s/d</span>
              <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}" class="form-control-pt" onchange="preserveScrollAndSubmit(this.form)" />
              <button type="submit" class="btn-search" style="padding:4px 10px; font-size:11.5px; border-radius:6px;">Pilih</button>
            @elseif($periode === 'tahunan')
              <select name="tahun" class="form-control-pt" onchange="preserveScrollAndSubmit(this.form)">
                @for($y = date('Y') + 1; $y >= 2024; $y--)
                  <option value="{{ $y }}" {{ $tahunSelected == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                @endfor
              </select>
            @else
              {{-- Bulanan --}}
              <select name="bulan" class="form-control-pt" onchange="preserveScrollAndSubmit(this.form)">
                @for($i = 0; $i < 12; $i++)
                  @php
                    $m = \Carbon\Carbon::today()->subMonths($i);
                    $val = $m->format('Y-m');
                  @endphp
                  <option value="{{ $val }}" {{ $bulanSelected === $val ? 'selected' : '' }}>
                    {{ $m->translatedFormat('F Y') }}
                  </option>
                @endfor
              </select>
            @endif
          </form>
        </div>

        {{-- TABEL RINCIAN LOG / REKAPITULASI JUMLAH KEHADIRAN --}}
        <div class="panel">
          <div class="panel-title">
            @if($periode === 'tahunan')
              <span><i class="bi bi-calendar3" style="color:var(--gold); margin-right:6px;"></i>Rekapitulasi Kehadiran per Bulan (Tahun {{ $tahunSelected }})</span>
              <span style="font-size:11.5px; font-weight:700; color:var(--text-3); font-family:var(--font-mono);">12 Bulan Terdata</span>
            @elseif($periode === 'bulanan')
              <span><i class="bi bi-pie-chart-fill" style="color:var(--gold); margin-right:6px;"></i>Ringkasan Jumlah Kehadiran ({{ $periodeText }})</span>
              <span style="font-size:11.5px; font-weight:700; color:var(--text-3); font-family:var(--font-mono);">Total {{ $stats['total'] }} Hari Efektif</span>
            @else
              <span><i class="bi bi-journal-check" style="color:var(--gold); margin-right:6px;"></i>Rincian Riwayat Kehadiran ({{ $periodeText }})</span>
              <span style="font-size:11.5px; font-weight:700; color:var(--text-3); font-family:var(--font-mono);">{{ $absensis->count() }} Hari Tercatat</span>
            @endif
          </div>

          <div class="mobile-scroll-hint">
            <i class="bi bi-arrows-expand"></i> Geser tabel ke samping untuk melihat data lengkap
          </div>

          <div class="table-wrap">
            @if($periode === 'tahunan')
              {{-- 1. REKAP TAHUNAN (JUMLAH PER BULAN) --}}
              <table>
                <thead>
                  <tr>
                    <th style="width:160px; white-space:nowrap;">Bulan</th>
                    <th style="width:90px; text-align:center; white-space:nowrap;">Hadir</th>
                    <th style="width:90px; text-align:center; white-space:nowrap;">Terlambat</th>
                    <th style="width:110px; text-align:center; white-space:nowrap;">Izin / Sakit</th>
                    <th style="width:110px; text-align:center; white-space:nowrap;">Alpha / Bolos</th>
                    <th style="width:100px; text-align:center; white-space:nowrap;">Total Hari</th>
                    <th style="width:130px; text-align:center; white-space:nowrap;">Kedisiplinan</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                    $totHadir = 0; $totTelat = 0; $totIzinSakit = 0; $totAlphaBolos = 0; $totSemua = 0;
                  @endphp
                  @foreach($rekapBulananTahunan as $rb)
                    @php
                      $totHadir += $rb['hadir'];
                      $totTelat += $rb['terlambat'];
                      $totIzinSakit += ($rb['izin'] + $rb['sakit']);
                      $totAlphaBolos += ($rb['alpha'] + $rb['bolos']);
                      $totSemua += $rb['total'];
                    @endphp
                    <tr>
                      <td style="white-space:nowrap;">
                        <strong style="color:var(--text); font-size:13px;">{{ $rb['bulan_nama'] }}</strong>
                      </td>
                      <td style="text-align:center; font-family:var(--font-mono); font-weight:800; font-size:13px; color:var(--text);">
                        {{ $rb['hadir'] }}
                      </td>
                      <td style="text-align:center; font-family:var(--font-mono); font-weight:800; font-size:13px; color:var(--text);">
                        {{ $rb['terlambat'] }}
                      </td>
                      <td style="text-align:center; font-family:var(--font-mono); font-weight:800; font-size:13px; color:var(--text);">
                        {{ $rb['izin'] + $rb['sakit'] }}
                      </td>
                      <td style="text-align:center; font-family:var(--font-mono); font-weight:800; font-size:13px; color:var(--text);">
                        {{ $rb['alpha'] + $rb['bolos'] }}
                      </td>
                      <td style="text-align:center; font-family:var(--font-mono); font-weight:800; font-size:13px; color:var(--text);">
                        {{ $rb['total'] }}
                      </td>
                      <td style="text-align:center; white-space:nowrap;">
                        @if($rb['total'] > 0)
                          <span class="badge" style="background:var(--bg-subtle); border:1px solid var(--border); color:var(--text); font-weight:800; font-size:11px; font-family:var(--font-mono);">
                            {{ $rb['persen'] }}%
                          </span>
                        @else
                          <span style="color:var(--text-3); font-size:12px;">—</span>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                  <tr style="background:var(--bg-subtle); font-weight:800; border-top:2px solid var(--border);">
                    <td style="white-space:nowrap; font-weight:900; color:var(--text);">
                      <i class="bi bi-calculator" style="color:var(--gold);"></i> TOTAL 1 TAHUN
                    </td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:900; color:var(--text);">{{ $totHadir }}</td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:900; color:var(--text);">{{ $totTelat }}</td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:900; color:var(--text);">{{ $totIzinSakit }}</td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:900; color:var(--text);">{{ $totAlphaBolos }}</td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:900; color:var(--text);">{{ $totSemua }}</td>
                    <td style="text-align:center;">
                      <span class="badge" style="background:var(--gold); color:#0F172A; font-weight:900; font-size:11px; font-family:var(--font-mono);">
                        {{ $stats['persen'] }}%
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>

            @elseif($periode === 'bulanan')
              {{-- 2. REKAP BULANAN (RINGKASAN JUMLAH KATEGORI) --}}
              <table>
                <thead>
                  <tr>
                    <th style="width:220px; white-space:nowrap;">Kategori Kehadiran</th>
                    <th style="width:130px; text-align:center; white-space:nowrap;">Jumlah (Hari/Kali)</th>
                    <th style="width:120px; text-align:center; white-space:nowrap;">Proporsi (%)</th>
                    <th style="min-width:180px;">Keterangan &amp; Penjelasan</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td style="white-space:nowrap;">
                      <span class="status-badge status-hadir" style="font-size:11.5px;">
                        <i class="bi bi-check-circle-fill"></i> Hadir Tepat Waktu
                      </span>
                    </td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:900; font-size:14px; color:var(--text);">
                      {{ $stats['hadir'] }} Hari
                    </td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:800; font-size:13px; color:var(--text-2);">
                      {{ $stats['total'] > 0 ? round(($stats['hadir'] / $stats['total']) * 100, 1) : 0 }}%
                    </td>
                    <td style="font-size:12px; color:var(--text-2);">
                      Tercatat hadir tepat waktu mengikuti pembelajaran di sekolah
                    </td>
                  </tr>
                  <tr>
                    <td style="white-space:nowrap;">
                      <span class="status-badge status-terlambat" style="font-size:11.5px;">
                        <i class="bi bi-clock-fill"></i> Terlambat Hadir
                      </span>
                    </td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:900; font-size:14px; color:var(--text);">
                      {{ $stats['terlambat'] }} Kali
                    </td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:800; font-size:13px; color:var(--text-2);">
                      {{ $stats['total'] > 0 ? round(($stats['terlambat'] / $stats['total']) * 100, 1) : 0 }}%
                    </td>
                    <td style="font-size:12px; color:var(--text-2);">
                      Tap masuk melewati batas jam toleransi gerbang
                    </td>
                  </tr>
                  <tr>
                    <td style="white-space:nowrap;">
                      <span class="status-badge status-izin" style="font-size:11.5px;">
                        <i class="bi bi-file-earmark-medical-fill"></i> Izin (Disetujui)
                      </span>
                    </td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:900; font-size:14px; color:var(--text);">
                      {{ $stats['izin'] }} Hari
                    </td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:800; font-size:13px; color:var(--text-2);">
                      {{ $stats['total'] > 0 ? round(($stats['izin'] / $stats['total']) * 100, 1) : 0 }}%
                    </td>
                    <td style="font-size:12px; color:var(--text-2);">
                      Disertai surat permohonan izin resmi dari orang tua/wali
                    </td>
                  </tr>
                  <tr>
                    <td style="white-space:nowrap;">
                      <span class="status-badge status-sakit" style="font-size:11.5px;">
                        <i class="bi bi-heart-pulse-fill"></i> Sakit
                      </span>
                    </td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:900; font-size:14px; color:var(--text);">
                      {{ $stats['sakit'] }} Hari
                    </td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:800; font-size:13px; color:var(--text-2);">
                      {{ $stats['total'] > 0 ? round(($stats['sakit'] / $stats['total']) * 100, 1) : 0 }}%
                    </td>
                    <td style="font-size:12px; color:var(--text-2);">
                      Disertai surat keterangan dokter atau konfirmasi wali murid
                    </td>
                  </tr>
                  <tr>
                    <td style="white-space:nowrap;">
                      <span class="status-badge status-alpha" style="font-size:11.5px;">
                        <i class="bi bi-x-circle-fill"></i> Alpha (Tanpa Keterangan)
                      </span>
                    </td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:900; font-size:14px; color:var(--text);">
                      {{ $stats['alpha'] }} Hari
                    </td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:800; font-size:13px; color:var(--text-2);">
                      {{ $stats['total'] > 0 ? round(($stats['alpha'] / $stats['total']) * 100, 1) : 0 }}%
                    </td>
                    <td style="font-size:12px; color:{{ $stats['alpha'] > 0 ? 'var(--red)' : 'var(--text-2)' }};">
                      {{ $stats['alpha'] > 0 ? 'Perlu perhatian wali murid & konfirmasi ke wali kelas' : 'Tidak ada catatan alpha (Tertib)' }}
                    </td>
                  </tr>
                  <tr>
                    <td style="white-space:nowrap;">
                      <span class="status-badge status-bolos" style="font-size:11.5px;">
                        <i class="bi bi-exclamation-triangle-fill"></i> Bolos
                      </span>
                    </td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:900; font-size:14px; color:var(--text);">
                      {{ $stats['bolos'] }} Kali
                    </td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:800; font-size:13px; color:var(--text-2);">
                      {{ $stats['total'] > 0 ? round(($stats['bolos'] / $stats['total']) * 100, 1) : 0 }}%
                    </td>
                    <td style="font-size:12px; color:{{ $stats['bolos'] > 0 ? '#991B1B' : 'var(--text-2)' }};">
                      {{ $stats['bolos'] > 0 ? 'Meninggalkan KBM tanpa izin piket' : 'Tidak ada catatan bolos' }}
                    </td>
                  </tr>
                  <tr style="background:var(--bg-subtle); font-weight:800; border-top:2px solid var(--border);">
                    <td style="white-space:nowrap; font-weight:900; color:var(--text);">
                      <i class="bi bi-calculator" style="color:var(--gold);"></i> TOTAL HARI EFEKTIF
                    </td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:900; font-size:14.5px; color:var(--text);">
                      {{ $stats['total'] }} Hari
                    </td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:900; color:var(--text-2);">
                      100%
                    </td>
                    <td style="font-size:12px; color:var(--text);">
                      Skor Kedisiplinan: <strong style="color:var(--text); font-family:var(--font-mono);">{{ $stats['persen'] }}%</strong> ({{ $stats['predikat'] }})
                    </td>
                  </tr>
                </tbody>
              </table>

            @else
              {{-- 3. REKAP HARIAN & MINGGUAN (TABEL RINCIAN LOG TANGGAL) --}}
              <table>
                <thead>
                  <tr>
                    <th style="width:170px; white-space:nowrap;">Hari &amp; Tanggal</th>
                    <th style="width:110px; text-align:center; white-space:nowrap;">Jam Masuk</th>
                    <th style="width:110px; text-align:center; white-space:nowrap;">Jam Pulang</th>
                    <th style="width:170px; text-align:center; white-space:nowrap;">Status Kehadiran</th>
                    <th style="min-width:180px;">Keterangan / Alasan</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($absensis as $abs)
                    <tr>
                      <td style="white-space:nowrap;">
                        <strong style="color:var(--text); font-size:13px;">{{ \Carbon\Carbon::parse($abs->tanggal)->translatedFormat('l') }}</strong>
                        <div style="font-size:11.5px; color:var(--text-3); font-family:var(--font-mono); margin-top:2px;">{{ \Carbon\Carbon::parse($abs->tanggal)->translatedFormat('d M Y') }}</div>
                      </td>
                      <td style="text-align:center; white-space:nowrap;">
                        <span style="font-family:var(--font-mono); font-weight:800; font-size:13px; color:var(--text); white-space:nowrap;">
                          {{ $abs->jam_masuk ? substr($abs->jam_masuk, 0, 5).' WIB' : '—' }}
                        </span>
                      </td>
                      <td style="text-align:center; white-space:nowrap;">
                        <span style="font-family:var(--font-mono); font-weight:800; font-size:13px; color:var(--text); white-space:nowrap;">
                          {{ $abs->jam_pulang ? substr($abs->jam_pulang, 0, 5).' WIB' : '—' }}
                        </span>
                      </td>
                      <td style="text-align:center; white-space:nowrap;">
                        @if($abs->status === 'hadir')
                          <span class="status-badge status-hadir"><i class="bi bi-check-circle-fill"></i> Hadir Tepat Waktu</span>
                        @elseif($abs->status === 'terlambat')
                          <span class="status-badge status-terlambat"><i class="bi bi-clock-fill"></i> Terlambat</span>
                        @elseif($abs->status === 'izin')
                          <span class="status-badge status-izin"><i class="bi bi-file-earmark-medical-fill"></i> Izin</span>
                        @elseif($abs->status === 'sakit')
                          <span class="status-badge status-sakit"><i class="bi bi-heart-pulse-fill"></i> Sakit</span>
                        @elseif($abs->status === 'bolos')
                          <span class="status-badge status-bolos"><i class="bi bi-exclamation-triangle-fill"></i> Bolos</span>
                        @elseif($abs->status === 'alpha')
                          <span class="status-badge status-alpha"><i class="bi bi-x-circle-fill"></i> Alpha</span>
                        @endif
                      </td>
                      <td style="font-size:12.5px; color:var(--text-2); min-width:180px;">
                        @if($abs->keterangan)
                          <span style="font-weight:600; color:var(--text);"><i class="bi bi-chat-left-text-fill" style="color:var(--gold); font-size:11px; margin-right:4px;"></i>{{ $abs->keterangan }}</span>
                        @elseif($abs->status === 'bolos')
                          <span style="color:#991B1B; font-weight:600;"><i class="bi bi-exclamation-triangle-fill"></i> Tidak tap pulang (tanpa izin piket)</span>
                        @elseif($abs->status === 'alpha')
                          <span style="color:#DC2626; font-weight:600;"><i class="bi bi-x-circle-fill"></i> Tidak hadir tanpa keterangan</span>
                        @elseif($abs->status === 'terlambat')
                          <span style="color:var(--amber); font-weight:600;"><i class="bi bi-clock-history"></i> Terlambat masuk gerbang</span>
                        @elseif($abs->status === 'hadir')
                          <span style="color:var(--text-3);"><i class="bi bi-check2"></i> Hadir pembelajaran reguler</span>
                        @else
                          -
                        @endif
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" style="text-align:center; padding:32px 16px; color:var(--text-3);">
                        <i class="bi bi-calendar-x" style="font-size:28px; color:var(--text-3); margin-bottom:8px; display:inline-block;"></i>
                        <div style="font-weight:700;">Tidak ada catatan kehadiran pada periode {{ $periodeText }}.</div>
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            @endif
          </div>
        </div>
      </div>

      {{-- 3. PORTOFOLIO KARAKTER, KEDISIPLINAN & SELF-REWARD --}}
      @php
        $poinBersih = $kasusDisiplin ? $kasusDisiplin->poin_bersih : 0;
        $totalPelanggaran = $kasusDisiplin ? $kasusDisiplin->total_poin_pelanggaran : 0;
        $totalPemulihan = $kasusDisiplin ? $kasusDisiplin->total_poin_pemulihan : 0;
        $a1 = $pengaturanDisiplin->ambang_tahap_1_wali ?? 10;
        $a2 = $pengaturanDisiplin->ambang_tahap_2_bk ?? 30;
        $a3 = $pengaturanDisiplin->ambang_tahap_3_wakasis ?? 50;
        $a4 = $pengaturanDisiplin->ambang_tahap_4_kepsek ?? 75;
      @endphp
      <div class="dossier-card" style="margin-top: 20px;">
        <div class="dossier-header">
          <div style="display:flex; align-items:center; gap:8px;">
            <span style="width:32px; height:32px; border-radius:8px; background:var(--gold-subtle); color:var(--gold); display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
              <i class="bi bi-shield-check"></i>
            </span>
            <div>
              <h3 style="font-size:14.5px; font-weight:800; color:var(--text); margin:0;">Portofolio Karakter &amp; Kredit Kedisiplinan</h3>
              <p style="font-size:11.5px; color:var(--text-3); margin:0;">Pantauan pembinaan karakter &amp; apresiasi tindakan positif siswa</p>
            </div>
          </div>
          <div>
            @if($poinBersih == 0)
              <span class="badge" style="background:rgba(34,197,94,0.12); color:#16A34A; border:1px solid rgba(34,197,94,0.25); font-weight:800; border-radius:20px; font-size:11px;">
                <i class="bi bi-check-circle-fill"></i> Status: Tertib &amp; Bebas Masalah (0 Poin)
              </span>
            @elseif($poinBersih >= $a4)
              <span class="badge" style="background:rgba(239,68,68,0.12); color:#DC2626; border:1px solid rgba(239,68,68,0.25); font-weight:800; border-radius:20px; font-size:11px;">
                <i class="bi bi-exclamation-octagon-fill"></i> Tahap 4: Penanganan Kepala Sekolah ({{ $poinBersih }} Poin)
              </span>
            @elseif($poinBersih >= $a3)
              <span class="badge" style="background:rgba(234,88,12,0.12); color:#EA580C; border:1px solid rgba(234,88,12,0.25); font-weight:800; border-radius:20px; font-size:11px;">
                <i class="bi bi-shield-exclamation"></i> Tahap 3: Pembinaan Kesiswaan ({{ $poinBersih }} Poin)
              </span>
            @elseif($poinBersih >= $a2)
              <span class="badge" style="background:rgba(37,99,235,0.12); color:#2563EB; border:1px solid rgba(37,99,235,0.25); font-weight:800; border-radius:20px; font-size:11px;">
                <i class="bi bi-person-lines-fill"></i> Tahap 2: Bimbingan Konseling BK ({{ $poinBersih }} Poin)
              </span>
            @else
              <span class="badge" style="background:var(--gold-subtle); color:var(--gold); border:1px solid var(--gold-border); font-weight:800; border-radius:20px; font-size:11px;">
                <i class="bi bi-info-circle-fill"></i> Tahap 1: Bimbingan Wali Kelas ({{ $poinBersih }} Poin)
              </span>
            @endif
          </div>
        </div>

        {{-- Body 2 Kolom --}}
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:14px;">
          
          {{-- Kolom Kiri: Apresiasi & Tindakan Positif --}}
          <div style="background:var(--bg-subtle); border:1px solid var(--border); border-radius:var(--r-md); padding:14px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
              <span style="font-size:12px; font-weight:800; color:var(--text); display:inline-flex; align-items:center; gap:5px;">
                <i class="bi bi-gift-fill" style="color:var(--green);"></i> Apresiasi &amp; Self-Reward
              </span>
              <span class="badge" style="background:rgba(34,197,94,0.12); color:#16A34A; border:1px solid rgba(34,197,94,0.25); font-weight:800; font-size:10.5px;">
                -{{ $totalPemulihan }} Poin
              </span>
            </div>

            @if($kasusDisiplin && $kasusDisiplin->rewards->count() > 0)
              <div style="display:flex; flex-direction:column; gap:6px;">
                @foreach($kasusDisiplin->rewards as $rew)
                  <div style="background:var(--bg-card); border:1px solid var(--border); border-radius:8px; padding:8px 10px; display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">
                    <div style="flex:1; min-width:0;">
                      <strong style="color:var(--text); font-size:11.5px; display:block; line-height:1.3;">{{ $rew->nama_tindakan }}</strong>
                      <div style="font-size:10px; color:var(--text-3); margin-top:2px;">
                        <i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($rew->tanggal)->translatedFormat('d M Y') }} · {{ $rew->dicatat_oleh }}
                      </div>
                    </div>
                    <span class="badge" style="background:rgba(34,197,94,0.12); color:#16A34A; font-weight:900; font-size:10.5px; flex-shrink:0;">
                      -{{ $rew->poin_dikurangi }} Poin
                    </span>
                  </div>
                @endforeach
              </div>
            @else
              <div style="text-align:center; padding:16px 10px; color:var(--text-3); font-size:11.5px; line-height:1.4;">
                <i class="bi bi-stars" style="font-size:20px; color:var(--green); opacity:0.6; display:block; margin-bottom:4px;"></i>
                Siswa dapat meraih poin pemulihan dan apresiasi melalui prestasi lomba, hafalan ibadah, bakti sosial, serta kehadiran 100% tepat waktu.
              </div>
            @endif
          </div>

          {{-- Kolom Kanan: Catatan Kedisiplinan & Pelanggaran --}}
          <div style="background:var(--bg-subtle); border:1px solid var(--border); border-radius:var(--r-md); padding:14px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
              <span style="font-size:12px; font-weight:800; color:var(--text); display:inline-flex; align-items:center; gap:5px;">
                <i class="bi bi-clock-history" style="color:var(--gold);"></i> Catatan Kedisiplinan
              </span>
              <span class="badge" style="background:rgba(239,68,68,0.12); color:#DC2626; border:1px solid rgba(239,68,68,0.25); font-weight:800; font-size:10.5px;">
                +{{ $totalPelanggaran }} Poin
              </span>
            </div>

            @if($kasusDisiplin && ($kasusDisiplin->total_alpha > 0 || $kasusDisiplin->total_bolos > 0 || $kasusDisiplin->total_terlambat > 0 || $kasusDisiplin->pelanggarans->count() > 0))
              <div style="display:flex; flex-direction:column; gap:6px; font-size:11.5px;">
                @if($kasusDisiplin->total_alpha > 0)
                  <div style="display:flex; justify-content:space-between; align-items:center; background:var(--bg-card); border:1px solid var(--border); padding:6px 10px; border-radius:6px;">
                    <span style="color:var(--text-2);"><i class="bi bi-x-circle-fill" style="color:var(--red); margin-right:4px;"></i> Alpha (Tidak Hadir):</span>
                    <strong style="color:var(--text); font-family:var(--font-mono);">{{ $kasusDisiplin->total_alpha }}h ({{ $kasusDisiplin->total_alpha * ($pengaturanDisiplin->bobot_alpha ?? 10) }} pt)</strong>
                  </div>
                @endif
                @if($kasusDisiplin->total_bolos > 0)
                  <div style="display:flex; justify-content:space-between; align-items:center; background:var(--bg-card); border:1px solid var(--border); padding:6px 10px; border-radius:6px;">
                    <span style="color:var(--text-2);"><i class="bi bi-door-open-fill" style="color:var(--amber); margin-right:4px;"></i> Bolos Jam Pelajaran:</span>
                    <strong style="color:var(--text); font-family:var(--font-mono);">{{ $kasusDisiplin->total_bolos }}x ({{ $kasusDisiplin->total_bolos * ($pengaturanDisiplin->bobot_bolos ?? 15) }} pt)</strong>
                  </div>
                @endif
                @if($kasusDisiplin->total_terlambat > 0)
                  @php
                    $hLate = max(0, $kasusDisiplin->total_terlambat - ($pengaturanDisiplin->toleransi_terlambat_piket ?? 2));
                  @endphp
                  <div style="display:flex; justify-content:space-between; align-items:center; background:var(--bg-card); border:1px solid var(--border); padding:6px 10px; border-radius:6px;">
                    <span style="color:var(--text-2);"><i class="bi bi-clock-fill" style="color:var(--blue); margin-right:4px;"></i> Keterlambatan:</span>
                    <strong style="color:var(--text); font-family:var(--font-mono);">{{ $kasusDisiplin->total_terlambat }}x ({{ $hLate * ($pengaturanDisiplin->bobot_terlambat ?? 3) }} pt)</strong>
                  </div>
                @endif
                @foreach($kasusDisiplin->pelanggarans as $pel)
                  <div style="display:flex; justify-content:space-between; align-items:flex-start; background:var(--bg-card); border:1px solid var(--border); padding:6px 10px; border-radius:6px; border-left:3px solid var(--gold);">
                    <div style="flex:1; min-width:0;">
                      <strong style="color:var(--text); font-size:11px; display:block;">{{ $pel->nama_pelanggaran }}</strong>
                      <div style="font-size:9.5px; color:var(--text-3);">{{ \Carbon\Carbon::parse($pel->tanggal)->translatedFormat('d M Y') }}</div>
                    </div>
                    <span style="color:var(--text); font-weight:800; font-size:11px; font-family:var(--font-mono); flex-shrink:0;">+{{ $pel->poin_ditambah }} pt</span>
                  </div>
                @endforeach
              </div>
            @else
              <div style="text-align:center; padding:16px 10px; color:var(--text-3); font-size:11.5px; line-height:1.4;">
                <i class="bi bi-shield-check" style="font-size:20px; color:var(--green); opacity:0.6; display:block; margin-bottom:4px;"></i>
                Tidak ada catatan pelanggaran tata tertib. Pertahankan kedisiplinan belajar ananda!
              </div>
            @endif
          </div>
        </div>

        {{-- Edukasi Kolaboratif Sekolah & Keluarga --}}
        <div style="margin-top:14px; background:var(--gold-subtle); border:1px solid var(--gold-border); border-radius:8px; padding:10px 14px; display:flex; align-items:flex-start; gap:10px;">
          <i class="bi bi-info-circle-fill" style="color:var(--gold); font-size:16px; flex-shrink:0; margin-top:2px;"></i>
          <span style="font-size:11.5px; color:var(--text-2); line-height:1.45;">
            <strong>Prinsip Pendidikan Positif:</strong> Seluruh catatan ketertiban bersifat edukatif dan dapat dipulihkan melalui perbaikan perilaku, keaktifan ibadah, serta konsistensi hadir tepat waktu di sekolah.
          </span>
        </div>
      </div>

    @endif

  </main>

  {{-- FOOTER --}}
  <footer class="footer">
    <div style="font-weight:800; color:var(--text); margin-bottom:3px;">SIRANI · Sistem Informasi Responsif Absensi</div>
    <div>SMK Negeri 1 Air Naningan · Layanan pemantauan kehadiran mandiri terpadu</div>
  </footer>

  <script>
    // Preservasi posisi scroll saat pergantian tab atau filter tanggal
    function preserveScrollAndSubmit(form) {
      sessionStorage.setItem('portal_scroll_y', window.scrollY);
      form.submit();
    }

    document.querySelectorAll('.period-btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        sessionStorage.setItem('portal_scroll_y', window.scrollY);
      });
    });

    window.addEventListener('beforeunload', function() {
      sessionStorage.setItem('portal_scroll_y', window.scrollY);
    });

    document.addEventListener('DOMContentLoaded', function() {
      const savedY = sessionStorage.getItem('portal_scroll_y');
      if (savedY !== null) {
        window.scrollTo({
          top: parseInt(savedY, 10),
          behavior: 'instant'
        });
        sessionStorage.removeItem('portal_scroll_y');
      }
    });

    function copyLinkPresensi(url) {
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(function() {
          showCopySuccess();
        }).catch(function() {
          fallbackCopy(url);
        });
      } else {
        fallbackCopy(url);
      }
    }

    function fallbackCopy(url) {
      const temp = document.createElement('input');
      temp.value = url;
      document.body.appendChild(temp);
      temp.select();
      document.execCommand('copy');
      document.body.removeChild(temp);
      showCopySuccess();
    }

    function showCopySuccess() {
      const btnText = document.getElementById('copyBtnText');
      if (btnText) {
        const orig = btnText.innerText;
        btnText.innerText = 'Tersalin!';
        setTimeout(function() {
          btnText.innerText = orig;
        }, 2000);
      }
    }

    function resetSiswaTersimpan() {
      window.location.href = '/cek-presensi';
    }
  </script>

</body>
</html>
