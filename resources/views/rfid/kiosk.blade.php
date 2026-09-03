<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Smart Gate Presensi RFID &amp; Barcode — SMKN 1 Air Naningan</title>
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
    :root, [data-theme="light"] {
      --bg: #F8FAFC;
      --bg-2: #FFFFFF;
      --bg-3: #F1F5F9;
      --surface: rgba(0,0,0,0.03);
      --border: rgba(0,0,0,0.08);
      --border-2: rgba(0,0,0,0.14);
      --text: #0F172A;
      --text-2: #475569;
      --text-3: #94A3B8;
      --green: #16A34A;
      --green-dim: rgba(22,163,74,0.1);
      --amber: #D97706;
      --amber-dim: rgba(217,119,6,0.1);
      --blue: #2563EB;
      --blue-dim: rgba(37,99,235,0.1);
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
      --green: #22C55E;
      --green-dim: rgba(34,197,94,0.15);
      --amber: #F59E0B;
      --amber-dim: rgba(245,158,11,0.15);
      --blue: #3B82F6;
      --blue-dim: rgba(59,130,246,0.15);
      --red: #EF4444;
      --red-dim: rgba(239,68,68,0.15);
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      background: var(--bg);
      color: var(--text);
      font-family: var(--font);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      overflow-x: hidden;
      transition: background .25s ease, color .25s ease;
    }

    .bg-orb { position: fixed; pointer-events: none; z-index: 0; border-radius: 50%; filter: blur(120px); opacity: .25; }
    .bg-orb-1 { width: 600px; height: 600px; background: radial-gradient(circle, rgba(0,0,0,0.2) 0%, transparent 70%); top: -150px; left: -150px; }
    .bg-orb-2 { width: 500px; height: 500px; background: radial-gradient(circle, rgba(37,99,235,0.2) 0%, transparent 70%); bottom: -100px; right: -100px; }
    .bg-grid { position: fixed; inset: 0; z-index: 0; pointer-events: none; background-image: linear-gradient(rgba(0,0,0,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,0.03) 1px, transparent 1px); background-size: 50px 50px; }

    header {
      position: relative;
      z-index: 10;
      padding: 16px 28px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      border-bottom: 1px solid var(--border);
      background: var(--bg-2);
    }
    .brand { display: flex; align-items: center; gap: 12px; }
    .brand-logo {
      width: 44px; height: 44px;
      border-radius: var(--r-sm);
      background: var(--surface);
      border: 1px solid var(--border-2);
      padding: 4px;
      display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .brand-logo img { width: 100%; height: 100%; object-fit: contain; }
    .brand-title { font-weight: 900; font-size: 16px; color: var(--text); }
    .brand-slogan { font-size: 11px; color: var(--text-3); font-weight: 600; display: block; }

    .header-actions { display: flex; align-items: center; gap: 10px; }
    .live-clock {
      padding: 6px 14px;
      background: var(--bg-3);
      border: 1px solid var(--border-2);
      border-radius: var(--r-sm);
      text-align: right;
    }
    .clock-time { font-family: var(--font-mono); font-size: 16px; font-weight: 900; color: var(--text); }
    .clock-date { font-size: 11px; color: var(--text-3); font-weight: 600; }

    .btn-tool {
      width: 38px; height: 38px;
      border-radius: var(--r-sm);
      background: var(--bg-3);
      border: 1px solid var(--border-2);
      color: var(--text);
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; font-size: 16px;
      transition: all .2s;
    }
    .btn-tool:hover { transform: scale(1.05); border-color: #000; }

    main {
      position: relative;
      z-index: 10;
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px 20px;
    }

    .kiosk-container {
      width: 100%;
      max-width: 1050px;
      display: grid;
      grid-template-columns: 1fr 1.1fr;
      gap: 24px;
    }
    @media (max-width: 860px) {
      .kiosk-container { grid-template-columns: 1fr; }
    }

    /* Tap Target Zone */
    .tap-card {
      background: var(--bg-2);
      border: 2px solid var(--border-2);
      border-radius: var(--r-xl);
      padding: 36px 28px;
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
      box-shadow: 0 12px 36px rgba(0,0,0,0.06);
    }
    .pulse-ring {
      width: 140px; height: 140px;
      border-radius: 50%;
      background: var(--surface);
      border: 2px dashed var(--border-2);
      display: flex; align-items: center; justify-content: center;
      font-size: 56px; color: var(--text);
      margin-bottom: 20px;
      position: relative;
      animation: pulse 2.5s infinite;
    }
    @keyframes pulse {
      0% { box-shadow: 0 0 0 0 rgba(0,0,0,0.15); }
      70% { box-shadow: 0 0 0 24px rgba(0,0,0,0); }
      100% { box-shadow: 0 0 0 0 rgba(0,0,0,0); }
    }
    .tap-title { font-size: 20px; font-weight: 900; color: var(--text); margin-bottom: 6px; }
    .tap-desc { font-size: 13px; color: var(--text-3); max-width: 320px; line-height: 1.5; }

    /* Hidden scanner input that always captures scanner keypresses */
    #rfidInput {
      position: absolute;
      opacity: 0;
      pointer-events: none;
      top: -9999px;
    }

    /* Result Card */
    .result-card {
      background: var(--bg-2);
      border: 2px solid var(--border-2);
      border-radius: var(--r-xl);
      padding: 28px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      min-height: 380px;
      box-shadow: 0 12px 36px rgba(0,0,0,0.06);
      transition: border-color .3s, background .3s;
    }
    .result-card.success { border-color: var(--green); }
    .result-card.warning { border-color: var(--amber); }
    .result-card.error { border-color: var(--red); }

    .person-hero {
      display: flex;
      align-items: center;
      gap: 18px;
      margin-bottom: 20px;
    }
    .person-photo {
      width: 80px; height: 80px;
      border-radius: 18px;
      background: var(--surface);
      border: 2px solid var(--border-2);
      object-fit: cover;
      flex-shrink: 0;
    }
    .person-name { font-size: 20px; font-weight: 900; color: var(--text); line-height: 1.25; }
    .person-sub { font-size: 13px; color: var(--text-3); font-weight: 600; margin-top: 4px; }
    .person-tag {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 6px;
      font-size: 11px;
      font-weight: 800;
      margin-top: 6px;
      font-family: var(--font-mono);
      background: var(--bg-3);
      color: var(--text-2);
    }

    .badge-status-lg {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 18px;
      border-radius: 12px;
      font-size: 14px;
      font-weight: 900;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      margin-bottom: 12px;
    }
    .badge-status-lg.hadir { background: var(--green-dim); color: var(--green); border: 1.5px solid var(--green); }
    .badge-status-lg.terlambat { background: var(--amber-dim); color: var(--amber); border: 1.5px solid var(--amber); }
    .badge-status-lg.pulang { background: var(--blue-dim); color: var(--blue); border: 1.5px solid var(--blue); }
    .badge-status-lg.error { background: var(--red-dim); color: var(--red); border: 1.5px solid var(--red); }

    .result-feed {
      border-top: 1px solid var(--border);
      padding-top: 16px;
      margin-top: auto;
    }
    .feed-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 8px 0;
      border-bottom: 1px solid var(--border);
      font-size: 12.5px;
    }
    .feed-item:last-child { border-bottom: none; }

    footer {
      position: relative;
      z-index: 10;
      padding: 12px 28px;
      background: var(--bg-2);
      border-top: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 12px;
      color: var(--text-3);
    }
  </style>
