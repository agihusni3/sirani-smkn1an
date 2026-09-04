<link rel="icon" type="image/png" href="/favicon.png" />
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="/vendor/bootstrap-icons/bootstrap-icons.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
<link rel="stylesheet" href="/build/assets/app-DhBeqZV0.css" />
<script>
  (function() {
    const saved = localStorage.getItem('smkn1_theme') || 'light';
    document.documentElement.setAttribute('data-theme', saved);
  })();
</script>
<style>
  /* ─── Design Tokens (Light Mode — Tipografi Jelas & Ergonomis) ─── */
  :root,
  [data-theme="light"] {
    --bg: #F8F9FA;
    --bg-2: #FFFFFF;
    --bg-3: #F1F3F5;
    --surface: rgba(0,0,0,0.025);
    --border: rgba(0,0,0,0.08);
    --border-2: rgba(0,0,0,0.13);
    --text: #0F172A;       /* Slate 900: Kontras tajam & sangat jelas */
    --text-2: #334155;     /* Slate 700: Nyaman untuk teks isi & subteks */
    --text-3: #64748B;     /* Slate 500: Terbaca jelas untuk metadata/hints */
    --gold: #B8860B;
    --gold-2: #DAA520;
    --gold-dim: rgba(184,134,11,0.08);
    --gold-glow: rgba(184,134,11,0.15);
    --navy: #2563EB;
    --green: #16A34A;
    --green-dim: rgba(22,163,74,0.08);
    --red: #DC2626;
    --red-dim: rgba(220,38,38,0.07);
    --amber: #B45309;
    --amber-dim: rgba(180,83,9,0.07);
    --r-sm: 8px; --r-md: 12px; --r-lg: 16px; --r-xl: 24px;
    --font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --font-mono: 'JetBrains Mono', 'Fira Code', monospace;
    --shadow-sm: 0 1px 3px rgba(0,0,0,.05);
    --shadow-md: 0 3px 10px rgba(0,0,0,.07);
    --shadow-lg: 0 8px 28px rgba(0,0,0,.09);
  }

  /* ─── Form & Input Ergonomis ─── */
  [data-theme="light"] input,
  [data-theme="light"] select,
  [data-theme="light"] textarea {
    color: #0F172A !important;
  }
  [data-theme="light"] input::placeholder,
  [data-theme="light"] textarea::placeholder {
    color: #94A3B8 !important;
    font-weight: 400 !important;
  }
  [data-theme="light"] label,
  [data-theme="light"] .form-label,
  [data-theme="light"] .form-field-label,
  [data-theme="light"] .form-group label {
    color: #1E293B !important;
    font-weight: 600;
  }

  [data-theme="dark"] {
    --bg: #0B0F19;
    --bg-2: #111827;
    --bg-3: #1F2937;
    --surface: rgba(255,255,255,0.06);
    --border: rgba(255,255,255,0.12);
    --border-2: rgba(255,255,255,0.20);
    --text: #F9FAFB;
    --text-2: #D1D5DB;
    --text-3: #9CA3AF;
    --gold: #FACC15;
    --gold-2: #FDE68A;
    --gold-dim: rgba(250,204,21,0.14);
    --gold-glow: rgba(250,204,21,0.35);
    --navy: #38BDF8;
    --green: #22C55E;
    --green-dim: rgba(34,197,94,0.15);
    --red: #EF4444;
    --red-dim: rgba(239,68,68,0.15);
    --amber: #F59E0B;
    --amber-dim: rgba(245,158,11,0.15);
    --shadow-sm: 0 1px 3px rgba(0,0,0,.5);
    --shadow-md: 0 4px 14px rgba(0,0,0,.6);
    --shadow-lg: 0 16px 48px rgba(0,0,0,.7);
    color-scheme: dark;
  }

  /* ─── Reset & Base Typography ─── */
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    background: var(--bg);
    color: var(--text);
    font-family: var(--font);
    font-size: 14.5px;
    font-weight: 500;
    line-height: 1.6;
    letter-spacing: -0.01em;
    min-height: 100vh;
    font-size: 13.5px;
    line-height: 1.5;
    display: flex;
    overflow-x: hidden;
    transition: background .25s ease, color .25s ease;
  }
  a { color: inherit; text-decoration: none; }

  /* ─── Layout & Standard Sidebar ─── */
  .app-container { display: flex; min-height: 100vh; width: 100%; position: relative; }
  .sidebar {
    width: 275px;
    min-width: 275px;
    height: 100vh;
    background: var(--bg-2);
    border-right: 1px solid var(--border);
    padding: 24px 16px;
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 100;
  }
  .main-content {
    flex: 1;
    min-width: 0;
    padding: 28px 36px;
    overflow-y: auto;
    min-height: 100vh;
    transition: padding 0.28s ease;
  }

  /* ─── Brand ─── */
  .brand { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 24px; padding: 0 4px; position: relative; width: 100%; }
  .brand-left { display: flex; align-items: center; gap: 10px; overflow: hidden; min-width: 0; }
  .brand-actions { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
  .brand-logo-card {
    width: 44px; height: 44px; border-radius: 12px;
    background: var(--bg-2); border: 1.5px solid var(--border-2);
    padding: 3px; display: flex; align-items: center; justify-content: center;
    box-shadow: var(--shadow-sm); flex-shrink: 0;
  }
  .brand-logo-img { width: 100%; height: 100%; object-fit: contain; }
  .brand-text { display: flex; flex-direction: column; white-space: nowrap; overflow: hidden; }
  .brand-name {
    font-size: 20px; font-weight: 900; line-height: 1.15;
    letter-spacing: -0.03em; color: var(--text);
  }
  .brand-subtitle {
    font-size: 11.5px; font-weight: 600; color: var(--text-3);
    line-height: 1.3; margin-top: 2px;
  }

  /* ─── Navigation ─── */
  .nav-group { margin-bottom: 24px; }
  .nav-label {
    font-family: var(--font-mono);
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .12em;
    color: var(--text-3);
    margin-bottom: 8px;
    padding: 0 12px;
  }
  .nav-item {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 14px;
    border-radius: var(--r-sm);
    color: var(--text-2);
    font-weight: 600;
    font-size: 13.5px;
    margin-bottom: 3px;
    transition: all .18s ease;
    border: 1px solid transparent;
    white-space: nowrap;
    text-decoration: none;
  }
  .nav-item i { font-size: 17px; width: 22px; text-align: center; flex-shrink: 0; }
  .nav-item:hover { background: var(--surface); color: var(--text); transform: translateX(2px); }
  .nav-item.active {
    background: #000000 !important;
    color: #FFFFFF !important;
    border-color: #000000 !important;
    font-weight: 800;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
  }
  .nav-item.active i,
  .nav-item.active .nav-icon {
    color: #FFFFFF !important;
  }
  .nav-item.active .nav-text {
    color: #FFFFFF !important;
  }

  /* ─── Page Header ─── */
  .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 34px; flex-wrap: wrap; gap: 16px; }
  .header-title h1 { font-size: 28px; font-weight: 800; letter-spacing: -0.03em; margin-bottom: 6px; }
  .header-title p { color: var(--text-2); font-size: 14.5px; font-weight: 500; }

  /* Greeting Badge (di header-actions kanan) */
  .greeting-badge {
    display: flex; align-items: center; gap: 5px;
    padding: 5px 12px; border-radius: 100px;
    background: var(--surface); border: 1px solid var(--border);
    white-space: nowrap;
  }
  .greeting-wave { font-size: 16px; animation: wave 2.5s ease-in-out infinite; display: inline-block; transform-origin: 70% 80%; }
  @keyframes wave {
    0%,60%,100% { transform: rotate(0deg); }
    10%,30% { transform: rotate(14deg); }
    20% { transform: rotate(-8deg); }
    40% { transform: rotate(-4deg); }
    50% { transform: rotate(10deg); }
  }
  .greeting-text { font-size: 13px; font-weight: 500; color: var(--text-2); }
  .greeting-text strong { color: var(--text); font-weight: 800; }

  /* Account icon-only button variant */
  .acct-icon-only { padding: 4px !important; border-radius: 50% !important; width: 40px; height: 40px; justify-content: center; }

  /* ─── Auto Refresh Widget ─── */
  .autorefresh-widget {
    display: flex;
    align-items: center;
    gap: 6px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 100px;
    padding: 5px 12px;
    font-size: 12px;
    font-family: var(--font-mono);
    color: var(--text-2);
    user-select: none;
  }
  .autorefresh-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--green);
    box-shadow: 0 0 8px var(--green);
    animation: pulse-live 1.5s infinite;
  }
  .autorefresh-dot.paused {
    background: var(--text-3);
    box-shadow: none;
    animation: none;
  }
  @keyframes pulse-live {
    0% { transform: scale(0.95); opacity: 0.7; }
    50% { transform: scale(1.2); opacity: 1; }
    100% { transform: scale(0.95); opacity: 0.7; }
  }
  .btn-autorefresh-action {
    background: transparent;
    border: none;
    color: var(--text-3);
    cursor: pointer;
    font-size: 13px;
    padding: 2px 4px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color .15s, background .15s;
  }
  .btn-autorefresh-action:hover {
    color: var(--text);
    background: var(--surface);
  }
  .autorefresh-spin {
    animation: spin 1s linear infinite;
  }
  @keyframes spin {
    100% { transform: rotate(360deg); }
  }

  /* ─── Header Actions (Theme + Account) ─── */
  .header-actions { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }

  /* Theme Switch */
  .theme-switch-wrap { display: flex; align-items: center; gap: 6px; }
  .theme-switch-icon { font-size: 14px; color: var(--text-3); transition: color .2s; }
  .theme-switch { position: relative; display: inline-block; width: 44px; height: 24px; flex-shrink:0; }
  .theme-switch input { opacity: 0; width: 0; height: 0; }
  .theme-slider {
    position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
    background: var(--bg-3); border: 1.5px solid var(--border-2);
    border-radius: 100px; transition: background .25s;
  }
  .theme-slider::before {
    content: ''; position: absolute;
    width: 16px; height: 16px; left: 3px; bottom: 3px;
    background: var(--text-3); border-radius: 50%;
    transition: transform .25s, background .25s;
  }
  .theme-switch input:checked + .theme-slider { background: #000000; border-color: #000000; }
  .theme-switch input:checked + .theme-slider::before { transform: translateX(20px); background: #FFFFFF; }

  /* Account Button */
  .acct-wrap { position: relative; }
  .acct-btn {
    display: flex; align-items: center; gap: 8px;
    background: var(--bg-2); border: 1.5px solid var(--border-2);
    border-radius: var(--r-sm); padding: 6px 12px 6px 6px;
    cursor: pointer; color: var(--text); font-family: var(--font);
    font-size: 13px; font-weight: 600; transition: border-color .2s, box-shadow .2s;
  }
  .acct-btn:hover { border-color: #000000; box-shadow: 0 0 0 3px rgba(0,0,0,0.12); }
  .acct-avatar {
    width: 30px; height: 30px; border-radius: 50%;
    background: #000000; border: 1.5px solid #000000;
    display: flex; align-items: center; justify-content: center;
    color: #FFFFFF; font-size: 16px; flex-shrink: 0;
  }
  .acct-name { max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .acct-chevron { font-size: 11px; color: var(--text-3); transition: transform .25s; }

  /* Account Dropdown (Desktop & Global) */
  .acct-dropdown {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    left: auto;
    width: 280px;
    min-width: 260px;
    max-width: calc(100vw - 24px);
    background: var(--bg-2) !important;
    border: 1.5px solid var(--border-2);
    border-radius: var(--r-md);
    box-shadow: 0 16px 45px -4px rgba(0, 0, 0, 0.28), 0 0 0 1px var(--border-2);
    opacity: 0;
    pointer-events: none;
    transform: translateY(-8px);
    transition: opacity .2s ease, transform .2s ease;
    z-index: 99999 !important;
    overflow: hidden;
    box-sizing: border-box;
  }
  .acct-dropdown.open {
    opacity: 1;
    pointer-events: all;
    transform: translateY(0);
  }
  .acct-dropdown-header {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 16px;
    background: var(--bg-3) !important;
    border-bottom: 1px solid var(--border-2);
  }
  .acct-avatar-lg {
    font-size: 28px;
    color: var(--text);
    line-height: 1;
    flex-shrink: 0;
  }
  .acct-dropdown-name {
    font-weight: 800;
    font-size: 13.5px;
    color: var(--text);
    white-space: normal;
    word-break: break-word;
    line-height: 1.35;
  }
  .acct-dropdown-email {
    font-size: 11px;
    color: var(--text-3);
    margin-top: 3px;
    white-space: normal;
    word-break: break-all;
    line-height: 1.35;
  }
  .acct-dropdown-role {
    display: inline-block;
    margin-top: 6px;
    background: #000000;
    color: #FFFFFF;
    border-radius: 100px;
    padding: 2px 10px;
    font-size: 10px;
    font-weight: 700;
    font-family: var(--font-mono);
    white-space: nowrap;
  }
  .acct-dropdown-divider {
    height: 1px;
    background: var(--border-2);
    margin: 0;
  }
  .acct-dropdown-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    color: var(--text);
    font-size: 12.5px;
    font-weight: 700;
    text-decoration: none;
    width: 100%;
    background: none;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-family: var(--font);
    transition: background .15s, color .15s;
    white-space: nowrap;
    text-align: left;
    box-sizing: border-box;
  }
  .acct-dropdown-item:hover {
    background: var(--bg-3) !important;
    color: var(--text);
  }
  .acct-dropdown-item i {
    font-size: 15px;
    width: 18px;
    text-align: center;
    flex-shrink: 0;
  }
  .acct-logout {
    color: var(--red) !important;
  }
  .acct-logout:hover {
    background: var(--red-dim) !important;
  }

  /* ─── Stat Cards ─── */
  .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 16px; margin-bottom: 28px; }
  .stat-card {
    background: var(--bg-2);
    border: 1px solid var(--border);
    border-radius: var(--r-md);
    padding: 18px 20px;
    box-shadow: var(--shadow-sm);
    transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }
  .stat-card:hover { border-color: #000000; box-shadow: var(--shadow-md); transform: translateY(-3px); }
  .stat-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; color: var(--text-2); font-size: 13px; font-weight: 700; }
  .stat-head i { font-size: 16px; opacity: 0.9; }
  .stat-val { font-size: 36px; font-weight: 900; line-height: 1; margin: 0; letter-spacing: -0.03em; font-family: var(--font-mono); }
  .stat-sub { font-size: 11.5px; color: var(--text-3); font-family: var(--font-mono); margin-top: 6px; }

  /* ─── Panels ─── */
  .panel {
    background: var(--bg-2);
    border: 1px solid var(--border);
    border-radius: var(--r-lg);
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-sm);
    transition: background .25s ease, border-color .25s ease;
  }
  .panel-title {
    font-size: 17px;
    font-weight: 800;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    letter-spacing: -0.02em;
    gap: 12px;
  }
  .panel-title > span:first-child, .panel-title > div:first-child {
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }
  .panel-title i { margin-right: 2px; }

  /* ─── Tables ─── */
  table, .data-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; color: var(--text); }
  th, .data-table th {
    font-family: var(--font);
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--text-2) !important;
    padding: 11px 16px;
    border-bottom: 1.5px solid var(--border-2);
    background: var(--bg-3);
  }
  td, .data-table td {
    padding: 12px 16px;
    border-bottom: 1px solid var(--border);
    color: var(--text);
    font-weight: 500;
    font-size: 13.5px;
    line-height: 1.5;
  }
  tr:hover td { background: rgba(0,0,0,0.02); }

  /* ─── Modern Clean Status Indicators (No Background, No Border) ─── */
  .piket-status-pill, .table-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 5.5px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.1px;
    padding: 0;
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    white-space: nowrap;
    line-height: 1.2;
    text-decoration: none !important;
  }
  .piket-status-pill i, .table-status-pill i {
    font-size: 12.5px;
    flex-shrink: 0;
  }
  .piket-status-pill.hadir, .table-status-pill.hadir,
  .piket-status-pill.aktif, .table-status-pill.aktif,
  .piket-status-pill.terkirim, .table-status-pill.terkirim,
  .piket-status-pill.disetujui, .table-status-pill.disetujui,
  .piket-status-pill.selesai, .table-status-pill.selesai {
    color: #16A34A !important;
  }
  [data-theme="dark"] .piket-status-pill.hadir, [data-theme="dark"] .table-status-pill.hadir,
  [data-theme="dark"] .piket-status-pill.aktif, [data-theme="dark"] .table-status-pill.aktif,
  [data-theme="dark"] .piket-status-pill.terkirim, [data-theme="dark"] .table-status-pill.terkirim,
  [data-theme="dark"] .piket-status-pill.disetujui, [data-theme="dark"] .table-status-pill.disetujui,
  [data-theme="dark"] .piket-status-pill.selesai, [data-theme="dark"] .table-status-pill.selesai {
    color: #22C55E !important;
  }

  .piket-status-pill.terlambat, .table-status-pill.terlambat,
  .piket-status-pill.pending, .table-status-pill.pending,
  .piket-status-pill.menunggu, .table-status-pill.menunggu {
    color: #D97706 !important;
  }
  [data-theme="dark"] .piket-status-pill.terlambat, [data-theme="dark"] .table-status-pill.terlambat,
  [data-theme="dark"] .piket-status-pill.pending, [data-theme="dark"] .table-status-pill.pending,
  [data-theme="dark"] .piket-status-pill.menunggu, [data-theme="dark"] .table-status-pill.menunggu {
    color: #F59E0B !important;
  }

  .piket-status-pill.pulang, .table-status-pill.pulang {
    color: #0284C7 !important;
  }
  [data-theme="dark"] .piket-status-pill.pulang, [data-theme="dark"] .table-status-pill.pulang {
    color: #38BDF8 !important;
  }

  .piket-status-pill.izin, .table-status-pill.izin,
  .piket-status-pill.sakit, .table-status-pill.sakit,
  .piket-status-pill.dispen, .table-status-pill.dispen,
  .piket-status-pill.cuti, .table-status-pill.cuti,
  .piket-status-pill.dinas_luar, .table-status-pill.dinas_luar {
    color: #2563EB !important;
  }
  [data-theme="dark"] .piket-status-pill.izin, [data-theme="dark"] .table-status-pill.izin,
  [data-theme="dark"] .piket-status-pill.sakit, [data-theme="dark"] .table-status-pill.sakit,
  [data-theme="dark"] .piket-status-pill.dispen, [data-theme="dark"] .table-status-pill.dispen,
  [data-theme="dark"] .piket-status-pill.cuti, [data-theme="dark"] .table-status-pill.cuti,
  [data-theme="dark"] .piket-status-pill.dinas_luar, [data-theme="dark"] .table-status-pill.dinas_luar {
    color: #60A5FA !important;
  }

  .piket-status-pill.pkl, .table-status-pill.pkl,
  .piket-status-pill.magang, .table-status-pill.magang {
    color: #7C3AED !important;
  }
  [data-theme="dark"] .piket-status-pill.pkl, [data-theme="dark"] .table-status-pill.pkl,
  [data-theme="dark"] .piket-status-pill.magang, [data-theme="dark"] .table-status-pill.magang {
    color: #A78BFA !important;
  }

  .piket-status-pill.belum, .table-status-pill.belum,
  .piket-status-pill.alpha, .table-status-pill.alpha,
  .piket-status-pill.gagal, .table-status-pill.gagal,
  .piket-status-pill.ditolak, .table-status-pill.ditolak {
    color: #DC2626 !important;
  }
  [data-theme="dark"] .piket-status-pill.belum, [data-theme="dark"] .table-status-pill.belum,
  [data-theme="dark"] .piket-status-pill.alpha, [data-theme="dark"] .table-status-pill.alpha,
  [data-theme="dark"] .piket-status-pill.gagal, [data-theme="dark"] .table-status-pill.gagal,
  [data-theme="dark"] .piket-status-pill.ditolak, [data-theme="dark"] .table-status-pill.ditolak {
    color: #EF4444 !important;
  }

  .piket-status-pill.bolos, .table-status-pill.bolos {
    color: #E11D48 !important;
  }
  [data-theme="dark"] .piket-status-pill.bolos, [data-theme="dark"] .table-status-pill.bolos {
    color: #F43F5E !important;
  }

  .piket-status-pill.netral, .table-status-pill.netral,
  .piket-status-pill.nonaktif, .table-status-pill.nonaktif,
  .piket-status-pill.dibatalkan, .table-status-pill.dibatalkan {
    color: var(--text-3) !important;
  }

  /* ─── Buttons — Minimalis ─── */
  .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: var(--r-sm);
    font-family: var(--font);
    font-weight: 600;
    font-size: 13.5px;
    cursor: pointer;
    border: none;
    transition: all .15s ease;
    letter-spacing: -0.01em;
  }
  .btn:hover { transform: translateY(-1px); }
  .btn:active { transform: translateY(0); }

  .btn-gold {
    background: #000000;
    color: #FFFFFF;
    border: 1px solid #000000;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
  }
  .btn-gold:hover { background: #262626; border-color: #262626; box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2); }

  .btn-danger {
    background: var(--bg-2);
    color: var(--red);
    border: 1px solid rgba(220,38,38,0.3);
  }
  .btn-danger:hover { background: var(--red-dim); }

  .btn-outline {
    background: var(--bg-2);
    color: var(--text);
    border: 1px solid var(--border-2);
  }
  .btn-outline:hover { background: var(--bg-3); border-color: var(--border-2); color: var(--text); }

  /* ─── Icon Action Button — Minimalis (No color gradient) ─── */
  .btn-icon {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: var(--r-sm);
    border: 1px solid var(--border-2);
    cursor: pointer;
    font-size: 13.5px;
    transition: all .18s ease;
    background: var(--bg-2);
    color: var(--text-2);
    text-decoration: none;
  }
  .btn-icon:hover {
    background: var(--bg-3);
    color: var(--text);
    border-color: var(--border-2);
    transform: none;
    box-shadow: none;
  }

  .btn-icon-edit  { color: var(--text-2); }
  .btn-icon-edit:hover { color: var(--text); }

  .btn-icon-danger { color: var(--red); }
  .btn-icon-danger:hover { background: var(--red-dim); border-color: rgba(220,38,38,0.3); color: var(--red); }

  .btn-icon-warning { color: var(--amber); }
  .btn-icon-warning:hover { background: var(--amber-dim); color: var(--amber); }

  .btn-icon-success { color: var(--green); }
  .btn-icon-success:hover { background: var(--green-dim); border-color: rgba(22,163,74,0.3); color: var(--green); }



  /* Tooltip Modern on Hover */
  [data-tooltip] {
    position: relative;
  }
  [data-tooltip]::before {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%) translateY(4px);
    padding: 5px 10px;
    background: linear-gradient(135deg, #1E293B, #0F172A);
    color: #F8FAFC;
    font-family: var(--font);
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
    border-radius: 6px;
    border: 1px solid rgba(255, 255, 255, 0.15);
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: all .18s cubic-bezier(.4, 0, .2, 1);
    z-index: 999;
  }
  [data-tooltip]::after {
    content: '';
    position: absolute;
    bottom: 110%;
    left: 50%;
    transform: translateX(-50%) translateY(4px);
    border: 5px solid transparent;
    border-top-color: #0F172A;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: all .18s cubic-bezier(.4, 0, .2, 1);
    z-index: 999;
  }
  [data-tooltip]:hover::before,
  [data-tooltip]:hover::after {
    opacity: 1;
    visibility: visible;
    transform: translateX(-50%) translateY(0);
  }

  select, input[type="text"], input[type="date"], input[type="datetime-local"], input[type="time"], input[type="month"], input[type="number"], input[type="password"], input[type="email"], input[type="search"], input[type="tel"], input[type="url"], textarea, .form-control {
    background: var(--bg-3);
    border: 1.5px solid var(--border-2);
    color: var(--text);
    padding: 0 14px;
    height: 42px;
    line-height: 42px;
    border-radius: var(--r-sm);
    font-family: var(--font);
    font-size: 13.5px;
    font-weight: 500;
    outline: none;
    box-sizing: border-box;
    transition: border-color .2s, box-shadow .2s;
    width: 100%;
  }
  textarea, textarea.form-control {
    height: auto;
    min-height: 80px;
    padding: 10px 14px;
    line-height: 1.5;
  }
  select:focus, input:focus, textarea:focus, .form-control:focus {
    border-color: #000000;
    box-shadow: 0 0 0 3px rgba(0,0,0,0.12);
  }

  /* ─── Custom File Input ─── */
  input[type="file"] {
    background: var(--bg-3);
    border: 1.5px solid var(--border-2);
    color: var(--text-2);
    padding: 4px 10px;
    height: 42px;
    border-radius: var(--r-sm);
    font-family: var(--font);
    font-size: 13px;
    outline: none;
    box-sizing: border-box;
    transition: border-color .2s, box-shadow .2s;
    width: 100%;
    display: flex;
    align-items: center;
    cursor: pointer;
  }
  input[type="file"]:hover {
    border-color: #000000;
  }
  input[type="file"]::file-selector-button {
    background: var(--bg-2);
    color: var(--text);
    border: 1px solid var(--border-2);
    padding: 0 14px;
    height: 30px;
    line-height: 28px;
    border-radius: var(--r-sm);
    font-family: var(--font);
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    margin-right: 12px;
    transition: all .2s ease;
  }
  input[type="file"]:hover::file-selector-button {
    background: #000000;
    color: #FFFFFF;
    border-color: #000000;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
  }
  .form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px 20px;
    align-items: flex-end;
  }
  .form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-bottom: 0;
  }
  .form-group label {
    display: block;
    font-family: var(--font);
    font-size: 12.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: var(--text-2);
    margin-bottom: 6px;
  }
  .form-group label span.required,
  .form-group label span[style*="color:var(--red)"] {
    color: var(--red);
    margin-left: 2px;
  }
  .form-row > div:last-child > .btn,
  .form-action-btn {
    height: 42px !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
  }

  /* ─── Alerts ─── */
  .alert-success { background: var(--green-dim); color: var(--green); padding: 14px 18px; border-radius: var(--r-sm); margin-bottom: 20px; font-weight: 600; border: 1px solid rgba(34,197,94,0.2); }
  .alert-error { background: var(--red-dim); color: var(--red); padding: 14px 18px; border-radius: var(--r-sm); margin-bottom: 20px; font-weight: 600; border: 1px solid rgba(239,68,68,0.2); }

  /* ─── Executive & Admin Hero Banner ─── */
  .exec-hero {
    background: linear-gradient(135deg, var(--bg-2) 0%, var(--bg-3) 100%);
    border: 1.5px solid var(--border-2);
    border-radius: var(--r-lg);
    padding: 22px 28px;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    position: relative;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
  }
  .exec-hero::before {
    content: '';
    position: absolute;
    top: -40%;
    right: -10%;
    width: 260px;
    height: 260px;
    background: radial-gradient(circle, rgba(202,138,4,0.18), transparent 70%);
    pointer-events: none;
  }
  .exec-date-card {
    background: rgba(0, 0, 0, 0.35);
    border: 1px solid var(--border-2);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    padding: 12px 18px;
    border-radius: var(--r-md);
    text-align: right;
  }
  [data-theme="light"] .exec-date-card {
    background: rgba(255, 255, 255, 0.85);
    border-color: rgba(0,0,0,0.1);
  }
  @media (max-width: 768px) {
    .exec-hero {
      flex-direction: column;
      align-items: stretch;
      padding: 18px 16px;
    }
    .exec-date-card {
      text-align: left;
    }
  }

  /* ─── Dashboard KPI Metrics ─── */
  .db-kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    gap: 14px;
    margin-bottom: 24px;
  }
  .db-kpi-card {
    background: var(--bg-2);
    border: 1px solid var(--border-2);
    border-radius: var(--r-md);
    padding: 14px 16px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all .2s ease;
    box-shadow: var(--shadow-sm);
    cursor: pointer;
  }
  .db-kpi-card:hover {
    border-color: var(--border);
    transform: translateY(-2px);
  }
  .db-kpi-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
  }
  .db-kpi-title {
    font-size: 10.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-3);
  }
  .db-kpi-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    background: var(--bg-3);
    color: var(--text);
    border: 1px solid var(--border-2);
    flex-shrink: 0;
  }
  .db-kpi-val {
    font-size: 24px;
    font-weight: 900;
    font-family: var(--font-mono);
    color: var(--text);
    line-height: 1.1;
  }
  .db-kpi-sub {
    font-size: 11px;
    color: var(--text-3);
    margin-top: 3px;
  }

  /* ─── Progress Bar ─── */
  .progress-bg { background: var(--bg-3); height: 8px; border-radius: 4px; overflow: hidden; width: 100%; margin-top: 6px; }
  .progress-fill { height: 100%; background: linear-gradient(90deg, #000000, var(--green)); border-radius: 4px; }

  /* ─── Modal Dialog ─── */
  .modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    padding: 20px;
    opacity: 0;
    transition: opacity .25s ease;
  }
  .modal-backdrop.active {
    display: flex;
    opacity: 1;
  }
  .modal-card {
    background: var(--bg-2);
    border: 1px solid var(--border-2);
    border-radius: var(--r-lg);
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.6);
    width: 100%;
    max-width: 800px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transform: scale(0.95) translateY(10px);
    transition: transform .25s cubic-bezier(0.16, 1, 0.3, 1);
    animation: modalIn .25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  }
  @keyframes modalIn {
    to { transform: scale(1) translateY(0); }
  }
  .modal-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 24px;
    border-bottom: 1px solid var(--border);
    background: var(--bg-2);
  }
  .modal-title {
    font-size: 16px;
    font-weight: 800;
    color: var(--text);
    display: flex;
    align-items: center;
    gap: 8px;
    letter-spacing: -0.02em;
  }
  .btn-close-modal {
    background: var(--bg-3);
    border: 1px solid var(--border-2);
    color: var(--text-2);
    width: 32px;
    height: 32px;
    border-radius: var(--r-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all .2s;
  }
  .btn-close-modal:hover {
    background: var(--red-dim);
    color: var(--red);
    border-color: rgba(239, 68, 68, 0.4);
  }
  .modal-footer {
    padding: 16px 24px;
    border-top: 1px solid var(--border);
    background: var(--bg-2);
  }

  /* ─── Universal Avatar Proportional Rules (Mencegah Foto Gepeng / Distorsi) ─── */
  .avatar-circle, .avatar-box {
    width: 38px;
    height: 38px;
    min-width: 38px;
    min-height: 38px;
    max-width: 38px;
    max-height: 38px;
    border-radius: 50%;
    overflow: hidden;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    background: var(--bg-3);
    border: 1.5px solid var(--border-2);
    flex-shrink: 0;
    vertical-align: middle;
  }
  .avatar-circle.avatar-sm, .avatar-box.avatar-sm {
    width: 32px; height: 32px; min-width: 32px; min-height: 32px; max-width: 32px; max-height: 32px;
  }
  .avatar-circle.avatar-lg, .avatar-box.avatar-lg {
    width: 52px; height: 52px; min-width: 52px; min-height: 52px; max-width: 52px; max-height: 52px;
  }
  .avatar-circle.gold-border { border-color: rgba(0,0,0,0.25); }
  .avatar-circle.blue-border { border-color: var(--navy); }

  .avatar-circle img,
  .avatar-box img,
  img.avatar-img,
  img.avatar-thumb {
    width: 100% !important;
    height: 100% !important;
    min-width: 100% !important;
    min-height: 100% !important;
    max-width: 100% !important;
    max-height: 100% !important;
    aspect-ratio: 1 / 1 !important;
    object-fit: cover !important;
    object-position: center !important;
    display: block !important;
    margin: 0 auto !important;
    flex-shrink: 0 !important;
  }

  .avatar-preview-wrapper {
    width: 72px;
    height: 72px;
    min-width: 72px;
    min-height: 72px;
    border-radius: 50%;
    overflow: hidden;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 2px solid rgba(0,0,0,0.25);
    background: var(--bg-3);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    flex-shrink: 0;
  }
  .avatar-preview-wrapper img {
    width: 100% !important;
    height: 100% !important;
    aspect-ratio: 1 / 1 !important;
    object-fit: cover !important;
    object-position: center 20% !important;
    display: block !important;
  }

  /* ─── Responsive & Mobile Navigation (Hamburger Drawer) ─── */
  .mobile-topbar {
    display: none;
  }
  .sidebar-close-btn {
    display: none;
  }
  .sidebar-backdrop {
    display: none;
  }

  @media (max-width: 1024px) {
    body {
      flex-direction: column !important;
    }
    .mobile-topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 16px;
      background: var(--bg-2);
      border-bottom: 1px solid var(--border-2);
      position: sticky;
      top: 0;
      z-index: 1000;
      width: 100%;
    }
    .mobile-hamburger-btn {
      width: 38px;
      height: 38px;
      background: var(--bg-3);
      border: 1.5px solid var(--border-2);
      border-radius: var(--r-sm);
      color: var(--text);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      cursor: pointer;
      pointer-events: auto !important;
      touch-action: manipulation !important;
      transition: all .2s;
    }
    .mobile-hamburger-btn:hover {
      background: rgba(0,0,0,0.08);
      color: #000000;
      border-color: #000000;
    }
    .sidebar {
      position: fixed !important;
      top: 0 !important;
      left: 0 !important;
      bottom: 0 !important;
      width: 290px !important;
      max-width: 86vw !important;
      height: 100vh !important;
      height: 100dvh !important;
      max-height: 100dvh !important;
      z-index: 1050 !important;
      transform: translateX(-110%) !important;
      visibility: hidden !important;
      pointer-events: none !important;
      display: none !important;
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.3s !important;
      box-shadow: 0 0 40px rgba(0,0,0,0.6) !important;
      background: var(--bg-2) !important;
      overflow-y: auto !important;
      -webkit-overflow-scrolling: touch !important;
      touch-action: pan-y !important;
      overscroll-behavior-y: contain !important;
      padding-bottom: calc(100px + env(safe-area-inset-bottom, 24px)) !important;
    }
    .sidebar.mobile-open {
      display: flex !important;
      transform: translateX(0) !important;
      visibility: visible !important;
      pointer-events: auto !important;
    }
    .sidebar-close-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 32px;
      height: 32px;
      background: var(--bg-3);
      border: 1px solid var(--border-2);
      color: var(--text-2);
      border-radius: var(--r-sm);
      cursor: pointer;
      pointer-events: auto !important;
      touch-action: manipulation !important;
      transition: all .2s;
    }
    .sidebar-close-btn:hover {
      background: var(--red-dim);
      color: var(--red);
      border-color: var(--red);
    }
    .sidebar-backdrop {
      display: none !important;
      position: fixed !important;
      top: -99999px !important;
      left: -99999px !important;
      width: 0 !important;
      height: 0 !important;
      background: rgba(0, 0, 0, 0.65);
      backdrop-filter: blur(4px);
      -webkit-backdrop-filter: blur(4px);
      z-index: -1 !important;
      opacity: 0;
      visibility: hidden !important;
      pointer-events: none !important;
      transition: opacity 0.3s ease, visibility 0.3s !important;
    }
    .sidebar-backdrop.active {
      display: block !important;
      top: 0 !important;
      left: 0 !important;
      right: 0 !important;
      bottom: 0 !important;
      width: 100vw !important;
      height: 100vh !important;
      z-index: 1040 !important;
      opacity: 1 !important;
      visibility: visible !important;
      pointer-events: auto !important;
    }
    .acct-dropdown {
      display: none;
      left: auto !important;
      right: 0 !important;
      min-width: 230px !important;
      max-width: min(90vw, 320px) !important;
    }
    .acct-dropdown.open {
      display: block !important;
    }
    .app-container {
      display: block !important;
      overflow-x: hidden !important;
    }
    .main-content {
      position: relative !important;
      z-index: 1 !important;
      padding: 14px 12px !important;
      padding-bottom: calc(85px + env(safe-area-inset-bottom, 24px)) !important;
      width: 100% !important;
      max-width: 100vw !important;
      box-sizing: border-box !important;
      overflow-x: hidden !important;
    }
    .header {
      margin-bottom: 16px !important;
      display: block !important;
      width: 100% !important;
    }
    .header-title {
      width: 100% !important;
    }
    .header-title h1 {
      font-size: 19px !important;
      line-height: 1.25 !important;
    }
    .header-title p {
      font-size: 12px !important;
      line-height: 1.4 !important;
      margin-top: 2px !important;
    }
    /* Sembunyikan header-actions dari dalam main-content di mobile agar tidak ganda */
    .main-content .header-actions {
      display: none !important;
    }
    .mobile-topbar .header-actions {
      display: inline-flex !important;
      align-items: center !important;
      gap: 6px !important;
      margin-left: 0 !important;
    }
    .header-actions > .btn,
    .header-actions > button,
    .header-actions > .acct-wrap > .acct-btn,
    .btn-icon-header,
    #themeToggleQuick,
    #acctBtn {
      flex: 0 0 36px !important;
      width: 36px !important;
      height: 36px !important;
      min-width: 36px !important;
      max-width: 36px !important;
      min-height: 36px !important;
      max-height: 36px !important;
      padding: 0 !important;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      box-sizing: border-box !important;
    }
    .acct-wrap {
      position: relative !important;
      flex: 0 0 36px !important;
      width: 36px !important;
      height: 36px !important;
    }
    .acct-dropdown {
      display: none;
      position: absolute !important;
      top: calc(100% + 8px) !important;
      right: 0 !important;
      left: auto !important;
      width: 270px !important;
      min-width: 250px !important;
      max-width: calc(100vw - 24px) !important;
      background: var(--bg-2) !important;
      border: 1.5px solid var(--border-2) !important;
      border-radius: var(--r-md) !important;
      box-shadow: 0 14px 40px rgba(0,0,0,0.25) !important;
      z-index: 9998 !important;
      overflow: hidden !important;
      box-sizing: border-box !important;
    }
    .acct-dropdown.open {
      display: block !important;
    }
    .acct-dropdown-item {
      display: flex !important;
      align-items: center !important;
      gap: 10px !important;
      width: 100% !important;
      min-width: 100% !important;
      max-width: 100% !important;
      height: auto !important;
      min-height: 40px !important;
      max-height: none !important;
      padding: 10px 14px !important;
      font-size: 12.5px !important;
      font-weight: 700 !important;
      color: var(--text) !important;
      white-space: nowrap !important;
      text-align: left !important;
      box-sizing: border-box !important;
      border: none !important;
      background: none !important;
      border-radius: 6px !important;
      cursor: pointer !important;
    }
    .acct-dropdown-item:hover {
      background: var(--bg-3) !important;
    }
    .acct-dropdown-item i {
      font-size: 15px !important;
      flex-shrink: 0 !important;
    }
    .modal-overlay:not(.open):not(.active),
    #modalProfilMandiri:not(.open):not(.active) {
      display: none !important;
      pointer-events: none !important;
    }
    .stats-grid, .grid-4, .grid-3, .db-kpi-grid {
      display: grid !important;
      grid-template-columns: repeat(2, 1fr) !important;
      gap: 10px !important;
      margin-bottom: 18px !important;
    }
    .db-kpi-card {
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      border-radius: var(--r-md);
      padding: 12px 14px !important;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: all .2s ease;
      box-shadow: var(--shadow-sm);
      cursor: pointer;
    }
    .db-kpi-card:hover {
      border-color: var(--border);
      transform: translateY(-2px);
    }
    .header {
      display: flex !important;
      align-items: center !important;
      justify-content: space-between !important;
      margin-bottom: 12px !important;
      gap: 8px !important;
    }
    .header-title h1 {
      font-size: 17px !important;
      margin-bottom: 0px !important;
      display: flex !important;
      align-items: center !important;
      gap: 6px !important;
    }
    .header-title p {
      display: none !important;
    }
    .db-kpi-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 4px;
    }
    .db-kpi-title {
      font-size: 10px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.4px;
      color: var(--text-3);
    }
    .db-kpi-icon {
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
    .db-kpi-val {
      font-size: 18px !important;
      font-weight: 900;
      font-family: var(--font-mono);
      color: #000000;
      line-height: 1.1;
    }
    .db-kpi-sub {
      font-size: 10px;
      color: var(--text-3);
      margin-top: 2px;
    }
    .db-kpi-grid,
    .guru-stat-grid,
    .siswa-stat-grid,
    .piket-stats-grid,
    .kpi-disiplin-grid,
    .lp-kpi-grid {
      grid-template-columns: repeat(2, 1fr) !important;
      gap: 8px !important;
      margin-bottom: 12px !important;
    }
    .db-kpi-card,
    .guru-stat-card,
    .siswa-stat-card,
    .piket-stat-card,
    .lp-kpi-card {
      padding: 8px 10px !important;
      border-radius: var(--r-sm) !important;
    }
    .panel, .card-dashboard {
      padding: 10px 12px !important;
      margin-bottom: 12px !important;
      border-radius: var(--r-md) !important;
      overflow: hidden !important;
    }
    .form-row, .row-form {
      grid-template-columns: 1fr !important;
      gap: 8px !important;
    }
    .tab-container {
      overflow-x: auto !important;
      white-space: nowrap !important;
      padding-bottom: 4px !important;
      -webkit-overflow-scrolling: touch;
      display: flex !important;
      gap: 4px !important;
    }
    .tab-btn, .nav-tab, .piket-tab-btn, .izin-tab-btn {
      flex-shrink: 0 !important;
      font-size: 11.5px !important;
      padding: 6px 12px !important;
      border-radius: 6px !important;
    }
    .table-container, .table-responsive {
      width: 100% !important;
      max-width: 100% !important;
      overflow-x: auto !important;
      -webkit-overflow-scrolling: touch;
      border-radius: var(--r-sm) !important;
      margin-bottom: 10px !important;
      border: 1px solid var(--border-2) !important;
    }
    .table-container table,
    .table-responsive table {
      min-width: 500px !important;
      width: 100% !important;
    }
    .table-container th, .table-container td,
    .table-responsive th, .table-responsive td {
      padding: 6px 8px !important;
      font-size: 11.5px !important;
    }
    .filter-bar, .search-filter-box {
      flex-direction: column !important;
      align-items: stretch !important;
      gap: 6px !important;
    }
    .filter-bar input, .filter-bar select, .filter-bar button {
      width: 100% !important;
      height: 32px !important;
      font-size: 11.5px !important;
    }
    .filter-pill, .btn-toggle-kat {
      padding: 3px 8px !important;
      font-size: 11px !important;
    }
    .badge {
      font-size: 10px !important;
      padding: 2px 6px !important;
    }
  }

  @media (max-width: 480px) {
    .modal-card, .modal-container {
      max-width: 95vw !important;
      padding: 16px 12px !important;
      border-radius: var(--r-lg) !important;
    }
    .btn {
      font-size: 12px !important;
      padding: 8px 12px !important;
    }
    .pagination-wrapper {
      flex-wrap: wrap !important;
      justify-content: center !important;
    }
  }

  /* ─── Global Modals & Containers ─── */
  .modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(10, 15, 29, 0.82);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 20px;
    opacity: 0;
    transition: opacity .2s ease;
  }
  .modal-overlay.active, .modal-overlay.open {
    display: flex;
    opacity: 1;
    animation: modalFadeIn .22s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  }
  .modal-container,
  .modal-card {
    background: var(--bg-2);
    border: 1.5px solid var(--border-2);
    border-radius: var(--r-xl);
    max-width: 640px;
    width: 100%;
    padding: 26px;
    box-shadow: 0 25px 70px rgba(0, 0, 0, 0.6);
    max-height: 90vh;
    overflow-y: auto;
    color: var(--text);
    position: relative;
    box-sizing: border-box;
    transform: scale(0.96) translateY(8px);
    transition: transform .22s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .modal-overlay.active .modal-container,
  .modal-overlay.active .modal-card,
  .modal-overlay.open .modal-container,
  .modal-overlay.open .modal-card {
    transform: scale(1) translateY(0);
  }
  .modal-close,
  .btn-close {
    background: var(--bg-3);
    border: 1.5px solid var(--border-2);
    color: var(--text-2);
    width: 34px;
    height: 34px;
    border-radius: var(--r-sm);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 20px;
    line-height: 1;
    transition: all .2s;
    flex-shrink: 0;
  }
  .modal-close:hover,
  .btn-close:hover {
    background: var(--red-dim);
    color: var(--red);
    border-color: rgba(239, 68, 68, 0.4);
    transform: scale(1.05);
  }
  @keyframes modalFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }

  /* ─── CUSTOM PAGINATION ─── */
  .custom-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 4px 0;
  }
  .pagination-wrapper {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: var(--bg-2);
    padding: 4px 6px;
    border-radius: var(--r-sm);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
  }
  .page-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    min-width: 32px;
    height: 32px;
    padding: 0 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    color: var(--text-2);
    text-decoration: none;
    background: transparent;
    border: 1px solid transparent;
    transition: all .15s ease;
    cursor: pointer;
  }
  .page-btn:hover:not(.disabled):not(.active) {
    background: var(--bg-3);
    color: var(--text);
    border-color: var(--border-2);
  }
  .page-btn.active {
    background: #000000 !important;
    color: #FFFFFF !important;
    border-color: #000000 !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
    font-weight: 900;
  }
  .page-btn.disabled {
    color: var(--text-3);
    opacity: 0.4;
    cursor: not-allowed;
    pointer-events: none;
  }
  .page-btn.dots {
    color: var(--text-3);
    pointer-events: none;
    min-width: 20px;
    padding: 0;
    font-weight: 800;
  }

  /* ═══════════════════════════════════════════════════════════════════
     STANDARISASI DESAIN MONOKROM MODERN (REKAP PRESENSI & LAPORAN STANDARD)
     ═══════════════════════════════════════════════════════════════════ */

  /* 1. Standard Segmented Control Switcher */
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
    background: #000000 !important;
    color: #FFFFFF !important;
    font-weight: 800;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.25);
  }
  .segmented-btn.active i {
    color: #FFFFFF !important;
  }

  /* 2. Standard Period Chips / Filter Pills */
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
  .period-chip:hover { color: var(--text); background: rgba(255,255,255,0.06); border-color: var(--border-2); }
  .period-chip.active { 
    background: #000000 !important; 
    color: #FFFFFF !important; 
    border-color: #000000 !important; 
    font-weight: 800; 
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25); 
  }
  .period-chip.active i {
    color: #FFFFFF !important;
  }

  /* 3. Standard Executive KPI Grid & Cards (Monochrome) */
  .lp-kpi-grid, .kpi-grid-mono {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(145px, 1fr));
    gap: 12px;
    margin-bottom: 24px;
  }
  .lp-kpi-card, .kpi-card-mono {
    background: var(--bg-2);
    border: 1px solid var(--border);
    border-radius: var(--r-md);
    padding: 14px 16px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all .2s ease;
    box-shadow: var(--shadow-sm);
  }
  .lp-kpi-card:hover, .kpi-card-mono:hover {
    border-color: var(--border-2);
    transform: translateY(-2px);
  }
  .lp-kpi-head, .kpi-head-mono {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
  }
  .lp-kpi-title, .kpi-title-mono {
    font-size: 10.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #000000;
  }
  .lp-kpi-icon, .kpi-icon-mono {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    background: rgba(0, 0, 0, 0.06);
    color: #000000;
    border: 1px solid rgba(0, 0, 0, 0.12);
  }
  .lp-kpi-val, .kpi-val-mono {
    font-size: 24px;
    font-weight: 900;
    font-family: var(--font-mono);
    color: #000000;
    line-height: 1.1;
  }

  /* 4. Standard Document & Print Outline Buttons */
  .btn-outline-mono, .btn-doc-action {
    height: 36px;
    font-size: 12px;
    font-weight: 800;
    color: #000000;
    border: 1.5px solid #000000;
    background: var(--bg-2);
    border-radius: var(--r-sm);
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    cursor: pointer;
    transition: all .15s ease;
  }
  .btn-outline-mono:hover, .btn-doc-action:hover {
    background: rgba(0, 0, 0, 0.04);
    color: #000000;
    border-color: #000000;
    transform: translateY(-1px);
  }
  .btn-outline-mono i, .btn-doc-action i {
    color: #000000;
  }

  /* 5. Standard Monochrome Badge */
  .badge-mono {
    background: rgba(0, 0, 0, 0.06);
    color: #000000;
    border: 1px solid rgba(0, 0, 0, 0.12);
    font-size: 11px;
    font-weight: 800;
    padding: 3px 10px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  .badge-mono i {
    color: #000000;
  }

  /* 6. Standard Monochrome WhatsApp Icon */
  .bi-whatsapp {
    color: inherit;
  }
  [data-theme="light"] .bi-whatsapp {
    color: #000000 !important;
  }
  [data-theme="dark"] .bi-whatsapp {
    color: #FFFFFF !important;
  }

  /* ═══════════════════════════════════════════════════════════════════
     FINAL COMPREHENSIVE DARK MODE ADAPTIVE ENGINE (HIGHEST PRIORITY)
     ═══════════════════════════════════════════════════════════════════ */
  html[data-theme="dark"],
  [data-theme="dark"] {
    color-scheme: dark;
  }

  /* 1. Global Inversion for Any Dark Color Inline or Inherited */
  [data-theme="dark"] [style*="color:#000"],
  [data-theme="dark"] [style*="color: #000"],
  [data-theme="dark"] [style*="color:#1"],
  [data-theme="dark"] [style*="color: #1"],
  [data-theme="dark"] [style*="color:#2"],
  [data-theme="dark"] [style*="color: #2"],
  [data-theme="dark"] [style*="color:#3"],
  [data-theme="dark"] [style*="color: #3"],
  [data-theme="dark"] [style*="color:black"],
  [data-theme="dark"] [style*="color: black"] {
    color: #F9FAFB !important;
  }

  /* 2. Text, Headings & Bold */
  [data-theme="dark"] h1, [data-theme="dark"] h2, [data-theme="dark"] h3,
  [data-theme="dark"] h4, [data-theme="dark"] h5, [data-theme="dark"] h6,
  [data-theme="dark"] strong, [data-theme="dark"] b,
  [data-theme="dark"] .fw-bold, [data-theme="dark"] .font-weight-bold {
    color: #FFFFFF !important;
  }

  [data-theme="dark"] p,
  [data-theme="dark"] span:not(.badge):not(.pulse-dot):not(.badge-mono):not(.piket-status-pill):not(.table-status-pill):not([class*="status-pill"]),
  [data-theme="dark"] div:not(.badge):not(.pulse-dot):not(.avatar-circle):not(.stat-icon):not(.lp-kpi-icon):not(.kpi-icon-mono),
  [data-theme="dark"] td, [data-theme="dark"] th {
    color: var(--text);
  }

  /* 3. Section Titles & Primary Icons */
  [data-theme="dark"] .header-title h1 i,
  [data-theme="dark"] .panel-title i,
  [data-theme="dark"] h1 > i,
  [data-theme="dark"] h2 > i,
  [data-theme="dark"] h3 > i {
    color: #38BDF8 !important;
  }

  /* 4. Active Sidebar Navigation Item */
  [data-theme="dark"] .nav-item.active {
    background: #FFFFFF !important;
    color: #0F172A !important;
    border-color: #FFFFFF !important;
    font-weight: 800 !important;
    box-shadow: 0 4px 14px rgba(255, 255, 255, 0.25) !important;
  }
  [data-theme="dark"] .nav-item.active i,
  [data-theme="dark"] .nav-item.active .nav-icon,
  [data-theme="dark"] .nav-item.active .nav-text {
    color: #0F172A !important;
    font-weight: 800 !important;
  }
  [data-theme="dark"] .nav-item:hover:not(.active) {
    background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important;
  }
  [data-theme="dark"] .nav-item:hover:not(.active) i {
    color: #38BDF8 !important;
  }

  /* 5. Sidebar Pin Button */
  [data-theme="dark"] .sidebar-pin-btn {
    background: var(--bg-3) !important;
    border-color: var(--border-2) !important;
    color: var(--text) !important;
  }
  [data-theme="dark"][data-sidebar-mode="pinned"] .sidebar-pin-btn,
  [data-theme="dark"] .sidebar-pin-btn:hover {
    background: #FFFFFF !important;
    color: #0F172A !important;
    border-color: #FFFFFF !important;
  }

  /* 6. Solid Buttons (Black -> High Contrast White in Dark Mode) */
  [data-theme="dark"] .btn-gold,
  [data-theme="dark"] .btn-primary,
  [data-theme="dark"] button[style*="background:#000"],
  [data-theme="dark"] button[style*="background: #000"],
  [data-theme="dark"] a[style*="background:#000"],
  [data-theme="dark"] a[style*="background: #000"],
  [data-theme="dark"] .btn[style*="background:#000"],
  [data-theme="dark"] .btn[style*="background: #000"],
  [data-theme="dark"] [style*="background:#000000"],
  [data-theme="dark"] [style*="background: #000000"] {
    background: #FFFFFF !important;
    color: #0F172A !important;
    border-color: #FFFFFF !important;
    font-weight: 800 !important;
  }
  [data-theme="dark"] .btn-gold *,
  [data-theme="dark"] .btn-primary *,
  [data-theme="dark"] button[style*="background:#000"] *,
  [data-theme="dark"] button[style*="background: #000"] *,
  [data-theme="dark"] a[style*="background:#000"] *,
  [data-theme="dark"] a[style*="background: #000"] * {
    color: #0F172A !important;
  }
  [data-theme="dark"] .btn-gold:hover,
  [data-theme="dark"] .btn-primary:hover,
  [data-theme="dark"] button[style*="background:#000"]:hover,
  [data-theme="dark"] a[style*="background:#000"]:hover {
    background: #E2E8F0 !important;
    border-color: #E2E8F0 !important;
    color: #0F172A !important;
  }

  /* 7. Outline & Document Action Buttons */
  [data-theme="dark"] .btn-outline,
  [data-theme="dark"] .btn-outline-mono,
  [data-theme="dark"] .btn-doc-action,
  [data-theme="dark"] [style*="border:1.5px solid #000"],
  [data-theme="dark"] [style*="border: 1.5px solid #000"],
  [data-theme="dark"] [style*="border:1px solid #000"],
  [data-theme="dark"] [style*="border: 1px solid #000"] {
    background: var(--bg-2) !important;
    color: #F9FAFB !important;
    border-color: rgba(255, 255, 255, 0.28) !important;
  }
  [data-theme="dark"] .btn-outline *,
  [data-theme="dark"] .btn-outline-mono *,
  [data-theme="dark"] .btn-doc-action * {
    color: #F9FAFB !important;
  }
  [data-theme="dark"] .btn-outline:hover,
  [data-theme="dark"] .btn-outline-mono:hover,
  [data-theme="dark"] .btn-doc-action:hover {
    background: var(--bg-3) !important;
    border-color: #FFFFFF !important;
    color: #FFFFFF !important;
  }
  [data-theme="dark"] .btn-outline:hover *,
  [data-theme="dark"] .btn-outline-mono:hover *,
  [data-theme="dark"] .btn-doc-action:hover * {
    color: #FFFFFF !important;
  }

  /* 8. Monochrome Executive KPI Cards (Laporan / Dashboard) */
  [data-theme="dark"] .lp-kpi-card,
  [data-theme="dark"] .kpi-card-mono {
    background: var(--bg-2) !important;
    border-color: var(--border-2) !important;
  }
  [data-theme="dark"] .lp-kpi-title,
  [data-theme="dark"] .kpi-title-mono {
    color: #CBD5E1 !important;
  }
  [data-theme="dark"] .lp-kpi-val,
  [data-theme="dark"] .kpi-val-mono {
    color: #FFFFFF !important;
  }
  [data-theme="dark"] .lp-kpi-icon,
  [data-theme="dark"] .kpi-icon-mono {
    background: rgba(255, 255, 255, 0.08) !important;
    border: 1px solid rgba(255, 255, 255, 0.18) !important;
    color: #38BDF8 !important;
  }

  /* 9. Segmented Tabs, Filter Pills & Period Chips */
  [data-theme="dark"] .segmented-btn.active,
  [data-theme="dark"] .period-chip.active,
  [data-theme="dark"] .filter-pill.active {
    background: #FFFFFF !important;
    color: #0F172A !important;
    border-color: #FFFFFF !important;
    font-weight: 800 !important;
    box-shadow: 0 2px 10px rgba(255, 255, 255, 0.2) !important;
  }
  [data-theme="dark"] .segmented-btn.active *,
  [data-theme="dark"] .period-chip.active *,
  [data-theme="dark"] .filter-pill.active * {
    color: #0F172A !important;
  }
  [data-theme="dark"] .segmented-btn:hover:not(.active),
  [data-theme="dark"] .period-chip:hover:not(.active),
  [data-theme="dark"] .filter-pill:hover:not(.active) {
    background: var(--bg-3) !important;
    color: #FFFFFF !important;
    border-color: rgba(255, 255, 255, 0.3) !important;
  }
  [data-theme="dark"] .piket-tab-btn.active,
  [data-theme="dark"] .tab-btn.active {
    color: #FFFFFF !important;
    border-bottom-color: #38BDF8 !important;
    font-weight: 800 !important;
  }

  /* 10. Badges & Status Chips */
  [data-theme="dark"] .badge,
  [data-theme="dark"] .badge-mono,
  [data-theme="dark"] [style*="background:rgba(0,0,0,0.06)"],
  [data-theme="dark"] [style*="background: rgba(0,0,0,0.06)"],
  [data-theme="dark"] [style*="background:rgba(0, 0, 0, 0.06)"],
  [data-theme="dark"] [style*="background: rgba(0, 0, 0, 0.06)"],
  [data-theme="dark"] [style*="background:rgba(0,0,0,0.08)"],
  [data-theme="dark"] [style*="background: rgba(0,0,0,0.08)"] {
    background: rgba(255, 255, 255, 0.10) !important;
    color: #F9FAFB !important;
    border: 1px solid rgba(255, 255, 255, 0.22) !important;
  }
  [data-theme="dark"] .badge-mono i,
  [data-theme="dark"] .badge i {
    color: #38BDF8 !important;
  }

  /* 11. Tables */
  [data-theme="dark"] table,
  [data-theme="dark"] .data-table {
    color: #F9FAFB !important;
  }
  [data-theme="dark"] th,
  [data-theme="dark"] .data-table th {
    background: #1F2937 !important;
    color: #E5E7EB !important;
    border-bottom: 2px solid rgba(255, 255, 255, 0.20) !important;
    font-weight: 800 !important;
  }
  [data-theme="dark"] td,
  [data-theme="dark"] .data-table td {
    border-bottom: 1px solid rgba(255, 255, 255, 0.10) !important;
    color: #F9FAFB !important;
  }
  [data-theme="dark"] tr:hover td,
  [data-theme="dark"] .data-table tr:hover td {
    background: rgba(255, 255, 255, 0.04) !important;
  }

  /* 12. Inputs, Selects, Textareas, Date Pickers */
  [data-theme="dark"] input,
  [data-theme="dark"] select,
  [data-theme="dark"] textarea {
    background: #1F2937 !important;
    color: #F9FAFB !important;
    border: 1.5px solid rgba(255, 255, 255, 0.22) !important;
  }
  [data-theme="dark"] input:focus,
  [data-theme="dark"] select:focus,
  [data-theme="dark"] textarea:focus {
    border-color: #38BDF8 !important;
    box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.25) !important;
  }
  [data-theme="dark"] input::placeholder,
  [data-theme="dark"] textarea::placeholder {
    color: #9CA3AF !important;
  }
  [data-theme="dark"] select option {
    background: #111827 !important;
    color: #F9FAFB !important;
  }
  [data-theme="dark"] input[type="date"]::-webkit-calendar-picker-indicator,
  [data-theme="dark"] input[type="time"]::-webkit-calendar-picker-indicator {
    filter: invert(1) brightness(1.6);
    cursor: pointer;
  }
  [data-theme="dark"] label,
  [data-theme="dark"] .form-label,
  [data-theme="dark"] .form-group label {
    color: #E5E7EB !important;
    font-weight: 700 !important;
  }

  /* 13. Panels, Modals, Dropdowns & Popovers */
  [data-theme="dark"] .panel,
  [data-theme="dark"] .card,
  [data-theme="dark"] .stat-card,
  [data-theme="dark"] .piket-hero,
  [data-theme="dark"] .pm-panel,
  [data-theme="dark"] .piket-stat-card,
  [data-theme="dark"] .siswa-stat-card,
  [data-theme="dark"] .guru-stat-card,
  [data-theme="dark"] .kpi-mini-card,
  [data-theme="dark"] .day-column,
  [data-theme="dark"] .piket-day-col,
  [data-theme="dark"] .piket-card-item {
    background: var(--bg-2) !important;
    border-color: var(--border-2) !important;
  }
  [data-theme="dark"] .day-column.today-column,
  [data-theme="dark"] .piket-day-col.today {
    border-color: #38BDF8 !important;
    background: linear-gradient(180deg, rgba(56, 189, 248, 0.08) 0%, var(--bg-2) 100%) !important;
    box-shadow: 0 0 18px rgba(56, 189, 248, 0.20) !important;
  }
  [data-theme="dark"] .modal-content,
  [data-theme="dark"] .modal-box,
  [data-theme="dark"] .modal-panel,
  [data-theme="dark"] .acct-dropdown,
  [data-theme="dark"] .pm-search-dropdown {
    background: #111827 !important;
    border: 1.5px solid rgba(255, 255, 255, 0.22) !important;
    color: #F9FAFB !important;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.85) !important;
  }
  [data-theme="dark"] .acct-dropdown-name {
    color: #FFFFFF !important;
  }
  [data-theme="dark"] .acct-dropdown-item {
    color: #D1D5DB !important;
  }
  [data-theme="dark"] .acct-dropdown-item:hover {
    background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important;
  }
  [data-theme="dark"] .pm-item:hover,
  [data-theme="dark"] .pm-item.active {
    background: rgba(255, 255, 255, 0.08) !important;
  }

  /* 14. Avatar Circle & Stat Icons */
  [data-theme="dark"] .avatar-circle span,
  [data-theme="dark"] .stat-icon,
  [data-theme="dark"] .siswa-stat-icon,
  [data-theme="dark"] .guru-stat-icon {
    background: rgba(255, 255, 255, 0.10) !important;
    border: 1px solid rgba(255, 255, 255, 0.22) !important;
    color: #FFFFFF !important;
  }

  /* 15. Pulse Dot */
  [data-theme="dark"] .pulse-dot {
    background: #38BDF8 !important;
    box-shadow: 0 0 10px rgba(56, 189, 248, 0.7) !important;
  }
</style>
