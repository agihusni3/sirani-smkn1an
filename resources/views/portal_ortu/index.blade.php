<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>{{ $siswa ? 'Rekap Kehadiran '.$siswa->nama.' — Portal Wali Murid' : 'Portal Kehadiran Siswa & Orang Tua — SMKN 1 Air Naningan' }}</title>
  
  {{-- PWA Meta Tags --}}
  <link rel="manifest" href="/manifest.json" />
  <meta name="theme-color" content="#0F172A" />
  <meta name="mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-status-bar-style" content="default" />
  <meta name="apple-mobile-web-app-title" content="SIRANI" />
  <link rel="apple-touch-icon" href="/icons/icon-192.png" />
  <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192.png" />
  
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
      gap: 10px;
      text-decoration: none;
      color: var(--text);
    }
    .brand-icon {
      width: 36px;
      height: 36px;
      background: var(--bg-subtle);
      border: 1px solid var(--border-2);
      border-radius: var(--r-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--text);
      font-size: 16px;
      flex-shrink: 0;
    }
    .brand-text h1 {
      font-size: 14px;
      font-weight: 800;
      line-height: 1.2;
      color: var(--text);
      letter-spacing: -0.01em;
    }
    .brand-text p {
      font-size: 11px;
      color: var(--text-3);
      font-weight: 600;
    }

    /* ─── Container ─── */
    .container {
      max-width: 1020px;
      margin: 0 auto;
      padding: 20px 16px 40px;
      width: 100%;
      flex: 1;
    }

    /* ─── Institutional Search Console ─── */
    .search-console-card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: var(--r-xl);
      padding: 24px 20px;
      box-shadow: var(--shadow-sm);
      margin-bottom: 24px;
    }
    .search-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 11px;
      font-weight: 800;
      color: var(--text-3);
      margin-bottom: 10px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .search-title {
      font-size: 21px;
      font-weight: 900;
      line-height: 1.25;
      margin-bottom: 8px;
      letter-spacing: -0.02em;
      color: var(--text);
    }
    .search-desc {
      font-size: 13px;
      color: var(--text-2);
      max-width: 640px;
      line-height: 1.55;
      margin-bottom: 18px;
    }

    .search-form-box {
      max-width: 600px;
      width: 100%;
    }
    .search-input-wrap {
      display: flex;
      background: var(--bg-subtle);
      border: 1px solid var(--border-2);
      border-radius: var(--r-md);
      padding: 4px;
      transition: all .2s ease;
      gap: 4px;
    }
    .search-input-wrap:focus-within {
      border-color: var(--text);
      background: var(--bg-card);
    }
    .search-input {
      flex: 1;
      border: none;
      outline: none;
      padding: 10px 12px;
      font-size: 13.5px;
      font-weight: 600;
      color: var(--text);
      background: transparent;
      font-family: var(--font-main);
      min-width: 0;
    }
    .search-input::placeholder {
      color: var(--text-3);
      font-weight: 500;
      font-size: 12.5px;
    }
    .btn-search {
      background: var(--text);
      color: var(--bg);
      font-weight: 800;
      font-size: 12.5px;
      border: none;
      border-radius: var(--r-sm);
      padding: 0 16px;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      transition: all .2s;
      font-family: var(--font-main);
      white-space: nowrap;
      height: 40px;
    }
    .btn-search:hover {
      opacity: 0.9;
    }

    /* ─── Highlights Features Grid ─── */
    .portal-features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 14px;
      margin-top: 22px;
      padding-top: 18px;
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
      color: var(--text);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
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

    @media (max-width: 600px) {
      .top-nav-inner { padding: 10px 14px; }
      .nav-actions button { padding: 5px 9px; font-size: 10.5px; }
      .search-title { font-size: 18px; }
      .search-console-card { padding: 20px 16px; border-radius: var(--r-lg); }
      .portal-features-grid { grid-template-columns: 1fr; }
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
    .compact-search-info {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }
    .compact-search-form {
      display: flex;
      align-items: center;
      gap: 6px;
      margin: 0;
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
      background: var(--bg-subtle);
      border: 2px solid var(--border-2);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 26px;
      font-weight: 900;
      color: var(--text);
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
      padding: 4px 10px;
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
      background: var(--bg-subtle);
      border-color: var(--border-2);
      color: var(--text);
      font-weight: 800;
    }

    @media (max-width: 600px) {
      .compact-search-card {
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
        padding: 12px;
      }
      .compact-search-info {
        justify-content: space-between;
      }
      .compact-search-form {
        width: 100%;
      }
      .compact-search-form input {
        flex: 1;
        width: 100% !important;
      }
      .dossier-card {
        padding: 18px 14px;
        border-radius: var(--r-lg);
      }
      .dossier-header {
        flex-direction: column;
        align-items: stretch;
        gap: 10px;
      }
      .dossier-header-title {
        text-align: center;
      }
      .portal-nav-btn-group {
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 5px !important;
        width: 100% !important;
      }
      .btn-portal-tab {
        padding: 7px 3px !important;
        font-size: 10.5px !important;
        gap: 3px !important;
        width: 100% !important;
      }
      .btn-portal-tab i {
        font-size: 11px !important;
      }
      .student-info-left {
        flex-direction: column;
        text-align: center;
        gap: 12px;
      }
      .student-meta-tags {
        justify-content: center;
      }
      .student-avatar {
        width: 64px;
        height: 64px;
      }
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
      flex-wrap: wrap;
      gap: 6px;
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
      color: var(--text);
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
      grid-template-columns: repeat(2, 1fr);
      gap: 10px;
      margin-bottom: 20px;
    }
    .discipline-score-card {
      grid-column: 1 / -1;
    }
    @media (min-width: 768px) {
      .stats-overview-grid {
        grid-template-columns: 1.35fr repeat(4, 1fr);
        gap: 12px;
      }
      .discipline-score-card {
        grid-column: auto;
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
    .portal-nav-btn-group {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 6px;
    }
    .btn-portal-tab {
      background: var(--bg-card);
      border: 1px solid var(--border-2);
      padding: 6px 10px;
      border-radius: var(--r-sm);
      font-size: 11.5px;
      font-weight: 800;
      color: #0F172A;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
      transition: all .2s ease;
      font-family: var(--font-main);
      white-space: nowrap;
      text-align: center;
    }
    .btn-portal-tab:hover {
      background: var(--bg-subtle);
      border-color: #000000;
    }
    .btn-portal-tab.active {
      background: #000000 !important;
      color: #FFFFFF !important;
      border-color: #000000 !important;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25) !important;
    }
    .btn-portal-tab.active i,
    .btn-portal-tab.active span {
      color: #FFFFFF !important;
    }
    .portal-badge-count {
      font-size: 9.5px;
      font-weight: 800;
      background: #000000;
      color: #FFFFFF;
      padding: 1px 5px;
      border-radius: 10px;
      margin-left: 2px;
      display: inline-block;
    }
    .btn-portal-tab.active .portal-badge-count {
      background: #FFFFFF !important;
      color: #000000 !important;
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

      <div class="nav-actions" style="display:flex; align-items:center; gap:8px;">
        <button type="button" id="btnPwaInstall" onclick="triggerPwaInstall()" style="background:var(--text); color:var(--bg); border:none; padding:6px 12px; border-radius:var(--r-sm); font-size:11.5px; font-weight:800; cursor:pointer; display:inline-flex; align-items:center; gap:5px; font-family:var(--font-main);">
          <i class="bi bi-phone"></i> <span>Instal Aplikasi</span>
        </button>
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
        <div id="savedStudentsContainer" style="display:none;"></div>

        <p class="search-desc">
          Selamat datang di portal presensi mandiri SMKN 1 Air Naningan. Masukkan <strong>NIS</strong>, <strong>NISN</strong>, atau <strong>Nomor WhatsApp Orang Tua</strong> untuk memantau catatan absensi Face ID, riwayat izin, dan kedisiplinan ananda secara real-time.
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
                placeholder="Ketik NIS, NISN, atau No. WhatsApp..."
                autocomplete="off"
                required
                autofocus
              />
              <button type="button" onclick="startQrScanner()" class="btn-search" style="background:var(--bg-subtle); color:var(--text); border:1px solid var(--border-2); padding:0 12px;" title="Scan QR Code Kartu Pelajar">
                <i class="bi bi-qr-code-scan"></i> <span style="display:none;" class="qr-btn-text">Scan</span>
              </button>
              <button type="submit" class="btn-search">
                <i class="bi bi-arrow-right-circle-fill"></i> Cek Presensi
              </button>
            </div>
            <div style="font-size:11.5px; color:var(--text-3); margin-top:6px; display:flex; align-items:center; gap:4px;">
              <i class="bi bi-info-circle"></i> <span>Tips: Bisa langsung cari menggunakan No. WhatsApp Orang Tua yang terdaftar.</span>
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
        <div class="compact-search-info">
          <span style="font-size:11.5px; font-weight:800; color:var(--text-3); text-transform:uppercase; letter-spacing:0.5px;">
            Siswa Terpilih:
          </span>
          <strong style="color:var(--text); font-size:13px;">{{ $siswa->nama }}</strong>
          <span style="font-size:11.5px; color:var(--text-3); font-family:var(--font-mono);">(NIS: {{ $siswa->nis }})</span>
        </div>

        <form method="GET" action="{{ route('portal.ortu.index') }}" class="compact-search-form">
          <input
            type="text"
            name="keyword"
            placeholder="Cari NIS lain..."
            class="form-control-pt"
            style="height:34px; padding:0 10px; font-size:12px;"
          />
          <button type="submit" class="btn-search" style="padding:0 14px; height:34px; font-size:11.5px;">
            <i class="bi bi-search"></i> Ganti
          </button>
        </form>
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
          <span class="dossier-header-title" style="font-size:11.5px; font-weight:800; color:var(--text); text-transform:uppercase; letter-spacing:0.5px;">
            Profil Lengkap Siswa
          </span>
          
          <div class="portal-nav-btn-group">
            <button type="button" id="btnToggleAbsen" onclick="togglePortalSection('absen')" class="btn-portal-tab">
              <i class="bi bi-calendar-check"></i> <span>Rekap Absen</span>
            </button>
            <button type="button" id="btnToggleKasus" onclick="togglePortalSection('kasus')" class="btn-portal-tab">
              <i class="bi bi-shield-check"></i> <span>Rekap Kasus</span>
            </button>
            <button type="button" id="btnTogglePengumuman" onclick="togglePortalSection('pengumuman')" class="btn-portal-tab">
              <i class="bi bi-megaphone"></i> <span>Pengumuman</span>
              @if(isset($pengumumans) && $pengumumans->count() > 0)
                <span class="portal-badge-count">{{ $pengumumans->count() }}</span>
              @endif
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
              <div style="font-size:12px; color:var(--text-2); display:flex; flex-wrap:wrap; gap:6px; justify-content:center;">
                <span>NIS: <strong style="font-family:var(--font-mono); color:var(--text);">{{ $siswa->nis }}</strong></span>
                <span>•</span>
                <span>NISN: <strong style="font-family:var(--font-mono); color:var(--text);">{{ $siswa->nisn ?: '-' }}</strong></span>
              </div>
              <div class="student-meta-tags">
                <span class="tag-pill">
                  <i class="bi bi-building"></i> Kelas {{ $rombel->nama_rombel ?? 'Belum Ada Rombel' }}
                </span>
                <span class="tag-pill">
                  <i class="bi bi-book-half"></i> {{ $rombel->jurusan->nama_jurusan ?? '-' }}
                </span>
                @if($siswa->status === 'pkl')
                  <span class="tag-pill" style="color:var(--text); font-weight:800;">
                    <i class="bi bi-briefcase"></i> Praktik Kerja (PKL)
                  </span>
                @endif
                @if($waliKelas)
                  <span class="tag-pill">
                    <i class="bi bi-person"></i> Wali Kelas: {{ $waliKelas->nama }}
                  </span>
                  @if($waliKelas->no_hp)
                    @php
                      $hpWaliClean = preg_replace('/[^0-9]/', '', $waliKelas->no_hp);
                      if (str_starts_with($hpWaliClean, '0')) $hpWaliClean = '62' . substr($hpWaliClean, 1);
                      $pesanWaWali = rawurlencode("Halo Bapak/Ibu Wali Kelas {$waliKelas->nama}, saya orang tua dari {$siswa->nama} (Kelas " . ($rombel->nama_rombel ?? '-') . "). Ingin berkonsultasi mengenai kehadiran/perkembangan belajar ananda.");
                    @endphp
                    <a href="https://wa.me/{{ $hpWaliClean }}?text={{ $pesanWaWali }}" target="_blank" class="tag-pill" style="color:#16A34A; font-weight:800;" title="Konsultasi WhatsApp dengan Wali Kelas">
                      <i class="bi bi-whatsapp"></i> Hubungi Wali Kelas
                    </a>
                  @endif
                @endif
                @if($siswa->nama_ortu)
                  <span class="tag-pill">
                    <i class="bi bi-people"></i> Wali Murid: {{ $siswa->nama_ortu }}
                  </span>
                @endif
              </div>
            </div>
          </div>

          {{-- Kanan: Status Kehadiran Hari Ini --}}
          <div class="today-widget">
            <div class="today-widget-title">
              <span style="font-weight:800; color:var(--text);">Kehadiran Hari Ini ({{ \Carbon\Carbon::today()->translatedFormat('d M Y') }})</span>
              <div>
                @if($todayAbsensi)
                  @if($todayAbsensi->status === 'hadir')
                    <span style="font-weight:800; font-size:12px; color:var(--text);">Hadir Tepat Waktu</span>
                  @elseif($todayAbsensi->status === 'terlambat')
                    <span style="font-weight:800; font-size:12px; color:var(--text);">Terlambat</span>
                  @elseif($todayAbsensi->status === 'izin')
                    <span style="font-weight:800; font-size:12px; color:var(--text);">Izin</span>
                  @elseif($todayAbsensi->status === 'sakit')
                    <span style="font-weight:800; font-size:12px; color:var(--text);">Sakit</span>
                  @elseif($todayAbsensi->status === 'bolos')
                    <span style="font-weight:800; font-size:12px; color:var(--text);">Bolos</span>
                  @elseif($todayAbsensi->status === 'alpha')
                    <span style="font-weight:800; font-size:12px; color:var(--text);">Alpha</span>
                  @endif
                @elseif($siswa->status === 'pkl')
                  <span style="font-weight:800; font-size:12px; color:var(--text);">PKL</span>
                @else
                  <span style="font-weight:800; font-size:12px; color:var(--text);">Belum Presensi</span>
                @endif
              </div>
            </div>

            <div class="today-time-grid">
              <div class="today-time-box">
                <div class="today-time-label">Masuk Gerbang</div>
                <div class="today-time-val">
                  {{ $todayAbsensi && $todayAbsensi->jam_masuk ? substr($todayAbsensi->jam_masuk, 0, 5).' WIB' : '—' }}
                </div>
              </div>
              <div class="today-time-box">
                <div class="today-time-label">Pulang Gerbang</div>
                <div class="today-time-val">
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
      <div id="riwayat-kehadiran" style="scroll-margin-top: 70px; display: {{ (request()->has('periode') || request()->has('tanggal') || request()->has('bulan') || request()->has('tahun')) ? 'block' : 'none' }};">
        
        {{-- KARTU SKOR DISIPLIN & 4 METRIK KEHADIRAN (KPI) --}}
        <div class="stats-overview-grid">
          {{-- Skor Disiplin Utama --}}
          <div class="discipline-score-card">
            <div class="discipline-val">{{ $stats['persen'] }}%</div>
            <div class="discipline-lbl">Tingkat Kedisiplinan</div>
            <div style="font-size:11.5px; font-weight:800; color:var(--text); margin-top:4px;">{{ $stats['predikat'] }} ({{ $stats['total'] }} Hari Aktif)</div>
          </div>

          {{-- 1. Hadir Tepat --}}
          <div class="stat-metric-card">
            <div class="stat-metric-title">
              <span style="font-weight:800; color:var(--text);">Hadir Tepat</span>
            </div>
            <div class="stat-metric-num" style="color:var(--text);">{{ $stats['hadir'] }}</div>
          </div>

          {{-- 2. Terlambat --}}
          <div class="stat-metric-card">
            <div class="stat-metric-title">
              <span style="font-weight:800; color:var(--text);">Terlambat</span>
            </div>
            <div class="stat-metric-num" style="color:var(--text);">{{ $stats['terlambat'] }}</div>
          </div>

          {{-- 3. Izin / Sakit --}}
          <div class="stat-metric-card">
            <div class="stat-metric-title">
              <span style="font-weight:800; color:var(--text);">Izin / Sakit</span>
            </div>
            <div class="stat-metric-num" style="color:var(--text);">{{ $stats['izin'] + $stats['sakit'] }}</div>
          </div>

          {{-- 4. Alpha / Bolos --}}
          <div class="stat-metric-card">
            <div class="stat-metric-title">
              <span style="font-weight:800; color:var(--text);">Alpha / Bolos</span>
            </div>
            <div class="stat-metric-num" style="color:var(--text);">{{ $stats['alpha'] + $stats['bolos'] }}</div>
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
                      <td style="text-align:center; white-space:nowrap; font-family:var(--font-mono); font-weight:800; font-size:13px; color:var(--text);">
                        @if($rb['total'] > 0)
                          {{ $rb['persen'] }}%
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
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:900; font-size:13.5px; color:var(--text);">
                      {{ $stats['persen'] }}%
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
                      <span style="font-weight:800; font-size:13px; color:var(--text);">
                        Hadir Tepat Waktu
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
                      <span style="font-weight:800; font-size:13px; color:var(--text);">
                        Terlambat Hadir
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
                      <span style="font-weight:800; font-size:13px; color:var(--text);">
                        Izin (Disetujui)
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
                      <span style="font-weight:800; font-size:13px; color:var(--text);">
                        Sakit
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
                      <span style="font-weight:800; font-size:13px; color:var(--text);">
                        Alpha (Tanpa Keterangan)
                      </span>
                    </td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:900; font-size:14px; color:var(--text);">
                      {{ $stats['alpha'] }} Hari
                    </td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:800; font-size:13px; color:var(--text-2);">
                      {{ $stats['total'] > 0 ? round(($stats['alpha'] / $stats['total']) * 100, 1) : 0 }}%
                    </td>
                    <td style="font-size:12px; color:var(--text);">
                      {{ $stats['alpha'] > 0 ? 'Perlu perhatian wali murid & konfirmasi ke wali kelas' : 'Tidak ada catatan alpha (Tertib)' }}
                    </td>
                  </tr>
                  <tr>
                    <td style="white-space:nowrap;">
                      <span style="font-weight:800; font-size:13px; color:var(--text);">
                        Bolos
                      </span>
                    </td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:900; font-size:14px; color:var(--text);">
                      {{ $stats['bolos'] }} Kali
                    </td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:800; font-size:13px; color:var(--text-2);">
                      {{ $stats['total'] > 0 ? round(($stats['bolos'] / $stats['total']) * 100, 1) : 0 }}%
                    </td>
                    <td style="font-size:12px; color:var(--text);">
                      {{ $stats['bolos'] > 0 ? 'Meninggalkan KBM tanpa izin piket' : 'Tidak ada catatan bolos' }}
                    </td>
                  </tr>
                  <tr style="background:var(--bg-subtle); font-weight:800; border-top:2px solid var(--border);">
                    <td style="white-space:nowrap; font-weight:900; color:var(--text);">
                      TOTAL HARI EFEKTIF
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
                          <span style="font-weight:800; font-size:12.5px; color:var(--text);">Hadir Tepat Waktu</span>
                        @elseif($abs->status === 'terlambat')
                          <span style="font-weight:800; font-size:12.5px; color:var(--text);">Terlambat</span>
                        @elseif($abs->status === 'izin')
                          <span style="font-weight:800; font-size:12.5px; color:var(--text);">Izin</span>
                        @elseif($abs->status === 'sakit')
                          <span style="font-weight:800; font-size:12.5px; color:var(--text);">Sakit</span>
                        @elseif($abs->status === 'bolos')
                          <span style="font-weight:800; font-size:12.5px; color:var(--text);">Bolos</span>
                        @elseif($abs->status === 'alpha')
                          <span style="font-weight:800; font-size:12.5px; color:var(--text);">Alpha</span>
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
      <div class="dossier-card" id="portofolio-karakter" style="margin-top: 20px; scroll-margin-top: 70px; display: none;">
        <div class="dossier-header">
          <div>
            <h3 style="font-size:14.5px; font-weight:800; color:var(--text); margin:0;">Portofolio Karakter &amp; Kredit Kedisiplinan</h3>
            <p style="font-size:11.5px; color:var(--text-3); margin-top:2px;">Pantauan pembinaan karakter &amp; apresiasi tindakan positif siswa</p>
          </div>
          <div>
            @if($poinBersih == 0)
              <span style="font-weight:800; font-size:12px; color:var(--text);">
                Status: Tertib &amp; Bebas Masalah (0 Poin)
              </span>
            @elseif($poinBersih >= $a4)
              <span style="font-weight:800; font-size:12px; color:var(--text);">
                Tahap 4: Penanganan Kepala Sekolah ({{ $poinBersih }} Poin)
              </span>
            @elseif($poinBersih >= $a3)
              <span style="font-weight:800; font-size:12px; color:var(--text);">
                Tahap 3: Pembinaan Kesiswaan ({{ $poinBersih }} Poin)
              </span>
            @elseif($poinBersih >= $a2)
              <span style="font-weight:800; font-size:12px; color:var(--text);">
                Tahap 2: Bimbingan Konseling BK ({{ $poinBersih }} Poin)
              </span>
            @else
              <span style="font-weight:800; font-size:12px; color:var(--text);">
                Tahap 1: Bimbingan Wali Kelas ({{ $poinBersih }} Poin)
              </span>
            @endif
          </div>
        </div>

        {{-- Body 2 Kolom --}}
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:14px;">
          
          {{-- Kolom Kiri: Apresiasi & Tindakan Positif --}}
          <div style="background:var(--bg-subtle); border:1px solid var(--border); border-radius:var(--r-md); padding:14px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
              <span style="font-size:12.5px; font-weight:800; color:var(--text);">
                Apresiasi &amp; Self-Reward
              </span>
              <span style="font-weight:900; font-size:13px; font-family:var(--font-mono); color:var(--text);">
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
                        {{ \Carbon\Carbon::parse($rew->tanggal)->translatedFormat('d M Y') }} · {{ $rew->dicatat_oleh }}
                      </div>
                    </div>
                    <span style="font-weight:800; font-size:11px; font-family:var(--font-mono); color:var(--text); flex-shrink:0;">
                      -{{ $rew->poin_dikurangi }} Poin
                    </span>
                  </div>
                @endforeach
              </div>
            @else
              <div style="text-align:center; padding:16px 10px; color:var(--text-3); font-size:11.5px; line-height:1.4;">
                Siswa dapat meraih poin pemulihan dan apresiasi melalui prestasi lomba, hafalan ibadah, bakti sosial, serta kehadiran 100% tepat waktu.
              </div>
            @endif
          </div>

          {{-- Kolom Kanan: Catatan Kedisiplinan & Pelanggaran --}}
          <div style="background:var(--bg-subtle); border:1px solid var(--border); border-radius:var(--r-md); padding:14px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
              <span style="font-size:12.5px; font-weight:800; color:var(--text);">
                Catatan Kedisiplinan
              </span>
              <span style="font-weight:900; font-size:13px; font-family:var(--font-mono); color:var(--text);">
                +{{ $totalPelanggaran }} Poin
              </span>
            </div>

            @if($kasusDisiplin && ($kasusDisiplin->total_alpha > 0 || $kasusDisiplin->total_bolos > 0 || $kasusDisiplin->total_terlambat > 0 || $kasusDisiplin->pelanggarans->count() > 0))
              <div style="display:flex; flex-direction:column; gap:6px; font-size:11.5px;">
                @if($kasusDisiplin->total_alpha > 0)
                  <div style="display:flex; justify-content:space-between; align-items:center; background:var(--bg-card); border:1px solid var(--border); padding:6px 10px; border-radius:6px;">
                    <span style="color:var(--text);">Alpha (Tidak Hadir):</span>
                    <strong style="color:var(--text); font-family:var(--font-mono);">{{ $kasusDisiplin->total_alpha }}h ({{ $kasusDisiplin->total_alpha * ($pengaturanDisiplin->bobot_alpha ?? 10) }} pt)</strong>
                  </div>
                @endif
                @if($kasusDisiplin->total_bolos > 0)
                  <div style="display:flex; justify-content:space-between; align-items:center; background:var(--bg-card); border:1px solid var(--border); padding:6px 10px; border-radius:6px;">
                    <span style="color:var(--text);">Bolos Jam Pelajaran:</span>
                    <strong style="color:var(--text); font-family:var(--font-mono);">{{ $kasusDisiplin->total_bolos }}x ({{ $kasusDisiplin->total_bolos * ($pengaturanDisiplin->bobot_bolos ?? 15) }} pt)</strong>
                  </div>
                @endif
                @if($kasusDisiplin->total_terlambat > 0)
                  @php
                    $hLate = max(0, $kasusDisiplin->total_terlambat - ($pengaturanDisiplin->toleransi_terlambat_piket ?? 2));
                  @endphp
                  <div style="display:flex; justify-content:space-between; align-items:center; background:var(--bg-card); border:1px solid var(--border); padding:6px 10px; border-radius:6px;">
                    <span style="color:var(--text);">Keterlambatan:</span>
                    <strong style="color:var(--text); font-family:var(--font-mono);">{{ $kasusDisiplin->total_terlambat }}x ({{ $hLate * ($pengaturanDisiplin->bobot_terlambat ?? 3) }} pt)</strong>
                  </div>
                @endif
                @foreach($kasusDisiplin->pelanggarans as $pel)
                  <div style="display:flex; justify-content:space-between; align-items:flex-start; background:var(--bg-card); border:1px solid var(--border); padding:6px 10px; border-radius:6px;">
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
                Tidak ada catatan pelanggaran tata tertib. Pertahankan kedisiplinan belajar ananda!
              </div>
            @endif
          </div>
        </div>

        {{-- Edukasi Kolaboratif Sekolah & Keluarga --}}
        <div style="margin-top:14px; background:var(--bg-subtle); border:1px solid var(--border); border-radius:8px; padding:10px 14px; display:flex; align-items:flex-start; gap:10px;">
          <span style="font-size:11.5px; color:var(--text); line-height:1.45;">
            <strong>Prinsip Pendidikan Positif:</strong> Seluruh catatan ketertiban bersifat edukatif dan dapat dipulihkan melalui perbaikan perilaku, keaktifan ibadah, serta konsistensi hadir tepat waktu di sekolah.
          </span>
        </div>
      </div>

      {{-- 4. PENGUMUMAN RESMI SEKOLAH --}}
      <div class="dossier-card" id="section-pengumuman" style="margin-top: 20px; scroll-margin-top: 70px; display: none;">
        <div class="dossier-header">
          <div>
            <h3 style="font-size:14.5px; font-weight:800; color:var(--text); margin:0;">Pengumuman &amp; Informasi Sekolah</h3>
            <p style="font-size:11.5px; color:var(--text-3); margin-top:2px;">Pemberitahuan resmi dari pihak SMKN 1 Air Naningan untuk wali murid</p>
          </div>
          @if(isset($pengumumans) && $pengumumans->count() > 0)
            <span style="font-size:11.5px; font-weight:800; color:var(--text);">
              {{ $pengumumans->count() }} Pengumuman Aktif
            </span>
          @endif
        </div>

        @if(isset($pengumumans) && $pengumumans->count() > 0)
          <div style="display:flex; flex-direction:column; gap:12px;">
            @foreach($pengumumans as $p)
              @php $badge = $p->kategori_badge; @endphp
              <div style="background:var(--bg-subtle); border:1px solid var(--border); border-radius:var(--r-md); padding:14px 16px;">
                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:6px; margin-bottom:4px;">
                  <span style="font-size:11px; font-weight:800; color:var(--text); text-transform:uppercase; letter-spacing:0.4px;">
                    {{ $badge['label'] }}
                  </span>
                  <span style="font-size:11px; color:var(--text-3); font-weight:600;">
                    <i class="bi bi-clock"></i> {{ $p->created_at->translatedFormat('d M Y') }}
                  </span>
                </div>
                <h4 style="font-size:14px; font-weight:800; color:var(--text); margin:0 0 6px;">{{ $p->judul }}</h4>
                <p style="font-size:12.5px; color:var(--text-2); line-height:1.5; margin:0; white-space:pre-line;">{{ $p->isi_pesan }}</p>
                
                @if($p->banner_url)
                  <div style="position:relative; margin-top:10px; border-radius:8px; overflow:hidden; border:1px solid var(--border); max-width:100%; cursor:zoom-in;" onclick="openImageZoom('{{ $p->banner_url }}', '{{ addslashes($p->judul) }}')">
                    <img src="{{ $p->banner_url }}" alt="{{ $p->judul }}" style="width:100%; max-height:380px; object-fit:contain; background:rgba(0,0,0,0.02); display:block;" />
                    <div style="position:absolute; bottom:8px; right:8px; background:rgba(15,23,42,0.85); color:#fff; font-size:11px; font-weight:700; padding:4px 10px; border-radius:6px; backdrop-filter:blur(4px); display:flex; align-items:center; gap:5px; pointer-events:none;">
                      <i class="bi bi-arrows-fullscreen"></i> Ketuk untuk Zoom
                    </div>
                  </div>
                @endif
              </div>
            @endforeach
          </div>
        @else
          <div style="text-align:center; padding:24px 16px; color:var(--text-3); font-size:12px;">
            <i class="bi bi-megaphone" style="font-size:24px; display:block; margin-bottom:6px; opacity:0.6;"></i>
            Saat ini belum ada pengumuman resmi terbaru dari sekolah.
          </div>
        @endif
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
    function togglePortalSection(type) {
      const secAbsen = document.getElementById('riwayat-kehadiran');
      const secKasus = document.getElementById('portofolio-karakter');
      const secPengumuman = document.getElementById('section-pengumuman');
      const btnAbsen = document.getElementById('btnToggleAbsen');
      const btnKasus = document.getElementById('btnToggleKasus');
      const btnPengumuman = document.getElementById('btnTogglePengumuman');

      if (type === 'absen') {
        if (!secAbsen) return;
        const isHidden = (secAbsen.style.display === 'none' || !secAbsen.style.display);
        if (isHidden) {
          secAbsen.style.display = 'block';
          if (secKasus) secKasus.style.display = 'none';
          if (secPengumuman) secPengumuman.style.display = 'none';
          if (btnAbsen) btnAbsen.classList.add('active');
          if (btnKasus) btnKasus.classList.remove('active');
          if (btnPengumuman) btnPengumuman.classList.remove('active');
          setTimeout(function() {
            secAbsen.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }, 50);
        } else {
          secAbsen.style.display = 'none';
          if (btnAbsen) btnAbsen.classList.remove('active');
        }
      } else if (type === 'kasus') {
        if (!secKasus) return;
        const isHidden = (secKasus.style.display === 'none' || !secKasus.style.display);
        if (isHidden) {
          secKasus.style.display = 'block';
          if (secAbsen) secAbsen.style.display = 'none';
          if (secPengumuman) secPengumuman.style.display = 'none';
          if (btnKasus) btnKasus.classList.add('active');
          if (btnAbsen) btnAbsen.classList.remove('active');
          if (btnPengumuman) btnPengumuman.classList.remove('active');
          setTimeout(function() {
            secKasus.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }, 50);
        } else {
          secKasus.style.display = 'none';
          if (btnKasus) btnKasus.classList.remove('active');
        }
      } else if (type === 'pengumuman') {
        if (!secPengumuman) return;
        const isHidden = (secPengumuman.style.display === 'none' || !secPengumuman.style.display);
        if (isHidden) {
          secPengumuman.style.display = 'block';
          if (secAbsen) secAbsen.style.display = 'none';
          if (secKasus) secKasus.style.display = 'none';
          if (btnPengumuman) btnPengumuman.classList.add('active');
          if (btnAbsen) btnAbsen.classList.remove('active');
          if (btnKasus) btnKasus.classList.remove('active');
          setTimeout(function() {
            secPengumuman.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }, 50);
        } else {
          secPengumuman.style.display = 'none';
          if (btnPengumuman) btnPengumuman.classList.remove('active');
        }
      }
    }
    window.togglePortalSection = togglePortalSection;

    document.addEventListener('DOMContentLoaded', function() {
      const secAbsen = document.getElementById('riwayat-kehadiran');
      const btnAbsen = document.getElementById('btnToggleAbsen');
      const secKasus = document.getElementById('portofolio-karakter');
      const btnKasus = document.getElementById('btnToggleKasus');
      const secPengumuman = document.getElementById('section-pengumuman');
      const btnPengumuman = document.getElementById('btnTogglePengumuman');

      const urlParams = new URLSearchParams(window.location.search);
      const hash = window.location.hash;

      if (hash === '#portofolio-karakter') {
        if (secKasus) secKasus.style.display = 'block';
        if (secAbsen) secAbsen.style.display = 'none';
        if (secPengumuman) secPengumuman.style.display = 'none';
        if (btnKasus) btnKasus.classList.add('active');
        if (btnAbsen) btnAbsen.classList.remove('active');
        if (btnPengumuman) btnPengumuman.classList.remove('active');
      } else if (hash === '#section-pengumuman' || hash === '#pengumuman') {
        if (secPengumuman) secPengumuman.style.display = 'block';
        if (secAbsen) secAbsen.style.display = 'none';
        if (secKasus) secKasus.style.display = 'none';
        if (btnPengumuman) btnPengumuman.classList.add('active');
        if (btnAbsen) btnAbsen.classList.remove('active');
        if (btnKasus) btnKasus.classList.remove('active');
      } else if (hash === '#riwayat-kehadiran' || urlParams.has('periode') || urlParams.has('tanggal') || urlParams.has('bulan') || urlParams.has('tahun')) {
        if (secAbsen) secAbsen.style.display = 'block';
        if (secKasus) secKasus.style.display = 'none';
        if (secPengumuman) secPengumuman.style.display = 'none';
        if (btnAbsen) btnAbsen.classList.add('active');
        if (btnKasus) btnKasus.classList.remove('active');
        if (btnPengumuman) btnPengumuman.classList.remove('active');
      }

      @if($siswa)
        saveSiswaToLocalStorage({
          nis: "{{ $siswa->nis }}",
          nama: "{{ $siswa->nama }}",
          rombel: "{{ $rombel->nama_rombel ?? '' }}",
          foto: "{{ $siswa->foto ? asset('storage/'.$siswa->foto) : '' }}"
        });
      @else
        renderSavedStudents();
      @endif
    });

    // Auto-Remember / LocalStorage Management
    function saveSiswaToLocalStorage(siswaData) {
      if (!siswaData || !siswaData.nis) return;
      let saved = [];
      try {
        saved = JSON.parse(localStorage.getItem('sirani_saved_students') || '[]');
      } catch (e) { saved = []; }

      saved = saved.filter(function(s) { return s.nis !== siswaData.nis; });
      saved.unshift(siswaData);
      if (saved.length > 5) saved = saved.slice(0, 5);
      localStorage.setItem('sirani_saved_students', JSON.stringify(saved));
    }

    function renderSavedStudents() {
      const container = document.getElementById('savedStudentsContainer');
      if (!container) return;

      let saved = [];
      try {
        saved = JSON.parse(localStorage.getItem('sirani_saved_students') || '[]');
      } catch (e) { saved = []; }

      if (saved.length === 0) {
        container.style.display = 'none';
        return;
      }

      let html = '<div style="background:var(--bg-subtle); border:1px solid var(--border); border-radius:var(--r-md); padding:12px 14px; margin-bottom:18px;">';
      html += '<div style="font-size:11.5px; font-weight:800; color:var(--text); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px; display:flex; justify-content:space-between; align-items:center;">';
      html += '<span><i class="bi bi-bookmark-check-fill" style="color:var(--text); margin-right:4px;"></i> Profil Anak Tersimpan di HP Ini</span>';
      html += '<button type="button" onclick="clearSavedStudents()" style="background:none; border:none; color:var(--text-3); font-size:11px; font-weight:700; cursor:pointer;"><i class="bi bi-trash"></i> Hapus</button>';
      html += '</div>';

      html += '<div style="display:flex; flex-direction:column; gap:6px;">';
      saved.forEach(function(s) {
        html += '<div style="background:var(--bg-card); border:1px solid var(--border); border-radius:var(--r-sm); padding:8px 12px; display:flex; justify-content:space-between; align-items:center; gap:8px;">';
        html += '<div style="display:flex; align-items:center; gap:10px; min-width:0;">';
        html += '<div style="width:32px; height:32px; border-radius:50%; background:var(--bg-subtle); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:13px; color:var(--text); flex-shrink:0; overflow:hidden;">';
        if (s.foto) {
          html += '<img src="' + s.foto + '" style="width:100%; height:100%; object-fit:cover;">';
        } else {
          html += s.nama.charAt(0).toUpperCase();
        }
        html += '</div>';
        html += '<div style="min-width:0;">';
        html += '<strong style="color:var(--text); font-size:12.5px; display:block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">' + s.nama + '</strong>';
        html += '<span style="font-size:11px; color:var(--text-3); font-family:var(--font-mono);">NIS: ' + s.nis + (s.rombel ? ' · ' + s.rombel : '') + '</span>';
        html += '</div>';
        html += '</div>';
        html += '<a href="/presensi-siswa/' + s.nis + '" class="btn-search" style="padding:4px 12px; height:30px; font-size:11.5px; text-decoration:none; flex-shrink:0;">Buka →</a>';
        html += '</div>';
      });
      html += '</div></div>';

      container.innerHTML = html;
      container.style.display = 'block';
    }

    function clearSavedStudents() {
      if (confirm('Hapus daftar profil anak yang tersimpan di perangkat ini?')) {
        localStorage.removeItem('sirani_saved_students');
        renderSavedStudents();
      }
    }

    // QR Code Scanner
    let html5QrCode = null;
    function startQrScanner() {
      const modal = document.getElementById('qrModal');
      if (!modal) return;
      modal.style.display = 'flex';

      if (!window.Html5Qrcode) {
        const script = document.createElement('script');
        script.src = "https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js";
        script.onload = function() { initQrCode(); };
        document.body.appendChild(script);
      } else {
        initQrCode();
      }
    }

    function initQrCode() {
      if (html5QrCode) {
        html5QrCode.stop().then(function() { startScanning(); }).catch(function() { startScanning(); });
      } else {
        startScanning();
      }
    }

    function startScanning() {
      html5QrCode = new Html5Qrcode("qr-reader");
      html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 220, height: 220 } },
        function(decodedText) {
          html5QrCode.stop().then(function() {
            closeQrScanner();
            let target = decodedText.trim();
            if (target.includes('/presensi-siswa/')) {
              window.location.href = target;
            } else {
              const cleanNis = target.replace(/[^0-9]/g, '');
              window.location.href = '/presensi-siswa/' + (cleanNis || target);
            }
          });
        },
        function(errorMessage) {}
      ).catch(function(err) {
        alert("Tidak dapat mengakses kamera: " + err);
        closeQrScanner();
      });
    }

    function closeQrScanner() {
      const modal = document.getElementById('qrModal');
      if (modal) modal.style.display = 'none';
      if (html5QrCode) {
        try { html5QrCode.stop(); } catch(e) {}
      }
    }

    // Service Worker Registration for PWA
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js').then(function(reg) {
          console.log('SIRANI PWA ServiceWorker ready:', reg.scope);
        }).catch(function(err) {
          console.log('SIRANI PWA ServiceWorker error:', err);
        });
      });
    }

    let deferredPrompt = null;
    window.addEventListener('beforeinstallprompt', function(e) {
      e.preventDefault();
      deferredPrompt = e;
      const btn = document.getElementById('btnPwaInstall');
      if (btn) btn.style.display = 'inline-flex';
    });

    function triggerPwaInstall() {
      if (deferredPrompt) {
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then(function(choiceResult) {
          if (choiceResult.outcome === 'accepted') {
            const btn = document.getElementById('btnPwaInstall');
            if (btn) btn.style.display = 'none';
          }
          deferredPrompt = null;
        });
      } else {
        const isIos = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        if (isIos) {
          alert('Panduan Instal di iPhone / iPad:\n\n1. Ketuk ikon "Bagikan / Share" (ikon kotak dengan panah ke atas di bilah bawah browser Safari).\n2. Gulir ke bawah dan pilih "Tambahkan ke Layar Utama" (Add to Home Screen).\n3. Ketuk "Tambah" di pojok kanan atas.');
        } else {
          alert('Panduan Instal di Android:\n\n1. Ketuk ikon menu browser (titik 3 di kanan atas).\n2. Pilih "Instal Aplikasi" atau "Tambahkan ke Layar Utama".');
        }
      }
    }

    // Image Zoom Lightbox Controller
    let currentZoomScale = 1;
    function openImageZoom(imgUrl, title) {
      const modal = document.getElementById('imageZoomModal');
      const modalImg = document.getElementById('zoomModalImg');
      const modalTitle = document.getElementById('zoomModalTitle');
      const downloadLink = document.getElementById('zoomDownloadLink');

      if (!modal || !modalImg) return;

      modalImg.src = imgUrl;
      if (modalTitle) modalTitle.innerText = title || 'Pengumuman Sekolah';
      if (downloadLink) downloadLink.href = imgUrl;

      currentZoomScale = 1;
      applyZoomScale();

      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }

    function closeImageZoom() {
      const modal = document.getElementById('imageZoomModal');
      if (modal) modal.style.display = 'none';
      document.body.style.overflow = '';
    }

    function adjustZoom(delta) {
      currentZoomScale = Math.min(Math.max(0.6, currentZoomScale + delta), 3.5);
      applyZoomScale();
    }

    function resetZoom() {
      currentZoomScale = 1;
      applyZoomScale();
    }

    function toggleDoubleZoom(e) {
      e.stopPropagation();
      if (currentZoomScale > 1.2) {
        currentZoomScale = 1;
      } else {
        currentZoomScale = 2;
      }
      applyZoomScale();
    }

    function applyZoomScale() {
      const img = document.getElementById('zoomModalImg');
      const text = document.getElementById('zoomLevelText');
      if (img) {
        img.style.transform = 'scale(' + currentZoomScale + ')';
        img.style.cursor = currentZoomScale > 1.2 ? 'zoom-out' : 'zoom-in';
      }
      if (text) {
        text.innerText = Math.round(currentZoomScale * 100) + '%';
      }
    }

    function handleModalBackdropClick(e) {
      if (e.target.id === 'imageZoomModal' || e.target.id === 'zoomViewport') {
        closeImageZoom();
      }
    }

    window.openImageZoom = openImageZoom;
    window.closeImageZoom = closeImageZoom;
    window.adjustZoom = adjustZoom;
    window.resetZoom = resetZoom;
    window.toggleDoubleZoom = toggleDoubleZoom;
    window.handleModalBackdropClick = handleModalBackdropClick;

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeImageZoom();
        closeQrScanner();
      }
    });
  </script>

  {{-- LIGHTBOX IMAGE ZOOM MODAL --}}
  <div id="imageZoomModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.94); z-index:99999; flex-direction:column; justify-content:space-between; user-select:none; backdrop-filter:blur(6px);" onclick="handleModalBackdropClick(event)">
    {{-- Top Controls Bar --}}
    <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 16px; background:rgba(15,23,42,0.85); border-bottom:1px solid rgba(255,255,255,0.12); color:#fff; z-index:10;">
      <div style="font-weight:800; font-size:13px; max-width:55%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" id="zoomModalTitle">
        Pengumuman Sekolah
      </div>
      <div style="display:flex; align-items:center; gap:6px;">
        <button type="button" onclick="adjustZoom(-0.3)" style="background:rgba(255,255,255,0.15); border:none; color:#fff; padding:6px 10px; border-radius:6px; font-size:13px; cursor:pointer;" title="Perkecil">
          <i class="bi bi-zoom-out"></i>
        </button>
        <button type="button" onclick="resetZoom()" style="background:rgba(255,255,255,0.15); border:none; color:#fff; padding:6px 8px; border-radius:6px; font-size:11px; font-weight:800; cursor:pointer; min-width:44px;" title="Reset Ukuran" id="zoomLevelText">
          100%
        </button>
        <button type="button" onclick="adjustZoom(0.3)" style="background:rgba(255,255,255,0.15); border:none; color:#fff; padding:6px 10px; border-radius:6px; font-size:13px; cursor:pointer;" title="Perbesar">
          <i class="bi bi-zoom-in"></i>
        </button>
        <a id="zoomDownloadLink" href="#" target="_blank" style="background:rgba(255,255,255,0.15); border:none; color:#fff; padding:6px 10px; border-radius:6px; font-size:13px; text-decoration:none; display:inline-flex; align-items:center;" title="Buka Gambar Asli">
          <i class="bi bi-box-arrow-up-right"></i>
        </a>
        <button type="button" onclick="closeImageZoom()" style="background:rgba(220,38,38,0.85); border:none; color:#fff; padding:6px 10px; border-radius:6px; font-size:13px; cursor:pointer; margin-left:4px;" title="Tutup">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
    </div>

    {{-- Image Display Area with Pan & Zoom --}}
    <div style="flex:1; display:flex; align-items:center; justify-content:center; overflow:auto; position:relative; padding:12px; cursor:grab;" id="zoomViewport">
      <img id="zoomModalImg" src="" alt="Pengumuman" style="max-width:96vw; max-height:82vh; object-fit:contain; transition:transform 0.15s ease-out; transform-origin:center center; box-shadow:0 10px 30px rgba(0,0,0,0.5); border-radius:6px;" onclick="toggleDoubleZoom(event)" />
    </div>

    {{-- Bottom Hint Bar --}}
    <div style="padding:10px 16px; background:rgba(15,23,42,0.85); text-align:center; color:rgba(255,255,255,0.75); font-size:11.5px; border-top:1px solid rgba(255,255,255,0.12);">
      <i class="bi bi-info-circle"></i> <strong>Ketuk 2x</strong> pada gambar untuk Zoom · Gunakan tombol <i class="bi bi-zoom-in"></i> / <i class="bi bi-zoom-out"></i> untuk mengatur skala
    </div>
  </div>

  {{-- QR SCANNER MODAL --}}
  <div id="qrModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:9999; align-items:center; justify-content:center; padding:16px;">
    <div style="background:var(--bg-card); border-radius:var(--r-lg); max-width:380px; width:100%; padding:20px; text-align:center; position:relative; box-shadow:var(--shadow-lg);">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
        <strong style="color:var(--text); font-size:14px;"><i class="bi bi-qr-code-scan"></i> Scan QR Kartu Pelajar</strong>
        <button type="button" onclick="closeQrScanner()" style="background:none; border:none; font-size:18px; color:var(--text-3); cursor:pointer;"><i class="bi bi-x-lg"></i></button>
      </div>
      <div id="qr-reader" style="width:100%; border-radius:8px; overflow:hidden;"></div>
      <div style="font-size:11.5px; color:var(--text-3); margin-top:12px;">Arahkan kamera ke QR Code pada kartu pelajar / rapor ananda.</div>
    </div>
  </div>

</body>
</html>
