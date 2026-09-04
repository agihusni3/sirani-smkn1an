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
  <link rel="stylesheet" href="{{ asset('css/welcome.css') }}?v={{ filemtime(public_path('css/welcome.css')) }}">
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
