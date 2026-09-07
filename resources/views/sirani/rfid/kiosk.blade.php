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
  <link rel="stylesheet" href="{{ asset('css/rfid-kiosk.css') }}?v={{ filemtime(public_path('css/rfid-kiosk.css')) }}">
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
        <span class="header-status-badge open">
          <span class="pulse-dot"></span> SMART GATE AKTIF
        </span>
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
    <a href="/sirani" class="action-btn" title="Kembali ke Dasbor SIRANI" style="text-decoration:none;">
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

      const curHour = new Date().getHours();
      let salam = 'Selamat pagi';
      if (curHour >= 11 && curHour < 15) {
        salam = 'Selamat siang';
      } else if (curHour >= 15 && curHour < 18) {
        salam = 'Selamat sore';
      } else if (curHour >= 18 || curHour < 5) {
        salam = 'Halo';
      }

      const speechNama = (d.nama || '').split(',')[0].trim();

      // Status variations
      if (st === 'selesai' || res.type === 'sudah_lengkap') {
        card.className = 'identity-result-card status-selesai';
        badge.className = 'result-badge-large selesai';
        badgeTxt.textContent = 'PRESENSI SELESAI';
        avatarWrap.style.borderColor = '#94A3B8';
        avatarWrap.style.boxShadow = '0 0 25px rgba(148,163,184,0.3)';
        countdownFill.style.background = '#94A3B8';
        msgTxt.textContent = res.message || 'Presensi hari ini sudah lengkap.';
        speak(`${salam}, ${speechNama}, presensi Anda hari ini sudah selesai.`);
      } else if (st === 'sudah_masuk' || res.type === 'sudah_masuk' || res.type === 'belum_waktunya_pulang') {
        card.className = 'identity-result-card status-info';
        badge.className = 'result-badge-large info';
        badgeTxt.textContent = (res.type === 'belum_waktunya_pulang') ? 'BELUM WAKTUNYA PULANG' : 'SUDAH PRESENSI MASUK';
        avatarWrap.style.borderColor = '#3B82F6';
        avatarWrap.style.boxShadow = '0 0 25px rgba(59,130,246,0.3)';
        countdownFill.style.background = '#3B82F6';
        msgTxt.textContent = res.message || 'Anda sudah melakukan presensi masuk.';
        speak(`${salam}, ${speechNama}, Anda sudah tercatat presensi masuk.`);
      } else if (st === 'terlambat') {
        card.className = 'identity-result-card status-terlambat';
        badge.className = 'result-badge-large terlambat';
        badgeTxt.textContent = 'TERLAMBAT';
        avatarWrap.style.borderColor = 'var(--amber)';
        avatarWrap.style.boxShadow = '0 0 25px var(--amber-glow)';
        countdownFill.style.background = 'var(--amber)';
        msgTxt.textContent = res.message || 'Presensi terlambat dicatat.';
        speak(`Perhatian, ${speechNama}, Anda tercatat terlambat.`);
      } else if (st === 'pulang') {
        card.className = 'identity-result-card status-pulang';
        badge.className = 'result-badge-large pulang';
        badgeTxt.textContent = 'BERHASIL PULANG';
        avatarWrap.style.borderColor = 'var(--cyan)';
        avatarWrap.style.boxShadow = '0 0 25px var(--cyan-glow)';
        countdownFill.style.background = 'var(--cyan)';
        msgTxt.textContent = res.message || 'Presensi pulang berhasil dicatat. Hati-hati di jalan!';
        speak(`Terima kasih, ${speechNama}, presensi pulang berhasil. Hati-hati di jalan.`);
      } else {
        card.className = 'identity-result-card status-hadir';
        badge.className = 'result-badge-large hadir';
        badgeTxt.textContent = 'BERHASIL HADIR';
        avatarWrap.style.borderColor = 'var(--emerald)';
        avatarWrap.style.boxShadow = '0 0 25px var(--emerald-glow)';
        countdownFill.style.background = 'var(--emerald)';
        msgTxt.textContent = res.message || 'Presensi masuk berhasil dicatat.';
        speak(`${salam}, ${speechNama}, presensi berhasil.`);
      }

    } else {
      playBeep('error');
      card.className = 'identity-result-card status-error';
      badge.className = 'result-badge-large error';
      badgeTxt.textContent = (res.type === 'di_luar_jam_operasional' || res.type === 'jam_tutup_terlewat') ? 'DITUTUP' : 'DITOLAK';
      photo.src = (res.data && res.data.foto) ? res.data.foto : '/img/user-default.png';
      avatarWrap.style.borderColor = 'var(--rose)';
      avatarWrap.style.boxShadow = '0 0 25px var(--rose-glow)';
      countdownFill.style.background = 'var(--rose)';
      nameEl.textContent = (res.data && res.data.nama) ? res.data.nama : 'Pemindaian Gagal';
      subTxt.textContent = (res.data && (res.data.sub || res.data.rombel_atau_jabatan)) ? `${res.data.sub || res.data.rombel_atau_jabatan} • ${res.data.identitas || ''}` : 'Sistem Smart Gate';
      timeTxt.textContent = (res.data && res.data.jam) ? `Presensi Pukul ${res.data.jam} WIB` : 'Di Luar Ketentuan';
      msgTxt.textContent = res.message || 'Kartu atau Barcode belum terdaftar di sistem.';
      speak(res.message ? res.message.split('.')[0] : 'Presensi tidak dapat diproses.');
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