</head>
<body>

<div class="bg-orb bg-orb-1"></div>
<div class="bg-orb bg-orb-2"></div>
<div class="bg-grid"></div>

<!-- HEADER -->
<header>
  <div class="brand">
    <div class="brand-logo">
      <img src="/img/logo.png" alt="Logo" />
    </div>
    <div>
      <div class="brand-title">SMKN 1 AIR NANINGAN</div>
      <span class="brand-slogan">Smart Gate RFID &amp; Barcode Presensi</span>
    </div>
  </div>

  <div class="header-actions">
    <div class="live-clock">
      <div class="clock-time" id="clockTime">--:--:-- WIB</div>
      <div class="clock-date" id="clockDate">{{ $hariIni }}</div>
    </div>
    <button type="button" class="btn-tool" onclick="toggleTheme()" title="Ganti Tema (Gelap/Terang)">
      <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
    </button>
    <button type="button" class="btn-tool" onclick="toggleFullscreen()" title="Layar Penuh (F11)">
      <i class="bi bi-fullscreen" id="fsIcon"></i>
    </button>
    <a href="/dashboard" class="btn-tool" title="Kembali ke Dasbor" style="text-decoration:none;">
      <i class="bi bi-speedometer2"></i>
    </a>
  </div>
</header>

<!-- MAIN CONTENT -->
<main onclick="focusScanner()">
  <input type="text" id="rfidInput" autofocus autocomplete="off" />

  <div class="kiosk-container">

    <!-- TAP ZONE -->
    <div class="tap-card">
      <div class="pulse-ring" id="pulseRing">
        <i class="bi bi-upc-scan"></i>
      </div>
      <div class="tap-title" id="tapPromptTitle">Tempelkan Kartu RFID / e-KTP</div>
      <div class="tap-desc" id="tapPromptDesc">
        Arahkan Barcode NISN siswa / NIP guru ke scanner USB, atau tempelkan kartu RFID / e-KTP pada alat pembaca.
      </div>


      <div style="margin-top:24px; display:flex; gap:12px; flex-wrap:wrap; justify-content:center;">
        <span class="person-tag"><i class="bi bi-credit-card-2-front"></i> RFID 13.56 MHz / 125 kHz</span>
        <span class="person-tag"><i class="bi bi-upc"></i> Barcode 1D / 2D</span>
        <span class="person-tag"><i class="bi bi-qr-code"></i> QR Code Siswa</span>
      </div>

      <div id="scanIndicator" style="margin-top:20px; font-size:12px; font-weight:700; color:var(--text-3);">
        <i class="bi bi-circle-fill" style="color:var(--green); font-size:8px; margin-right:4px;"></i> Pemindai Siap Menerima Input
      </div>
    </div>

    <!-- RESULT FEEDBACK CARD -->
    <div class="result-card" id="resultCard">
      <div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
          <span style="font-size:11px; font-weight:800; text-transform:uppercase; color:var(--text-3); letter-spacing:0.5px;">HASIL PEMINDAIAN TERAKHIR</span>
          <span id="badgeAction" class="badge-status-lg hadir" style="display:none;"></span>
        </div>

        <div class="person-hero" id="personHero" style="display:none;">
          <img src="/img/user-default.png" alt="Foto" class="person-photo" id="personPhoto" />
          <div>
            <div class="person-name" id="personName">-</div>
            <div class="person-sub" id="personSub">-</div>
            <span class="person-tag" id="personTag">-</span>
          </div>
        </div>

        <div id="emptyHero" style="padding:40px 0; text-align:center; color:var(--text-3);">
          <i class="bi bi-person-badge" style="font-size:48px; opacity:0.3; display:block; margin-bottom:10px;"></i>
          <div style="font-size:14px; font-weight:700;">Belum Ada Scan</div>
          <div style="font-size:12px;">Data kehadiran akan muncul di sini secara instan saat kartu/barcode terbaca.</div>
        </div>

        <div id="resultMessage" style="font-size:13.5px; font-weight:700; margin-top:10px; display:none;"></div>
      </div>

      <!-- RECENT LOG MINI FEED -->
      <div class="result-feed">
        <div style="font-size:11px; font-weight:800; text-transform:uppercase; color:var(--text-3); letter-spacing:0.5px; margin-bottom:8px;">
          <i class="bi bi-clock-history"></i> Log Presensi Terkini
        </div>
        <div id="recentList">
          <div style="font-size:12px; color:var(--text-3); padding:4px 0;">Menunggu pemindaian hari ini...</div>
        </div>
      </div>
    </div>

  </div>
