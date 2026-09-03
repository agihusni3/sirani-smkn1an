<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Smart Gate Presensi RFID &amp; Barcode — SMKN 1 Air Naningan</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script>
    (function() {
      const saved = localStorage.getItem('smkn1_theme') || 'dark';
      document.documentElement.setAttribute('data-theme', saved);
    })();
  </script>
  <style>
    :root, [data-theme="light"] {
      --bg: #F8FAFC;
      --bg-gradient: radial-gradient(circle at 50% -20%, #E2E8F0 0%, #F8FAFC 100%);
      --card-bg: rgba(255, 255, 255, 0.88);
      --card-border: rgba(15, 23, 42, 0.08);
      --card-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.07);
      --surface: rgba(15, 23, 42, 0.03);
      --surface-2: rgba(15, 23, 42, 0.05);
      --text-main: #0F172A;
      --text-sub: #475569;
      --text-muted: #94A3B8;
      --emerald: #10B981;
      --emerald-glow: rgba(16, 185, 129, 0.15);
      --amber: #F59E0B;
      --amber-glow: rgba(245, 158, 11, 0.15);
      --cyan: #06B6D4;
      --cyan-glow: rgba(6, 182, 212, 0.15);
      --rose: #F43F5E;
      --rose-glow: rgba(244, 63, 94, 0.15);
      --scanner-ring: rgba(16, 185, 129, 0.35);
      --laser-color: #10B981;
      --font-main: 'Plus Jakarta Sans', sans-serif;
      --font-mono: 'JetBrains Mono', monospace;
    }

    [data-theme="dark"] {
      --bg: #070B14;
      --bg-gradient: radial-gradient(circle at 50% -20%, #111C35 0%, #070B14 100%);
      --card-bg: rgba(13, 20, 34, 0.85);
      --card-border: rgba(255, 255, 255, 0.08);
      --card-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
      --surface: rgba(255, 255, 255, 0.04);
      --surface-2: rgba(255, 255, 255, 0.07);
      --text-main: #F8FAFC;
      --text-sub: #94A3B8;
      --text-muted: #64748B;
      --emerald: #10B981;
      --emerald-glow: rgba(16, 185, 129, 0.2);
      --amber: #F59E0B;
      --amber-glow: rgba(245, 158, 11, 0.2);
      --cyan: #38BDF8;
      --cyan-glow: rgba(56, 189, 248, 0.2);
      --rose: #FB7185;
      --rose-glow: rgba(251, 113, 133, 0.2);
      --scanner-ring: rgba(16, 185, 129, 0.4);
      --laser-color: #38BDF8;
      --font-main: 'Plus Jakarta Sans', sans-serif;
      --font-mono: 'JetBrains Mono', monospace;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      background: var(--bg);
      background-image: var(--bg-gradient);
      color: var(--text-main);
      font-family: var(--font-main);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      overflow-x: hidden;
      user-select: none;
      transition: background .25s ease, color .25s ease;
    }

    /* Ambient Background Elements */
    .ambient-glow {
      position: fixed;
      pointer-events: none;
      z-index: 0;
      border-radius: 50%;
      filter: blur(140px);
      opacity: 0.22;
    }
    .ambient-glow-1 { width: 600px; height: 600px; background: #06B6D4; top: -180px; left: -150px; }
    .ambient-glow-2 { width: 550px; height: 550px; background: #10B981; bottom: -150px; right: -120px; }
    .grid-overlay {
      position: fixed; inset: 0; z-index: 0; pointer-events: none;
      background-image: linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
      background-size: 40px 40px;
    }

    /* Header Navigation */
    header {
      position: relative;
      z-index: 10;
      padding: 14px 28px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      border-bottom: 1px solid var(--card-border);
      background: var(--card-bg);
      backdrop-filter: blur(16px);
      box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    }
    .brand { display: flex; align-items: center; gap: 12px; }
    .brand-logo-wrap {
      width: 44px; height: 44px;
      border-radius: 12px;
      background: var(--surface);
      border: 1px solid var(--card-border);
      padding: 4px;
      display: flex; align-items: center; justify-content: center;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .brand-logo-wrap img { width: 100%; height: 100%; object-fit: contain; }
    .brand-title { font-size: 15.5px; font-weight: 900; letter-spacing: -0.3px; color: var(--text-main); }
    .brand-sub { font-size: 11.5px; color: var(--text-muted); font-weight: 600; display: flex; align-items: center; gap: 6px; }

    .header-status-badge {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      padding: 5px 12px;
      border-radius: 100px;
      font-size: 11.5px;
      font-weight: 800;
      font-family: var(--font-mono);
      letter-spacing: 0.3px;
    }
    .header-status-badge.open {
      background: var(--emerald-glow);
      color: var(--emerald);
      border: 1px solid rgba(16,185,129,0.3);
    }
    .header-status-badge.closed {
      background: var(--rose-glow);
      color: var(--rose);
      border: 1px solid rgba(244,63,94,0.3);
    }

    .pulse-dot {
      width: 7px; height: 7px; border-radius: 50%;
      background: currentColor;
      box-shadow: 0 0 10px currentColor;
      animation: pulseGlow 1.8s infinite;
    }
    @keyframes pulseGlow {
      0% { opacity: 1; transform: scale(1); }
      50% { opacity: 0.4; transform: scale(0.85); }
      100% { opacity: 1; transform: scale(1); }
    }

    .header-right { display: flex; align-items: center; gap: 12px; }
    .live-clock-pill {
      background: var(--surface);
      border: 1px solid var(--card-border);
      border-radius: 10px;
      padding: 6px 14px;
      text-align: right;
    }
    .clock-hms { font-family: var(--font-mono); font-size: 15.5px; font-weight: 900; color: var(--text-main); letter-spacing: 0.5px; }
    .clock-ymd { font-size: 11px; color: var(--text-muted); font-weight: 600; }

    .action-btn {
      width: 38px; height: 38px;
      border-radius: 10px;
      background: var(--surface);
      border: 1px solid var(--card-border);
      color: var(--text-main);
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; font-size: 15px;
      transition: all .2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .action-btn:hover {
      background: var(--surface-2);
      border-color: rgba(255,255,255,0.25);
      transform: translateY(-1px);
    }

    /* Main Kiosk Area */
    main {
      position: relative;
      z-index: 10;
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
    }

    .kiosk-grid {
      width: 100%;
      max-width: 1080px;
      display: grid;
      grid-template-columns: 1fr 1.15fr;
      gap: 24px;
    }
    @media (max-width: 920px) {
      .kiosk-grid { grid-template-columns: 1fr; }
    }

    /* Common Card Styling */
    .kiosk-card {
      background: var(--card-bg);
      backdrop-filter: blur(20px);
      border: 1px solid var(--card-border);
      border-radius: 24px;
      padding: 30px;
      box-shadow: var(--card-shadow);
      display: flex;
      flex-direction: column;
      position: relative;
      overflow: hidden;
      transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .kiosk-card:hover {
      border-color: rgba(255,255,255,0.14);
    }

    /* ─── SCANNER HOLOGRAPHIC TARGET ZONE ─── */
    .tap-zone {
      align-items: center;
      justify-content: center;
      text-align: center;
    }

    /* Cybernetic Scanner Frame */
    .scanner-holo-portal {
      width: 170px; height: 170px;
      position: relative;
      display: flex; align-items: center; justify-content: center;
      margin-bottom: 24px;
    }

    /* Rotating Outer Ring */
    .holo-ring-outer {
      position: absolute; inset: 0;
      border-radius: 50%;
      border: 2px dashed var(--scanner-ring);
      animation: rotateRing 14s linear infinite;
    }
    @keyframes rotateRing {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }

    /* Middle Breathing Circle */
    .holo-ring-inner {
      position: absolute; inset: 12px;
      border-radius: 50%;
      background: radial-gradient(circle, var(--emerald-glow) 0%, transparent 70%);
      border: 1px solid rgba(16, 185, 129, 0.25);
      animation: breatheGlow 3s ease-in-out infinite alternate;
    }
    @keyframes breatheGlow {
      0% { transform: scale(0.96); opacity: 0.6; }
      100% { transform: scale(1.04); opacity: 1; }
    }

    /* Cyber Corner Brackets */
    .holo-reticle {
      position: absolute; inset: 26px;
      border-radius: 20px;
      border: 1px solid rgba(255, 255, 255, 0.08);
      overflow: hidden;
      display: flex; align-items: center; justify-content: center;
      background: var(--surface);
    }
    
    /* Animated Laser Scanner Line */
    .laser-beam {
      position: absolute;
      left: 0; right: 0; height: 2px;
      background: linear-gradient(90deg, transparent, var(--laser-color), transparent);
      box-shadow: 0 0 14px var(--laser-color);
      animation: scanSweep 2.4s ease-in-out infinite alternate;
      z-index: 2;
    }
    @keyframes scanSweep {
      0% { top: 8%; opacity: 0.2; }
      50% { opacity: 1; }
      100% { top: 92%; opacity: 0.2; }
    }

    /* Iconic Glyph in Center */
    .holo-icon {
      font-size: 50px;
      color: var(--text-main);
      z-index: 3;
      filter: drop-shadow(0 0 10px rgba(16,185,129,0.3));
      transition: all .25s ease;
    }

    .tap-title-big {
      font-size: 21px;
      font-weight: 900;
      letter-spacing: -0.4px;
      color: var(--text-main);
      margin-bottom: 8px;
    }
    .tap-desc-txt {
      font-size: 13px;
      color: var(--text-sub);
      max-width: 320px;
      line-height: 1.55;
    }

    /* Protocol Pills */
    .protocol-chips {
      margin-top: 22px;
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      justify-content: center;
    }
    .protocol-chip {
      padding: 5px 11px;
      border-radius: 8px;
      background: var(--surface);
      border: 1px solid var(--card-border);
      font-size: 11px;
      font-weight: 800;
      font-family: var(--font-mono);
      color: var(--text-sub);
      display: inline-flex;
      align-items: center;
      gap: 6px;
      letter-spacing: 0.2px;
    }

    /* Live Sensor Radar Status */
    .sensor-live-bar {
      margin-top: 24px;
      padding: 8px 16px;
      border-radius: 100px;
      background: var(--surface);
      border: 1px solid var(--card-border);
      font-size: 11.5px;
      font-weight: 800;
      color: var(--emerald);
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-family: var(--font-mono);
    }

    /* ─── RESULT & MONITOR FEED CARD ─── */
    .monitor-card {
      justify-content: space-between;
    }
    .monitor-card.success-border { border-color: rgba(16,185,129,0.45); }
    .monitor-card.warning-border { border-color: rgba(245,158,11,0.45); }
    .monitor-card.error-border { border-color: rgba(244,63,94,0.45); }

    .card-top-hud {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-bottom: 14px;
      border-bottom: 1px solid var(--card-border);
      margin-bottom: 16px;
    }
    .card-top-title {
      font-size: 11.5px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      color: var(--text-muted);
      display: flex;
      align-items: center;
      gap: 6px;
    }

    /* Standby State Graphic */
    .standby-state-box {
      padding: 24px 0;
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 190px;
    }
    .standby-icon-halo {
      width: 70px; height: 70px;
      border-radius: 20px;
      background: var(--surface);
      border: 1px solid var(--card-border);
      display: flex; align-items: center; justify-content: center;
      font-size: 32px;
      color: var(--text-muted);
      margin-bottom: 12px;
    }
    .standby-title { font-size: 15px; font-weight: 800; color: var(--text-main); }
    .standby-desc { font-size: 12px; color: var(--text-sub); max-width: 320px; line-height: 1.45; margin-top: 4px; }

    /* Live Daily Counters in Standby */
    .daily-stats-row {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 8px;
      width: 100%;
      margin-top: 16px;
    }
    .stat-pill {
      background: var(--surface);
      border: 1px solid var(--card-border);
      border-radius: 12px;
      padding: 10px 8px;
      text-align: center;
    }
    .stat-pill-val { font-size: 16px; font-weight: 900; font-family: var(--font-mono); }
    .stat-pill-label { font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.4px; margin-top: 2px; }

    /* Active Result Presentation */
    .person-result-wrap {
      display: flex;
      align-items: center;
      gap: 20px;
      padding: 10px 0 16px 0;
    }
    .person-photo-frame {
      width: 90px; height: 90px;
      border-radius: 22px;
      background: var(--surface);
      border: 2.5px solid var(--emerald);
      box-shadow: 0 0 20px var(--emerald-glow);
      object-fit: cover;
      flex-shrink: 0;
      transition: border-color .3s, box-shadow .3s;
    }
    .person-name-big {
      font-size: 22px;
      font-weight: 900;
      letter-spacing: -0.4px;
      color: var(--text-main);
      line-height: 1.2;
    }
    .person-class-sub {
      font-size: 13.5px;
      color: var(--text-sub);
      font-weight: 700;
      margin-top: 5px;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .person-nisn-badge {
      display: inline-block;
      padding: 3px 8px;
      border-radius: 6px;
      background: var(--surface-2);
      border: 1px solid var(--card-border);
      font-family: var(--font-mono);
      font-size: 11px;
      font-weight: 800;
      color: var(--text-main);
      margin-top: 6px;
    }

    .badge-status-highlight {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 18px;
      border-radius: 12px;
      font-size: 13.5px;
      font-weight: 900;
      font-family: var(--font-mono);
      letter-spacing: 0.5px;
      text-transform: uppercase;
      margin-top: 10px;
    }
    .badge-status-highlight.hadir {
      background: var(--emerald-glow);
      color: var(--emerald);
      border: 1.5px solid var(--emerald);
    }
    .badge-status-highlight.terlambat {
      background: var(--amber-glow);
      color: var(--amber);
      border: 1.5px solid var(--amber);
    }
    .badge-status-highlight.pulang {
      background: var(--cyan-glow);
      color: var(--cyan);
      border: 1.5px solid var(--cyan);
    }
    .badge-status-highlight.error {
      background: var(--rose-glow);
      color: var(--rose);
      border: 1.5px solid var(--rose);
    }

    .feedback-msg {
      font-size: 13.5px;
      font-weight: 700;
      margin-top: 12px;
      padding: 10px 14px;
      border-radius: 10px;
      background: var(--surface);
      border: 1px solid var(--card-border);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* Recent Scans Mini List */
    .recent-feed-section {
      border-top: 1px solid var(--card-border);
      padding-top: 14px;
      margin-top: auto;
    }
    .recent-feed-head {
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      color: var(--text-muted);
      margin-bottom: 8px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .recent-feed-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 7px 0;
      border-bottom: 1px solid var(--card-border);
      font-size: 12.5px;
      animation: fadeInSlide .3s ease-out;
    }
    .recent-feed-item:last-child { border-bottom: none; }
    @keyframes fadeInSlide {
      from { opacity: 0; transform: translateY(-4px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Footer */
    footer {
      position: relative;
      z-index: 10;
      padding: 12px 28px;
      background: var(--card-bg);
      border-top: 1px solid var(--card-border);
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 12px;
      color: var(--text-muted);
      backdrop-filter: blur(16px);
    }

    /* Hidden scanner input capturing scanner keypresses */
    #rfidInput {
      position: absolute;
      opacity: 0;
      pointer-events: none;
      top: -9999px;
    }
  </style>
</head>
<body>

<div class="ambient-glow ambient-glow-1"></div>
<div class="ambient-glow ambient-glow-2"></div>
<div class="grid-overlay"></div>

<!-- HEADER NAVIGATION -->
<header>
  <div class="brand">
    <div class="brand-logo-wrap">
      <img src="/img/logo.png" alt="Logo SMKN 1 Air Naningan" />
    </div>
    <div>
      <div class="brand-title">SMKN 1 AIR NANINGAN</div>
      <div class="brand-sub">
        <span>Smart Gate System</span>
        <span>•</span>
        @if($jadwal && $jadwal->is_sesi_buka)
          <span class="header-status-badge open">
            <span class="pulse-dot"></span> GERBANG DIBUKA
          </span>
        @else
          <span class="header-status-badge closed">
            <span class="pulse-dot"></span> GERBANG DITUTUP
          </span>
        @endif
      </div>
    </div>
  </div>

  <div class="header-right">
    <div class="live-clock-pill">
      <div class="clock-hms" id="clockTime">--:--:-- WIB</div>
      <div class="clock-ymd" id="clockDate">{{ $hariIni }}</div>
    </div>

    <!-- Voice Announcement Toggle -->
    <button type="button" class="action-btn" id="btnVoiceToggle" onclick="toggleVoice()" title="Suara Pengumuman Suara (Aktif/Mute)">
      <i class="bi bi-volume-up-fill" id="voiceIcon"></i>
    </button>

    <!-- Theme Switcher -->
    <button type="button" class="action-btn" onclick="toggleTheme()" title="Ganti Tema (Gelap/Terang)">
      <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
    </button>

    <!-- Fullscreen -->
    <button type="button" class="action-btn" onclick="toggleFullscreen()" title="Layar Penuh (F11)">
      <i class="bi bi-fullscreen" id="fsIcon"></i>
    </button>

    <!-- Back to Dashboard -->
    <a href="/dashboard" class="action-btn" title="Kembali ke Dasbor" style="text-decoration:none;">
      <i class="bi bi-speedometer2"></i>
    </a>
  </div>
</header>

<!-- MAIN KIOSK INTERFACE -->
<main onclick="focusScanner()">
  <input type="text" id="rfidInput" autofocus autocomplete="off" />

  <div class="kiosk-grid">

    <!-- LEFT: SCANNER SENSOR TARGET ZONE -->
    <div class="kiosk-card tap-zone">
      
      <!-- Holographic Reticle Portal -->
      <div class="scanner-holo-portal" id="holoPortal">
        <div class="holo-ring-outer"></div>
        <div class="holo-ring-inner"></div>
        <div class="holo-reticle">
          <div class="laser-beam"></div>
          <i class="bi bi-upc-scan holo-icon" id="holoIcon"></i>
        </div>
      </div>

      <div class="tap-title-big" id="tapTitle">Tempelkan Kartu / Scan Barcode</div>
      <div class="tap-desc-txt" id="tapDesc">
        Dekatkan Kartu Pelajar RFID / e-KTP ke sensor atau arahkan Barcode NISN siswa ke pemindai scanner.
      </div>

      <!-- Protocol Supported Chips -->
      <div class="protocol-chips">
        <span class="protocol-chip"><i class="bi bi-credit-card-2-front"></i> RFID 13.56 MHz / 125 kHz</span>
        <span class="protocol-chip"><i class="bi bi-upc"></i> Barcode 1D / 2D</span>
        <span class="protocol-chip"><i class="bi bi-qr-code"></i> QR Code NISN</span>
      </div>

      <!-- Real-Time Radar Status Indicator -->
      <div class="sensor-live-bar" id="sensorIndicator">
        <span class="pulse-dot"></span>
        <span>PEMINDAI SIAP MEMINDAI • RESPON &lt; 0.1s</span>
      </div>
    </div>

    <!-- RIGHT: REAL-TIME SCAN MONITOR & RECENT FEED -->
    <div class="kiosk-card monitor-card" id="monitorCard">
      
      <div>
        <div class="card-top-hud">
          <span class="card-top-title">
            <i class="bi bi-cpu-fill" style="color:var(--cyan);"></i> Monitor Hasil Pemindaian
          </span>
          <span id="badgeAction" class="badge-status-highlight hadir" style="display:none; margin:0;"></span>
        </div>

        <!-- Standby Idle State -->
        <div class="standby-state-box" id="standbyBox">
          <div class="standby-icon-halo">
            <i class="bi bi-person-bounding-box"></i>
          </div>
          <div class="standby-title">Menunggu Kartu atau Barcode</div>
          <div class="standby-desc">
            Data kehadiran siswa, rombel, jam tiba, dan notifikasi ke orang tua akan langsung tampil di sini seketika.
          </div>

          <!-- Quick Statistics Counters -->
          <div class="daily-stats-row">
            <div class="stat-pill">
              <div class="stat-pill-val" style="color:var(--emerald);">{{ $totalHadirHariIni ?? 0 }}</div>
              <div class="stat-pill-label">Hadir Tepat</div>
            </div>
            <div class="stat-pill">
              <div class="stat-pill-val" style="color:var(--amber);">{{ $totalTerlambatHariIni ?? 0 }}</div>
              <div class="stat-pill-label">Terlambat</div>
            </div>
            <div class="stat-pill">
              <div class="stat-pill-val" style="color:var(--cyan);">{{ $totalPulangHariIni ?? 0 }}</div>
              <div class="stat-pill-label">Sudah Pulang</div>
            </div>
          </div>
        </div>

        <!-- Active Result Data State -->
        <div class="person-result-wrap" id="personResultWrap" style="display:none;">
          <img src="/img/user-default.png" alt="Foto Siswa" class="person-photo-frame" id="personPhoto" />
          <div>
            <div class="person-name-big" id="personName">-</div>
            <div class="person-class-sub" id="personSub">-</div>
            <div class="person-nisn-badge" id="personTag">-</div>
          </div>
        </div>

        <!-- Feedback Notification Bar -->
        <div id="resultMessage" class="feedback-msg" style="display:none;">
          <i class="bi bi-info-circle-fill"></i>
          <span id="resultMessageTxt">Presensi berhasil dicatat.</span>
        </div>
      </div>

      <!-- RECENT SCANS LOG FEED -->
      <div class="recent-feed-section">
        <div class="recent-feed-head">
          <span><i class="bi bi-clock-history"></i> Log Presensi Terkini</span>
          <span style="font-family:var(--font-mono); font-size:10px; color:var(--text-muted);">Real-time Sync</span>
        </div>
        <div id="recentList">
          @if(isset($initialRecentScans) && $initialRecentScans->isNotEmpty())
            @foreach($initialRecentScans as $rec)
              @php
                $p = $rec->pemilik;
                $nama = $p->nama ?? 'Pengguna';
                $sub = ($rec->pemilik_type === 'siswa') ? ($p->siswaRombel?->rombel?->nama_rombel ?? 'Siswa') : 'Guru';
                $jam = $rec->jam_pulang ?: $rec->jam_masuk;
                $st = $rec->status ?: 'hadir';
              @endphp
              <div class="recent-feed-item">
                <div>
                  <strong style="color:var(--text-main);">{{ $nama }}</strong>
                  <span style="color:var(--text-muted); font-size:11px;"> • {{ $sub }}</span>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                  <span class="person-nisn-badge" style="padding:2px 6px; font-size:10px; text-transform:uppercase;">{{ $st }}</span>
                  <span style="font-family:var(--font-mono); font-size:11px; color:var(--text-muted);">{{ substr($jam ?? '', 0, 5) }} WIB</span>
                </div>
              </div>
            @endforeach
          @else
            <div style="font-size:12px; color:var(--text-muted); padding:4px 0;">Menunggu pemindaian presensi pertama hari ini...</div>
          @endif
        </div>
      </div>

    </div>

  </div>
</main>

<!-- FOOTER INFORMATION -->
<footer>
  <div>
    <strong>SMKN 1 Air Naningan</strong> &bull; SIRANI Smart Gate Terminal &bull; T.A. 2026/2027
  </div>
  <div>
    Toleransi Masuk: <strong>{{ $jadwal ? substr($jadwal->jam_masuk_toleransi, 0, 5) : '07:15' }} WIB</strong> &bull;
    Tutup Gerbang: <strong>{{ $jadwal ? substr($jadwal->jam_tutup_gerbang ?? '17:00', 0, 5) : '17:00' }} WIB</strong>
  </div>
</footer>

<script>
  let scannerBuffer = '';
  let scannerTimeout = null;
  let voiceEnabled = true;
  const recentItems = [];

  function focusScanner() {
    const inp = document.getElementById('rfidInput');
    if (inp) inp.focus();
  }

  // Live Digital Clock Updater
  function updateClock() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    const s = String(now.getSeconds()).padStart(2, '0');
    const el = document.getElementById('clockTime');
    if (el) el.textContent = `${h}:${m}:${s} WIB`;
  }
  setInterval(updateClock, 1000);
  updateClock();

  // Fullscreen Mode
  function toggleFullscreen() {
    if (!document.fullscreenElement) {
      document.documentElement.requestFullscreen().catch(() => {});
      document.getElementById('fsIcon').className = 'bi bi-fullscreen-exit';
    } else {
      document.exitFullscreen().catch(() => {});
      document.getElementById('fsIcon').className = 'bi bi-fullscreen';
    }
  }

  // Theme Mode
  function toggleTheme() {
    const cur = document.documentElement.getAttribute('data-theme') || 'dark';
    const next = cur === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('smkn1_theme', next);
    document.getElementById('themeIcon').className = next === 'dark' ? 'bi bi-moon-stars-fill' : 'bi bi-sun-fill';
  }

  // Voice Toggle
  function toggleVoice() {
    voiceEnabled = !voiceEnabled;
    const icon = document.getElementById('voiceIcon');
    if (icon) {
      icon.className = voiceEnabled ? 'bi bi-volume-up-fill' : 'bi bi-volume-mute-fill';
      icon.style.color = voiceEnabled ? 'inherit' : 'var(--rose)';
    }
    if (voiceEnabled) speak('Suara panduan diaktifkan');
  }

  // Web Speech Announcement
  function speak(text) {
    if (!voiceEnabled) return;
    if ('speechSynthesis' in window) {
      try {
        window.speechSynthesis.cancel();
        const utter = new SpeechSynthesisUtterance(text);
        utter.lang = 'id-ID';
        utter.rate = 0.95;
        const voices = window.speechSynthesis.getVoices();
        const idVoice = voices.find(v => v.lang && (v.lang === 'id-ID' || v.lang.startsWith('id')));
        if (idVoice) utter.voice = idVoice;
        window.speechSynthesis.speak(utter);
      } catch (e) {}
    }
  }

  // Audio Beep Chime
  function playBeep(type = 'success') {
    try {
      const ctx = new (window.AudioContext || window.webkitAudioContext)();
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.connect(gain);
      gain.connect(ctx.destination);
      if (type === 'success') {
        osc.frequency.setValueAtTime(880, ctx.currentTime);
        osc.frequency.setValueAtTime(1320, ctx.currentTime + 0.08);
        gain.gain.setValueAtTime(0.25, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.28);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.28);
      } else {
        osc.frequency.setValueAtTime(320, ctx.currentTime);
        osc.frequency.setValueAtTime(220, ctx.currentTime + 0.12);
        gain.gain.setValueAtTime(0.35, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.32);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.32);
      }
    } catch (e) {}
  }

  // Process RFID or Barcode Code
  async function processCode(code) {
    const cleanCode = code.trim();
    if (!cleanCode || cleanCode.length < 3) return;

    const ind = document.getElementById('sensorIndicator');
    if (ind) ind.innerHTML = '<span class="pulse-dot" style="background:var(--cyan); box-shadow:0 0 10px var(--cyan);"></span><span>MEMPROSES DATA PRESENSI...</span>';

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    try {
      const res = await fetch('/api/v1/rfid-scan', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf,
        },
        body: JSON.stringify({ uid: cleanCode })
      });

      const data = await res.json();
      renderScanResult(data);
    } catch (err) {
      renderScanResult({
        success: false,
        message: 'Gagal menghubungi server presensi. Pastikan koneksi stabil.'
      });
    } finally {
      if (ind) ind.innerHTML = '<span class="pulse-dot"></span><span>PEMINDAI SIAP MEMINDAI • RESPON &lt; 0.1s</span>';
      focusScanner();
    }
  }

  // Render Result Feedback
  function renderScanResult(res) {
    const card = document.getElementById('monitorCard');
    const standby = document.getElementById('standbyBox');
    const personWrap = document.getElementById('personResultWrap');
    const badge = document.getElementById('badgeAction');
    const photo = document.getElementById('personPhoto');
    const nameEl = document.getElementById('personName');
    const subEl = document.getElementById('personSub');
    const tagEl = document.getElementById('personTag');
    const msgBox = document.getElementById('resultMessage');
    const msgTxt = document.getElementById('resultMessageTxt');

    standby.style.display = 'none';
    personWrap.style.display = 'flex';
    badge.style.display = 'inline-flex';
    msgBox.style.display = 'flex';

    if (res.success && res.data) {
      playBeep('success');
      const d = res.data;
      const st = (d.status || 'hadir').toLowerCase();

      // Border and Shadow Glow
      if (st === 'terlambat') {
        card.className = 'kiosk-card monitor-card warning-border';
        photo.style.borderColor = 'var(--amber)';
        photo.style.boxShadow = '0 0 20px var(--amber-glow)';
      } else if (st === 'pulang') {
        card.className = 'kiosk-card monitor-card success-border';
        photo.style.borderColor = 'var(--cyan)';
        photo.style.boxShadow = '0 0 20px var(--cyan-glow)';
      } else {
        card.className = 'kiosk-card monitor-card success-border';
        photo.style.borderColor = 'var(--emerald)';
        photo.style.boxShadow = '0 0 20px var(--emerald-glow)';
      }

      photo.src = d.foto || d.foto_url || '/img/user-default.png';
      nameEl.textContent = d.nama || 'Pengguna';

      const subInfo = (d.sub || d.rombel_atau_jabatan || '').trim();
      const idInfo = (d.identitas || '').trim();
      subEl.innerHTML = subInfo ? `<i class="bi bi-mortarboard-fill"></i> ${subInfo}` : (idInfo ? `NISN: ${idInfo}` : '-');

      const isGuru = (d.tipe === 'guru' || d.type === 'guru');
      const roleText = isGuru ? 'Guru / Pegawai' : 'Siswa';
      const jamText = d.jam || d.jam_masuk || d.jam_pulang || '';
      tagEl.textContent = jamText ? `${roleText} • ${jamText} WIB` : roleText;

      badge.className = 'badge-status-highlight ' + st;
      badge.innerHTML = `<i class="bi bi-check-circle-fill"></i> ${st.toUpperCase()}`;

      msgTxt.textContent = res.message || 'Presensi berhasil dicatat.';

      // Voice greeting
      const speechNama = (d.nama || '').split(',')[0].trim();
      if (st === 'terlambat') {
        speak(`Perhatian, ${speechNama}, Anda tercatat terlambat.`);
      } else if (st === 'pulang') {
        speak(`Terima kasih, ${speechNama}, presensi pulang berhasil. Hati-hati di jalan.`);
      } else {
        speak(`Selamat pagi, ${speechNama}, presensi berhasil.`);
      }

      // Prepend to recent feed
      addRecentItem(d.nama, d.sub || d.rombel_atau_jabatan || '', st, d.jam || 'Baru saja');
    } else {
      playBeep('error');
      card.className = 'kiosk-card monitor-card error-border';
      photo.src = '/img/user-default.png';
      photo.style.borderColor = 'var(--rose)';
      photo.style.boxShadow = '0 0 20px var(--rose-glow)';
      nameEl.textContent = 'Pemindaian Gagal';
      subEl.innerHTML = '<i class="bi bi-shield-x"></i> Kartu / Barcode Tidak Valid';
      tagEl.textContent = 'Akses Ditolak';
      badge.className = 'badge-status-highlight error';
      badge.innerHTML = '<i class="bi bi-x-circle-fill"></i> DITOLAK';
      msgTxt.textContent = res.message || 'Kartu belum terdaftar atau gerbang ditutup.';
      speak('Kartu tidak valid atau belum terdaftar.');
    }
  }

  function addRecentItem(nama, sub, status, time) {
    const list = document.getElementById('recentList');
    recentItems.unshift({ nama, sub, status, time });
    if (recentItems.length > 5) recentItems.pop();

    list.innerHTML = recentItems.map(item => `
      <div class="recent-feed-item">
        <div>
          <strong style="color:var(--text-main);">${item.nama}</strong>
          ${item.sub ? `<span style="color:var(--text-muted); font-size:11px;"> • ${item.sub}</span>` : ''}
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
          <span class="person-nisn-badge" style="padding:2px 6px; font-size:10px; text-transform:uppercase;">${item.status}</span>
          <span style="font-family:var(--font-mono); font-size:11px; color:var(--text-muted);">${item.time}</span>
        </div>
      </div>
    `).join('');
  }

  // Keystroke listener for USB Barcode & RFID Reader
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      if (scannerBuffer.length >= 3) {
        processCode(scannerBuffer);
        scannerBuffer = '';
      }
      return;
    }

    if (e.key.length === 1) {
      scannerBuffer += e.key;
      clearTimeout(scannerTimeout);
      scannerTimeout = setTimeout(() => {
        if (scannerBuffer.length >= 6) {
          processCode(scannerBuffer);
        }
        scannerBuffer = '';
      }, 80);
    }
  });

  document.addEventListener('DOMContentLoaded', () => {
    focusScanner();
    setInterval(focusScanner, 3000);
  });
</script>
</body>
</html>
