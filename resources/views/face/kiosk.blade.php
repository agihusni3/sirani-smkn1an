<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Smart Gate Presensi — SIRANI (SMKN 1 Air Naningan)</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script>
    (function() {
      const saved = localStorage.getItem('smkn1_theme') || 'dark';
      document.documentElement.setAttribute('data-theme', saved);
    })();
  </script>
  <style>
    :root,
    [data-theme="light"] {
      --bg: #F1F5F9;
      --bg-2: #FFFFFF;
      --bg-3: #E2E8F0;
      --surface: rgba(0,0,0,0.03);
      --border: rgba(0,0,0,0.09);
      --border-2: rgba(0,0,0,0.15);
      --text: #0F172A;
      --text-2: #475569;
      --text-3: #94A3B8;
      --gold: #CA8A04;
      --gold-2: #EAB308;
      --gold-dim: rgba(202,138,4,0.1);
      --gold-glow: rgba(202,138,4,0.25);
      --navy: #2563EB;
      --green: #16A34A;
      --green-dim: rgba(22,163,74,0.12);
      --red: #DC2626;
      --red-dim: rgba(220,38,38,0.1);
      --r-sm: 10px; --r-md: 14px; --r-lg: 20px; --r-xl: 28px;
      --font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      --font-mono: 'JetBrains Mono', monospace;
    }

    [data-theme="dark"] {
      --bg: #070B14;
      --bg-2: #0D1422;
      --bg-3: #151D2E;
      --surface: rgba(255,255,255,0.04);
      --border: rgba(255,255,255,0.08);
      --border-2: rgba(255,255,255,0.14);
      --text: #F1F5F9;
      --text-2: #94A3B8;
      --text-3: #64748B;
      --gold: #FACC15;
      --gold-2: #FDE68A;
      --gold-dim: rgba(250,204,21,0.12);
      --gold-glow: rgba(250,204,21,0.35);
      --navy: #3B82F6;
      --green: #22C55E;
      --green-dim: rgba(34,197,94,0.15);
      --red: #EF4444;
      --red-dim: rgba(239,68,68,0.15);
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { background: var(--bg); color: var(--text); font-family: var(--font); min-height: 100vh; min-height: 100dvh; display: flex; flex-direction: column; justify-content: space-between; overflow-x: hidden; padding-top: env(safe-area-inset-top, 0px); padding-bottom: env(safe-area-inset-bottom, 0px); transition: background .25s ease, color .25s ease; }

    .bg-orb { position: fixed; pointer-events: none; z-index: 0; border-radius: 50%; filter: blur(120px); opacity: .35; }
    .bg-orb-1 { width: 700px; height: 700px; background: radial-gradient(circle, rgba(250,204,21,0.3) 0%, transparent 70%); top: -200px; left: -200px; }
    .bg-orb-2 { width: 600px; height: 600px; background: radial-gradient(circle, rgba(59,130,246,0.25) 0%, transparent 70%); bottom: -150px; right: -150px; }
    .bg-grid { position: fixed; inset: 0; z-index: 0; pointer-events: none; background-image: linear-gradient(rgba(0,0,0,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,0.04) 1px, transparent 1px); background-size: 60px 60px; }

    /* ══ HEADER ══ */
    header { position: relative; z-index: 10; padding: 18px 28px; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; }
    .brand { display: flex; align-items: center; gap: 12px; }
    .brand-logo { 
      width: 44px; 
      height: 44px; 
      border-radius: var(--r-sm);
      background: var(--surface);
      border: 1px solid var(--border-2);
      padding: 4px;
      display: flex; 
      align-items: center; 
      justify-content: center;
      flex-shrink: 0;
    }
    .brand-logo img { width: 100%; height: 100%; object-fit: contain; }
    .brand-title { font-weight: 900; font-size: 16px; line-height: 1.2; letter-spacing: -0.02em; color: var(--text); }
    .brand-slogan { display: block; font-size: 11px; color: var(--text-3); font-weight: 600; letter-spacing: 0; margin-top: 2px; }

    .ai-badge {
      font-family: var(--font-mono);
      font-size: 10px;
      background: var(--gold-dim);
      border: 1px solid rgba(250,204,21,0.3);
      color: var(--gold);
      padding: 2px 7px;
      border-radius: 6px;
      font-weight: 800;
      letter-spacing: 0.05em;
    }

    /* Digital Real-Time Clock Widget */
    .kios-clock-widget {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      justify-content: center;
      padding: 6px 14px;
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      border-radius: var(--r-sm);
      user-select: none;
    }
    .kios-clock-time {
      font-family: var(--font-mono);
      font-size: 15px;
      font-weight: 800;
      color: var(--text);
      letter-spacing: 0.04em;
      line-height: 1.1;
    }
    .kios-clock-date {
      font-size: 11px;
      color: var(--text-3);
      font-weight: 600;
      margin-top: 2px;
    }

    .nav-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

    /* ══ MAIN KIOSK CONTAINER ══ */
    main { position: relative; z-index: 10; max-width: 900px; width: 100%; margin: 0 auto; text-align: center; padding: 24px 20px; transition: max-width .3s ease; }
    .tap-card { max-width: 720px; margin: 0 auto; background: var(--bg-2); border: 1px solid var(--border-2); border-radius: var(--r-xl); padding: 32px 28px; box-shadow: 0 20px 50px rgba(0,0,0,0.18); }

    .tap-title { font-size: 24px; font-weight: 900; margin-bottom: 6px; letter-spacing: -0.02em; color: var(--text); }
    .tap-subtitle { color: var(--text-2); font-size: 13.5px; max-width: 580px; margin: 0 auto 20px; font-weight: 500; line-height: 1.55; }

    /* ══ CAMERA VIEWPORT ══ */
    .cam-viewport-frame {
      position: relative;
      width: 100%;
      max-width: 560px;
      height: 360px;
      margin: 0 auto 20px;
      border-radius: var(--r-lg);
      overflow: hidden;
      background: #02040A;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1.5px solid var(--border-2);
      box-shadow: 0 12px 36px rgba(0,0,0,0.35);
      transform: translateZ(0);
      -webkit-transform: translateZ(0);
    }
    #videoFeed {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transform: scaleX(-1) translateZ(0);
      -webkit-transform: scaleX(-1) translateZ(0);
      will-change: transform;
      backface-visibility: hidden;
      -webkit-backface-visibility: hidden;
    }

    .corner-bracket { position: absolute; width: 24px; height: 24px; border: 2.5px solid var(--gold); pointer-events: none; z-index: 5; }
    .corner-tl { top: 14px; left: 14px; border-right: none; border-bottom: none; }
    .corner-tr { top: 14px; right: 14px; border-left: none; border-bottom: none; }
    .corner-bl { bottom: 14px; left: 14px; border-right: none; border-top: none; }
    .corner-br { bottom: 14px; right: 14px; border-left: none; border-top: none; }

    .reticle-container {
      position: absolute;
      width: 210px;
      height: 260px;
      border: 2px dashed rgba(250, 204, 21, 0.4);
      border-radius: 40px;
      pointer-events: none;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: border-color .25s ease, box-shadow .25s ease;
      z-index: 4;
      overflow: hidden;
    }
    .reticle-container.active-match {
      border: 3px solid var(--green);
      box-shadow: 0 0 35px rgba(34, 197, 94, 0.5), inset 0 0 20px rgba(34, 197, 94, 0.35);
    }
    .laser-beam {
      position: absolute;
      left: 0;
      right: 0;
      top: 0;
      height: 2.5px;
      background: linear-gradient(90deg, transparent, var(--gold), #fff, var(--gold), transparent);
      box-shadow: 0 0 12px var(--gold);
      animation: laserSweepGPU 2.6s ease-in-out infinite;
      will-change: transform;
    }
    @keyframes laserSweepGPU {
      0%, 100% { transform: translateY(16px); opacity: 0.3; }
      50% { transform: translateY(230px); opacity: 1; }
    }

    .hud-status-bottom {
      position: absolute;
      bottom: 14px;
      left: 50%;
      transform: translateX(-50%);
      background: rgba(13, 20, 34, 0.9);
      border: 1px solid var(--border-2);
      padding: 6px 16px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 700;
      color: #fff;
      display: flex;
      align-items: center;
      gap: 8px;
      backdrop-filter: blur(10px);
      box-shadow: 0 4px 16px rgba(0,0,0,0.3);
      white-space: nowrap;
      z-index: 6;
    }

    .kios-btn-group {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-top: 16px;
      flex-wrap: wrap;
    }
    .scan-btn {
      background: #000000;
      color: #FFFFFF;
      font-family: var(--font);
      font-weight: 800;
      font-size: 12.5px;
      padding: 9px 18px;
      border-radius: var(--r-sm);
      border: 1px solid #000000;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 7px;
      transition: all .2s ease;
    }
    .scan-btn:hover {
      background: #262626;
      border-color: #262626;
      transform: translateY(-1px);
    }
    .scan-btn-alt {
      background: var(--bg-3);
      border: 1px solid var(--border-2);
      color: var(--text);
    }
    .scan-btn-alt:hover {
      border-color: var(--border);
      background: var(--bg);
      color: var(--text);
    }

    /* ══ KIOSK STATE TRANSITIONS ══ */
    .kios-state {
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      transition: opacity .25s ease, transform .25s ease;
    }
    .kios-state.fade-enter {
      animation: kiosFadeIn .3s ease forwards;
    }
    @keyframes kiosFadeIn {
      from { opacity: 0; transform: scale(.97); }
      to { opacity: 1; transform: scale(1); }
    }

    /* ══ IDENTITY CARD RESULT (SMART GATE) ══ */
    .identity-card-wrapper {
      width: 100%;
      max-width: 860px;
      background: var(--bg-2);
      border: 1.5px solid var(--border-2);
      border-radius: var(--r-xl);
      padding: 32px 36px;
      box-shadow: 0 16px 40px rgba(0,0,0,0.15);
      text-align: left;
      transition: border-color .3s ease, box-shadow .3s ease, max-width .3s ease;
    }
    .identity-card-wrapper.status-success {
      border-color: rgba(34, 197, 94, 0.5);
      box-shadow: 0 0 35px rgba(34, 197, 94, 0.15);
    }
    .identity-card-wrapper.status-warning {
      border-color: rgba(245, 158, 11, 0.5);
      box-shadow: 0 0 35px rgba(245, 158, 11, 0.15);
    }
    .identity-card-wrapper.status-info {
      border-color: rgba(59, 130, 246, 0.5);
      box-shadow: 0 0 35px rgba(59, 130, 246, 0.15);
    }
    .identity-card-wrapper.status-error {
      border-color: rgba(239, 68, 68, 0.5);
      box-shadow: 0 0 35px rgba(239, 68, 68, 0.15);
    }

    .identity-header-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      margin-bottom: 20px;
      padding-bottom: 16px;
      border-bottom: 1px solid var(--border-2);
    }
    .result-status-badge {
      font-family: var(--font-mono);
      font-size: 13px;
      font-weight: 800;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      padding: 6px 16px;
      border-radius: 20px;
      border: 1px solid transparent;
    }
    .identity-terminal-badge {
      font-size: 12px;
      font-weight: 700;
      color: var(--text-3);
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: var(--bg-3);
      border: 1px solid var(--border-2);
      padding: 5px 12px;
      border-radius: 20px;
    }

    .identity-body-row {
      display: flex;
      align-items: center;
      gap: 28px;
      margin-bottom: 20px;
    }
    @media (max-width: 768px) {
      .identity-body-row {
        flex-direction: column;
        text-align: center;
        gap: 18px;
      }
    }

    .identity-photo-container {
      position: relative;
      width: 120px;
      height: 120px;
      flex-shrink: 0;
      border-radius: 20px;
      overflow: hidden;
      border: 2.5px solid var(--gold);
      box-shadow: 0 0 25px var(--gold-glow);
      background: var(--bg-3);
    }
    .identity-photo-img {
      width: 100%;
      height: 100%;
      aspect-ratio: 1 / 1;
      object-fit: cover;
      object-position: center 20%;
      display: block;
    }

    .identity-text-details {
      flex: 1;
      min-width: 0;
    }
    .identity-name {
      font-size: clamp(20px, 2.5vw, 30px);
      font-weight: 900;
      color: var(--text);
      line-height: 1.25;
      letter-spacing: -0.02em;
      margin-bottom: 6px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    @media (max-width: 768px) {
      .identity-name {
        white-space: normal;
        font-size: clamp(18px, 4.5vw, 24px);
      }
    }
    .identity-sub {
      font-size: 14.5px;
      color: var(--text-2);
      font-weight: 600;
      margin-bottom: 12px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    @media (max-width: 768px) {
      .identity-sub {
        white-space: normal;
        font-size: 13.5px;
      }
    }
    .identity-time-tag {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: var(--bg-3);
      border: 1px solid var(--border-2);
      padding: 5px 12px;
      border-radius: var(--r-sm);
      font-size: 13px;
      font-weight: 800;
      color: var(--text);
      font-family: var(--font-mono);
    }

    .identity-message-box {
      padding: 14px 18px;
      border-radius: var(--r-md);
      font-size: 13.5px;
      font-weight: 700;
      display: flex;
      align-items: center;
      border: 1px solid var(--border-2);
      background: var(--bg-3);
    }

    .kios-countdown-container {
      margin-top: 20px;
      padding-top: 16px;
      border-top: 1px dashed var(--border-2);
    }
    .kios-countdown-track {
      width: 100%;
      height: 6px;
      background: rgba(0,0,0,0.1);
      border-radius: 10px;
      overflow: hidden;
      margin-bottom: 8px;
    }
    [data-theme="dark"] .kios-countdown-track {
      background: rgba(255,255,255,0.1);
    }
    .kios-countdown-bar {
      height: 100%;
      width: 100%;
      background: var(--gold);
      border-radius: 10px;
      transition: width 0.1s linear;
    }
    .kios-countdown-text {
      font-size: 12.5px;
      color: var(--text-3);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      font-weight: 600;
    }
    .kios-countdown-text strong {
      color: var(--gold);
      font-family: var(--font-mono);
      font-size: 14px;
    }

    /* ─── MOBILE RESPONSIVE ADAPTATIONS (<= 640px) ─── */
    @media (max-width: 640px) {
      header {
        padding: 12px 14px;
        gap: 10px;
      }
      .brand-logo {
        width: 38px;
        height: 38px;
      }
      .brand-title {
        font-size: 15px;
      }
      .brand-slogan {
        display: none;
      }
      .kios-clock-widget {
        padding: 4px 8px;
      }
      .kios-clock-time {
        font-size: 13px;
      }
      .kios-clock-date {
        display: none;
      }
      .btn-icon-kios {
        width: 36px;
        height: 36px;
        font-size: 15px;
      }
      main {
        padding: 10px 8px 24px;
        width: 100%;
        max-width: 100vw;
        box-sizing: border-box;
      }
      .tap-card {
        padding: 18px 12px;
        border-radius: var(--r-md);
        box-shadow: 0 12px 30px rgba(0,0,0,0.15);
      }
      .cam-viewport-frame {
        height: clamp(250px, 66vw, 340px);
        max-width: 100%;
        border-radius: var(--r-md);
        margin-bottom: 14px;
      }
      .reticle-container {
        width: 160px;
        height: 200px;
        border-radius: 30px;
      }
      .hud-status-bottom {
        font-size: 10.5px;
        padding: 5px 10px;
        max-width: 92%;
        white-space: normal;
        text-align: center;
        bottom: 10px;
        line-height: 1.3;
      }
      .tap-title {
        font-size: 19px;
        margin-bottom: 6px;
      }
      .tap-subtitle {
        font-size: 12.5px;
        margin-bottom: 16px;
        line-height: 1.4;
      }
      .kios-btn-group {
        flex-direction: column;
        gap: 8px;
        width: 100%;
      }
      .scan-btn {
        width: 100%;
        justify-content: center;
        padding: 10px 14px;
        font-size: 12.5px;
      }
      .identity-card-wrapper {
        padding: 16px 12px;
        border-radius: var(--r-md);
      }
      .identity-body-row {
        flex-direction: column;
        gap: 12px;
        text-align: center;
        margin-bottom: 16px;
      }
      .identity-photo-container {
        width: 100px;
        height: 100px;
        border-radius: 18px;
        margin: 0 auto;
      }
      .identity-name {
        font-size: 18px;
        white-space: normal;
        line-height: 1.25;
      }
      .identity-sub {
        font-size: 13px;
        margin-bottom: 10px;
      }
      .identity-time-tag {
        font-size: 12px;
        padding: 4px 10px;
      }
      .identity-message-box {
        font-size: 12.5px;
        padding: 10px 12px;
      }
      .kios-countdown-container {
        margin-top: 16px;
        padding-top: 12px;
      }
      .kios-countdown-text {
        font-size: 12px;
      }
    }

    .btn-kios-action {
      width: 38px;
      height: 38px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      border-radius: var(--r-sm);
      color: var(--text);
      font-size: 14px;
      cursor: pointer;
      transition: all .2s ease;
    }
    .btn-kios-action:hover {
      border-color: #000000;
      color: #000000;
    }
    .btn-kios-login {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 0 14px;
      height: 38px;
      background: #000000;
      border: 1px solid #000000;
      border-radius: var(--r-sm);
      color: #FFFFFF;
      font-weight: 800;
      font-size: 12.5px;
      text-decoration: none;
      transition: all .2s ease;
    }
    .btn-kios-login:hover {
      background: #262626;
      border-color: #262626;
    }

    /* ══ FULLSCREEN PROPORTIONAL ADAPTIVE STYLES ══ */
    :fullscreen,
    body.is-fullscreen {
      width: 100vw;
      height: 100vh;
      overflow: hidden;
    }
    :fullscreen header,
    body.is-fullscreen header {
      padding: 12px 24px;
    }
    :fullscreen main,
    body.is-fullscreen main {
      max-width: 760px;
      width: 100%;
      min-height: calc(100vh - 100px);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 10px 16px;
      margin: 0 auto;
    }
    :fullscreen .tap-card,
    body.is-fullscreen .tap-card {
      width: 100%;
      max-width: 660px;
      padding: 24px 28px;
      border-radius: var(--r-lg);
      box-shadow: 0 16px 48px rgba(0,0,0,0.3);
    }
    :fullscreen .cam-viewport-frame,
    body.is-fullscreen .cam-viewport-frame {
      max-width: 480px;
      height: 310px;
      margin: 0 auto 14px;
    }
    :fullscreen .tap-title,
    body.is-fullscreen .tap-title {
      font-size: 21px;
      font-weight: 900;
      margin-bottom: 4px;
      line-height: 1.2;
    }
    :fullscreen .tap-subtitle,
    body.is-fullscreen .tap-subtitle {
      font-size: 12.5px;
      margin-bottom: 14px;
    }
    :fullscreen .kios-btn-group,
    body.is-fullscreen .kios-btn-group {
      margin-top: 10px;
    }
    :fullscreen footer,
    body.is-fullscreen footer {
      padding: 8px 16px;
      font-size: 11px;
    }

    footer { position: relative; z-index: 10; padding: 16px; text-align: center; font-size: 12px; color: var(--text-3); font-family: var(--font-mono); font-weight: 600; }
  </style>
</head>
<body>

<div class="bg-orb bg-orb-1"></div>
<div class="bg-orb bg-orb-2"></div>
<div class="bg-grid"></div>

<!-- ══ HEADER / NAV ══ -->
<header>
  <div class="brand">
    <div class="brand-logo">
      <img src="/img/logo.png" alt="Logo SMKN 1 Air Naningan" />
    </div>
    <div class="brand-title">
      <div style="display:flex; align-items:center; gap:8px;">
        <span style="letter-spacing:-0.03em; font-weight:900;">SIRANI</span>
        <span class="ai-badge">SMART GATE</span>
      </div>
      <span class="brand-slogan">SMKN 1 Air Naningan</span>
    </div>
  </div>
  
  <div class="nav-actions">
    <!-- Real-time Clock Widget -->
    <div class="kios-clock-widget" title="Waktu Real-time">
      <div class="kios-clock-time" id="kiosLiveClock">--:--:-- WIB</div>
      <div class="kios-clock-date" id="kiosLiveDate">Memuat tanggal...</div>
    </div>

    <!-- Fullscreen Button -->
    <button id="btnFullscreenKios" type="button" class="btn-kios-action" title="Layar Penuh" onclick="toggleKiosFullscreen()">
      <i id="fsIcon" class="bi bi-arrows-fullscreen"></i>
    </button>

    <!-- Theme Toggle -->
    <button id="themeToggleKios" type="button" class="btn-kios-action" title="Ganti Tema" onclick="toggleTheme()">
      <i id="kiosThemeIcon" class="bi bi-moon-stars-fill"></i>
    </button>

    <!-- Login Button -->
    <a href="/login" class="btn-kios-login" title="Login Sistem">
      <i class="bi bi-box-arrow-in-right"></i>
      <span>Masuk</span>
    </a>
  </div>
</header>

<!-- ══ MAIN KIOSK ══ -->
<main>
  <div class="tap-card" id="mainTapCard">

    <!-- STATE 1: IDLE / KAMERA PEMINDAI WAJAH -->
    <div id="cameraState" class="kios-state active">
      @php
        $todayStr = \Carbon\Carbon::today()->toDateString();
        $liburKios = \App\Models\HariLibur::getLiburHariIni($todayStr);
        $isLiburKios = \App\Models\HariLibur::isLibur($todayStr);
        $pengumumanKios = \App\Models\Pengumuman::forKios()->latest()->first();
      @endphp

      @if($isLiburKios)
        <div style="background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.4); border-radius:10px; padding:5px 14px; display:inline-flex; align-items:center; gap:8px; margin-bottom:14px; font-weight:700; color:#EF4444; font-size:12px;">
          <span style="font-family:var(--font-mono); font-size:10px; font-weight:800; background:#EF4444; color:#fff; padding:1px 5px; border-radius:4px;">LIBUR</span>
          <span>{{ $liburKios ? $liburKios->nama_libur : 'Hari Libur / Akhir Pekan' }}</span>
        </div>
      @endif

      @if(isset($isSesiBuka) && !$isSesiBuka)
        <div style="background:rgba(239,68,68,0.12); border:1px solid #EF4444; border-radius:10px; padding:6px 14px; display:inline-flex; align-items:center; gap:8px; margin-bottom:14px; font-weight:700; color:#EF4444; font-size:12px;">
          <span style="font-family:var(--font-mono); font-size:10px; font-weight:800; background:#EF4444; color:#fff; padding:1px 5px; border-radius:4px;">TERKUNCI</span>
          <span>Sesi Gerbang Belum Dibuka oleh Guru Piket ({{ $dibukaOleh ?: 'Petugas Piket' }})</span>
        </div>
      @endif

      @if($pengumumanKios)
        <div style="background:var(--gold-dim); border:1px solid var(--border-2); border-radius:10px; padding:5px 14px; display:inline-flex; align-items:center; gap:8px; margin-bottom:14px; font-weight:700; color:var(--gold); font-size:11.5px; max-width:90%;">
          <span style="font-family:var(--font-mono); font-size:9.5px; font-weight:800; background:var(--gold); color:#0F172A; padding:1px 5px; border-radius:4px;">INFO</span>
          <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><strong>{{ $pengumumanKios->judul }}:</strong> {{ \Illuminate\Support\Str::limit($pengumumanKios->isi_pesan, 60) }}</span>
        </div>
      @endif

      <!-- Viewport Frame Kamera -->
      <div class="cam-viewport-frame">
        <video id="videoFeed" autoplay playsinline muted></video>
        
        <!-- Corners -->
        <div class="corner-bracket corner-tl"></div>
        <div class="corner-bracket corner-tr"></div>
        <div class="corner-bracket corner-bl"></div>
        <div class="corner-bracket corner-br"></div>

        <!-- Reticle Target Frame -->
        <div class="reticle-container" id="reticle">
          <div class="laser-beam"></div>
        </div>

        <!-- Floating Status Pill -->
        <div class="hud-status-bottom">
          <span style="width:6px; height:6px; border-radius:50%; background:var(--gold); display:inline-block; flex-shrink:0;"></span>
          <span id="camStatusText">Kamera Face ID Aktif · Silakan Arahkan Wajah</span>
        </div>
      </div>

      <h1 class="tap-title">Arahkan Wajah ke Kamera</h1>
      <p class="tap-subtitle">Posisikan wajah Anda tegak menghadap kamera untuk verifikasi presensi otomatis.</p>

      <div class="kios-btn-group">
        <button type="button" class="scan-btn scan-btn-alt" onclick="switchCamera()" title="Ganti Kamera Depan / Belakang">
          <i class="bi bi-camera"></i> Ganti Kamera
        </button>
        <button type="button" class="scan-btn" onclick="syncDatabase()" title="Perbarui Data Face ID">
          <i class="bi bi-arrow-repeat"></i> Sinkron Face ID (<span id="dbCountLabel">...</span>)
        </button>
      </div>

    </div>

    <!-- STATE 2: RESPONSE IDENTITAS UTUH -->
    <div id="responseState" class="kios-state" style="display:none;">
      <div class="identity-card-wrapper" id="identityWrapper">
        
        {{-- Status Kehadiran Header --}}
        <div class="identity-header-row">
          <div id="resultStatus" class="result-status-badge">HADIR</div>
          <div class="identity-terminal-badge">
            <span id="resultTerminal">Smart Gate Presensi</span>
          </div>
        </div>

        {{-- Body Identitas --}}
        <div class="identity-body-row">
          {{-- Foto Profil Besar --}}
          <div class="identity-photo-container">
            <img id="resultPhoto" class="identity-photo-img" src="" alt="Foto Profil" />
          </div>

          {{-- Detail Teks Identitas --}}
          <div class="identity-text-details">
            <h2 id="resultName" class="identity-name">-</h2>
            <div id="resultSub" class="identity-sub">-</div>
            
            <div class="identity-time-tag">
              <i class="bi bi-clock-fill" style="color:var(--gold);"></i>
              <span id="resultTime">-</span>
            </div>
          </div>
        </div>

        {{-- Pesan Notifikasi Sukses / Gagal --}}
        <div id="resultMessageBox" class="identity-message-box">
          <div id="resultMessageText">-</div>
        </div>

        {{-- Auto-Reset Countdown Bar --}}
        <div class="kios-countdown-container">
          <div class="kios-countdown-track">
            <div id="countdownProgressBar" class="kios-countdown-bar"></div>
          </div>
          <div class="kios-countdown-text">
            <i class="bi bi-arrow-repeat spin-icon"></i>
            <span>Kembali ke mode pemindaian dalam <strong id="countdownSecondsText">6</strong> detik...</span>
          </div>
        </div>

      </div>
    </div>

  </div>
</main>

<!-- ══ FOOTER ══ -->
<footer>
  © {{ date('Y') }} SIRANI — Sistem Informasi Responsif Absensi SMKN 1 Air Naningan.
</footer>

<script src="/face-api.min.js"></script>
<script>
  let registeredDescriptors = [];
  let isProcessingFrame = false;
  let activeStream = null;
  let currentFacingMode = 'user';
  let lastScannedId = null;
  let lastScannedTime = 0;
  const COOLDOWN_MS = 6000;
  const checkedInUsersToday = new Set();
  const alreadyNotifiedStandby = new Map();
  let countdownTimer = null;
  let isAiModelLoaded = false;

  // Inisialisasi Model AI Face-API Deep Learning
  async function loadFaceAiModels() {
    if (isAiModelLoaded) return true;
    try {
      if (typeof faceapi !== 'undefined') {
        await Promise.all([
          faceapi.nets.ssdMobilenetv1.loadFromUri('/models'),
          faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
          faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
          faceapi.nets.faceRecognitionNet.loadFromUri('/models')
        ]);
        isAiModelLoaded = true;
        console.log("✅ Face-API ResNet & SSD MobileNet Models Loaded.");
        return true;
      }
    } catch (err) {
      console.warn("Face-API loading note:", err);
      try {
        await Promise.all([
          faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
          faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
          faceapi.nets.faceRecognitionNet.loadFromUri('/models')
        ]);
        isAiModelLoaded = true;
        return true;
      } catch (e2) {
        console.error("Secondary model load failed:", e2);
      }
    }
    return false;
  }

  // 1. Digital Clock
  function updateKiosClock() {
    const now = new Date();
    const timeStr = now.toLocaleTimeString('id-ID', { hour12: false }) + ' WIB';
    const dateStr = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    document.getElementById('kiosLiveClock').textContent = timeStr;
    document.getElementById('kiosLiveDate').textContent = dateStr;
  }
  setInterval(updateKiosClock, 1000);
  updateKiosClock();

  // 2. Fullscreen & Theme Toggle
  function toggleKiosFullscreen() {
    if (!document.fullscreenElement) {
      document.documentElement.requestFullscreen().catch(() => {});
    } else {
      document.exitFullscreen().catch(() => {});
    }
  }

  document.addEventListener('fullscreenchange', () => {
    const isFs = !!document.fullscreenElement;
    document.body.classList.toggle('is-fullscreen', isFs);
    const fsIcon = document.getElementById('fsIcon');
    if (fsIcon) {
      fsIcon.className = isFs ? 'bi bi-fullscreen-exit' : 'bi bi-arrows-fullscreen';
    }
  });

  function toggleTheme() {
    const current = document.documentElement.getAttribute('data-theme') || 'dark';
    const next = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('smkn1_theme', next);
    updateThemeIcon(next);
  }

  function updateThemeIcon(t) {
    const icon = document.getElementById('kiosThemeIcon');
    if (icon) icon.className = (t === 'dark') ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
  }
  updateThemeIcon(document.documentElement.getAttribute('data-theme') || 'dark');

  // 3. Inisialisasi Kamera 60 FPS Halus
  async function initCamera() {
    const video = document.getElementById('videoFeed');
    if (activeStream) {
      activeStream.getTracks().forEach(track => track.stop());
    }

    try {
      const stream = await navigator.mediaDevices.getUserMedia({
        video: {
          width: { ideal: 1280, max: 1920 },
          height: { ideal: 720, max: 1080 },
          frameRate: { ideal: 60, min: 30 },
          facingMode: currentFacingMode
        }
      });
      activeStream = stream;
      video.srcObject = stream;

      if (currentFacingMode === 'user') {
        video.style.transform = 'scaleX(-1) translateZ(0)';
      } else {
        video.style.transform = 'scaleX(1) translateZ(0)';
      }

      document.getElementById('camStatusText').textContent = (currentFacingMode === 'user' ? 'Kamera Depan Aktif · Mendeteksi Wajah' : 'Kamera Belakang Aktif · Arahkan ke Wajah');
    } catch (err) {
      console.error(err);
      document.getElementById('camStatusText').textContent = 'Kamera tidak dapat diakses / Izin ditolak';
    }
  }

  function switchCamera() {
    currentFacingMode = (currentFacingMode === 'user') ? 'environment' : 'user';
    initCamera();
  }

  // 4. Muat Database Biometrik & Deteksi Engine AI
  let activePythonEngine = false; // Flag: apakah Python InsightFace aktif

  async function loadDescriptors() {
    try {
      const res = await fetch('/api/v1/face-descriptors');
      const json = await res.json();
      if (json.success) {
        registeredDescriptors = json.data || [];
        const count = json.count || 0;
        activePythonEngine = (json.engine === 'insightface_arcface_r100');

        const dbLabel = document.getElementById('dbCountLabel');
        if (dbLabel) {
          dbLabel.innerHTML = `${count} Face ID Aktif`;
        }

        if (count === 0) {
          const statusText = document.getElementById('camStatusText');
          if (statusText) {
            statusText.textContent = '⚠️ Belum ada Face ID terdaftar. Silakan rekam wajah di menu Data Guru / Siswa.';
            statusText.style.color = '#FACC15';
          }
        }

        console.log(`[SIRANI] Engine: ${json.engine || 'faceapi'} | Dim: ${json.embedding_dim || 128}-D | Count: ${count}`);
      }
    } catch (e) {
      console.warn(e);
      const dbLabel = document.getElementById('dbCountLabel');
      if (dbLabel) dbLabel.textContent = 'Ready';
    }
  }

  function syncDatabase() {
    loadDescriptors();
    speakVoice("Database Face ID berhasil disinkronkan.");
  }

  // 5. Suara Ramah TTS
  function speakVoice(text) {
    if ('speechSynthesis' in window) {
      window.speechSynthesis.cancel();
      const utter = new SpeechSynthesisUtterance(text);
      utter.lang = 'id-ID';
      utter.rate = 1.05;
      window.speechSynthesis.speak(utter);
    }
  }

  // 6. Ekstraksi Vektor Wajah Cerdas (Zero-Mean & L2-Normalized) dengan Deteksi Wajah ROI
  function extractFaceDescriptorFromCtx(ctx, width, height) {
    // 1. Fokuskan Region-of-Interest (ROI) pada area reticle wajah (tengah kamera)
    const roiX = Math.floor(width * 0.20);
    const roiY = Math.floor(height * 0.10);
    const roiW = Math.floor(width * 0.60);
    const roiH = Math.floor(height * 0.80);

    const imgData = ctx.getImageData(roiX, roiY, roiW, roiH);
    const data = imgData.data;

    // 2. Cek Variansi & Kontur Wajah (Anti-Ghosting / Anti-Dinding Kosong)
    let totalLum = 0;
    const pixelCount = data.length / 4;
    const lumArray = new Float32Array(pixelCount);

    for (let i = 0; i < pixelCount; i++) {
      const idx = i * 4;
      const lum = data[idx] * 0.299 + data[idx + 1] * 0.587 + data[idx + 2] * 0.114;
      lumArray[i] = lum;
      totalLum += lum;
    }

    const meanLum = totalLum / pixelCount;
    let variance = 0;
    for (let i = 0; i < pixelCount; i++) {
      const diff = lumArray[i] - meanLum;
      variance += diff * diff;
    }
    const stdDev = Math.sqrt(variance / pixelCount);

    // Jika variansi terlalu rendah (dinding polos, tanpa objek wajah di reticle), abaikan
    if (stdDev < 12) {
      return null;
    }

    // 3. Ekstraksi 8x8 Grid Intensitas Lokal (64) + 8x8 Gradien Kontur (64) = 128 Vektor
    const gridRows = 8;
    const gridCols = 8;
    const cellW = Math.floor(roiW / gridCols);
    const cellH = Math.floor(roiH / gridRows);
    const rawVector = [];

    for (let r = 0; r < gridRows; r++) {
      for (let c = 0; c < gridCols; c++) {
        let cellSum = 0;
        let count = 0;
        for (let y = r * cellH; y < (r + 1) * cellH; y++) {
          for (let x = c * cellW; x < (c + 1) * cellW; x++) {
            const pIdx = y * roiW + x;
            if (pIdx < pixelCount) {
              cellSum += lumArray[pIdx];
              count++;
            }
          }
        }
        rawVector.push(count > 0 ? (cellSum / count) : 0);
      }
    }

    // Gradien Kontur
    for (let r = 0; r < gridRows; r++) {
      for (let c = 0; c < gridCols; c++) {
        const curr = rawVector[r * gridCols + c];
        const right = (c < gridCols - 1) ? rawVector[r * gridCols + (c + 1)] : curr;
        const down = (r < gridRows - 1) ? rawVector[(r + 1) * gridCols + c] : curr;
        const grad = Math.abs(right - curr) + Math.abs(down - curr);
        rawVector.push(grad);
      }
    }

    // 4. Zero-Mean Centering
    let vecSum = 0;
    for (let i = 0; i < rawVector.length; i++) vecSum += rawVector[i];
    const vecMean = vecSum / rawVector.length;

    const zeroMeanVector = [];
    let normSq = 0;
    for (let i = 0; i < rawVector.length; i++) {
      const val = rawVector[i] - vecMean;
      zeroMeanVector.push(val);
      normSq += val * val;
    }

    // 5. L2 Normalization
    const norm = Math.sqrt(normSq);
    if (norm === 0) return null;

    const descriptor = [];
    for (let i = 0; i < zeroMeanVector.length; i++) {
      descriptor.push(parseFloat((zeroMeanVector[i] / norm).toFixed(6)));
    }

    return descriptor;
  }

  function calculateCosineSimilarity(vecA, vecB) {
    if (!vecA || !vecB || vecA.length !== vecB.length) return 0;
    let dotProduct = 0;
    for (let i = 0; i < vecA.length; i++) {
      dotProduct += vecA[i] * vecB[i];
    }
    return Math.max(0, dotProduct);
  }

  // 7. Face Recognition Loop — Dual Mode:
  //    Mode A: Python InsightFace ArcFace R100 (99.83%) — Server-side JPEG verify
  //    Mode B: face-api.js ResNet-34 (97.7%) — Browser-side embedding (fallback)
  function startFaceRecognitionLoop() {
    const video = document.getElementById('videoFeed');
    const reticle = document.getElementById('reticle');
    const statusText = document.getElementById('camStatusText');

    // Capture video frame sebagai JPEG base64
    function captureJpeg(quality = 0.88) {
      const canvas = document.createElement('canvas');
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      canvas.getContext('2d').drawImage(video, 0, 0);
      return canvas.toDataURL('image/jpeg', quality);
    }

    setInterval(async () => {
      const isShowingResult = (document.getElementById('responseState').style.display !== 'none');
      if (isShowingResult || isProcessingFrame || !video.videoWidth) return;

      if (registeredDescriptors.length === 0) {
        statusText.textContent = '⚠️ Belum ada Face ID terdaftar. Silakan rekam wajah di menu Data Guru / Siswa.';
        statusText.style.color = '#FACC15';
        reticle.classList.remove('active-match');
        return;
      }

      isProcessingFrame = true;
      try {
        // ═══ MODE A: PYTHON INSIGHTFACE ARCFACE R100 (Server-side) ═══
        if (activePythonEngine) {
          const imageBase64 = captureJpeg(0.88);

          // Kirim gambar + semua kandidat ke Laravel → Python verify
          const res = await fetch('/api/v1/face-scan', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
              image_base64: imageBase64,
              candidates: registeredDescriptors.map(c => ({
                id: c.id,
                type: c.type,
                nama: c.nama,
                embedding: c.embedding,
              })),
            })
          });

          const json = await res.json();

          if (json.status === 'no_match' || json.status === 'no_face') {
            const reason = json.reason || 'no_match';
            if (reason === 'no_face') {
              statusText.textContent = (currentFacingMode === 'user' ? 'Kamera AI Aktif · Silakan Arahkan Wajah ke Kamera' : 'Kamera Belakang AI Aktif');
              statusText.style.color = '#fff';
            } else if (reason === 'borderline') {
              statusText.textContent = json.message || 'Mendeteksi... Dekatkan wajah';
              statusText.style.color = '#FACC15';
            } else {
              statusText.textContent = 'Wajah Terdeteksi · Tidak ada kecocokan di database';
              statusText.style.color = '#E2E8F0';
            }
            reticle.classList.remove('active-match');
            return;
          }

          if (json.success && json.data) {
            const match = json.data;
            const candidateKey = `${match.type || 'siswa'}_${match.id}`;

            if (checkedInUsersToday.has(candidateKey)) {
              const now = Date.now();
              const lastNotified = alreadyNotifiedStandby.get(candidateKey) || 0;
              if ((now - lastNotified) > 25000) {
                alreadyNotifiedStandby.set(candidateKey, now);
                statusText.textContent = `${match.nama} sudah melakukan presensi hari ini.`;
                statusText.style.color = '#38BDF8';
                speakVoice(`${match.nama}, Anda sudah melakukan presensi.`);
              } else {
                statusText.textContent = 'Kamera Standby · Silakan Siswa/Guru Berikutnya';
                statusText.style.color = '#94A3B8';
              }
              reticle.classList.remove('active-match');
              return;
            }

            const matchPct = match.match_pct || Math.round((match.similarity || 0) * 100);
            statusText.textContent = `${match.nama} (${matchPct}% · ${match.elapsed_ms || 0}ms)`;
            statusText.style.color = '#10B981';
            reticle.classList.add('active-match');

            // triggerAttendance sudah dilakukan di Laravel (scan already recorded)
            // Langsung tampilkan result card
            await showServerSideResult(json, match);
          }

          return; // Mode A selesai
        }

        // ═══ MODE B: FACE-API.JS RESNET-34 (Browser-side fallback) ═══
        await loadFaceAiModels();

        if (typeof faceapi === 'undefined' || !isAiModelLoaded) {
          statusText.textContent = 'Memuat Model AI Biometrik ResNet...';
          return;
        }

        const allDetections = await faceapi
          .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.20 }))
          .withFaceLandmarks()
          .withFaceDescriptors();

        const vW = video.videoWidth;
        const vH = video.videoHeight;
        const roiLeft = vW * 0.18;
        const roiRight = vW * 0.82;
        const roiTop = vH * 0.08;
        const roiBottom = vH * 0.92;
        const minFaceWidth = vW * 0.15;

        const insideFaces = allDetections.filter(d => {
          const box = d.detection.box;
          const centerX = box.x + box.width / 2;
          const centerY = box.y + box.height / 2;
          return (
            centerX >= roiLeft && centerX <= roiRight &&
            centerY >= roiTop && centerY <= roiBottom &&
            box.width >= minFaceWidth
          );
        });

        if (insideFaces.length === 0) {
          if (allDetections.length > 0) {
            statusText.textContent = 'Posisikan wajah tepat di dalam kotak pemindaian';
            statusText.style.color = '#FACC15';
          } else {
            statusText.textContent = (currentFacingMode === 'user' ? 'Kamera AI Aktif · Silakan Arahkan Wajah ke Kamera' : 'Kamera Belakang AI Aktif · Arahkan ke Wajah');
            statusText.style.color = '#fff';
          }
          reticle.classList.remove('active-match');
          return;
        }

        insideFaces.sort((a, b) => b.detection.box.area - a.detection.box.area);
        const primaryFace = insideFaces[0];
        const currentDescriptor = Array.from(primaryFace.descriptor);

        let bestMatch = null;
        let lowestDist = 999;
        const DISTANCE_THRESHOLD = 0.56;

        // Fungsi Universal Jarak Vektor Biometrik (128-D & 512-D)
        function calcVecDist(vecA, vecB) {
          if (!vecA || !vecB || !vecA.length || !vecB.length) return 999;
          if (vecA.length === vecB.length) {
            let s = 0;
            for (let i = 0; i < vecA.length; i++) {
              const d = vecA[i] - vecB[i];
              s += d * d;
            }
            return Math.sqrt(s);
          }
          const minLen = Math.min(vecA.length, vecB.length);
          let dot = 0, nA = 0, nB = 0;
          for (let i = 0; i < minLen; i++) {
            dot += vecA[i] * vecB[i];
            nA += vecA[i] * vecA[i];
            nB += vecB[i] * vecB[i];
          }
          const denom = Math.sqrt(nA) * Math.sqrt(nB);
          const cos = denom > 0 ? (dot / denom) : 0;
          return Math.sqrt(Math.max(0, 2 * (1 - cos)));
        }

        for (const candidate of registeredDescriptors) {
          if (!candidate.embedding) continue;
          let dist = calcVecDist(currentDescriptor, candidate.embedding);
          if (dist < lowestDist) {
            lowestDist = dist;
            bestMatch = candidate;
          }
        }

        const cosSim = Math.max(0, 1 - (lowestDist * lowestDist / 2));
        const matchPct = Math.round(cosSim * 100);

        if (bestMatch && lowestDist <= DISTANCE_THRESHOLD) {
          const candidateKey = `${bestMatch.type}_${bestMatch.id}`;

          if (checkedInUsersToday.has(candidateKey)) {
            const now = Date.now();
            const lastNotified = alreadyNotifiedStandby.get(candidateKey) || 0;
            if ((now - lastNotified) > 25000) {
              alreadyNotifiedStandby.set(candidateKey, now);
              statusText.textContent = `${bestMatch.nama} sudah melakukan presensi hari ini.`;
              statusText.style.color = '#38BDF8';
              speakVoice(`${bestMatch.nama}, Anda sudah melakukan presensi.`);
            } else {
              statusText.textContent = 'Kamera Standby · Silakan Siswa/Guru Berikutnya';
              statusText.style.color = '#94A3B8';
            }
            reticle.classList.remove('active-match');
            return;
          }

          statusText.textContent = `Wajah Dikenali: ${bestMatch.nama} (${matchPct}% Akurat)`;
          statusText.style.color = '#10B981';
          reticle.classList.add('active-match');
          await triggerAttendance(bestMatch.type, bestMatch.id, bestMatch);
        } else if (bestMatch && lowestDist <= 0.65) {
          statusText.textContent = `Mendeteksi: ${bestMatch.nama} (${matchPct}% · Menyesuaikan...)`;
          statusText.style.color = '#FACC15';
          reticle.classList.remove('active-match');
        } else {
          statusText.textContent = 'Wajah Terdeteksi · Mencari Kecocokan di Database...';
          statusText.style.color = '#E2E8F0';
          reticle.classList.remove('active-match');
        }

      } catch (err) {
        console.error(err);
      } finally {
        isProcessingFrame = false;
      }
    }, 300);
  }

  // 7b. Tampilkan Hasil Presensi dari Python Server (Mode A)
  async function showServerSideResult(json, match) {
    const candidateKey = `${match.type || 'siswa'}_${match.id}`;
    if (checkedInUsersToday.has(candidateKey)) return;

    const now = Date.now();
    if (lastScannedId === match.id && (now - lastScannedTime) < COOLDOWN_MS) return;

    lastScannedId = match.id;
    lastScannedTime = now;
    checkedInUsersToday.add(candidateKey);
    alreadyNotifiedStandby.set(candidateKey, now);

    // json sudah berisi hasil absensi dari server (scan dilakukan bersamaan dengan verify di Mode A)
    const item = json.data || {};
    const nama = item.nama || match.nama;
    const identitas = item.identitas || '';
    const sub = item.rombel_atau_jabatan || '';
    const foto = item.foto_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(nama)}&background=CA8A04&color=fff&bold=true`;
    const nowTimeStr = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
    const jamMasuk = item.jam_masuk ? (item.jam_masuk.substring(0, 5) + ' WIB') : nowTimeStr;
    const jamPulang = item.jam_pulang ? (item.jam_pulang.substring(0, 5) + ' WIB') : null;

    displayIdentityResult(json, {
      nama, identitas, sub, foto,
      jam_masuk: jamMasuk,
      jam_pulang: jamPulang,
      status: item.status || (json.type === 'jam_masuk' ? 'hadir' : 'pulang'),
    });

    if (json.type === 'jam_masuk') {
      speakVoice(`Selamat pagi ${nama}. Presensi masuk berhasil dicatat.`);
    } else if (json.type === 'jam_pulang') {
      speakVoice(`Selamat beristirahat ${nama}. Absen pulang berhasil.`);
    } else if (json.type === 'sudah_masuk') {
      speakVoice(`${nama}, Anda sudah melakukan presensi.`);
    } else {
      speakVoice(json.message || "Presensi diproses.");
    }
    console.log(`[Face Scan] ${nama} - ${json.type} | sim=${match.similarity}`);
  }

  // 8. Eksekusi Presensi & Tampilkan Identitas
  async function triggerAttendance(type, id, person) {
    const candidateKey = `${type}_${id}`;
    if (checkedInUsersToday.has(candidateKey)) {
      return;
    }

    const now = Date.now();
    if (lastScannedId === id && (now - lastScannedTime) < COOLDOWN_MS) {
      return;
    }

    lastScannedId = id;
    lastScannedTime = now;

    // Tandai langsung di state lokal agar ketika countdown selesai, kamera langsung tenang dalam mode standby
    checkedInUsersToday.add(candidateKey);
    alreadyNotifiedStandby.set(candidateKey, now);

    try {
      const res = await fetch('/api/v1/face-scan', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ type, id })
      });
      const data = await res.json();

      const item = data.data || {};
      const nama = item.nama || person.nama;
      const identitas = item.identitas || person.identitas;
      const sub = item.rombel_atau_jabatan || person.rombel_atau_jabatan;
      const foto = item.foto_url || person.foto_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(nama)}&background=CA8A04&color=fff&bold=true`;
      const nowTimeStr = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
      const jamMasuk = item.jam_masuk ? (item.jam_masuk.substring(0, 5) + ' WIB') : nowTimeStr;
      const jamPulang = item.jam_pulang ? (item.jam_pulang.substring(0, 5) + ' WIB') : null;

      displayIdentityResult(data, {
        nama: nama,
        identitas: identitas,
        sub: sub,
        foto: foto,
        jam_masuk: jamMasuk,
        jam_pulang: jamPulang,
        status: item.status || (data.type === 'jam_masuk' ? 'hadir' : 'pulang')
      });

      if (data.type === 'jam_masuk') {
        speakVoice(`Selamat pagi ${nama}. Presensi masuk berhasil dicatat.`);
      } else if (data.type === 'jam_pulang') {
        speakVoice(`Selamat beristirahat ${nama}. Absen pulang berhasil.`);
      } else if (data.type === 'sudah_masuk') {
        speakVoice(`${nama}, Anda sudah melakukan presensi.`);
      } else {
        speakVoice(data.message || data.error || "Presensi diproses.");
      }

    } catch (e) {
      console.error(e);
    }
  }

  // Render Kartu Identitas Hero & Auto-Countdown Return
  function displayIdentityResult(res, d) {
    const camState = document.getElementById('cameraState');
    const responseState = document.getElementById('responseState');
    const wrapper = document.getElementById('identityWrapper');
    const resultStatus = document.getElementById('resultStatus');
    const resultPhoto = document.getElementById('resultPhoto');
    const resultName = document.getElementById('resultName');
    const resultSub = document.getElementById('resultSub');
    const resultTime = document.getElementById('resultTime');
    const resultMessageBox = document.getElementById('resultMessageBox');
    const resultMessageText = document.getElementById('resultMessageText');

    camState.style.display = 'none';
    responseState.style.display = 'block';
    responseState.classList.add('fade-enter');

    resultPhoto.src = d.foto;
    resultName.textContent = d.nama;
    resultSub.textContent = `${d.identitas} · ${d.sub}`;

    const dateTodayStr = new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    const jamStr = d.jam_pulang ? `Pulang: ${d.jam_pulang}` : `Masuk: ${d.jam_masuk}`;
    resultTime.textContent = `${dateTodayStr} · ${jamStr}`;

    if (res.success) {
      wrapper.className = 'identity-card-wrapper status-success';
      
      if (res.type === 'pulang_cepat') {
        resultStatus.textContent = 'PULANG CEPAT (IZIN)';
        resultStatus.style.color = 'var(--gold)';
        resultStatus.style.borderColor = 'var(--gold)';
        resultStatus.style.background = 'var(--gold-dim)';
      } else if (res.type === 'jam_pulang') {
        resultStatus.textContent = 'BERHASIL PULANG';
        resultStatus.style.color = '#38BDF8';
        resultStatus.style.borderColor = '#38BDF8';
        resultStatus.style.background = 'rgba(56,189,248,0.15)';
      } else {
        const isLate = (d.status === 'terlambat');
        resultStatus.textContent = isLate ? 'TERLAMBAT' : 'BERHASIL HADIR';
        resultStatus.style.color = isLate ? '#F59E0B' : 'var(--green)';
        resultStatus.style.borderColor = isLate ? '#F59E0B' : 'var(--green)';
        resultStatus.style.background = isLate ? 'rgba(245,158,11,0.15)' : 'var(--green-dim)';
      }

      resultMessageBox.style.background = 'var(--green-dim)';
      resultMessageBox.style.borderColor = 'rgba(34,197,94,0.4)';
      resultMessageText.innerHTML = `<i class="bi bi-check-circle-fill" style="color:var(--green); margin-right:8px; font-size:18px;"></i> ${res.message}`;

    } else if (res.type === 'gerbang_ditutup') {
      wrapper.className = 'identity-card-wrapper status-warning';
      resultStatus.textContent = 'GERBANG DITUTUP';
      resultStatus.style.color = '#EF4444';
      resultStatus.style.borderColor = '#EF4444';
      resultStatus.style.background = 'rgba(239,68,68,0.15)';

      resultMessageBox.style.background = 'rgba(239,68,68,0.12)';
      resultMessageBox.style.borderColor = 'rgba(239,68,68,0.4)';
      resultMessageText.innerHTML = `<i class="bi bi-lock-fill" style="color:#EF4444; margin-right:8px; font-size:18px;"></i> ${res.message}`;

    } else if (res.status === 'warning' || res.type === 'belum_waktunya_pulang') {
      wrapper.className = 'identity-card-wrapper status-warning';
      resultStatus.textContent = 'BELUM JAM PULANG';
      resultStatus.style.color = '#F59E0B';
      resultStatus.style.borderColor = '#F59E0B';
      resultStatus.style.background = 'rgba(245,158,11,0.15)';

      resultMessageBox.style.background = 'rgba(245,158,11,0.12)';
      resultMessageBox.style.borderColor = 'rgba(245,158,11,0.4)';
      resultMessageText.innerHTML = `<i class="bi bi-exclamation-triangle-fill" style="color:#F59E0B; margin-right:8px; font-size:18px;"></i> ${res.message}`;

    } else if (res.status === 'info' || res.type === 'sudah_masuk') {
      wrapper.className = 'identity-card-wrapper status-info';
      resultStatus.textContent = 'SUDAH ABSEN HARI INI';
      resultStatus.style.color = '#38BDF8';
      resultStatus.style.borderColor = '#38BDF8';
      resultStatus.style.background = 'rgba(56,189,248,0.15)';

      resultMessageBox.style.background = 'rgba(56,189,248,0.12)';
      resultMessageBox.style.borderColor = 'rgba(56,189,248,0.4)';
      resultMessageText.innerHTML = `<i class="bi bi-shield-fill-check" style="color:#38BDF8; margin-right:8px; font-size:18px;"></i> ${res.message || 'Presensi Anda sudah tercatat hari ini. Silakan langsung menuju kelas.'}`;

    } else {
      wrapper.className = 'identity-card-wrapper status-error';
      resultStatus.textContent = 'PRESENSI DITOLAK';
      resultStatus.style.color = 'var(--red)';
      resultStatus.style.borderColor = 'var(--red)';
      resultStatus.style.background = 'var(--red-dim)';

      resultMessageBox.style.background = 'var(--red-dim)';
      resultMessageBox.style.borderColor = 'rgba(239,68,68,0.4)';
      resultMessageText.innerHTML = `<i class="bi bi-x-circle-fill" style="color:var(--red); margin-right:8px; font-size:18px;"></i> ${res.error || res.message || 'Presensi gagal diproses'}`;
    }

    startCountdown(3.5);
  }

  function startCountdown(totalSeconds) {
    if (countdownTimer) clearInterval(countdownTimer);

    const progressBar = document.getElementById('countdownProgressBar');
    const secondsText = document.getElementById('countdownSecondsText');
    let remainingMs = totalSeconds * 1000;
    const intervalMs = 100;

    secondsText.textContent = totalSeconds;
    progressBar.style.width = '100%';

    countdownTimer = setInterval(() => {
      remainingMs -= intervalMs;
      const pct = (remainingMs / (totalSeconds * 1000)) * 100;
      progressBar.style.width = Math.max(0, pct) + '%';
      secondsText.textContent = Math.ceil(remainingMs / 1000);

      if (remainingMs <= 0) {
        clearInterval(countdownTimer);
        returnToCameraView();
      }
    }, intervalMs);
  }

  function returnToCameraView() {
    if (countdownTimer) clearInterval(countdownTimer);
    document.getElementById('responseState').style.display = 'none';
    const camState = document.getElementById('cameraState');
    camState.style.display = 'flex';
    camState.classList.add('fade-enter');
  }

  // Setup Lifecycle
  document.addEventListener('DOMContentLoaded', () => {
    initCamera();
    loadFaceAiModels();
    loadDescriptors().then(() => {
      startFaceRecognitionLoop();
    });
  });
</script>
</body>
</html>
