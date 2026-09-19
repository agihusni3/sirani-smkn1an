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
        @if($isLibur ?? false)
          <span class="header-status-badge" style="background:rgba(239,68,68,0.15);color:#ef4444;border-color:rgba(239,68,68,0.3);">
            <span class="pulse-dot" style="background:#ef4444;"></span> HARI LIBUR
          </span>
        @else
          <span class="header-status-badge open">
            <span class="pulse-dot"></span> SMART GATE AKTIF
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

    <!-- Absen Terkini & Pantau Gagal Button -->
    <button type="button" class="monitor-trigger-btn" id="btnOpenMonitor" onclick="openMonitorDrawer()" title="Absen Terkini & Pantau Gagal">
      <i class="bi bi-activity" style="color:var(--cyan);"></i>
      <span class="monitor-btn-label">Absen Terkini</span>
      <span class="monitor-badge-pill" id="headerFailedBadge" style="display:none;">0 Gagal</span>
    </button>

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

<!-- MAIN KIOSK INTERFACE (2-COLUMN GRID) -->
<main onclick="focusScanner()">
  <input type="text" id="rfidInput" autofocus autocomplete="off" />

  <div class="kiosk-layout-grid">

    <!-- ══ SEBELAH KIRI: TEMPELKAN KARTU / SCAN BARCODE & HASIL IDENTITAS ══ -->
    <div class="kiosk-col-left kiosk-col-scanner" id="kioskColLeft">
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

          <div class="scanner-protocols">
            <span class="protocol-pill"><i class="bi bi-broadcast-pin"></i> RFID 13.56 MHz</span>
            <span class="protocol-pill"><i class="bi bi-qr-code-scan"></i> Barcode 1D / 2D QR</span>
            <span class="protocol-pill"><i class="bi bi-cloud-check"></i> Cloud Sync</span>
          </div>
        </div>

        <!-- ══ STATE 2: RESPONSE IDENTITAS PROFIL LENGKAP (KETIKA SCAN BERHASIL) ══ -->
        <div id="responseState" class="kios-fade-enter" style="display:none;">
          <div class="identity-result-card" id="identityCard">

            {{-- Top Header Row --}}
            <div class="identity-top-bar">
              <div class="identity-badge-group">
                <div class="result-badge-large hadir" id="resBadge">
                  <i class="bi bi-check-circle-fill" id="resBadgeIcon"></i>
                  <span id="resBadgeText">BERHASIL HADIR</span>
                </div>
                <span class="identity-role-pill" id="resRolePill">SISWA AKTIF</span>
              </div>
              <div class="identity-gate-label">
                <span class="live-dot-pulse"></span>
                <span>Smart Gate Presensi SMKN 1 Air Naningan</span>
              </div>
            </div>

            {{-- Body: Foto & Profil Lengkap --}}
            <div class="identity-body">
              <div class="identity-avatar-wrap" id="avatarWrap">
                <img id="resPhoto" class="identity-avatar-img" src="/img/user-default.png" alt="Foto Profil" onerror="this.src='/img/user-default.png'" />
                <div class="avatar-status-pip" id="avatarPip">
                  <i class="bi bi-check" id="avatarPipIcon"></i>
                </div>
              </div>

              <div class="identity-details">
                <div class="identity-name-text" id="resName">-</div>

                <!-- Grid Informasi Profil Siswa / Guru -->
                <div class="identity-meta-grid">
                  <div class="identity-meta-item">
                    <span class="meta-label" id="lblNisn">NISN / NIP</span>
                    <span class="meta-value" id="resNisnNip">-</span>
                  </div>
                  <div class="identity-meta-item">
                    <span class="meta-label" id="lblKelas">Kelas / Rombel</span>
                    <span class="meta-value highlight" id="resKelas">-</span>
                  </div>
                  <div class="identity-meta-item">
                    <span class="meta-label" id="lblJurusan">Program Keahlian</span>
                    <span class="meta-value" id="resJurusan">-</span>
                  </div>
                  <div class="identity-meta-item">
                    <span class="meta-label">Waktu Presensi</span>
                    <span class="meta-value time" id="resTimeTxt">-</span>
                  </div>
                </div>
              </div>
            </div>

            {{-- Pesan Notifikasi & Info WhatsApp --}}
            <div class="identity-notification-box" id="resMessageBox">
              <div class="notification-main-text">
                <i class="bi bi-info-circle-fill" id="resMsgIcon"></i>
                <span id="resMessageTxt">Presensi berhasil dicatat. Selamat belajar!</span>
              </div>
              <div class="notification-wa-badge" id="resWaBox" style="display:none;">
                <i class="bi bi-whatsapp"></i>
                <span id="resWaTxt">Notifikasi WhatsApp terkirim ke Orang Tua</span>
              </div>
            </div>

            {{-- Auto-Reset Countdown Bar & Quick Button --}}
            <div class="countdown-section">
              <div class="countdown-track">
                <div class="countdown-fill" id="countdownFill"></div>
              </div>
              <div class="countdown-caption-bar">
                <span>Kembali ke mode pemindaian dalam <strong id="countdownSec">5</strong> detik...</span>
                <button type="button" class="btn-scan-next" onclick="returnToScanner()" title="Kembali ke pemindai sekarang">
                  <i class="bi bi-arrow-repeat"></i> Scan Berikutnya
                </button>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>

    <!-- ══ SEBELAH KANAN: AKTIVITAS & PANTAUAN ABSENSI GERBANG REALTIME ══ -->
    <div class="kiosk-col-right kiosk-col-monitor" id="kioskColRight">
      <div class="monitor-panel-card">

        <!-- Panel Header -->
        <div class="monitor-panel-header">
          <div class="monitor-panel-title-group">
            <div class="monitor-panel-title">
              <span class="live-dot-pulse"></span>
              <span>Aktivitas Gerbang Presensi</span>
            </div>
            <div class="monitor-panel-sub">Pantauan Realtime Siswa &amp; Guru SMKN 1 Air Naningan</div>
          </div>
          <button type="button" class="drawer-icon-btn" onclick="fetchMonitorFeed(true)" title="Segarkan Data Realtime">
            <i class="bi bi-arrow-clockwise" id="monitorRefreshIcon"></i>
          </button>
        </div>

        <!-- Quick Stats Counter Grid -->
        <div class="monitor-stats-grid">
          <div class="monitor-stat-item" onclick="switchMonitorTab('live')" style="cursor:pointer;" title="Klik untuk lihat Semua Hadir">
            <div class="monitor-stat-val c-emerald" id="mStatHadir">{{ $totalHadirHariIni ?? 0 }}</div>
            <div class="monitor-stat-lbl">Hadir</div>
          </div>
          <div class="monitor-stat-item" onclick="switchMonitorTab('live')" style="cursor:pointer;" title="Klik untuk lihat Terlambat">
            <div class="monitor-stat-val c-amber" id="mStatTerlambat">{{ $totalTerlambatHariIni ?? 0 }}</div>
            <div class="monitor-stat-lbl">Terlambat</div>
          </div>
          <div class="monitor-stat-item" onclick="switchMonitorTab('gagal')" style="cursor:pointer;" id="mStatGagalCard" title="Klik untuk lihat Kartu Gagal">
            <div class="monitor-stat-val c-rose" id="mStatGagal">0</div>
            <div class="monitor-stat-lbl">Gagal Scan</div>
          </div>
          <div class="monitor-stat-item" onclick="switchMonitorTab('belum')" style="cursor:pointer;" title="Klik untuk pantau Siswa Belum Hadir">
            <div class="monitor-stat-val c-slate" id="mStatBelum">{{ ($isLibur ?? false) ? 0 : ($totalBelumHadirHariIni ?? 0) }}</div>
            <div class="monitor-stat-lbl">Belum Hadir</div>
          </div>
        </div>

        <!-- ══ LIVE ACTIVITY TICKER SPOTLIGHT ══ -->
        <div class="kiosk-live-ticker" id="kioskLiveTicker" onclick="handleTickerClick()" title="Klik untuk beralih tab aktivitas">
          <div class="ticker-pulse-icon">
            <span class="ticker-dot"></span>
            <i class="bi bi-broadcast"></i>
          </div>
          <div class="ticker-content" id="tickerContent">
            <span class="ticker-label">ABSEN TERKINI:</span>
            <span class="ticker-text" id="tickerText">Memuat aktivitas gerbang presensi...</span>
          </div>
          <div class="ticker-meta" id="tickerMeta">
            <span class="ticker-stat" id="tickerStatHadir"><i class="bi bi-check-circle-fill"></i> <span id="tickerCountHadir">{{ $totalHadirHariIni ?? 0 }}</span> Hadir</span>
            <span class="ticker-stat failed" id="tickerStatGagal" style="display:none;"><i class="bi bi-exclamation-octagon-fill"></i> <span id="tickerCountGagal">0</span> Gagal</span>
            <span class="ticker-arrow"><i class="bi bi-chevron-right"></i></span>
          </div>
        </div>

        <!-- Tab Buttons Navigation -->
        <div class="monitor-tabs">
          <button type="button" class="monitor-tab-btn active" id="tabBtnLive" onclick="switchMonitorTab('live')">
            <i class="bi bi-broadcast"></i>
            <span>Absen Terkini</span>
            <span class="tab-badge" id="badgeTabLive">0</span>
          </button>
          <button type="button" class="monitor-tab-btn" id="tabBtnGagal" onclick="switchMonitorTab('gagal')">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>Gagal Scan</span>
            <span class="tab-badge danger" id="badgeTabGagal">0</span>
          </button>
          <button type="button" class="monitor-tab-btn" id="tabBtnBelum" onclick="switchMonitorTab('belum')">
            <i class="bi bi-person-x-fill"></i>
            <span>Belum Hadir</span>
            <span class="tab-badge" id="badgeTabBelum">{{ ($isLibur ?? false) ? 0 : ($totalBelumHadirHariIni ?? 0) }}</span>
          </button>
        </div>

        <!-- Tab Content 1: Live Feed Siswa yang Baru Hadir (Default Aktif) -->
        <div class="monitor-drawer-body" id="tabContentLive">
          <div id="listLiveScans">
            <!-- Diisi oleh JS -->
          </div>
        </div>

        <!-- Tab Content 2: Gagal Absen / Kartu Ditolak -->
        <div class="monitor-drawer-body" id="tabContentGagal" style="display:none;">
          <div id="listFailedScans">
            <!-- Diisi oleh JS -->
          </div>
        </div>

        <!-- Tab Content 3: Belum Hadir Hari Ini -->
        <div class="monitor-drawer-body" id="tabContentBelum" style="display:none;">
          <div class="monitor-filter-bar">
            <input type="text" class="monitor-search-input" id="searchBelumInput" placeholder="Cari nama siswa / NISN..." oninput="filterBelumHadir()" />
            <select class="monitor-select-rombel" id="filterRombelSelect" onchange="filterBelumHadir()">
              <option value="">Semua Kelas</option>
            </select>
          </div>
          <div id="listBelumHadir">
            <!-- Diisi oleh JS -->
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
      // Auto-refresh monitor feed instantly upon any scan
      fetchMonitorFeed();
    } catch (err) {
      showIdentityResult({
        success: false,
        message: 'Gagal menghubungi server presensi. Pastikan koneksi stabil.'
      });
      fetchMonitorFeed();
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
    const badgeIcon = document.getElementById('resBadgeIcon');
    const rolePill = document.getElementById('resRolePill');
    const photo = document.getElementById('resPhoto');
    const avatarWrap = document.getElementById('avatarWrap');
    const avatarPip = document.getElementById('avatarPip');
    const avatarPipIcon = document.getElementById('avatarPipIcon');
    const nameEl = document.getElementById('resName');
    const resNisnNip = document.getElementById('resNisnNip');
    const lblNisn = document.getElementById('lblNisn');
    const resKelas = document.getElementById('resKelas');
    const lblKelas = document.getElementById('lblKelas');
    const resJurusan = document.getElementById('resJurusan');
    const timeTxt = document.getElementById('resTimeTxt');
    const msgTxt = document.getElementById('resMessageTxt');
    const msgIcon = document.getElementById('resMsgIcon');
    const waBox = document.getElementById('resWaBox');
    const waTxt = document.getElementById('resWaTxt');
    const countdownFill = document.getElementById('countdownFill');

    // Switch view
    scannerState.style.display = 'none';
    responseState.style.display = 'block';

    if (res.data) {
      playBeep(res.success ? 'success' : 'error');
      const d = res.data;
      const isSiswa = (d.tipe === 'siswa');
      const st = (d.status || 'hadir').toLowerCase();

      photo.src = d.foto || d.foto_url || '/img/user-default.png';
      nameEl.textContent = d.nama || 'Pengguna';
      if (rolePill) rolePill.textContent = d.tipe_label || (isSiswa ? 'SISWA AKTIF' : 'GURU / PTK');

      if (lblNisn) lblNisn.textContent = isSiswa ? 'NISN Siswa' : 'NIP / ID Pegawai';
      if (resNisnNip) resNisnNip.textContent = d.nisn ? `${d.nisn}` : (d.nip ? `${d.nip}` : (d.identitas || '-'));

      if (lblKelas) lblKelas.textContent = isSiswa ? 'Kelas / Rombel' : 'Jabatan';
      if (resKelas) resKelas.textContent = d.kelas || d.sub || d.rombel_atau_jabatan || '-';

      if (resJurusan) resJurusan.textContent = d.jurusan || (isSiswa ? 'Umum' : (d.sub || '-'));

      const jam = d.jam || d.jam_masuk || d.jam_pulang || '';
      if (timeTxt) timeTxt.textContent = jam ? `${jam} WIB` : 'Presensi Tercatat';

      if (waBox) {
        if (d.no_hp_ortu) {
          waBox.style.display = 'inline-flex';
          if (waTxt) waTxt.textContent = `Notifikasi WhatsApp terkirim ke Orang Tua (${d.no_hp_ortu})`;
        } else if (isSiswa) {
          waBox.style.display = 'inline-flex';
          if (waTxt) waTxt.textContent = 'Notifikasi WhatsApp otomatis terkirim ke Orang Tua';
        } else {
          waBox.style.display = 'none';
        }
      }

      // Greetings
      const curHour = new Date().getHours();
      let salam = 'Selamat pagi';
      if (curHour >= 11 && curHour < 15) salam = 'Selamat siang';
      else if (curHour >= 15 && curHour < 18) salam = 'Selamat sore';
      else if (curHour >= 18 || curHour < 5) salam = 'Halo';
      const speechNama = (d.nama || '').split(',')[0].trim();

      if (st === 'libur' || res.type === 'hari_libur') {
        card.className = 'identity-result-card status-libur';
        badge.className = 'result-badge-large libur';
        badgeTxt.textContent = 'HARI LIBUR SEKOLAH';
        if (badgeIcon) badgeIcon.className = 'bi bi-calendar-check-fill';
        avatarWrap.style.borderColor = '#6366f1';
        avatarWrap.style.boxShadow = '0 0 25px rgba(99,102,241,0.3)';
        if (avatarPip) avatarPip.style.background = '#6366f1';
        if (avatarPipIcon) avatarPipIcon.className = 'bi bi-calendar-check';
        countdownFill.style.background = '#6366f1';
        if (msgIcon) msgIcon.className = 'bi bi-info-circle-fill';
        msgTxt.textContent = res.message || 'Hari ini sekolah libur. Data kartu terverifikasi aktif.';
        speak(`${salam}, ${speechNama}. Hari ini libur sekolah.`);
      } else if (st === 'selesai' || res.type === 'sudah_lengkap') {
        card.className = 'identity-result-card status-selesai';
        badge.className = 'result-badge-large selesai';
        badgeTxt.textContent = 'PRESENSI LENGKAP';
        if (badgeIcon) badgeIcon.className = 'bi bi-check-all';
        avatarWrap.style.borderColor = '#94A3B8';
        avatarWrap.style.boxShadow = '0 0 25px rgba(148,163,184,0.3)';
        if (avatarPip) avatarPip.style.background = '#94A3B8';
        if (avatarPipIcon) avatarPipIcon.className = 'bi bi-check';
        countdownFill.style.background = '#94A3B8';
        if (msgIcon) msgIcon.className = 'bi bi-check-circle-fill';
        msgTxt.textContent = res.message || 'Presensi hari ini sudah lengkap (Masuk & Pulang).';
        speak(`${salam}, ${speechNama}, presensi Anda hari ini sudah selesai.`);
      } else if (st === 'sudah_masuk' || res.type === 'belum_waktunya_pulang') {
        card.className = 'identity-result-card status-info';
        badge.className = 'result-badge-large info';
        badgeTxt.textContent = (res.type === 'belum_waktunya_pulang') ? 'BELUM WAKTUNYA PULANG' : 'SUDAH PRESENSI MASUK';
        if (badgeIcon) badgeIcon.className = 'bi bi-info-circle-fill';
        avatarWrap.style.borderColor = '#3B82F6';
        avatarWrap.style.boxShadow = '0 0 25px rgba(59,130,246,0.3)';
        if (avatarPip) avatarPip.style.background = '#3B82F6';
        if (avatarPipIcon) avatarPipIcon.className = 'bi bi-hourglass-split';
        countdownFill.style.background = '#3B82F6';
        if (msgIcon) msgIcon.className = 'bi bi-info-circle-fill';
        msgTxt.textContent = res.message || 'Anda sudah melakukan presensi masuk.';
        speak(`${salam}, ${speechNama}, Anda sudah tercatat presensi masuk.`);
      } else if (st === 'terlambat') {
        card.className = 'identity-result-card status-terlambat';
        badge.className = 'result-badge-large terlambat';
        badgeTxt.textContent = 'TERLAMBAT';
        if (badgeIcon) badgeIcon.className = 'bi bi-clock-history';
        avatarWrap.style.borderColor = 'var(--amber)';
        avatarWrap.style.boxShadow = '0 0 25px var(--amber-glow)';
        if (avatarPip) avatarPip.style.background = 'var(--amber)';
        if (avatarPipIcon) avatarPipIcon.className = 'bi bi-exclamation';
        countdownFill.style.background = 'var(--amber)';
        if (msgIcon) msgIcon.className = 'bi bi-exclamation-triangle-fill';
        msgTxt.textContent = res.message || 'Presensi terlambat dicatat.';
        speak(`Perhatian, ${speechNama}, Anda tercatat terlambat.`);
      } else if (st === 'pulang') {
        card.className = 'identity-result-card status-pulang';
        badge.className = 'result-badge-large pulang';
        badgeTxt.textContent = 'BERHASIL PULANG';
        if (badgeIcon) badgeIcon.className = 'bi bi-box-arrow-right';
        avatarWrap.style.borderColor = 'var(--cyan)';
        avatarWrap.style.boxShadow = '0 0 25px var(--cyan-glow)';
        if (avatarPip) avatarPip.style.background = 'var(--cyan)';
        if (avatarPipIcon) avatarPipIcon.className = 'bi bi-check';
        countdownFill.style.background = 'var(--cyan)';
        if (msgIcon) msgIcon.className = 'bi bi-check-circle-fill';
        msgTxt.textContent = res.message || 'Presensi pulang berhasil dicatat. Hati-hati di jalan!';
        speak(`Terima kasih, ${speechNama}, presensi pulang berhasil. Hati-hati di jalan.`);
      } else if (!res.success) {
        card.className = 'identity-result-card status-error';
        badge.className = 'result-badge-large error';
        badgeTxt.textContent = (res.type === 'di_luar_jam_operasional' || res.type === 'jam_tutup_terlewat') ? 'DITUTUP' : 'DITOLAK';
        if (badgeIcon) badgeIcon.className = 'bi bi-x-circle-fill';
        avatarWrap.style.borderColor = 'var(--rose)';
        avatarWrap.style.boxShadow = '0 0 25px var(--rose-glow)';
        if (avatarPip) avatarPip.style.background = 'var(--rose)';
        if (avatarPipIcon) avatarPipIcon.className = 'bi bi-x';
        countdownFill.style.background = 'var(--rose)';
        if (msgIcon) msgIcon.className = 'bi bi-exclamation-circle-fill';
        msgTxt.textContent = res.message || 'Presensi tidak dapat diproses saat ini.';
        speak(res.message ? res.message.split('.')[0] : 'Presensi tidak dapat diproses.');
      } else {
        card.className = 'identity-result-card status-hadir';
        badge.className = 'result-badge-large hadir';
        badgeTxt.textContent = 'BERHASIL HADIR';
        if (badgeIcon) badgeIcon.className = 'bi bi-check-circle-fill';
        avatarWrap.style.borderColor = 'var(--emerald)';
        avatarWrap.style.boxShadow = '0 0 25px var(--emerald-glow)';
        if (avatarPip) avatarPip.style.background = 'var(--emerald)';
        if (avatarPipIcon) avatarPipIcon.className = 'bi bi-check';
        countdownFill.style.background = 'var(--emerald)';
        if (msgIcon) msgIcon.className = 'bi bi-check-circle-fill';
        msgTxt.textContent = res.message || 'Presensi masuk berhasil dicatat. Selamat belajar!';
        speak(`${salam}, ${speechNama}, presensi berhasil. Selamat belajar.`);
      }

    } else {
      // Pemindaian kartu tidak dikenal
      playBeep('error');
      card.className = 'identity-result-card status-error';
      badge.className = 'result-badge-large error';
      badgeTxt.textContent = 'DITOLAK';
      if (badgeIcon) badgeIcon.className = 'bi bi-x-circle-fill';
      photo.src = '/img/user-default.png';
      avatarWrap.style.borderColor = 'var(--rose)';
      avatarWrap.style.boxShadow = '0 0 25px var(--rose-glow)';
      if (avatarPip) avatarPip.style.background = 'var(--rose)';
      if (avatarPipIcon) avatarPipIcon.className = 'bi bi-x';
      countdownFill.style.background = 'var(--rose)';
      nameEl.textContent = 'Kartu / Barcode Tidak Dikenal';
      if (rolePill) rolePill.textContent = 'TIDAK TERDAFTAR';
      if (lblNisn) lblNisn.textContent = 'Status Kartu';
      if (resNisnNip) resNisnNip.textContent = 'Belum Dipairing';
      if (lblKelas) lblKelas.textContent = 'Tindakan';
      if (resKelas) resKelas.textContent = 'Daftarkan di Menu RFID';
      if (resJurusan) resJurusan.textContent = 'SIRANI Gate';
      if (timeTxt) timeTxt.textContent = '--:-- WIB';
      if (waBox) waBox.style.display = 'none';
      if (msgIcon) msgIcon.className = 'bi bi-exclamation-octagon-fill';
      msgTxt.textContent = res.message || 'Kartu atau Barcode belum terdaftar pada sistem SIRANI SMKN 1 Air Naningan.';
      speak('Kartu atau kode barcode belum terdaftar.');
    }

    startCountdown(6);
  }

  // Countdown timer to return to scanner
  function startCountdown(totalSec) {
    if (countdownTimer) clearInterval(countdownTimer);

    const fill = document.getElementById('countdownFill');
    const secTxt = document.getElementById('countdownSec');
    let remainMs = totalSec * 1000;
    const intervalMs = 100;

    if (secTxt) secTxt.textContent = totalSec;
    if (fill) fill.style.width = '100%';

    countdownTimer = setInterval(() => {
      remainMs -= intervalMs;
      const pct = (remainMs / (totalSec * 1000)) * 100;
      if (fill) fill.style.width = Math.max(0, pct) + '%';
      if (secTxt) secTxt.textContent = Math.ceil(remainMs / 1000);

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

  // ══════════════════════════════════════════════════════════════════
  // ══ ABSEN TERKINI & MONITOR SISWA GAGAL (REAL-TIME FEED & DRAWER) ══
  // ══════════════════════════════════════════════════════════════════

  let monitorData = {
    recent_scans: [],
    failed_scans: [],
    belum_hadir: [],
    stats: {
      total_hadir: 0,
      total_terlambat: 0,
      total_pulang: 0,
      total_gagal: 0,
      total_belum_absen: 0
    }
  };
  let currentMonitorTab = 'live';
  let rombelOptionsPopulated = false;

  function handleTickerClick() {
    if (monitorData.failed_scans && monitorData.failed_scans.length > 0) {
      switchMonitorTab('gagal');
    } else {
      switchMonitorTab('live');
    }
  }

  function openMonitorDrawer(tab = null) {
    if (tab) {
      switchMonitorTab(tab);
    } else if (monitorData.failed_scans && monitorData.failed_scans.length > 0) {
      switchMonitorTab('gagal');
    } else {
      switchMonitorTab('live');
    }
    const el = document.getElementById('kioskColRight') || document.getElementById('kioskColLeft');
    if (el && window.innerWidth <= 1080) {
      el.scrollIntoView({ behavior: 'smooth' });
    }
    fetchMonitorFeed(true);
    focusScanner();
  }

  function switchMonitorTab(tabName) {
    currentMonitorTab = tabName;
    const tabs = ['gagal', 'belum', 'live'];
    tabs.forEach(t => {
      const btn = document.getElementById('tabBtn' + t.charAt(0).toUpperCase() + t.slice(1));
      const content = document.getElementById('tabContent' + t.charAt(0).toUpperCase() + t.slice(1));
      if (btn) btn.classList.toggle('active', t === tabName);
      if (content) content.style.display = (t === tabName) ? 'flex' : 'none';
    });

    if (tabName === 'gagal') renderFailedScans(monitorData.failed_scans || []);
    if (tabName === 'belum') filterBelumHadir();
    if (tabName === 'live') renderLiveFeed(monitorData.recent_scans || []);
    focusScanner();
  }

  async function fetchMonitorFeed(isManual = false) {
    const refreshIcon = document.getElementById('monitorRefreshIcon');
    if (refreshIcon && isManual) refreshIcon.classList.add('spin-anim');

    try {
      const res = await fetch('/api/v1/kiosk-monitor-feed', {
        headers: { 'Accept': 'application/json' }
      });
      if (!res.ok) throw new Error('Network response was not ok');
      const data = await res.json();
      monitorData = data;
      updateMonitorUI();
    } catch (err) {
      console.warn('Gagal memuat feed monitor:', err);
      // Fallback endpoint via web route jika api prefix berbeda
      try {
        const resWeb = await fetch('/kiosk/monitor-feed', {
          headers: { 'Accept': 'application/json' }
        });
        if (resWeb.ok) {
          monitorData = await resWeb.json();
          updateMonitorUI();
        }
      } catch (e2) {}
    } finally {
      if (refreshIcon) {
        setTimeout(() => refreshIcon.classList.remove('spin-anim'), 400);
      }
    }
  }

  function updateMonitorUI() {
    const stats = monitorData.stats || {};
    const isLibur = (monitorData.is_libur !== undefined) ? monitorData.is_libur : (stats.is_libur || false);
    const totalGagal = stats.total_gagal || 0;
    const totalHadir = stats.total_hadir || 0;
    const totalTerlambat = stats.total_terlambat || 0;
    const totalBelum = isLibur ? 0 : (stats.total_belum_absen || 0);

    // Header Badge
    const headerBadge = document.getElementById('headerFailedBadge');
    if (headerBadge) {
      if (totalGagal > 0) {
        headerBadge.textContent = `${totalGagal} Gagal`;
        headerBadge.style.display = 'inline-block';
      } else {
        headerBadge.style.display = 'none';
      }
    }

    // Quick Stats Bar in Drawer
    const elHadir = document.getElementById('mStatHadir');
    const elTerlambat = document.getElementById('mStatTerlambat');
    const elGagal = document.getElementById('mStatGagal');
    const elBelum = document.getElementById('mStatBelum');
    if (elHadir) elHadir.textContent = totalHadir;
    if (elTerlambat) elTerlambat.textContent = totalTerlambat;
    if (elGagal) elGagal.textContent = totalGagal;
    if (elBelum) elBelum.textContent = totalBelum;

    // Tab Badges
    const badgeGagal = document.getElementById('badgeTabGagal');
    const badgeBelum = document.getElementById('badgeTabBelum');
    const badgeLive = document.getElementById('badgeTabLive');
    if (badgeGagal) badgeGagal.textContent = totalGagal;
    if (badgeBelum) badgeBelum.textContent = totalBelum;
    if (badgeLive) badgeLive.textContent = (monitorData.recent_scans || []).length;

    // Live Ticker Below Scanner
    const ticker = document.getElementById('kioskLiveTicker');
    const tickerText = document.getElementById('tickerText');
    const tickerCountHadir = document.getElementById('tickerCountHadir');
    const tickerCountGagal = document.getElementById('tickerCountGagal');
    const tickerStatGagal = document.getElementById('tickerStatGagal');

    if (tickerCountHadir) tickerCountHadir.textContent = totalHadir;
    if (tickerCountGagal) tickerCountGagal.textContent = totalGagal;

    if (ticker && tickerText) {
      if (totalGagal > 0 && monitorData.failed_scans && monitorData.failed_scans.length > 0) {
        ticker.classList.add('has-alert');
        if (tickerStatGagal) tickerStatGagal.style.display = 'inline-flex';
        const lastFail = monitorData.failed_scans[0];
        const failTarget = lastFail.nama || (lastFail.uid ? `Kartu UID: ${lastFail.uid}` : 'Kartu Tidak Dikenal');
        const failJam = lastFail.jam || lastFail.time || '--:--';
        const failAlasan = lastFail.alasan || lastFail.pesan || lastFail.message || 'Kartu belum terdaftar di sistem';
        tickerText.innerHTML = `<strong>PERHATIAN GAGAL ABSEN:</strong> ${escapeHtml(failTarget)} &bull; ${escapeHtml(failAlasan)} (${escapeHtml(failJam)} WIB)`;
      } else if (monitorData.recent_scans && monitorData.recent_scans.length > 0) {
        ticker.classList.remove('has-alert');
        if (tickerStatGagal) tickerStatGagal.style.display = 'none';
        const lastScan = monitorData.recent_scans[0];
        const rombel = lastScan.rombel_atau_jabatan ? ` (${lastScan.rombel_atau_jabatan})` : '';
        tickerText.innerHTML = `<strong>TERBARU:</strong> ${escapeHtml(lastScan.nama)}${escapeHtml(rombel)} &bull; Status <strong>${escapeHtml(lastScan.status_label || lastScan.status)}</strong> (${lastScan.jam} WIB)`;
      } else {
        ticker.classList.remove('has-alert');
        if (tickerStatGagal) tickerStatGagal.style.display = 'none';
        tickerText.textContent = 'Smart Gate Siap &bull; Belum ada pemindaian kartu hari ini.';
      }
    }

    // Populate rombel options once
    populateRombelOptions();

    // Re-render active tab content
    if (currentMonitorTab === 'gagal') renderFailedScans(monitorData.failed_scans || []);
    if (currentMonitorTab === 'belum') filterBelumHadir();
    if (currentMonitorTab === 'live') renderLiveFeed(monitorData.recent_scans || []);
  }

  function populateRombelOptions() {
    if (rombelOptionsPopulated || !monitorData.belum_hadir) return;
    const select = document.getElementById('filterRombelSelect');
    if (!select) return;

    const rombels = [...new Set(monitorData.belum_hadir.map(s => s.rombel).filter(Boolean))].sort();
    if (rombels.length === 0) return;

    select.innerHTML = '<option value="">Semua Kelas (' + monitorData.belum_hadir.length + ')</option>';
    rombels.forEach(r => {
      const opt = document.createElement('option');
      opt.value = r;
      opt.textContent = r;
      select.appendChild(opt);
    });
    rombelOptionsPopulated = true;
  }

  // Render Tab 1: Gagal Absen & Ditolak
  function renderFailedScans(list) {
    const container = document.getElementById('listFailedScans');
    if (!container) return;

    if (!list || list.length === 0) {
      container.innerHTML = `
        <div class="drawer-empty-state">
          <div class="drawer-empty-icon" style="color:var(--emerald);">
            <i class="bi bi-shield-check"></i>
          </div>
          <div class="drawer-empty-title">Semua Pemindaian Aman</div>
          <div class="drawer-empty-desc">
            Belum ada siswa atau kartu yang gagal absen / ditolak pada Smart Gate hari ini.
          </div>
        </div>
      `;
      return;
    }

    let html = '';
    list.forEach((item, idx) => {
      const nama = item.nama || 'Kartu Tidak Dikenal';
      const uid = item.uid || '-';
      const sub = item.rombel || item.identitas || 'Pengguna Tidak Terdaftar';
      const alasan = item.alasan || item.pesan || 'Kartu belum terdaftar di sistem';
      const jam = item.jam || '--:--';
      const waOrtu = item.no_hp_ortu || '';
      const hpClean = item.hp_clean || '';

      let actionButtons = '';
      if (uid && uid !== '-') {
        actionButtons += `
          <button type="button" class="btn-action-sm" onclick="copyUid('${escapeHtml(uid)}')">
            <i class="bi bi-copy"></i> Salin UID: ${escapeHtml(uid)}
          </button>
          <a href="/kartu-rfid" class="btn-action-sm" target="_blank" title="Buka modul manajemen RFID">
            <i class="bi bi-credit-card-2-front"></i> Daftarkan Kartu
          </a>
        `;
      }
      if (hpClean) {
        const pesanWa = encodeURIComponent(`Assalamu'alaikum Wr. Wb. Pemberitahuan Smart Gate SMKN 1 Air Naningan: Kartu absensi ananda ${nama} (${sub}) gagal dipindai pada ${jam} WIB dengan keterangan: "${alasan}". Mohon konfirmasi atau hubungi pihak sekolah.`);
        actionButtons += `
          <a href="https://wa.me/${hpClean}?text=${pesanWa}" target="_blank" class="btn-action-sm btn-wa">
            <i class="bi bi-whatsapp"></i> Hubungi Ortu
          </a>
        `;
      }

      html += `
        <div class="failed-scan-card">
          <div class="failed-icon-badge">
            <i class="bi bi-exclamation-octagon-fill"></i>
          </div>
          <div class="failed-info">
            <div class="failed-header-row">
              <span class="failed-name">${escapeHtml(nama)}</span>
              <span class="failed-time">${escapeHtml(jam)} WIB</span>
            </div>
            <span class="failed-reason-tag">${escapeHtml(alasan)}</span>
            <div class="failed-desc">
              Keterangan: ${escapeHtml(item.pesan || alasan)} &bull; ${escapeHtml(sub)}
            </div>
            <div class="failed-actions">
              ${actionButtons}
            </div>
          </div>
        </div>
      `;
    });

    container.innerHTML = html;
  }

  // Render Tab 2: Belum Hadir Hari Ini
  function filterBelumHadir() {
    const searchVal = (document.getElementById('searchBelumInput')?.value || '').toLowerCase().trim();
    const rombelVal = document.getElementById('filterRombelSelect')?.value || '';
    const container = document.getElementById('listBelumHadir');
    if (!container) return;

    let list = monitorData.belum_hadir || [];
    if (rombelVal) {
      list = list.filter(s => s.rombel === rombelVal);
    }
    if (searchVal) {
      list = list.filter(s => {
        const n = (s.nama || '').toLowerCase();
        const nis = (s.nisn || '').toLowerCase();
        const r = (s.rombel || '').toLowerCase();
        return n.includes(searchVal) || nis.includes(searchVal) || r.includes(searchVal);
      });
    }

    if (list.length === 0) {
      const stats = monitorData.stats || {};
      const isLibur = (monitorData.is_libur !== undefined) ? monitorData.is_libur : (stats.is_libur || false);
      let emptyTitle = 'Tidak Ada Data Siswa';
      let emptyDesc = (searchVal || rombelVal) ? 'Tidak ada siswa belum hadir yang cocok dengan filter pencarian.' : 'Semua siswa telah tercatat hadir hari ini!';

      if (isLibur && !searchVal && !rombelVal) {
        emptyTitle = 'Hari Ini Libur Sekolah';
        emptyDesc = 'Hari ini libur sekolah / akhir pekan. Tidak ada presensi siswa yang wajib hadir atau dihitung belum hadir.';
      }

      container.innerHTML = `
        <div class="drawer-empty-state">
          <div class="drawer-empty-icon" style="color:var(--cyan);">
            <i class="bi ${isLibur ? 'bi-calendar-check' : 'bi-people-fill'}"></i>
          </div>
          <div class="drawer-empty-title">${emptyTitle}</div>
          <div class="drawer-empty-desc">
            ${emptyDesc}
          </div>
        </div>
      `;
      return;
    }

    let html = '';
    list.slice(0, 100).forEach(s => {
      const nama = s.nama || 'Siswa';
      const rombel = s.rombel || '-';
      const nisn = s.nisn || '-';
      const foto = s.foto || '/img/user-default.png';
      const hpClean = s.hp_clean || '';

      let waBtn = '';
      if (hpClean) {
        const pesanWa = encodeURIComponent(`Assalamu'alaikum Wr. Wb. Pemberitahuan Smart Gate SMKN 1 Air Naningan: Menginformasikan bahwa ananda ${nama} (${rombel}) hingga saat ini belum tercatat melakukan presensi kehadiran di sekolah. Mohon informasi dan konfirmasi kehadiran ananda. Terima kasih.`);
        waBtn = `
          <a href="https://wa.me/${hpClean}?text=${pesanWa}" target="_blank" class="btn-action-sm btn-wa" title="Kirim Peringatan WA ke Orang Tua">
            <i class="bi bi-whatsapp"></i> WA Ortu
          </a>
        `;
      } else {
        waBtn = `<span class="btn-action-sm" style="opacity:0.5;cursor:default;" title="Nomor WA ortu belum terdaftar"><i class="bi bi-telephone-x"></i> No WA -</span>`;
      }

      html += `
        <div class="belum-hadir-card">
          <img src="${escapeHtml(foto)}" alt="${escapeHtml(nama)}" class="belum-foto-3x4" onerror="this.src='/img/user-default.png'" />
          <div class="belum-info">
            <div class="belum-nama">${escapeHtml(nama)}</div>
            <div class="belum-sub">
              <strong>${escapeHtml(rombel)}</strong> &bull; NISN: ${escapeHtml(nisn)}
            </div>
          </div>
          <div class="belum-actions">
            ${waBtn}
          </div>
        </div>
      `;
    });

    if (list.length > 100) {
      html += `
        <div style="text-align:center;padding:12px;font-size:12px;color:var(--text-muted);">
          Menampilkan 100 dari ${list.length} siswa. Gunakan pencarian atau filter kelas untuk melihat spesifik.
        </div>
      `;
    }

    container.innerHTML = html;
  }

  // Render Tab 3: Semua Aktivitas Live
  function renderLiveFeed(list) {
    const container = document.getElementById('listLiveScans');
    if (!container) return;

    if (!list || list.length === 0) {
      container.innerHTML = `
        <div class="drawer-empty-state">
          <div class="drawer-empty-icon" style="color:var(--text-muted);">
            <i class="bi bi-broadcast"></i>
          </div>
          <div class="drawer-empty-title">Belum Ada Aktivitas</div>
          <div class="drawer-empty-desc">
            Aliran presensi Smart Gate akan tampil di sini secara kronologis saat kartu di-tap.
          </div>
        </div>
      `;
      return;
    }

    let html = '';
    list.forEach(item => {
      const nama = item.nama || 'Pengguna';
      const sub = item.rombel_atau_jabatan || item.sub || 'Warga Sekolah';
      const st = (item.status || 'hadir').toLowerCase();
      const stLabel = item.status_label || item.status || 'Hadir';
      const jam = item.jam || '--:--';
      const foto = item.foto || '/img/user-default.png';

      let stClass = 'hadir';
      if (st.includes('terlambat')) stClass = 'terlambat';
      else if (st.includes('pulang')) stClass = 'pulang';
      else if (st.includes('gagal') || st.includes('tolak')) stClass = 'gagal';

      html += `
        <div class="feed-scan-item">
          <div class="feed-scan-left">
            <img src="${escapeHtml(foto)}" alt="${escapeHtml(nama)}" class="feed-avatar" onerror="this.src='/img/user-default.png'" />
            <div class="feed-name-box">
              <div class="feed-name">${escapeHtml(nama)}</div>
              <div class="feed-sub">${escapeHtml(sub)}</div>
            </div>
          </div>
          <div class="feed-scan-right">
            <span class="feed-status-badge ${stClass}">${escapeHtml(stLabel)}</span>
            <div class="feed-time">${escapeHtml(jam)} WIB</div>
          </div>
        </div>
      `;
    });

    container.innerHTML = html;
  }

  // Helper Copy UID
  function copyUid(uid) {
    if (!uid) return;
    navigator.clipboard.writeText(uid).then(() => {
      alert('UID Kartu berhasil disalin: ' + uid);
    }).catch(() => {
      prompt('Salin UID kartu berikut:', uid);
    });
  }

  // Helper Escape HTML
  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // ── SCANNER INPUT HANDLER (via hidden rfidInput) ──
  // Tangkap input saat rfidInput aktif (Enter dari barcode scanner)
  const rfidInputEl = document.getElementById('rfidInput');
  if (rfidInputEl) {
    rfidInputEl.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        const val = rfidInputEl.value.trim();
        if (val.length >= 3) {
          processCode(val);
        }
        rfidInputEl.value = '';
      }
    });
    rfidInputEl.addEventListener('input', () => {
      // Jika scanner kirim semua sekaligus tanpa Enter (jarang tapi bisa terjadi)
      clearTimeout(scannerTimeout);
      scannerTimeout = setTimeout(() => {
        const val = rfidInputEl.value.trim();
        if (val.length >= 3) {
          processCode(val);
          rfidInputEl.value = '';
        }
      }, 120);
    });
  }

  // ── GLOBAL KEYDOWN: Tangkap barcode/RFID dari scanner USB HID ──
  // Scanner USB mengirim karakter sangat cepat (<80ms antar karakter)
  // PENTING: tangkap SEMUA keydown kecuali sedang di textarea biasa
  let lastKeyTime = 0;
  document.addEventListener('keydown', (e) => {
    // Escape key
    if (e.key === 'Escape') {
      focusScanner();
      return;
    }

    const activeTag = document.activeElement?.tagName?.toLowerCase();
    const isInTextarea = activeTag === 'textarea';

    // Enter key: proses buffer atau submit dari rfidInput
    if (e.key === 'Enter') {
      if (scannerBuffer.length >= 3) {
        processCode(scannerBuffer);
        scannerBuffer = '';
      } else if (activeTag === 'input' && document.activeElement?.id === 'rfidInput') {
        const val = document.getElementById('rfidInput')?.value?.trim();
        if (val && val.length >= 3) {
          processCode(val);
          document.getElementById('rfidInput').value = '';
        }
      }
      return;
    }

    // Abaikan modifier keys
    if (e.key.length !== 1 || e.ctrlKey || e.altKey || e.metaKey) return;

    // Jika aktif di textarea biasa (bukan rfidInput), abaikan
    if (isInTextarea) return;

    // Cek kecepatan ketik — scanner USB sangat cepat (< 80ms per karakter)
    const now = Date.now();
    const timeDiff = now - lastKeyTime;
    lastKeyTime = now;

    // Jika ketik manual lambat (> 300ms) dan sedang fokus di input selain rfidInput: abaikan
    if (timeDiff > 300 && activeTag === 'input' && document.activeElement?.id !== 'rfidInput') {
      return;
    }

    // Redirect semua keystroke scanner ke rfidInput
    const inp = document.getElementById('rfidInput');
    if (inp && document.activeElement?.id !== 'rfidInput') {
      inp.focus();
    }

    // Akumulasi buffer scanner
    scannerBuffer += e.key;
    clearTimeout(scannerTimeout);
    scannerTimeout = setTimeout(() => {
      if (scannerBuffer.length >= 3) {
        processCode(scannerBuffer);
      }
      scannerBuffer = '';
    }, 80);
  });

  document.addEventListener('DOMContentLoaded', () => {
    focusScanner();
    // Re-fokus ke rfidInput setiap 5 detik untuk jaga-jaga
    setInterval(focusScanner, 5000);
    // Initial fetch of monitor feed & recurring polling every 12 seconds
    fetchMonitorFeed();
    setInterval(() => fetchMonitorFeed(false), 12000);

    // Jika user klik di elemen non-interaktif panel kanan (monitor), kembalikan fokus ke scanner
    const rightPanel = document.getElementById('kioskColRight') || document.getElementById('kioskColLeft');
    if (rightPanel) {
      rightPanel.addEventListener('click', (e) => {
        const clickedTag = e.target?.tagName?.toLowerCase();
        if (!['input', 'select', 'button', 'a', 'textarea'].includes(clickedTag)) {
          setTimeout(focusScanner, 100);
        }
      });
    }
  });
</script>
</body>
</html>
