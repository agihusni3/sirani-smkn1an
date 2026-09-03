<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kios Tap RFID — SIRANI (SMKN 1 Air Naningan)</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script>
    (function() {
      const saved = localStorage.getItem('smkn1_theme') || 'light';
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
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { background: var(--bg); color: var(--text); font-family: var(--font); min-height: 100vh; display: flex; flex-direction: column; justify-content: space-between; overflow-x: hidden; transition: background .25s ease, color .25s ease; }

    .bg-orb { position: fixed; pointer-events: none; z-index: 0; border-radius: 50%; filter: blur(120px); opacity: .35; }
    .bg-orb-1 { width: 700px; height: 700px; background: radial-gradient(circle, rgba(250,204,21,0.3) 0%, transparent 70%); top: -200px; left: -200px; }
    .bg-orb-2 { width: 600px; height: 600px; background: radial-gradient(circle, rgba(59,130,246,0.25) 0%, transparent 70%); bottom: -150px; right: -150px; }
    .bg-grid { position: fixed; inset: 0; z-index: 0; pointer-events: none; background-image: linear-gradient(rgba(0,0,0,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,0.04) 1px, transparent 1px); background-size: 60px 60px; }

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

    main { position: relative; z-index: 10; max-width: 900px; width: 100%; margin: 0 auto; text-align: center; padding: 24px 20px; transition: max-width .3s ease; }
    .tap-card { max-width: 720px; margin: 0 auto; background: var(--bg-2); border: 1px solid var(--border-2); border-radius: var(--r-xl); padding: 36px 32px; box-shadow: 0 20px 50px rgba(0,0,0,0.18); }

    .pulse-container { position: relative; width: 130px; height: 130px; margin: 0 auto 24px; display: flex; align-items: center; justify-content: center; }
    .pulse-ring { position: absolute; border-radius: 50%; border: 2px solid #000000; top: 50%; left: 50%; transform: translate(-50%, -50%); animation: pulse-expand 2.5s ease-out infinite; }
    .pulse-ring:nth-child(2) { animation-delay: .8s; }
    .pulse-ring:nth-child(3) { animation-delay: 1.6s; }
    @keyframes pulse-expand { 0% { width: 80px; height: 80px; opacity: 1; } 100% { width: 200px; height: 200px; opacity: 0; } }

    .pulse-core { width: 90px; height: 90px; border-radius: 50%; background: #000000; border: 2px solid #000000; display: flex; align-items: center; justify-content: center; font-size: 34px; color: #FFFFFF; box-shadow: 0 4px 20px rgba(0,0,0,0.25); }

    .tap-title { font-size: 24px; font-weight: 900; margin-bottom: 6px; letter-spacing: -0.02em; color: var(--text); }
    .tap-subtitle { color: var(--text-2); font-size: 13.5px; max-width: 540px; margin: 0 auto 24px; font-weight: 500; line-height: 1.55; }

    .scan-form { display: flex; gap: 10px; max-width: 440px; margin: 0 auto; }
    .scan-input { flex: 1; background: var(--bg-3); border: 1px solid var(--border-2); color: var(--text); padding: 12px 16px; border-radius: var(--r-sm); font-family: var(--font-mono); font-size: 15px; outline: none; text-align: center; letter-spacing: .08em; transition: border-color .2s, box-shadow .2s; }
    .scan-input:focus { border-color: #000000; box-shadow: 0 0 14px rgba(0,0,0,0.15); }
    .scan-btn { background: #000000; color: #FFFFFF; font-family: var(--font); font-weight: 800; font-size: 13px; padding: 0 20px; border-radius: var(--r-sm); border: 1px solid #000000; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
    .scan-btn:hover { background: #262626; border-color: #262626; }

    .result-box { display: none; margin-top: 24px; padding: 20px 24px; border-radius: var(--r-md); text-align: left; animation: fadeIn .3s ease; }
    .result-box.success { display: block; background: var(--green-dim); border: 1px solid rgba(34,197,94,0.3); }
    .result-box.warning { display: block; background: rgba(245,158,11,0.12); border: 1px solid rgba(245,158,11,0.3); }
    .result-box.info { display: block; background: rgba(59,130,246,0.12); border: 1px solid rgba(59,130,246,0.3); }
    .result-box.error { display: block; background: var(--red-dim); border: 1px solid rgba(239,68,68,0.3); }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    .result-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
    .result-name { font-size: 17px; font-weight: 800; color: var(--text); }
    .result-status { font-family: var(--font-mono); font-size: 11.5px; font-weight: 800; text-transform: uppercase; padding: 4px 12px; border-radius: 20px; background: rgba(0,0,0,0.15); }

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
      text-decoration: none;
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
      padding: 28px 28px;
      border-radius: var(--r-lg);
      box-shadow: 0 16px 48px rgba(0,0,0,0.3);
    }
    :fullscreen .pulse-container,
    body.is-fullscreen .pulse-container {
      width: 120px;
      height: 120px;
      margin-bottom: 20px;
    }
    :fullscreen .pulse-core,
    body.is-fullscreen .pulse-core {
      width: 84px;
      height: 84px;
      font-size: 30px;
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
      margin-bottom: 20px;
    }
    :fullscreen .scan-form,
    body.is-fullscreen .scan-form {
      max-width: 440px;
    }

    /* ═══════════════════════════════════════════════════════════════════ */
    /* RESPONSIVE DESIGN (TABLET & MOBILE)                                */
    /* ═══════════════════════════════════════════════════════════════════ */
    @media (max-width: 900px) {
      header {
        padding: 14px 20px;
        gap: 12px;
      }
      .brand-title {
        font-size: 17px;
      }
      .brand-slogan {
        font-size: 11px;
      }
      .kios-clock-time {
        font-size: 14px;
      }
      .kios-clock-date {
        font-size: 10px;
      }
    }

    @media (max-width: 640px) {
      header {
        padding: 12px 14px;
        flex-direction: column;
        align-items: stretch;
        gap: 10px;
      }
      .brand {
        justify-content: center;
        text-align: center;
        gap: 10px;
      }
      .brand-logo {
        width: 40px !important;
        height: 40px !important;
        border-radius: 10px !important;
      }
      .brand-title {
        font-size: 15px !important;
      }
      .brand-title > div {
        justify-content: center;
      }
      .brand-slogan {
        display: none !important;
      }
      .nav-actions {
        justify-content: center;
        gap: 6px;
        width: 100%;
      }
      .kios-clock-widget {
        display: none !important;
      }
      .btn-icon-kios {
        width: 40px;
        height: 40px;
        font-size: 16px;
        border-radius: 10px;
      }

      main {
        padding: 14px 12px;
        margin: auto;
        width: 100%;
      }
      .tap-card {
        padding: 24px 16px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        width: 100%;
      }
      .pulse-container {
        width: 110px;
        height: 110px;
        margin-bottom: 16px;
      }
      .pulse-core {
        width: 76px;
        height: 76px;
        font-size: 28px;
      }
      @keyframes pulse-expand {
        0% { width: 60px; height: 60px; opacity: 1; }
        100% { width: 160px; height: 160px; opacity: 0; }
      }
      .tap-title {
        font-size: 20px;
        line-height: 1.25;
        margin-bottom: 6px;
      }
      .tap-subtitle {
        font-size: 12.5px;
        margin-bottom: 18px;
        line-height: 1.45;
      }
      .scan-form {
        flex-direction: column;
        gap: 8px;
        width: 100%;
        max-width: 100%;
      }
      .scan-input {
        padding: 12px 14px;
        font-size: 15px;
        width: 100%;
      }
      .scan-btn {
        padding: 12px 20px;
        font-size: 14px;
        width: 100%;
        justify-content: center;
      }

      /* Response Identity Card Mobile */
      .identity-card-wrapper {
        padding: 20px 14px;
        border-radius: 18px;
      }
      .identity-header-row {
        flex-direction: row;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 16px;
        padding-bottom: 12px;
      }
      .result-status-badge {
        font-size: 12px;
        padding: 5px 12px;
      }
      .identity-terminal-badge {
        font-size: 11px;
        padding: 4px 10px;
      }
      .identity-photo-container {
        width: 100px;
        height: 100px;
        border-radius: 16px;
        margin: 0 auto 12px;
      }
      .identity-name {
        font-size: 20px;
        margin-bottom: 4px;
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
        font-size: 13px;
        padding: 12px 14px;
      }
      .kios-countdown-container {
        margin-top: 16px;
        padding-top: 12px;
      }
      .kios-countdown-text {
        font-size: 11.5px;
      }

      footer {
        padding: 14px 12px;
        font-size: 10.5px;
        line-height: 1.4;
      }
    }

    footer { position: relative; z-index: 10; padding: 20px; text-align: center; font-size: 12px; color: var(--text-3); font-family: var(--font-mono); font-weight: 600; }
  </style>
</head>
<body>

<div class="bg-orb bg-orb-1"></div>
<div class="bg-orb bg-orb-2"></div>
<div class="bg-grid"></div>

<!-- HEADER / NAV -->
<header>
  <div class="brand">
    <div class="brand-logo" style="width:44px; height:44px; border-radius:var(--r-sm); background:var(--bg-2); border:1px solid var(--border-2); padding:4px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
      <img src="/img/logo.png" alt="Logo SMKN 1 Air Naningan" style="width:100%; height:100%; object-fit:contain;" />
    </div>
    <div class="brand-title">
      <div style="display:flex; align-items:center; gap:8px;">
        <span style="letter-spacing:-0.03em; font-weight:900;">SIRANI</span>
        <span class="ai-badge" style="font-family:var(--font-mono); font-size:10px; background:var(--bg-3); border:1px solid var(--border-2); color:var(--text); padding:2px 7px; border-radius:6px; font-weight:800;">TAP RFID</span>
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

    <!-- Switch to Smart Gate Button -->
    <a href="/smart-gate" class="btn-kios-action" style="color:var(--text); border-color:var(--border-2);" title="Smart Gate Presensi">
      <i class="bi bi-upc-scan"></i>
    </a>


    <!-- Fullscreen Button -->
    <button id="btnFullscreenKios" type="button" class="btn-kios-action" title="Layar Penuh">
      <i id="fsIcon" class="bi bi-arrows-fullscreen"></i>
    </button>

    <!-- Theme Toggle -->
    <button id="themeToggleKios" type="button" class="btn-kios-action" title="Ganti Tema">
      <i id="kiosThemeIcon" class="bi bi-moon-stars-fill"></i>
    </button>

    <!-- Login Button -->
    <a href="/login" class="btn-kios-login" title="Login Sistem">
      <i class="bi bi-box-arrow-in-right"></i>
      <span>Masuk</span>
    </a>
  </div>
</header>

<!-- MAIN TAP KIOSK -->
<main>
  <div class="tap-card" id="mainTapCard">

    <!-- STATE 1: IDLE / SILAKAN TEMPEL KARTU -->
    <div id="idleState" class="kios-state active">
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

      @if($pengumumanKios)
        <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:10px; padding:5px 14px; display:inline-flex; align-items:center; gap:8px; margin-bottom:14px; font-weight:700; color:var(--text); font-size:11.5px; max-width:90%;">
          <span style="font-family:var(--font-mono); font-size:9.5px; font-weight:800; background:var(--text); color:var(--bg); padding:1px 5px; border-radius:4px;">INFO</span>
          <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><strong>{{ $pengumumanKios->judul }}:</strong> {{ \Illuminate\Support\Str::limit($pengumumanKios->isi_pesan, 60) }}</span>
        </div>
      @endif

      <div class="pulse-container">
        <div class="pulse-ring"></div>
        <div class="pulse-ring"></div>
        <div class="pulse-ring"></div>
        <div class="pulse-core"><i class="bi bi-credit-card-2-front-fill"></i></div>
      </div>
      
    </div>

    <!-- STATE 2: RESPONSE IDENTITAS UTUH (HANYA IDENTITAS YANG TAMPIL) -->
    <div id="responseState" class="kios-state" style="display:none;">
      <div class="identity-card-wrapper">
        
        {{-- Status Kehadiran Header --}}
        <div class="identity-header-row">
          <div id="resultStatus" class="result-status-badge">HADIR</div>
          <div class="identity-terminal-badge">
            <i class="bi bi-geo-alt-fill" style="color:var(--green);"></i>
            <span id="resultTerminal">Kios Pintu Utama</span>
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
              <i class="bi bi-clock-fill" style="color:var(--text-2);"></i>
              <span id="resultTime">-</span>
            </div>
          </div>
        </div>

        {{-- Pesan Notifikasi Sukses / Gagal --}}
        <div id="resultMessageBox" class="identity-message-box">
          <div id="resultMessageText">-</div>
        </div>

        {{-- Auto-Reset Countdown Bar (15 Detik) --}}
        <div class="kios-countdown-container">
          <div class="kios-countdown-track">
            <div id="countdownProgressBar" class="kios-countdown-bar"></div>
          </div>
          <div class="kios-countdown-text">
            <i class="bi bi-arrow-repeat spin-icon"></i>
            <span>Kembali ke mode tempel dalam <strong id="countdownSecondsText">15</strong> detik (atau tap kartu berikutnya)</span>
          </div>
        </div>

        {{-- Hidden Active Input untuk Tap Berikutnya --}}
        <form id="rfidFormActive" onsubmit="handleScanSubmit(event, 'uidInputActive')" style="position:absolute; opacity:0; pointer-events:none; left:-9999px;">
          <input type="text" id="uidInputActive" autocomplete="off" />
        </form>

      </div>
    </div>

  </div>
</main>

<!-- FOOTER -->
<footer>
  © {{ date('Y') }} SIRANI — Sistem Informasi Responsif Absensi SMKN 1 Air Naningan.
</footer>

<style>
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

  /* ─── Identity Card Layout ─── */
  .identity-card-wrapper {
    width: 100%;
    max-width: 960px;
    background: var(--bg-3);
    border: 2px solid var(--border-2);
    border-radius: var(--r-xl);
    padding: 36px 44px;
    box-shadow: var(--shadow-lg);
    text-align: left;
    transition: border-color .3s ease, box-shadow .3s ease, max-width .3s ease;
  }

  .identity-card-wrapper.status-success {
    border-color: rgba(34, 197, 94, 0.6);
    box-shadow: 0 0 40px rgba(34, 197, 94, 0.2);
    background: linear-gradient(180deg, rgba(34,197,94,0.06) 0%, var(--bg-2) 100%);
  }
  .identity-card-wrapper.status-warning {
    border-color: rgba(245, 158, 11, 0.6);
    box-shadow: 0 0 40px rgba(245, 158, 11, 0.2);
    background: linear-gradient(180deg, rgba(245,158,11,0.06) 0%, var(--bg-2) 100%);
  }
  .identity-card-wrapper.status-info {
    border-color: rgba(59, 130, 246, 0.6);
    box-shadow: 0 0 40px rgba(59, 130, 246, 0.2);
    background: linear-gradient(180deg, rgba(59,130,246,0.06) 0%, var(--bg-2) 100%);
  }
  .identity-card-wrapper.status-error {
    border-color: rgba(239, 68, 68, 0.6);
    box-shadow: 0 0 40px rgba(239, 68, 68, 0.2);
    background: linear-gradient(180deg, rgba(239,68,68,0.06) 0%, var(--bg-2) 100%);
  }

  .identity-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
    padding-bottom: 18px;
    border-bottom: 1px solid var(--border-2);
  }
  .result-status-badge {
    font-family: var(--font-mono);
    font-size: 14px;
    font-weight: 900;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    padding: 8px 20px;
    border-radius: 100px;
    border: 1.5px solid transparent;
  }
  .identity-terminal-badge {
    font-size: 13px;
    font-weight: 700;
    color: var(--text-2);
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--bg-2);
    border: 1px solid var(--border-2);
    padding: 6px 14px;
    border-radius: 100px;
  }

  .identity-body-row {
    display: flex;
    align-items: center;
    gap: 32px;
    margin-bottom: 24px;
  }
  @media (max-width: 768px) {
    .identity-body-row {
      flex-direction: column;
      text-align: center;
      gap: 20px;
    }
  }

  .identity-photo-container {
    position: relative;
    width: 140px;
    height: 140px;
    flex-shrink: 0;
    border-radius: 24px;
    overflow: hidden;
    border: 3.5px solid var(--border-2);
    box-shadow: 0 0 30px rgba(0,0,0,0.25);
    background: var(--bg-2);
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
    font-size: clamp(20px, 2.8vw, 36px);
    font-weight: 900;
    color: var(--text);
    line-height: 1.2;
    letter-spacing: -0.02em;
    margin-bottom: 8px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  @media (max-width: 768px) {
    .identity-name {
      white-space: normal;
      font-size: clamp(18px, 4.5vw, 26px);
    }
  }
  .identity-sub {
    font-size: 16px;
    color: var(--text-2);
    font-weight: 600;
    margin-bottom: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  @media (max-width: 768px) {
    .identity-sub {
      white-space: normal;
      font-size: 14px;
    }
  }
  .identity-time-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--bg-2);
    border: 1px solid var(--border-2);
    padding: 6px 14px;
    border-radius: var(--r-sm);
    font-size: 14px;
    font-weight: 700;
    color: var(--text);
    font-family: var(--font-mono);
  }

  .identity-message-box {
    padding: 16px 20px;
    border-radius: var(--r-md);
    font-size: 15px;
    font-weight: 700;
    display: flex;
    align-items: center;
    border: 1.5px solid var(--border-2);
    background: var(--bg-2);
  }

  .kios-countdown-container {
    margin-top: 24px;
    padding-top: 18px;
    border-top: 1px dashed var(--border-2);
  }
  .kios-countdown-track {
    width: 100%;
    height: 8px;
    background: rgba(0,0,0,0.1);
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 10px;
  }
  [data-theme="dark"] .kios-countdown-track {
    background: rgba(255,255,255,0.1);
  }
  .kios-countdown-bar {
    height: 100%;
    width: 100%;
    background: var(--text);
    border-radius: 10px;
    transition: width 0.1s linear;
  }
  .kios-countdown-text {
    font-size: 13.5px;
    color: var(--text-3);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-weight: 600;
  }
  .kios-countdown-text strong {
    color: var(--text);
    font-family: var(--font-mono);
    font-size: 15px;
  }

  /* Fullscreen scale */
  :fullscreen .identity-card-wrapper,
  body.is-fullscreen .identity-card-wrapper {
    max-width: 1100px;
    padding: 48px 56px;
    border-radius: 40px;
  }
  :fullscreen .identity-photo-container,
  body.is-fullscreen .identity-photo-container {
    width: 190px;
    height: 190px;
    border-radius: 30px;
    border-width: 5px;
  }
  :fullscreen .identity-name,
  body.is-fullscreen .identity-name {
    font-size: clamp(34px, 4.5vw, 48px);
  }
  :fullscreen .identity-sub,
  body.is-fullscreen .identity-sub {
    font-size: 20px;
  }
  :fullscreen .result-status-badge,
  body.is-fullscreen .result-status-badge {
    font-size: 18px;
    padding: 10px 28px;
  }
  :fullscreen .identity-time-tag,
  body.is-fullscreen .identity-time-tag {
    font-size: 17px;
    padding: 8px 18px;
  }
  :fullscreen .identity-message-box,
  body.is-fullscreen .identity-message-box {
    font-size: 19px;
    padding: 20px 24px;
    border-radius: 18px;
  }
  :fullscreen .kios-countdown-text,
  body.is-fullscreen .kios-countdown-text {
    font-size: 16px;
  }
</style>

<script>
  let audioCtx = null;
  function playAudioTone(isSuccess = true) {
    try {
      if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
      if (audioCtx.state === 'suspended') audioCtx.resume();
      
      const ctx = audioCtx;
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      
      osc.connect(gain);
      gain.connect(ctx.destination);
      
      if (isSuccess) {
        osc.type = 'sine';
        osc.frequency.setValueAtTime(587.33, ctx.currentTime);
        osc.frequency.setValueAtTime(880.00, ctx.currentTime + 0.1);
        gain.gain.setValueAtTime(0.15, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.35);
        osc.start();
        osc.stop(ctx.currentTime + 0.35);
      } else {
        osc.type = 'sawtooth';
        osc.frequency.setValueAtTime(220, ctx.currentTime);
        gain.gain.setValueAtTime(0.2, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);
        osc.start();
        osc.stop(ctx.currentTime + 0.3);
      }
    } catch(e) {}
  }

  // ── Digital Real-time Clock ──────────────────────────────
  function updateLiveClock() {
    const now = new Date();
    const clockEl = document.getElementById('kiosLiveClock');
    const dateEl  = document.getElementById('kiosLiveDate');

    if (clockEl) {
      const h = String(now.getHours()).padStart(2, '0');
      const m = String(now.getMinutes()).padStart(2, '0');
      const s = String(now.getSeconds()).padStart(2, '0');
      clockEl.textContent = `${h}:${m}:${s} WIB`;
    }

    if (dateEl) {
      const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
      dateEl.textContent = now.toLocaleDateString('id-ID', options);
    }
  }

  setInterval(updateLiveClock, 1000);
  updateLiveClock();

  // ── Fullscreen Toggle ──────────────────────────────────────
  const fsBtn  = document.getElementById('btnFullscreenKios');
  const fsIcon = document.getElementById('fsIcon');

  function updateFsUi() {
    const isFs = !!document.fullscreenElement;
    document.body.classList.toggle('is-fullscreen', isFs);
    if (isFs) {
      if (fsIcon) fsIcon.className = 'bi bi-fullscreen-exit';
      if (fsBtn)  fsBtn.setAttribute('title', 'Keluar Layar Penuh');
    } else {
      if (fsIcon) fsIcon.className = 'bi bi-arrows-fullscreen';
      if (fsBtn)  fsBtn.setAttribute('title', 'Mode Layar Penuh (Fullscreen)');
    }
  }

  if (fsBtn) {
    fsBtn.addEventListener('click', () => {
      if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(() => {});
      } else {
        if (document.exitFullscreen) {
          document.exitFullscreen().catch(() => {});
        }
      }
    });
  }

  document.addEventListener('fullscreenchange', updateFsUi);

  function setKiosTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('smkn1_theme', theme);
    const icon = document.getElementById('kiosThemeIcon');
    const btn = document.getElementById('themeToggleKios');
    if (icon) {
      icon.className = (theme === 'light') ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
    }
    if (btn) {
      btn.setAttribute('title', (theme === 'light') ? 'Ganti ke Mode Gelap' : 'Ganti ke Mode Terang');
    }
  }

  const savedKiosTheme = localStorage.getItem('smkn1_theme') || 'light';
  setKiosTheme(savedKiosTheme);

  document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('themeToggleKios');
    if (toggleBtn) {
      toggleBtn.addEventListener('click', () => {
        const current = document.documentElement.getAttribute('data-theme') || 'light';
        const next = current === 'dark' ? 'light' : 'dark';
        setKiosTheme(next);
      });
    }
    focusActiveInput();
  });

  const idleState = document.getElementById('idleState');
  const responseState = document.getElementById('responseState');
  const inputIdle = document.getElementById('uidInputIdle');
  const inputActive = document.getElementById('uidInputActive');

  const resultPhoto = document.getElementById('resultPhoto');
  const resultName = document.getElementById('resultName');
  const resultSub = document.getElementById('resultSub');
  const resultStatus = document.getElementById('resultStatus');
  const resultTime = document.getElementById('resultTime');
  const resultMessageBox = document.getElementById('resultMessageBox');
  const resultMessageText = document.getElementById('resultMessageText');

  function focusActiveInput() {
    if (responseState && responseState.style.display !== 'none') {
      if (inputActive) inputActive.focus();
    } else {
      if (inputIdle) inputIdle.focus();
    }
  }

  document.addEventListener('click', focusActiveInput);
  document.addEventListener('keydown', (e) => {
    // Keep input focused when typing anywhere
    if (document.activeElement !== inputIdle && document.activeElement !== inputActive) {
      focusActiveInput();
    }
  });

  // ── Auto-Reset 15 Detik Logic ──────────────────────────────
  let autoResetTimer = null;
  let countdownInterval = null;
  const RESET_SECONDS = 15;

  function showResponseState() {
    if (idleState) idleState.style.setProperty('display', 'none', 'important');
    if (responseState) {
      responseState.style.setProperty('display', 'flex', 'important');
      responseState.classList.remove('fade-enter');
      void responseState.offsetWidth;
      responseState.classList.add('fade-enter');
    }
    if (inputActive) {
      inputActive.value = '';
      inputActive.focus();
    }
    startAutoResetTimer();
  }

  function showIdleState() {
    if (autoResetTimer) clearTimeout(autoResetTimer);
    if (countdownInterval) clearInterval(countdownInterval);
    if (responseState) responseState.style.setProperty('display', 'none', 'important');
    if (idleState) {
      idleState.style.setProperty('display', 'flex', 'important');
      idleState.classList.remove('fade-enter');
      void idleState.offsetWidth;
      idleState.classList.add('fade-enter');
    }
    if (inputIdle) {
      inputIdle.value = '';
      inputIdle.focus();
    }
  }

  function startAutoResetTimer() {
    if (autoResetTimer) clearTimeout(autoResetTimer);
    if (countdownInterval) clearInterval(countdownInterval);

    let timeLeft = RESET_SECONDS * 10;
    const totalUnits = RESET_SECONDS * 10;
    const bar = document.getElementById('countdownProgressBar');
    const text = document.getElementById('countdownSecondsText');

    if (bar) bar.style.width = '100%';
    if (text) text.textContent = RESET_SECONDS;

    countdownInterval = setInterval(() => {
      timeLeft--;
      if (bar) {
        const pct = Math.max(0, (timeLeft / totalUnits) * 100);
        bar.style.width = `${pct}%`;
      }
      if (text) {
        text.textContent = Math.ceil(timeLeft / 10);
      }

      if (timeLeft <= 0) {
        clearInterval(countdownInterval);
        showIdleState();
      }
    }, 100);

    autoResetTimer = setTimeout(() => {
      showIdleState();
    }, RESET_SECONDS * 1000);
  }

  async function handleScanSubmit(e, inputId) {
    e.preventDefault();
    const inputEl = document.getElementById(inputId);
    const uid = inputEl ? inputEl.value.trim() : '';
    if (!uid) return;

    const wrapper = document.querySelector('.identity-card-wrapper');

    try {
      const response = await fetch('/api/v1/scan', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ uid: uid, terminal: 'Kios Pintu Utama' })
      });

      const res = await response.json();

      if (res.success) {
        playAudioTone(true);
        const d = res.data || {};
        
        if (wrapper) {
          wrapper.className = 'identity-card-wrapper status-success';
        }

        resultPhoto.src = d.foto_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(d.nama || 'User')}&background=CA8A04&color=fff&bold=true`;
        
        resultName.textContent = d.nama || 'Pengguna RFID';
        resultSub.textContent = `${d.identitas} · ${d.rombel_atau_jabatan}`;
        
        if (res.type === 'pulang_cepat') {
          resultStatus.textContent = 'PULANG CEPAT (IZIN)';
          resultStatus.style.color = '#F59E0B';
          resultStatus.style.borderColor = '#F59E0B';
          resultStatus.style.background = 'rgba(245,158,11,0.15)';
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
        
        const jam = d.jam_pulang ? `Pulang: ${d.jam_pulang}` : `Masuk: ${d.jam_masuk}`;
        resultTime.textContent = `${d.tanggal} · ${jam} WIB`;
        
        resultMessageBox.style.background = 'var(--green-dim)';
        resultMessageBox.style.borderColor = 'rgba(34,197,94,0.4)';
        resultMessageText.innerHTML = `<i class="bi bi-check-circle-fill" style="color:var(--green); margin-right:8px; font-size:18px;"></i> ${res.message}`;
      } else if (res.status === 'warning' || res.type === 'belum_waktunya_pulang') {
        playAudioTone(false);
        if (wrapper) wrapper.className = 'identity-card-wrapper status-warning';
        
        const d = res.data || {};
        resultPhoto.src = d.foto_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(d.nama || 'User')}&background=F59E0B&color=fff&bold=true`;

        resultName.textContent = d.nama || 'Belum Waktunya Pulang';
        resultSub.textContent = d.identitas ? `${d.identitas} · ${d.rombel_atau_jabatan}` : 'Peringatan Kepulangan';
        resultStatus.textContent = 'BELUM JAM PULANG';
        resultStatus.style.color = '#F59E0B';
        resultStatus.style.borderColor = '#F59E0B';
        resultStatus.style.background = 'rgba(245,158,11,0.15)';

        resultTime.textContent = new Date().toLocaleTimeString('id-ID') + ' WIB';
        
        resultMessageBox.style.background = 'rgba(245,158,11,0.12)';
        resultMessageBox.style.borderColor = 'rgba(245,158,11,0.4)';
        resultMessageText.innerHTML = `<i class="bi bi-exclamation-triangle-fill" style="color:#F59E0B; margin-right:8px; font-size:18px;"></i> ${res.message}`;
      } else if (res.status === 'info' || res.type === 'sudah_masuk') {
        playAudioTone(true);
        if (wrapper) wrapper.className = 'identity-card-wrapper status-info';

        const d = res.data || {};
        resultPhoto.src = d.foto_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(d.nama || 'User')}&background=3B82F6&color=fff&bold=true`;

        resultName.textContent = d.nama || 'Sudah Tercatat';
        resultSub.textContent = d.identitas ? `${d.identitas} · ${d.rombel_atau_jabatan}` : 'Informasi Presensi';
        resultStatus.textContent = 'SUDAH MASUK';
        resultStatus.style.color = '#60A5FA';
        resultStatus.style.borderColor = '#60A5FA';
        resultStatus.style.background = 'rgba(59,130,246,0.15)';

        resultTime.textContent = new Date().toLocaleTimeString('id-ID') + ' WIB';
        
        resultMessageBox.style.background = 'rgba(59,130,246,0.12)';
        resultMessageBox.style.borderColor = 'rgba(59,130,246,0.4)';
        resultMessageText.innerHTML = `<i class="bi bi-info-circle-fill" style="color:#60A5FA; margin-right:8px; font-size:18px;"></i> ${res.message}`;
      } else {
        playAudioTone(false);
        if (wrapper) wrapper.className = 'identity-card-wrapper status-error';

        resultPhoto.src = `https://ui-avatars.com/api/?name=X&background=EF4444&color=fff&bold=true`;
        
        resultName.textContent = 'Pemindaian Gagal';
        resultSub.textContent = `UID: ${uid}`;
        resultStatus.textContent = 'DITOLAK';
        resultStatus.style.color = 'var(--red)';
        resultStatus.style.borderColor = 'var(--red)';
        resultStatus.style.background = 'var(--red-dim)';
        
        resultTime.textContent = new Date().toLocaleTimeString('id-ID') + ' WIB';
        
        resultMessageBox.style.background = 'var(--red-dim)';
        resultMessageBox.style.borderColor = 'rgba(239,68,68,0.4)';
        resultMessageText.innerHTML = `<i class="bi bi-x-circle-fill" style="color:var(--red); margin-right:8px; font-size:18px;"></i> ${res.error || res.message}`;
      }

      showResponseState();

    } catch (err) {
      playAudioTone(false);
      if (wrapper) wrapper.className = 'identity-card-wrapper status-error';

      resultPhoto.src = `https://ui-avatars.com/api/?name=Error&background=EF4444&color=fff&bold=true`;
      resultName.textContent = 'Kesalahan Koneksi';
      resultSub.textContent = 'Gagal menghubungi server';
      resultStatus.textContent = 'OFFLINE';
      resultStatus.style.color = 'var(--red)';
      resultStatus.style.borderColor = 'var(--red)';
      resultStatus.style.background = 'var(--red-dim)';
      resultTime.textContent = '-';
      
      resultMessageBox.style.background = 'var(--red-dim)';
      resultMessageBox.style.borderColor = 'rgba(239,68,68,0.4)';
      resultMessageText.innerHTML = `<i class="bi bi-exclamation-triangle-fill" style="color:var(--red); margin-right:8px;"></i> Gagal terhubung ke server absensi.`;
      
      showResponseState();
    }

    if (inputEl) inputEl.value = '';
    focusActiveInput();
  }
</script>

</body>
</html>
