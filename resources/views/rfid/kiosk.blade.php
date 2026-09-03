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
      --card-bg: rgba(255, 255, 255, 0.92);
      --card-border: rgba(15, 23, 42, 0.09);
      --card-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
      --surface: rgba(15, 23, 42, 0.03);
      --surface-2: rgba(15, 23, 42, 0.06);
      --text-main: #0F172A;
      --text-sub: #475569;
      --text-muted: #94A3B8;
      --emerald: #16A34A;
      --emerald-glow: rgba(22, 163, 74, 0.18);
      --amber: #D97706;
      --amber-glow: rgba(217, 119, 6, 0.18);
      --cyan: #0284C7;
      --cyan-glow: rgba(2, 132, 199, 0.18);
      --rose: #DC2626;
      --rose-glow: rgba(220, 38, 38, 0.18);
      --scanner-ring: rgba(22, 163, 74, 0.35);
      --laser-color: #16A34A;
      --font-main: 'Plus Jakarta Sans', sans-serif;
      --font-mono: 'JetBrains Mono', monospace;
    }

    [data-theme="dark"] {
      --bg: #070B14;
      --bg-gradient: radial-gradient(circle at 50% -20%, #111C35 0%, #070B14 100%);
      --card-bg: rgba(13, 20, 34, 0.88);
      --card-border: rgba(255, 255, 255, 0.09);
      --card-shadow: 0 25px 50px -10px rgba(0, 0, 0, 0.65);
      --surface: rgba(255, 255, 255, 0.04);
      --surface-2: rgba(255, 255, 255, 0.08);
      --text-main: #F8FAFC;
      --text-sub: #94A3B8;
      --text-muted: #64748B;
      --emerald: #10B981;
      --emerald-glow: rgba(16, 185, 129, 0.22);
      --amber: #F59E0B;
      --amber-glow: rgba(245, 158, 11, 0.22);
      --cyan: #38BDF8;
      --cyan-glow: rgba(56, 189, 248, 0.22);
      --rose: #FB7185;
      --rose-glow: rgba(251, 113, 133, 0.22);
      --scanner-ring: rgba(16, 185, 129, 0.45);
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
      justify-content: space-between;
      overflow-x: hidden;
      user-select: none;
      transition: background .25s ease, color .25s ease;
    }

    /* Ambient Background Orbs */
    .ambient-glow {
      position: fixed; pointer-events: none; z-index: 0; border-radius: 50%;
      filter: blur(140px); opacity: 0.22;
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
      gap: 6px;
      padding: 4px 10px;
      border-radius: 100px;
      font-size: 11px;
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
      width: 6px; height: 6px; border-radius: 50%;
      background: currentColor;
      box-shadow: 0 0 8px currentColor;
      animation: pulseGlow 1.8s infinite;
    }
    @keyframes pulseGlow {
      0% { opacity: 1; transform: scale(1); }
      50% { opacity: 0.35; transform: scale(0.85); }
      100% { opacity: 1; transform: scale(1); }
    }

    .header-right { display: flex; align-items: center; gap: 10px; }
    .live-clock-pill {
      background: var(--surface);
      border: 1px solid var(--card-border);
      border-radius: 10px;
      padding: 6px 14px;
      text-align: right;
    }
    .clock-hms { font-family: var(--font-mono); font-size: 15px; font-weight: 900; color: var(--text-main); }
    .clock-ymd { font-size: 10.5px; color: var(--text-muted); font-weight: 600; }

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
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 24px 20px;
      max-width: 820px;
      width: 100%;
      margin: 0 auto;
    }

    /* Center Stage Container */
    .kiosk-stage-box {
      width: 100%;
      transition: all .3s ease;
    }

    /* Fade Enter Transition */
    .kios-fade-enter {
      animation: kiosFadeIn .32s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes kiosFadeIn {
      from { opacity: 0; transform: scale(0.96) translateY(6px); }
      to { opacity: 1; transform: scale(1) translateY(0); }
    }

    /* ══ STATE 1: CENTRAL SCANNER CARD ══ */
    .scanner-main-card {
      background: var(--card-bg);
      backdrop-filter: blur(20px);
      border: 1.5px solid var(--card-border);
      border-radius: 28px;
      padding: 40px 32px;
      box-shadow: var(--card-shadow);
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    /* Cybernetic Scanner Holographic Portal */
    .scanner-portal {
      width: 170px; height: 170px;
      position: relative;
      display: flex; align-items: center; justify-content: center;
      margin-bottom: 24px;
    }
    .portal-ring-outer {
      position: absolute; inset: 0;
      border-radius: 50%;
      border: 2px dashed var(--scanner-ring);
      animation: rotateRing 14s linear infinite;
    }
    @keyframes rotateRing {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    .portal-ring-inner {
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
    .portal-reticle {
      position: absolute; inset: 26px;
      border-radius: 22px;
      border: 1px solid rgba(255, 255, 255, 0.08);
      overflow: hidden;
      display: flex; align-items: center; justify-content: center;
      background: var(--surface);
    }
    .portal-laser {
      position: absolute;
      left: 0; right: 0; height: 2.5px;
      background: linear-gradient(90deg, transparent, var(--laser-color), #fff, var(--laser-color), transparent);
      box-shadow: 0 0 14px var(--laser-color);
      animation: scanSweep 2.2s ease-in-out infinite alternate;
      z-index: 2;
    }
    @keyframes scanSweep {
      0% { top: 6%; opacity: 0.3; }
      50% { opacity: 1; }
      100% { top: 94%; opacity: 0.3; }
    }
    .portal-glyph {
      font-size: 52px;
      color: var(--text-main);
      z-index: 3;
      filter: drop-shadow(0 0 12px var(--emerald-glow));
    }

    .scanner-title {
      font-size: 23px;
      font-weight: 900;
      letter-spacing: -0.4px;
      color: var(--text-main);
      margin-bottom: 8px;
    }
    .scanner-desc {
      font-size: 13.5px;
      color: var(--text-sub);
      max-width: 440px;
      line-height: 1.55;
    }

    .scanner-protocols {
      margin-top: 24px;
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      justify-content: center;
    }
    .protocol-pill {
      padding: 6px 12px;
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
    }

    .scanner-status-indicator {
      margin-top: 24px;
      padding: 8px 18px;
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

    /* ══ STATE 2: RESPONSE IDENTITAS LENGKAP (SMART GATE) ══ */
    .identity-result-card {
      width: 100%;
      background: var(--card-bg);
      backdrop-filter: blur(22px);
      border: 2px solid var(--emerald);
      border-radius: 28px;
      padding: 34px 38px;
      box-shadow: 0 25px 60px -10px rgba(0,0,0,0.4), 0 0 35px var(--emerald-glow);
      text-align: left;
      transition: all .3s ease;
    }
    .identity-result-card.status-hadir {
      border-color: var(--emerald);
      box-shadow: 0 25px 60px -10px rgba(0,0,0,0.4), 0 0 35px var(--emerald-glow);
    }
    .identity-result-card.status-terlambat {
      border-color: var(--amber);
      box-shadow: 0 25px 60px -10px rgba(0,0,0,0.4), 0 0 35px var(--amber-glow);
    }
    .identity-result-card.status-pulang {
      border-color: var(--cyan);
      box-shadow: 0 25px 60px -10px rgba(0,0,0,0.4), 0 0 35px var(--cyan-glow);
    }
    .identity-result-card.status-error {
      border-color: var(--rose);
      box-shadow: 0 25px 60px -10px rgba(0,0,0,0.4), 0 0 35px var(--rose-glow);
    }

    .identity-top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-bottom: 16px;
      border-bottom: 1px solid var(--card-border);
      margin-bottom: 22px;
    }
    .result-badge-large {
      font-family: var(--font-mono);
      font-size: 13.5px;
      font-weight: 900;
      letter-spacing: 0.6px;
      text-transform: uppercase;
      padding: 6px 18px;
      border-radius: 100px;
      display: inline-flex;
      align-items: center;
      gap: 7px;
    }
    .result-badge-large.hadir {
      background: var(--emerald-glow);
      color: var(--emerald);
      border: 1px solid var(--emerald);
    }
    .result-badge-large.terlambat {
      background: var(--amber-glow);
      color: var(--amber);
      border: 1px solid var(--amber);
    }
    .result-badge-large.pulang {
      background: var(--cyan-glow);
      color: var(--cyan);
      border: 1px solid var(--cyan);
    }
    .result-badge-large.error {
      background: var(--rose-glow);
      color: var(--rose);
      border: 1px solid var(--rose);
    }

    .identity-gate-label {
      font-size: 12px;
      font-weight: 700;
      color: var(--text-muted);
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: var(--surface);
      border: 1px solid var(--card-border);
      padding: 5px 12px;
      border-radius: 100px;
      font-family: var(--font-mono);
    }

    .identity-body {
      display: flex;
      align-items: center;
      gap: 26px;
      margin-bottom: 22px;
    }
    @media (max-width: 640px) {
      .identity-body { flex-direction: column; text-align: center; }
    }

    .identity-avatar-wrap {
      position: relative;
      width: 110px; height: 110px;
      border-radius: 24px;
      overflow: hidden;
      border: 3px solid var(--emerald);
      box-shadow: 0 0 25px var(--emerald-glow);
      flex-shrink: 0;
      background: var(--surface);
    }
    .identity-avatar-img {
      width: 100%; height: 100%;
      object-fit: cover;
      display: block;
    }

    .identity-details { flex: 1; min-width: 0; }
    .identity-name-text {
      font-size: 26px;
      font-weight: 900;
      letter-spacing: -0.4px;
      color: var(--text-main);
      line-height: 1.25;
      margin-bottom: 6px;
    }
    .identity-sub-text {
      font-size: 14.5px;
      font-weight: 700;
      color: var(--text-sub);
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .identity-time-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: var(--surface-2);
      border: 1px solid var(--card-border);
      padding: 4px 12px;
      border-radius: 8px;
      font-family: var(--font-mono);
      font-size: 12.5px;
      font-weight: 800;
      color: var(--text-main);
    }

    .identity-notification-box {
      padding: 12px 18px;
      border-radius: 12px;
      background: var(--surface);
      border: 1px solid var(--card-border);
      font-size: 13.5px;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    /* Auto Reset Countdown Bar */
    .countdown-section {
      margin-top: 22px;
      padding-top: 16px;
      border-top: 1px dashed var(--card-border);
    }
    .countdown-track {
      width: 100%;
      height: 6px;
      background: var(--surface-2);
      border-radius: 10px;
      overflow: hidden;
      margin-bottom: 8px;
    }
    .countdown-fill {
      height: 100%;
      width: 100%;
      background: var(--emerald);
      border-radius: 10px;
      transition: width 0.1s linear;
    }
    .countdown-caption {
      font-size: 12px;
      color: var(--text-muted);
      font-weight: 600;
      text-align: center;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
    }
    .countdown-caption strong {
      color: var(--emerald);
      font-family: var(--font-mono);
      font-size: 13.5px;
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

    /* Hidden USB Scanner Input */
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
    <button type="button" class="action-btn" id="btnVoiceToggle" onclick="toggleVoice()" title="Suara Pengumuman (Aktif/Mute)">
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

  <div class="kiosk-stage-box">

    <!-- ══ STATE 1: CENTRAL SCANNER CARD (STANDBY) ══ -->
    <div id="scannerState" class="scanner-main-card">
      <div class="scanner-portal">
        <div class="portal-ring-outer"></div>
        <div class="portal-ring-inner"></div>
        <div class="portal-reticle">
          <div class="portal-laser"></div>
          <i class="bi bi-upc-scan portal-glyph"></i>
        </div>
      </div>

      <h1 class="scanner-title">Tempelkan Kartu RFID / Scan Barcode</h1>
      <p class="scanner-desc">
        Dekatkan Kartu Pelajar RFID / e-KTP ke sensor pembaca atau arahkan Barcode / QR Code NISN siswa ke scanner USB.
      </p>

      <div class="scanner-status-indicator" id="scannerStatus">
        <span class="pulse-dot"></span>
        <span>PEMINDAI SIAP MENERIMA INPUT</span>
      </div>
    </div>

    <!-- ══ STATE 2: RESPONSE IDENTITAS LENGKAP (KETIKA SCAN BERHASIL) ══ -->
    <div id="responseState" class="kios-fade-enter" style="display:none;">
      <div class="identity-result-card" id="identityCard">

        {{-- Top Header Row --}}
        <div class="identity-top-bar">
          <div class="result-badge-large hadir" id="resBadge">
            <span id="resBadgeText">BERHASIL HADIR</span>
          </div>
          <div class="identity-gate-label">
            <span>Smart Gate Presensi SMKN 1 Air Naningan</span>
          </div>
        </div>

        {{-- Body: Foto & Identitas --}}
        <div class="identity-body">
          <div class="identity-avatar-wrap" id="avatarWrap">
            <img id="resPhoto" class="identity-avatar-img" src="/img/user-default.png" alt="Foto Profil" />
          </div>

          <div class="identity-details">
            <div class="identity-name-text" id="resName">-</div>
            <div class="identity-sub-text" id="resSub">
              <span id="resSubTxt">-</span>
            </div>
            <div class="identity-time-pill" id="resTimePill">
              <span id="resTimeTxt">-</span>
            </div>
          </div>
        </div>

        {{-- Pesan Notifikasi --}}
        <div class="identity-notification-box" id="resMessageBox">
          <span id="resMessageTxt">Presensi berhasil dicatat. Notifikasi otomatis dikirimkan ke orang tua.</span>
        </div>

        {{-- Auto-Reset Countdown Bar --}}
        <div class="countdown-section">
          <div class="countdown-track">
            <div class="countdown-fill" id="countdownFill"></div>
          </div>
          <div class="countdown-caption">
            <span>Kembali ke mode pemindaian dalam <strong id="countdownSec">4</strong> detik...</span>
          </div>
        </div>

      </div>
    </div>

  </div>
</main>

<!-- FOOTER -->
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
  let countdownTimer = null;

  function focusScanner() {
    const inp = document.getElementById('rfidInput');
    if (inp) inp.focus();
  }

  // Live Clock
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

  // Fullscreen
  function toggleFullscreen() {
    if (!document.fullscreenElement) {
      document.documentElement.requestFullscreen().catch(() => {});
      document.getElementById('fsIcon').className = 'bi bi-fullscreen-exit';
    } else {
      document.exitFullscreen().catch(() => {});
      document.getElementById('fsIcon').className = 'bi bi-fullscreen';
    }
  }

  // Theme
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

  // Audio Beep
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

  // Process RFID / Barcode input
  async function processCode(code) {
    const cleanCode = code.trim();
    if (!cleanCode || cleanCode.length < 3) return;

    const ind = document.getElementById('scannerStatus');
    if (ind) ind.innerHTML = '<span class="pulse-dot" style="background:var(--cyan);"></span><span>MEMPROSES DATA PRESENSI...</span>';

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
      showIdentityResult(data);
    } catch (err) {
      showIdentityResult({
        success: false,
        message: 'Gagal menghubungi server presensi. Pastikan koneksi stabil.'
      });
    } finally {
      if (ind) ind.innerHTML = '<span class="pulse-dot"></span><span>PEMINDAI SIAP MENERIMA INPUT</span>';
      focusScanner();
    }
  }

  // Switch to Full Identity Result View
  function showIdentityResult(res) {
    const scannerState = document.getElementById('scannerState');
    const responseState = document.getElementById('responseState');
    const card = document.getElementById('identityCard');
    const badge = document.getElementById('resBadge');
    const badgeTxt = document.getElementById('resBadgeText');
    const photo = document.getElementById('resPhoto');
    const avatarWrap = document.getElementById('avatarWrap');
    const nameEl = document.getElementById('resName');
    const subTxt = document.getElementById('resSubTxt');
    const timeTxt = document.getElementById('resTimeTxt');
    const msgBox = document.getElementById('resMessageBox');
    const msgTxt = document.getElementById('resMessageTxt');
    const countdownFill = document.getElementById('countdownFill');

    // Switch view
    scannerState.style.display = 'none';
    responseState.style.display = 'block';

    if (res.success && res.data) {
      playBeep('success');
      const d = res.data;
      const st = (d.status || 'hadir').toLowerCase();

      photo.src = d.foto || d.foto_url || '/img/user-default.png';
      nameEl.textContent = d.nama || 'Pengguna';

      const subInfo = (d.sub || d.rombel_atau_jabatan || '').trim();
      const idInfo = (d.identitas || '').trim();
      subTxt.textContent = subInfo ? `${subInfo} • ${idInfo}` : (idInfo || 'Warga Sekolah');

      const jam = d.jam || d.jam_masuk || d.jam_pulang || '';
      timeTxt.textContent = jam ? `Presensi Pukul ${jam} WIB` : 'Presensi Berhasil Dicatat';

      // Status variations
      if (st === 'terlambat') {
        card.className = 'identity-result-card status-terlambat';
        badge.className = 'result-badge-large terlambat';
        badgeTxt.textContent = 'TERLAMBAT';
        avatarWrap.style.borderColor = 'var(--amber)';
        avatarWrap.style.boxShadow = '0 0 25px var(--amber-glow)';
        countdownFill.style.background = 'var(--amber)';
        msgTxt.textContent = res.message || 'Presensi terlambat dicatat.';
      } else if (st === 'pulang') {
        card.className = 'identity-result-card status-pulang';
        badge.className = 'result-badge-large pulang';
        badgeTxt.textContent = 'BERHASIL PULANG';
        avatarWrap.style.borderColor = 'var(--cyan)';
        avatarWrap.style.boxShadow = '0 0 25px var(--cyan-glow)';
        countdownFill.style.background = 'var(--cyan)';
        msgTxt.textContent = res.message || 'Presensi pulang berhasil dicatat. Hati-hati di jalan!';
      } else {
        card.className = 'identity-result-card status-hadir';
        badge.className = 'result-badge-large hadir';
        badgeTxt.textContent = 'BERHASIL HADIR';
        avatarWrap.style.borderColor = 'var(--emerald)';
        avatarWrap.style.boxShadow = '0 0 25px var(--emerald-glow)';
        countdownFill.style.background = 'var(--emerald)';
        msgTxt.textContent = res.message || 'Presensi masuk berhasil dicatat.';
      }

      // Voice greeting
      const speechNama = (d.nama || '').split(',')[0].trim();
      if (st === 'terlambat') {
        speak(`Perhatian, ${speechNama}, Anda tercatat terlambat.`);
      } else if (st === 'pulang') {
        speak(`Terima kasih, ${speechNama}, presensi pulang berhasil. Hati-hati di jalan.`);
      } else {
        speak(`Selamat pagi, ${speechNama}, presensi berhasil.`);
      }

    } else {
      playBeep('error');
      card.className = 'identity-result-card status-error';
      badge.className = 'result-badge-large error';
      badgeTxt.textContent = 'DITOLAK';
      photo.src = '/img/user-default.png';
      avatarWrap.style.borderColor = 'var(--rose)';
      avatarWrap.style.boxShadow = '0 0 25px var(--rose-glow)';
      countdownFill.style.background = 'var(--rose)';
      nameEl.textContent = 'Pemindaian Gagal';
      subTxt.textContent = 'Kartu atau Barcode Tidak Terdaftar';
      timeTxt.textContent = 'Sistem Smart Gate';
      msgTxt.textContent = res.message || 'Kartu belum terdaftar atau gerbang sedang ditutup.';
      speak('Kartu tidak valid atau belum terdaftar.');
    }

    startCountdown(4);
  }

  // Countdown timer to return to scanner
  function startCountdown(totalSec) {
    if (countdownTimer) clearInterval(countdownTimer);

    const fill = document.getElementById('countdownFill');
    const secTxt = document.getElementById('countdownSec');
    let remainMs = totalSec * 1000;
    const intervalMs = 100;

    secTxt.textContent = totalSec;
    fill.style.width = '100%';

    countdownTimer = setInterval(() => {
      remainMs -= intervalMs;
      const pct = (remainMs / (totalSec * 1000)) * 100;
      fill.style.width = Math.max(0, pct) + '%';
      secTxt.textContent = Math.ceil(remainMs / 1000);

      if (remainMs <= 0) {
        clearInterval(countdownTimer);
        returnToScanner();
      }
    }, intervalMs);
  }

  // Return back to central scanner
  function returnToScanner() {
    if (countdownTimer) clearInterval(countdownTimer);
    document.getElementById('responseState').style.display = 'none';
    const sc = document.getElementById('scannerState');
    sc.style.display = 'flex';
    sc.classList.add('kios-fade-enter');
    focusScanner();
  }

  // Global key listener for USB barcode/RFID reader keyboard emulation
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