</main>

<!-- FOOTER -->
<footer>
  <div>
    <strong>SMKN 1 Air Naningan</strong> &bull; SIRANI Smart Gate System &bull; Tahun Ajaran 2026/2027
  </div>
  <div>
    Sesi: <strong>{{ ($jadwal && $jadwal->is_sesi_buka) ? 'GERBANG DIBUKA' : 'GERBANG DITUTUP' }}</strong> &bull;
    Toleransi Masuk: <strong>{{ $jadwal ? substr($jadwal->jam_masuk_toleransi, 0, 5) : '07:15' }} WIB</strong>
  </div>
</footer>

<script>
  let scannerBuffer = '';
  let scannerTimeout = null;
  const recentItems = [];

  function focusScanner() {
    const inp = document.getElementById('rfidInput');
    if (inp) inp.focus();
  }

  // Real-Time Clock
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

  // Web Speech Announcement
  function speak(text) {
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

  // Sound Beep
  function playBeep(type = 'success') {
    try {
      const ctx = new (window.AudioContext || window.webkitAudioContext)();
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.connect(gain);
      gain.connect(ctx.destination);
      if (type === 'success') {
        osc.frequency.setValueAtTime(880, ctx.currentTime);
        osc.frequency.setValueAtTime(1320, ctx.currentTime + 0.1);
        gain.gain.setValueAtTime(0.3, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.3);
      } else {
        osc.frequency.setValueAtTime(300, ctx.currentTime);
        osc.frequency.setValueAtTime(200, ctx.currentTime + 0.15);
        gain.gain.setValueAtTime(0.4, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.35);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.35);
      }
    } catch (e) {}
  }

  // Handle Scan Request
  async function processCode(code) {
    const cleanCode = code.trim();
    if (!cleanCode || cleanCode.length < 3) return;

    const ind = document.getElementById('scanIndicator');
    if (ind) ind.innerHTML = '<i class="bi bi-arrow-repeat spin" style="color:var(--blue);"></i> Memproses data presensi...';

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
      if (ind) ind.innerHTML = '<i class="bi bi-circle-fill" style="color:var(--green); font-size:8px; margin-right:4px;"></i> Pemindai Siap Menerima Input';
      focusScanner();
    }
  }

  function renderScanResult(res) {
    const card = document.getElementById('resultCard');
    const hero = document.getElementById('personHero');
    const empty = document.getElementById('emptyHero');
    const badge = document.getElementById('badgeAction');
    const photo = document.getElementById('personPhoto');
    const nameEl = document.getElementById('personName');
    const subEl = document.getElementById('personSub');
    const tagEl = document.getElementById('personTag');
    const msgEl = document.getElementById('resultMessage');

    card.className = 'result-card ' + (res.success ? (res.data?.status === 'terlambat' ? 'warning' : 'success') : 'error');
    empty.style.display = 'none';
    hero.style.display = 'flex';
    badge.style.display = 'inline-flex';
    msgEl.style.display = 'block';

    if (res.success && res.data) {
      playBeep('success');
      const d = res.data;
      photo.src = d.foto || d.foto_url || '/img/user-default.png';
      nameEl.textContent = d.nama || 'Pengguna';

      // 1. Format Subtitle: Jabatan / Kelas dan Identitas (NISN / NIP)
      const subInfo = (d.sub || d.rombel_atau_jabatan || '').trim();
      const idInfo = (d.identitas || '').trim();
      if (subInfo && idInfo && subInfo !== idInfo) {
        subEl.textContent = `${subInfo} • ${idInfo}`;
      } else {
        subEl.textContent = subInfo || idInfo || '-';
      }

      // 2. Format Tag Role & Waktu Scan
      const isGuru = (d.tipe === 'guru' || d.type === 'guru');
      const roleText = isGuru ? 'Guru / Pegawai' : 'Siswa';
      const jamText = d.jam || d.jam_masuk || d.jam_pulang || '';
      tagEl.textContent = jamText ? `${roleText} • ${jamText} WIB` : roleText;

      const st = (d.status || 'hadir').toLowerCase();
      badge.className = 'badge-status-lg ' + st;
      badge.innerHTML = `<i class="bi bi-check-circle-fill"></i> ${st.toUpperCase()}`;

      msgEl.style.color = st === 'terlambat' ? 'var(--amber)' : 'var(--green)';
      msgEl.textContent = res.message || 'Presensi berhasil dicatat.';

      // Speech voice
      const speechNama = (d.nama || '').split(',')[0].trim();
      if (st === 'terlambat') {
        speak(`Perhatian, ${speechNama}, Anda tercatat terlambat.`);
      } else if (st === 'pulang') {
        speak(`Terima kasih, ${speechNama}, presensi pulang berhasil. Hati-hati di jalan.`);
      } else {
        speak(`Selamat pagi, ${speechNama}, presensi berhasil.`);
      }

      // Add to recent feed
      addRecentItem(d.nama, d.sub || d.rombel_atau_jabatan || '', st, d.jam || 'Baru saja');
    } else {
      playBeep('error');
      photo.src = '/img/user-default.png';
      nameEl.textContent = 'Gagal';
      subEl.textContent = 'Kartu / Barcode Tidak Valid';
      tagEl.textContent = 'Tolak';
      badge.className = 'badge-status-lg error';
      badge.innerHTML = '<i class="bi bi-x-circle-fill"></i> DITOLAK';
      msgEl.style.color = 'var(--red)';
      msgEl.textContent = res.message || 'Kartu belum terdaftar atau gerbang ditutup.';
      speak('Kartu tidak valid atau belum terdaftar.');
    }
  }

  function addRecentItem(nama, sub, status, time) {
    const list = document.getElementById('recentList');
    recentItems.unshift({ nama, sub, status, time });
    if (recentItems.length > 5) recentItems.pop();

    list.innerHTML = recentItems.map(item => `
      <div class="feed-item">
        <div>
          <strong style="color:var(--text);">${item.nama}</strong>
          ${item.sub ? `<span style="color:var(--text-3); font-size:11px;"> • ${item.sub}</span>` : ''}
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
          <span class="person-tag" style="padding:2px 6px;">${item.status.toUpperCase()}</span>
          <span style="font-family:var(--font-mono); font-size:11px; color:var(--text-3);">${item.time}</span>
        </div>
      </div>
    `).join('');
  }


  // Global key listener for barcode scanner / RFID reader keyboard emulation
  document.addEventListener('keydown', (e) => {
    // If Enter key is pressed, process collected buffer
    if (e.key === 'Enter') {
      if (scannerBuffer.length >= 3) {
        processCode(scannerBuffer);
        scannerBuffer = '';
      }
      return;
    }

    // Capture characters
    if (e.key.length === 1) {
      scannerBuffer += e.key;
      clearTimeout(scannerTimeout);
      scannerTimeout = setTimeout(() => {
        // If timed out and no Enter sent, check buffer length
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
