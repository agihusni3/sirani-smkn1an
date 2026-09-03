<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>{{ $siswa ? ($modeAkses === 'siswa' ? 'Kartu & Presensi Siswa '.$siswa->nama.' — SIRANI' : 'Rekap Kehadiran '.$siswa->nama.' — Portal Wali Murid') : 'Portal Kehadiran Siswa & Orang Tua — SMKN 1 Air Naningan' }}</title>
  
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
  
  {{-- QR Code Generator & Canvas Exporter --}}
  <script src="/qrcode.min.js"></script>
  <script src="/html2canvas.min.js"></script>

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

    /* ─── Portal Top 3 Tabs Navigation ─── */
    .portal-main-tabs {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 8px;
      margin-bottom: 16px;
    }
    .portal-main-tab {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 7px;
      padding: 10px 14px;
      font-size: 12px;
      font-weight: 800;
      border-radius: 12px;
      background: var(--bg-card);
      border: 1px solid var(--border);
      color: var(--text-2);
      cursor: pointer;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      font-family: var(--font-main);
      box-shadow: 0 1px 3px rgba(0,0,0,0.04);
      white-space: nowrap;
    }
    .portal-main-tab i {
      font-size: 13px;
    }
    .portal-main-tab.active {
      background: #0f172a;
      color: #ffffff;
      border-color: #0f172a;
      box-shadow: 0 4px 12px rgba(15,23,42,0.25);
    }
    .portal-main-tab:hover:not(.active) {
      background: var(--bg-subtle);
      color: var(--text);
      border-color: var(--border-2);
    }

    /* ─── Card Siswa Emerald (SMKN 1 AN Theme) ─── */
    .card-siswa-emerald {
      background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%);
      border-radius: 18px;
      padding: 16px 18px;
      color: #ffffff;
      box-shadow: 0 8px 24px rgba(6,78,59,0.25);
      position: relative;
      overflow: hidden;
    }
    .card-siswa-emerald-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 14px;
    }
    .card-siswa-emerald-logo {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .card-siswa-emerald-logo img, .card-siswa-emerald-logo .logo-icon-em {
      width: 24px;
      height: 24px;
      border-radius: 50%;
      object-fit: cover;
    }
    .card-siswa-emerald-logo span {
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 0.6px;
      color: #ffffff;
      text-transform: uppercase;
    }
    .card-siswa-emerald-badge {
      background: rgba(255,255,255,0.2);
      border: 1px solid rgba(255,255,255,0.3);
      backdrop-filter: blur(4px);
      color: #ffffff;
      font-size: 9.5px;
      font-weight: 800;
      padding: 2.5px 9px;
      border-radius: 20px;
      letter-spacing: 0.5px;
    }
    .card-siswa-emerald-body {
      display: flex;
      align-items: center;
      gap: 14px;
    }
    .card-siswa-emerald-avatar {
      width: 56px;
      height: 56px;
      border-radius: 14px;
      background: linear-gradient(135deg, #b45309, #d97706);
      border: 2px solid rgba(255,255,255,0.4);
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      font-weight: 900;
      color: #ffffff;
      flex-shrink: 0;
      overflow: hidden;
    }
    .card-siswa-emerald-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .card-siswa-emerald-info {
      flex: 1;
      min-width: 0;
    }
    .card-siswa-emerald-name {
      font-size: 16px;
      font-weight: 900;
      color: #ffffff;
      line-height: 1.25;
      margin-bottom: 2px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .card-siswa-emerald-nisn {
      font-size: 12px;
      font-weight: 700;
      color: rgba(255,255,255,0.85);
      margin-bottom: 2px;
      font-family: var(--font-mono);
      letter-spacing: 0.3px;
    }
    .card-siswa-emerald-kelas {
      font-size: 11.5px;
      font-weight: 600;
      color: rgba(255,255,255,0.85);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    /* ─── Card Scanner Gerbang & Kiosk (Putih) ─── */
    .card-scanner-kiosk {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 18px;
      padding: 22px 20px 20px;
      margin-top: 14px;
      text-align: center;
      box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    }
    .card-scanner-kiosk-title {
      font-size: 12px;
      font-weight: 900;
      color: #334155;
      letter-spacing: 1.2px;
      text-transform: uppercase;
      margin-bottom: 16px;
    }
    .qr-interactive-box {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 16px;
      padding: 14px 14px 10px;
      display: inline-block;
      cursor: pointer;
      box-shadow: 0 4px 14px rgba(0,0,0,0.05);
      transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s;
    }
    .qr-interactive-box:hover {
      transform: scale(1.02);
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    }
    .qr-code-canvas-wrap {
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto;
    }
    .qr-code-number-label {
      font-size: 15px;
      font-weight: 900;
      font-family: var(--font-mono);
      color: #0f172a;
      letter-spacing: 2px;
      margin-top: 8px;
      text-align: center;
    }
    .qr-tap-instruction {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      color: #64748b;
      font-size: 11px;
      font-weight: 700;
      margin-top: 12px;
      margin-bottom: 18px;
      cursor: pointer;
    }
    .btn-qr-action-download {
      width: 100%;
      padding: 12px 16px;
      background: #f8fafc;
      border: 1px solid #cbd5e1;
      border-radius: 12px;
      font-size: 12.5px;
      font-weight: 800;
      color: #334155;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      cursor: pointer;
      font-family: var(--font-main);
      transition: all 0.2s;
    }
    .btn-qr-action-download:hover {
      background: #f1f5f9;
      border-color: #94a3b8;
      color: #0f172a;
    }
    .btn-qr-action-wa {
      width: 100%;
      padding: 12px 16px;
      background: #16a34a;
      border: none;
      border-radius: 12px;
      font-size: 12.5px;
      font-weight: 800;
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      text-decoration: none;
      font-family: var(--font-main);
      margin-top: 10px;
      box-shadow: 0 4px 14px rgba(22,163,74,0.3);
      transition: all 0.2s;
    }
    .btn-qr-action-wa:hover {
      background: #15803d;
      transform: translateY(-1px);
    }

    /* ─── Fullscreen Auto-Zoom & Pure White Max Brightness Overlay ─── */
    #qrZoomOverlay {
      display: none;
      position: fixed;
      inset: 0;
      z-index: 999999;
      background: #ffffff;
      flex-direction: column;
      align-items: center;
      justify-content: space-between;
      padding: 30px 20px 24px;
      user-select: none;
      cursor: pointer;
      animation: zoomFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes zoomFadeIn {
      from { opacity: 0; transform: scale(0.94); }
      to { opacity: 1; transform: scale(1); }
    }
    .zoom-overlay-header {
      text-align: center;
      width: 100%;
    }
    .zoom-overlay-body {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      flex: 1;
    }
    .zoom-overlay-footer {
      text-align: center;
      width: 100%;
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
      .portal-nav-btn-group.has-barcode {
        grid-template-columns: repeat(2, 1fr) !important;
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
    .portal-nav-btn-group.has-barcode {
      grid-template-columns: repeat(4, 1fr);
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
      background: rgba(0,0,0,0.06);
      color: var(--text);
    }
    .period-btn.active {
      background: #000000 !important;
      color: #FFFFFF !important;
      font-weight: 800;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
    }
    .period-btn.active i,
    .period-btn.active span {
      color: #FFFFFF !important;
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
          <h1>SIRANI · Portal Presensi Siswa &amp; Orang Tua</h1>
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
          <i class="bi bi-shield-check"></i> Layanan Presensi Terpadu Siswa &amp; Orang Tua
        </span>
        <div id="savedStudentsContainer" style="display:none;"></div>

        <p class="search-desc">
          Selamat datang di portal presensi mandiri SMKN 1 Air Naningan. Masukkan <strong>NISN</strong> atau <strong>Nomor WhatsApp Orang Tua</strong> untuk memantau catatan absensi, riwayat kehadiran, dan kedisiplinan secara real-time.
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
                placeholder="Ketik NISN atau No. WhatsApp..."
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
              <i class="bi bi-qr-code-scan"></i>
            </div>
            <div class="portal-feature-text">
              <strong>Smart Gate RFID &amp; QR</strong>
              <span>Presensi otomatis via kartu RFID &amp; QR Code di gerbang sekolah.</span>
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
      {{-- HASIL DATA SISWA TERPILIH --}}

      {{-- 3 TAB NAVIGASI UTAMA ATAS SESUAI DESAIN --}}
      <div class="portal-main-tabs">
        <button type="button" id="btnTabKartuQr" onclick="switchPortalMainTab('kartu-qr')" class="portal-main-tab active">
          <i class="bi bi-qr-code"></i> <span>Kartu &amp; QR Code</span>
        </button>
        <button type="button" id="btnTabAbsen" onclick="switchPortalMainTab('absen')" class="portal-main-tab">
          <i class="bi bi-calendar3"></i> <span>Riwayat Absensi</span>
        </button>
        <button type="button" id="btnTabPengumuman" onclick="switchPortalMainTab('pengumuman')" class="portal-main-tab">
          <i class="bi bi-megaphone-fill"></i> <span>Pengumuman</span>
          @if(isset($pengumumans) && $pengumumans->count() > 0)
            <span class="portal-badge-count">{{ $pengumumans->count() }}</span>
          @endif
        </button>
      </div>

      {{-- TAB 1: KARTU & QR CODE SCANNER (EMERALD CARD + SCANNER KIOSK) --}}
      <div id="section-kartu-qr" style="display: block;">
        {{-- AREA KARTU DIGITAL SISWA UTUH (DITAMPILKAN & DIUNDUH SECARA IDENTIK) --}}
        <div id="kartuSiswaDigitalArea" style="padding:12px; border-radius:24px; background:var(--bg-2, #0f172a); margin-bottom:14px; transition:background-color .25s ease;">
          {{-- CARD SISWA HIJAU EMERALD SMKN 1 AIR NANINGAN --}}
          <div class="card-siswa-emerald">
            <div class="card-siswa-emerald-header">
              <div class="card-siswa-emerald-logo">
                <div style="width:24px; height:24px; background:rgba(255,255,255,0.25); border-radius:50%; display:flex; align-items:center; justify-content:center; border:1px solid rgba(255,255,255,0.4);">
                  <i class="bi bi-mortarboard-fill" style="font-size:12px; color:#fff;"></i>
                </div>
                <span>SMK NEGERI 1 AIR NANINGAN</span>
              </div>
              <div class="card-siswa-emerald-badge">SISWA</div>
            </div>
            <div class="card-siswa-emerald-body">
              <div class="card-siswa-emerald-avatar">
                @if($siswa->foto && file_exists(public_path('storage/'.$siswa->foto)))
                  <img src="{{ asset('storage/'.$siswa->foto) }}" alt="{{ $siswa->nama }}" />
                @else
                  {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                @endif
              </div>
              <div class="card-siswa-emerald-info">
                <div class="card-siswa-emerald-name">{{ $siswa->nama }}</div>
                <div class="card-siswa-emerald-nisn">NISN: {{ $siswa->nisn ?: $siswa->nis }}</div>
                <div class="card-siswa-emerald-kelas">{{ $rombel->nama_rombel ?? 'X' }} - {{ $rombel->jurusan->nama_jurusan ?? 'Semua Jurusan' }}</div>
              </div>
            </div>
          </div>

          {{-- CARD SCANNER GERBANG & KIOSK (PUTIH) --}}
          <div class="card-scanner-kiosk" style="margin-bottom:0;">
            <div class="card-scanner-kiosk-title">SCANNER GERBANG &amp; KIOSK</div>

            {{-- KOTAK QR INTERAKTIF --}}
            <div class="qr-interactive-box" onclick="toggleQrFullscreenZoom(true)" title="Sentuh untuk zoom &amp; maksimalkan kecerahan">
              <div id="scannerQrContainer" class="qr-code-canvas-wrap" style="min-width:190px; min-height:190px; display:flex; align-items:center; justify-content:center;"></div>

              <div class="qr-code-number-label">{{ $codeValue }}</div>
            </div>

            {{-- PETUNJUK TAP TO ZOOM --}}
            <div class="qr-tap-instruction" onclick="toggleQrFullscreenZoom(true)">
              <i class="bi bi-arrows-fullscreen"></i>
              <span>Sentuh gambar QR untuk memperbesar &amp; maksimalkan kecerahan</span>
            </div>
          </div>
        </div>

        {{-- TOMBOL AKSI KARTU --}}
        <div style="display:flex; flex-direction:column; gap:10px;">
          {{-- TOMBOL 1: SIMPAN GAMBAR KARTU IDENTIK --}}
          <button type="button" id="btnDownloadSiswaCard" onclick="downloadSiswaFullCard()" class="btn-qr-action-download">
            <i class="bi bi-download"></i> Simpan Gambar Kartu ke Galeri HP
          </button>

          {{-- TOMBOL 2: KIRIM VIA WHATSAPP --}}
          @php
            $pesanWaGateway = rawurlencode("Halo, ini Kartu Presensi Siswa {$siswa->nama} (NISN: " . ($siswa->nisn ?: $siswa->nis) . ") SMKN 1 Air Naningan. Dapat digunakan untuk scan presensi di gerbang & kiosk sekolah.");
          @endphp
          <a href="https://wa.me/?text={{ $pesanWaGateway }}" target="_blank" class="btn-qr-action-wa">
            <i class="bi bi-whatsapp"></i> Kirim via WhatsApp Gateway
          </a>
        </div>
      </div>


      {{-- TAB 2: RIWAYAT ABSENSI & DOSSIER SISWA --}}
      <div id="section-absen-wrap" style="display: none;">
        {{-- DOSSIER DIGITAL DETAIL --}}
        <div class="dossier-card">
          <div class="dossier-header">
            <span class="dossier-header-title" style="font-size:11.5px; font-weight:800; color:var(--text); text-transform:uppercase; letter-spacing:0.5px;">
              Profil Lengkap &amp; Kehadiran Hari Ini
            </span>
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
                {{ $todayAbsensi ? ($todayAbsensi->keterangan ?: 'Tervalidasi via Smart Gate RFID/QR SMKN 1 AN') : ($siswa->status === 'pkl' ? 'Siswa sedang melaksanakan PKL di Industri' : 'Belum ada rekaman presensi di gerbang hari ini') }}
              </div>
            </div>
          </div>
        </div>

        {{-- RIWAYAT KEHADIRAN TERPADU --}}
        <div id="riwayat-kehadiran" style="scroll-margin-top: 70px;">
        
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

    // ===== MAIN TABS SWITCHER (KARTU & QR, RIWAYAT ABSENSI, PENGUMUMAN) =====
    function switchPortalMainTab(tabName) {
      const secKartuQr = document.getElementById('section-kartu-qr');
      const secAbsenWrap = document.getElementById('section-absen-wrap');
      const secPengumuman = document.getElementById('section-pengumuman');
      
      const btnKartuQr = document.getElementById('btnTabKartuQr');
      const btnAbsen = document.getElementById('btnTabAbsen');
      const btnPengumuman = document.getElementById('btnTabPengumuman');

      if (tabName === 'kartu-qr') {
        if (secKartuQr) secKartuQr.style.display = 'block';
        if (secAbsenWrap) secAbsenWrap.style.display = 'none';
        if (secPengumuman) secPengumuman.style.display = 'none';

        if (btnKartuQr) btnKartuQr.classList.add('active');
        if (btnAbsen) btnAbsen.classList.remove('active');
        if (btnPengumuman) btnPengumuman.classList.remove('active');
        if (typeof renderPortalQrCode === 'function') {
          setTimeout(renderPortalQrCode, 50);
        }
      } else if (tabName === 'absen') {
        if (secKartuQr) secKartuQr.style.display = 'none';
        if (secAbsenWrap) secAbsenWrap.style.display = 'block';
        if (secPengumuman) secPengumuman.style.display = 'none';

        if (btnKartuQr) btnKartuQr.classList.remove('active');
        if (btnAbsen) btnAbsen.classList.add('active');
        if (btnPengumuman) btnPengumuman.classList.remove('active');
      } else if (tabName === 'pengumuman') {
        if (secKartuQr) secKartuQr.style.display = 'none';
        if (secAbsenWrap) secAbsenWrap.style.display = 'none';
        if (secPengumuman) secPengumuman.style.display = 'block';

        if (btnKartuQr) btnKartuQr.classList.remove('active');
        if (btnAbsen) btnAbsen.classList.remove('active');
        if (btnPengumuman) btnPengumuman.classList.add('active');
      }
    }
    window.switchPortalMainTab = switchPortalMainTab;

    @if($siswa)
    // ===== QR CODE & BARCODE SCANNER KIOSK & AUTO-ZOOM MAX BRIGHTNESS =====
    const KARTU_CODE_VALUE = @json($codeValue);
    let screenWakeLock = null;

    function renderPortalQrCode() {
      const codeVal = KARTU_CODE_VALUE || '{{ $siswa->nisn ?? $siswa->nis }}';
      if (!codeVal) return;

      // 1. Render Normal Scanner QR Code (2D)
      const containerScanner = document.getElementById('scannerQrContainer');
      if (containerScanner) {
        containerScanner.innerHTML = '';
        if (typeof QRCode !== 'undefined') {
          try {
            new QRCode(containerScanner, {
              text: codeVal,
              width: 190,
              height: 190,
              colorDark: '#000000',
              colorLight: '#ffffff',
              correctLevel: (typeof QRCode.CorrectLevel !== 'undefined') ? QRCode.CorrectLevel.M : 0
            });
          } catch(e) {
            console.warn('QR render error:', e);
          }
        }
      }

      // 2. Render Fullscreen Zoomed QR Code (2D)
      const containerZoomed = document.getElementById('zoomedQrContainer');
      if (containerZoomed) {
        containerZoomed.innerHTML = '';
        if (typeof QRCode !== 'undefined') {
          try {
            new QRCode(containerZoomed, {
              text: codeVal,
              width: 240,
              height: 240,
              colorDark: '#000000',
              colorLight: '#ffffff',
              correctLevel: (typeof QRCode.CorrectLevel !== 'undefined') ? QRCode.CorrectLevel.H : 0
            });
          } catch(e) {
            console.warn('QR Zoom render error:', e);
          }
        }
      }
    }

    // AUTO-ZOOM & MAX BRIGHTNESS TOGGLE
    async function toggleQrFullscreenZoom(isOpen) {
      const overlay = document.getElementById('qrZoomOverlay');
      if (!overlay) return;

      if (isOpen) {
        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        renderPortalQrCode();

        // Aktifkan Screen WakeLock API agar layar HP tetap menyala terang (tidak mati/redup saat antre scan)
        try {
          if ('wakeLock' in navigator && navigator.wakeLock) {
            screenWakeLock = await navigator.wakeLock.request('screen');
          }
        } catch(err) {
          console.log('WakeLock not supported or denied', err);
        }
      } else {
        overlay.style.display = 'none';
        document.body.style.overflow = '';

        // Lepaskan WakeLock (kecerahan kembali normal)
        if (screenWakeLock !== null) {
          try {
            await screenWakeLock.release();
            screenWakeLock = null;
          } catch(err) {}
        }
      }
    }
    window.toggleQrFullscreenZoom = toggleQrFullscreenZoom;

    // SIMPAN GAMBAR KARTU SISWA UTUH KE GALERI HP
    async function downloadSiswaFullCard() {
      const cardElement = document.getElementById('kartuSiswaDigitalArea');
      if (!cardElement) return;

      const btn = document.getElementById('btnDownloadSiswaCard');
      const origHtml = btn ? btn.innerHTML : '';
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Merender Gambar Kartu...';
      }

      try {
        await new Promise(r => setTimeout(r, 80));

        const isDark = document.documentElement.classList.contains('dark') || document.body.classList.contains('dark');
        const bgColor = isDark ? '#0f172a' : '#ffffff';

        const canvas = await html2canvas(cardElement, {
          scale: 3,
          useCORS: true,
          allowTaint: true,
          backgroundColor: bgColor,
          logging: false,
          scrollX: 0,
          scrollY: -window.scrollY
        });

        const safeFilename = 'KARTU_PRESENSI_{{ Str::slug($siswa->nama) }}_{{ $codeValue }}.png';

        if (navigator.canShare && window.File) {
          try {
            canvas.toBlob(async (blob) => {
              if (blob) {
                const file = new File([blob], safeFilename, { type: 'image/png' });
                if (navigator.canShare({ files: [file] })) {
                  await navigator.share({
                    files: [file],
                    title: 'Kartu Presensi Siswa',
                    text: 'Kartu Presensi Digital SMKN 1 Air Naningan - {{ $siswa->nama }}'
                  });
                  return;
                }
              }
              triggerSiswaDownload(canvas, safeFilename);
            }, 'image/png');
            return;
          } catch(e) {
            triggerSiswaDownload(canvas, safeFilename);
            return;
          }
        }

        triggerSiswaDownload(canvas, safeFilename);
      } catch (err) {
        console.error('Gagal render kartu siswa:', err);
        // Fallback
        const containerScanner = document.getElementById('scannerQrContainer');
        const qrCanvasOrImg = containerScanner?.querySelector('canvas') || containerScanner?.querySelector('img');
        if (qrCanvasOrImg) {
          const link = document.createElement('a');
          link.download = 'QR_Presensi_{{ Str::slug($siswa->nama) }}_{{ $codeValue }}.png';
          link.href = (qrCanvasOrImg.tagName === 'CANVAS') ? qrCanvasOrImg.toDataURL('image/png') : qrCanvasOrImg.src;
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        } else {
          alert('Gagal mengunduh kartu. Silakan muat ulang halaman.');
        }
      } finally {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = origHtml;
        }
      }
    }

    function triggerSiswaDownload(canvas, filename) {
      const link = document.createElement('a');
      link.download = filename;
      link.href = canvas.toDataURL('image/png');
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }

    window.downloadSiswaFullCard = downloadSiswaFullCard;
    window.downloadQrOnly = downloadSiswaFullCard;

    window.renderPortalQrCode = renderPortalQrCode;

    function openKartuDigital() {
      const modal = document.getElementById('kartuDigitalModal');
      if (!modal) return;
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
      if (!kartuQrRendered) {
        renderKartuQr();
        kartuQrRendered = true;
      }
      // Animasi masuk
      const inner = modal.querySelector('[onclick="event.stopPropagation()"]');
      if (inner) {
        inner.style.opacity = '0';
        inner.style.transform = 'translateY(20px) scale(0.96)';
        requestAnimationFrame(() => {
          inner.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
          inner.style.opacity = '1';
          inner.style.transform = 'translateY(0) scale(1)';
        });
      }
    }

    function closeKartuDigital(e) {
      if (e && e.target && !e.target.closest('[onclick="event.stopPropagation()"]') === false) return;
      const modal = document.getElementById('kartuDigitalModal');
      if (!modal) return;
      modal.style.display = 'none';
      document.body.style.overflow = '';
    }

    let kartuQrRendered = false;
    function renderKartuQr() {
      const codeVal = KARTU_CODE_VALUE || '{{ $siswa->nisn ?? $siswa->nis }}';
      if (!codeVal) return;

      // Render QR Code di Modal Kartu Digital
      const qrContainer = document.getElementById('kartuQrContainer');
      if (qrContainer && typeof QRCode !== 'undefined') {
        try {
          qrContainer.innerHTML = '';
          new QRCode(qrContainer, {
            text: codeVal,
            width: 120,
            height: 120,
            colorDark: '#0f172a',
            colorLight: '#ffffff',
            correctLevel: (typeof QRCode.CorrectLevel !== 'undefined') ? QRCode.CorrectLevel.M : 0
          });
        } catch(e) {
          console.warn('QR render error:', e);
        }
      }
    }

    async function downloadKartuDigital() {
      const card = document.getElementById('kartuDigitalCard');
      if (!card) return;
      try {
        if (typeof html2canvas !== 'undefined') {
          const canvas = await html2canvas(card, { scale: 3, useCORS: true, backgroundColor: null });
          const link = document.createElement('a');
          link.download = 'kartu-pelajar-{{ Str::slug($siswa->nama) }}.png';
          link.href = canvas.toDataURL('image/png');
          link.click();
        } else {
          const printContent = card.outerHTML;
          const printWin = window.open('', '_blank', 'width=400,height=600');
          printWin.document.write(`<html><head><title>Kartu Pelajar Digital</title><style>body{margin:0;padding:16px;background:#1e293b;display:flex;justify-content:center;align-items:center;min-height:100vh;font-family:system-ui,sans-serif;}</style></head><body>${printContent}</body></html>`);
          printWin.document.close();
          printWin.focus();
          setTimeout(() => { printWin.print(); printWin.close(); }, 500);
        }
      } catch(e) {
        alert('Fitur unduh memerlukan koneksi internet untuk library tambahan. Coba gunakan Screenshot layar.');
      }
    }

    async function shareKartuDigital() {
      const card = document.getElementById('kartuDigitalCard');
      if (!card) return;
      try {
        if (navigator.share) {
          const shareUrl = window.location.href;
          await navigator.share({
            title: 'Kartu Pelajar Digital - {{ $siswa->nama }}',
            text: 'Lihat kartu pelajar digital {{ $siswa->nama }} ({{ $siswa->nis }}) di portal SIRANI SMKN 1 Air Naningan.',
            url: shareUrl
          });
        } else {
          const url = window.location.href;
          navigator.clipboard.writeText(url).then(() => {
            alert('Link portal berhasil disalin ke clipboard!');
          });
        }
      } catch(e) {
        console.warn('Share error:', e);
      }
    }

    window.openKartuDigital = openKartuDigital;
    window.closeKartuDigital = closeKartuDigital;
    window.downloadKartuDigital = downloadKartuDigital;
    window.shareKartuDigital = shareKartuDigital;
    @endif

    document.addEventListener('DOMContentLoaded', function() {
      @if($siswa)
        // Render QR Code & Barcode pada kartu scanner gerbang & kiosk
        if (typeof renderPortalQrCode === 'function') {
          renderPortalQrCode();
        }

        // Cek URL params / hash untuk navigasi tab
        const urlParams = new URLSearchParams(window.location.search);
        const hash = window.location.hash;

        if (hash === '#riwayat-kehadiran' || hash === '#absen' || urlParams.has('periode') || urlParams.has('tanggal') || urlParams.has('bulan') || urlParams.has('tahun')) {
          switchPortalMainTab('absen');
        } else if (hash === '#section-pengumuman' || hash === '#pengumuman') {
          switchPortalMainTab('pengumuman');
        } else {
          switchPortalMainTab('kartu-qr');
        }

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

    window.addEventListener('load', function() {
      @if($siswa)
        if (typeof renderPortalQrCode === 'function') {
          renderPortalQrCode();
        }
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

  @if($siswa)
  {{-- KARTU DIGITAL SISWA MODAL — REDESIGN PREMIUM --}}
  <div id="kartuDigitalModal" style="display:none; position:fixed; inset:0; z-index:99998; align-items:center; justify-content:center; padding:20px; background:rgba(2,8,23,0.92); backdrop-filter:blur(20px) saturate(1.5);" onclick="closeKartuDigital(event)">
    <div style="width:100%; max-width:400px; position:relative;" onclick="event.stopPropagation()">

      {{-- Close --}}
      <button onclick="closeKartuDigital()" style="position:absolute; top:-18px; right:-10px; z-index:10; background:rgba(255,255,255,0.1); backdrop-filter:blur(10px); border:1px solid rgba(255,255,255,0.2); width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:14px; color:#fff; cursor:pointer; transition:all .2s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
        <i class="bi bi-x-lg"></i>
      </button>

      {{-- === KARTU UTAMA === --}}
      <div id="kartuDigitalCard" style="border-radius:24px; overflow:hidden; box-shadow:0 32px 80px rgba(0,0,0,0.7), 0 0 0 1px rgba(255,255,255,0.08); font-family:var(--font-main); position:relative;">

        {{-- ===== SISI DEPAN KARTU ===== --}}
        {{-- Background gradient utama --}}
        <div style="background:linear-gradient(135deg, #0a0f1e 0%, #0d1f4a 30%, #1a1060 60%, #2d0a4e 100%); padding:0; position:relative; min-height:220px; overflow:hidden;">

          {{-- Holographic stripe horizontal --}}
          <div style="position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg, #6366f1, #8b5cf6, #ec4899, #f59e0b, #10b981, #6366f1); background-size:200% 100%; animation:holoShift 3s linear infinite;"></div>

          {{-- Grid pattern overlay --}}
          <div style="position:absolute; inset:0; opacity:0.04; background-image:linear-gradient(rgba(255,255,255,0.8) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.8) 1px, transparent 1px); background-size:24px 24px;"></div>

          {{-- Glow orbs dekoratif --}}
          <div style="position:absolute; top:-60px; right:-60px; width:200px; height:200px; border-radius:50%; background:radial-gradient(circle, rgba(139,92,246,0.25) 0%, transparent 70%);"></div>
          <div style="position:absolute; bottom:-80px; left:-40px; width:220px; height:220px; border-radius:50%; background:radial-gradient(circle, rgba(99,102,241,0.2) 0%, transparent 70%);"></div>
          <div style="position:absolute; top:50%; right:30px; width:80px; height:80px; border-radius:50%; background:radial-gradient(circle, rgba(236,72,153,0.15) 0%, transparent 70%);"></div>

          {{-- === ROW 1: Logo + Tahun === --}}
          <div style="padding:18px 20px 0; display:flex; align-items:center; justify-content:space-between; position:relative; z-index:2;">
            <div style="display:flex; align-items:center; gap:10px;">
              {{-- Logo badge --}}
              <div style="width:36px; height:36px; background:linear-gradient(135deg,rgba(139,92,246,0.4),rgba(99,102,241,0.3)); border:1px solid rgba(255,255,255,0.2); border-radius:10px; display:flex; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
                <i class="bi bi-mortarboard-fill" style="color:#c4b5fd; font-size:16px;"></i>
              </div>
              <div>
                <div style="font-size:7.5px; font-weight:800; color:rgba(196,181,253,0.8); letter-spacing:2px; text-transform:uppercase; line-height:1;">KARTU PELAJAR DIGITAL</div>
                <div style="font-size:12px; font-weight:900; color:#fff; line-height:1.3; margin-top:2px;">SMK Negeri 1 Air Naningan</div>
              </div>
            </div>
            {{-- Contactless icon + TA --}}
            <div style="text-align:right;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" style="opacity:0.6; display:block; margin-left:auto; margin-bottom:3px;">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" fill="rgba(139,92,246,0.3)" stroke="rgba(196,181,253,0.6)" stroke-width="1.5"/>
                <path d="M8 12c0-2.21 1.79-4 4-4s4 1.79 4 4" stroke="rgba(196,181,253,0.8)" stroke-width="1.5" stroke-linecap="round" fill="none"/>
                <path d="M6 12c0-3.31 2.69-6 6-6s6 2.69 6 6" stroke="rgba(167,139,250,0.5)" stroke-width="1.5" stroke-linecap="round" fill="none"/>
              </svg>
              <div style="font-size:9px; font-weight:800; color:rgba(196,181,253,0.7); line-height:1;">TA {{ date('Y') }}/{{ date('Y')+1 }}</div>
              <div style="font-size:7px; color:rgba(255,255,255,0.3); margin-top:1px; letter-spacing:0.5px;">SIRANI · v2</div>
            </div>
          </div>

          {{-- === ROW 2: Foto + Info === --}}
          <div style="padding:14px 20px 20px; display:flex; gap:16px; align-items:flex-end; position:relative; z-index:2;">
            {{-- Foto --}}
            <div style="flex-shrink:0; position:relative;">
              {{-- Ring glow --}}
              <div style="position:absolute; inset:-3px; border-radius:16px; background:linear-gradient(135deg, #6366f1, #8b5cf6, #ec4899); padding:2px; opacity:0.8;">
                <div style="width:100%; height:100%; background:#0d1f4a; border-radius:14px;"></div>
              </div>
              @if($siswa->foto)
                <img src="{{ asset('storage/'.$siswa->foto) }}" alt="{{ $siswa->nama }}" style="position:relative; z-index:1; width:76px; height:92px; object-fit:cover; border-radius:14px; display:block;" />
              @else
                <div style="position:relative; z-index:1; width:76px; height:92px; background:linear-gradient(135deg,#312e81,#4c1d95,#6d28d9); border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:32px; font-weight:900; color:rgba(255,255,255,0.9); font-family:var(--font-main);">{{ strtoupper(substr($siswa->nama, 0, 1)) }}</div>
              @endif
            </div>

            {{-- Info --}}
            <div style="flex:1; min-width:0; padding-bottom:4px;">
              <div style="font-size:18px; font-weight:900; color:#fff; line-height:1.15; margin-bottom:10px; letter-spacing:-0.3px;">{{ $siswa->nama }}</div>
              <div style="display:flex; flex-direction:column; gap:4px;">
                <div style="display:flex; align-items:center; gap:0;">
                  <span style="font-size:9px; color:rgba(196,181,253,0.6); font-weight:700; text-transform:uppercase; letter-spacing:0.8px; width:32px;">NIS</span>
                  <span style="font-size:11px; color:#e2e8f0; font-weight:800; font-family:var(--font-mono); letter-spacing:1px;">{{ $siswa->nis ?: '—' }}</span>
                </div>
                <div style="display:flex; align-items:center; gap:0;">
                  <span style="font-size:9px; color:rgba(196,181,253,0.6); font-weight:700; text-transform:uppercase; letter-spacing:0.8px; width:32px;">NISN</span>
                  <span style="font-size:11px; color:#e2e8f0; font-weight:800; font-family:var(--font-mono); letter-spacing:1px;">{{ $siswa->nisn ?: '—' }}</span>
                </div>
                <div style="margin-top:4px; display:flex; gap:5px; flex-wrap:wrap;">
                  <span style="background:rgba(99,102,241,0.25); border:1px solid rgba(99,102,241,0.4); color:#c4b5fd; font-size:9.5px; font-weight:800; padding:3px 9px; border-radius:20px; display:inline-flex; align-items:center; gap:4px; backdrop-filter:blur(4px);">
                    <i class="bi bi-building" style="font-size:8px;"></i> {{ $rombel->nama_rombel ?? '—' }}
                  </span>
                  <span style="background:rgba(236,72,153,0.2); border:1px solid rgba(236,72,153,0.35); color:#f9a8d4; font-size:9px; font-weight:700; padding:3px 9px; border-radius:20px; display:inline-flex; align-items:center; gap:4px; backdrop-filter:blur(4px);">
                    <i class="bi bi-tools" style="font-size:8px;"></i> {{ Str::words($rombel->jurusan->nama_jurusan ?? '—', 3, '…') }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          {{-- Chip EMV --}}
          <div style="position:absolute; bottom:16px; right:20px; z-index:2;">
            <div style="width:32px; height:24px; background:linear-gradient(135deg,#d4a017,#f5d06b,#b8860b); border-radius:5px; position:relative; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.4);">
              <div style="position:absolute; inset:0; background:repeating-linear-gradient(0deg, transparent, transparent 6px, rgba(0,0,0,0.15) 6px, rgba(0,0,0,0.15) 7px);"></div>
              <div style="position:absolute; left:50%; top:0; bottom:0; width:1px; background:rgba(0,0,0,0.2); transform:translateX(-50%);"></div>
            </div>
          </div>
        </div>

        {{-- ===== BAGIAN BAWAH: BARCODE + QR ===== --}}
        <div style="background:#f8fafc; padding:16px 20px 14px;">
          {{-- Garis dekoratif atas --}}
          <div style="height:3px; background:linear-gradient(90deg, #6366f1, #8b5cf6, #ec4899, #f59e0b); border-radius:2px; margin-bottom:14px; opacity:0.6;"></div>

          {{-- QR Code Section - full width --}}
          <div style="text-align:center;">
            <div style="display:flex; align-items:center; justify-content:center; gap:5px; margin-bottom:8px;">
              <i class="bi bi-qr-code" style="font-size:10px; color:#8b5cf6;"></i>
              <span style="font-size:8.5px; font-weight:800; color:#8b5cf6; text-transform:uppercase; letter-spacing:1.2px;">QR Presensi · ID: {{ $codeValue }}</span>
            </div>
            <div style="background:#fff; border-radius:10px; padding:8px; border:1px solid #e2e8f0; box-shadow:0 1px 4px rgba(0,0,0,0.06); display:inline-block;">
              <div id="kartuQrContainer" style="width:120px; height:120px; display:flex; align-items:center; justify-content:center;"></div>
            </div>
            <div style="font-size:8.5px; color:#94a3b8; text-align:center; margin-top:6px; font-family:var(--font-mono); letter-spacing:2px;">{{ $codeValue }}</div>
          </div>

          {{-- Footer --}}
          <div style="margin-top:12px; display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center; gap:5px;">
              <div style="width:5px; height:5px; border-radius:50%; background:linear-gradient(135deg,#6366f1,#ec4899);"></div>
              <span style="font-size:8px; color:#94a3b8; font-weight:700; letter-spacing:0.3px;">SMKN 1 Air Naningan · Tanggamus · Lampung</span>
            </div>
            <span style="font-size:7.5px; color:#cbd5e1; font-family:var(--font-mono); letter-spacing:0.5px;">portal.sirani</span>
          </div>
        </div>
      </div>

      {{-- ===== TOMBOL AKSI ===== --}}
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:14px;">
        <button onclick="downloadKartuDigital()" style="background:#fff; color:#0f172a; border:none; padding:11px 16px; border-radius:14px; font-size:12px; font-weight:800; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:7px; font-family:var(--font-main); box-shadow:0 4px 20px rgba(0,0,0,0.4); transition:all .2s; letter-spacing:0.2px;" onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.5)'" onmouseout="this.style.transform='';this.style.boxShadow='0 4px 20px rgba(0,0,0,0.4)'">
          <i class="bi bi-download" style="font-size:13px;"></i> Unduh Kartu
        </button>
        <button onclick="shareKartuDigital()" style="background:linear-gradient(135deg,rgba(99,102,241,0.2),rgba(139,92,246,0.2)); color:#e2e8f0; border:1px solid rgba(139,92,246,0.4); padding:11px 16px; border-radius:14px; font-size:12px; font-weight:800; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:7px; font-family:var(--font-main); transition:all .2s; backdrop-filter:blur(10px); letter-spacing:0.2px;" onmouseover="this.style.background='linear-gradient(135deg,rgba(99,102,241,0.35),rgba(139,92,246,0.35))';this.style.transform='translateY(-1px)'" onmouseout="this.style.background='linear-gradient(135deg,rgba(99,102,241,0.2),rgba(139,92,246,0.2))';this.style.transform=''">
          <i class="bi bi-share" style="font-size:13px;"></i> Bagikan
        </button>
      </div>

      {{-- Label kecil --}}
      <div style="text-align:center; margin-top:10px; font-size:10px; color:rgba(255,255,255,0.25); letter-spacing:0.5px; font-family:var(--font-main);">
        Kartu ini merupakan identitas digital resmi siswa SMKN 1 Air Naningan
      </div>
    </div>
  </div>

  {{-- FULLSCREEN AUTO-ZOOM & MAX BRIGHTNESS (PURE WHITE SCREEN) OVERLAY --}}
  <div id="qrZoomOverlay" onclick="toggleQrFullscreenZoom(false)" title="Sentuh untuk minimize &amp; kembalikan kecerahan normal">
    <div class="zoom-overlay-header">
      <div style="font-size:11px; font-weight:800; color:#64748b; letter-spacing:1.5px; text-transform:uppercase;">SMK NEGERI 1 AIR NANINGAN</div>
      <div style="font-size:15px; font-weight:900; color:#0f172a; margin-top:2px; letter-spacing:0.5px;">SCANNER GERBANG &amp; KIOSK</div>
    </div>
    <div class="zoom-overlay-body">
      <div style="background:#ffffff; padding:18px 18px 12px; border-radius:24px; box-shadow:0 16px 50px rgba(0,0,0,0.12); border:2px solid #e2e8f0; display:inline-block; max-width:92vw;">
        <div id="zoomedQrContainer" style="min-width:240px; min-height:240px; display:flex; align-items:center; justify-content:center; margin:0 auto;"></div>
        <div style="font-family:var(--font-mono); font-size:22px; font-weight:900; letter-spacing:3px; color:#0f172a; margin-top:10px; text-align:center;">{{ $codeValue }}</div>
      </div>
      <div style="margin-top:14px; text-align:center;">
        <div style="font-size:18px; font-weight:900; color:#0f172a; line-height:1.2;">{{ $siswa->nama }}</div>
        <div style="font-size:13px; font-weight:700; color:#64748b; margin-top:3px;">{{ $rombel->nama_rombel ?? 'X' }} · {{ $rombel->jurusan->nama_jurusan ?? 'Semua Jurusan' }}</div>
      </div>
    </div>
    <div class="zoom-overlay-footer">
      <div style="display:inline-flex; align-items:center; gap:8px; background:#0f172a; color:#ffffff; padding:10px 22px; border-radius:30px; font-size:12px; font-weight:800; box-shadow:0 4px 18px rgba(0,0,0,0.25);">
        <i class="bi bi-hand-index-thumb"></i>
        <span>Sentuh layar untuk minimize &amp; kembalikan kecerahan</span>
      </div>
    </div>
  </div>

  {{-- CSS Animasi Holo --}}
  <style>
    @keyframes holoShift {
      0% { background-position: 0% 0%; }
      100% { background-position: 200% 0%; }
    }
  </style>
  @endif

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
