<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Jam Sekolah &amp; Sesi Operasional — SIRANI SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <style>
    /* ── Pusat Kendali Timeline & Sesi Styling ── */
    .hero-sesi-banner {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-xl);
      padding: 24px 28px;
      margin-bottom: 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 20px;
      position: relative;
      overflow: hidden;
      box-shadow: var(--shadow-sm);
    }
    .hero-sesi-banner::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: var(--gold);
    }

    /* Live Digital Clock Card */
    .digital-clock-box {
      background: var(--bg-3);
      border: 1px solid var(--border-2);
      border-radius: var(--r-md);
      padding: 14px 20px;
      text-align: center;
      min-width: 220px;
      box-shadow: inset 0 2px 6px rgba(0,0,0,0.2);
    }
    .digital-clock-time {
      font-size: 28px;
      font-weight: 900;
      font-family: var(--font-mono);
      color: var(--text);
      letter-spacing: 1.5px;
      line-height: 1.2;
    }
    .pulse-indicator {
      display: inline-block;
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: var(--gold);
      animation: pulseAnim 1.8s infinite;
      margin-right: 4px;
    }
    @keyframes pulseAnim {
      0% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(202,138,4,0.7); }
      70% { transform: scale(1.1); box-shadow: 0 0 0 8px rgba(202,138,4,0); }
      100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(202,138,4,0); }
    }

    /* ── Visual Day Timeline ── */
    .timeline-container {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-lg);
      padding: 22px 24px;
      margin-bottom: 24px;
      box-shadow: var(--shadow-sm);
    }
    .timeline-track {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 12px;
      margin-top: 16px;
      position: relative;
    }
    @media (max-width: 768px) {
      .timeline-track {
        grid-template-columns: 1fr;
      }
    }
    .timeline-phase {
      background: var(--bg-3);
      border: 1.5px solid var(--border-2);
      border-radius: var(--r-md);
      padding: 16px 14px;
      position: relative;
      transition: all .2s ease;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .timeline-phase.active {
      border-color: var(--gold);
      background: rgba(202,138,4,0.06);
      box-shadow: 0 0 0 1px var(--gold);
    }
    .phase-badge {
      font-size: 10.5px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 3px 9px;
      border-radius: 20px;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      width: fit-content;
      margin-bottom: 8px;
      background: var(--bg-2);
      border: 1px solid var(--border);
      color: var(--text-2);
    }
    .timeline-phase.active .phase-badge {
      border-color: var(--gold);
      color: var(--gold);
      background: var(--gold-dim);
    }
    .phase-time {
      font-size: 20px;
      font-weight: 900;
      font-family: var(--font-mono);
      margin: 4px 0 2px 0;
      color: var(--text);
    }
    .phase-desc {
      font-size: 11.5px;
      color: var(--text-3);
      line-height: 1.35;
    }

    /* ── Preset Scenario Cards Grid ── */
    .scenario-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 12px;
      margin-bottom: 20px;
    }
    .scenario-card {
      background: var(--bg-3);
      border: 1.5px solid var(--border);
      border-radius: var(--r-md);
      padding: 16px;
      cursor: pointer;
      transition: all .2s ease;
      text-align: left;
      position: relative;
    }
    .scenario-card:hover {
      border-color: var(--gold);
      transform: translateY(-2px);
      box-shadow: var(--shadow-sm);
      background: var(--surface);
    }
    .scenario-card.selected {
      border-color: var(--gold);
      background: rgba(202,138,4,0.08);
      box-shadow: 0 0 0 1px var(--gold);
    }
    .scenario-icon {
      width: 32px;
      height: 32px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 15px;
      margin-bottom: 10px;
      background: var(--bg-2);
      color: var(--text-2);
      border: 1px solid var(--border);
    }
    .scenario-card:hover .scenario-icon,
    .scenario-card.selected .scenario-icon {
      color: var(--gold);
      border-color: var(--gold);
      background: var(--gold-dim);
    }
    .scenario-title {
      font-size: 13.5px;
      font-weight: 800;
      color: var(--text);
      margin-bottom: 4px;
    }
    .scenario-meta {
      font-size: 11px;
      font-family: var(--font-mono);
      color: var(--text-3);
      font-weight: 700;
    }
    .scenario-card.selected .scenario-meta {
      color: var(--gold);
    }

    /* ── Studio Two-Column Grid ── */
    .studio-layout {
      display: grid;
      grid-template-columns: 1.25fr 0.95fr;
      gap: 24px;
      align-items: start;
      margin-bottom: 28px;
    }
    @media (max-width: 992px) {
      .studio-layout {
        grid-template-columns: 1fr;
      }
    }

    /* Form Fields */
    .time-input-wrap {
      position: relative;
    }
    .time-input-wrap input[type="time"] {
      width: 100%;
      height: 44px;
      padding: 0 14px;
      font-family: var(--font-mono);
      font-size: 16px;
      font-weight: 800;
      background: var(--bg-3);
      border: 1.5px solid var(--border-2);
      border-radius: var(--r-sm);
      color: var(--text);
      transition: border-color .2s, box-shadow .2s;
    }
    .time-input-wrap input[type="time"]:focus {
      outline: none;
      border-color: var(--gold);
      box-shadow: 0 0 0 3px var(--gold-glow);
    }
  </style>
</head>
<body>

<div class="app-container">
  @include('partials.sidebar')

  <main class="main-content">
    <header class="header" style="margin-bottom:20px;">
      <div class="header-title">
        <h1 style="margin:0; font-size:22px; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-clock-history" style="color:var(--gold);"></i> Jam Sekolah &amp; Sesi Operasional
        </h1>
        <p style="margin-top:2px; font-size:13px; color:var(--text-3);">
          Kendali batas toleransi presensi pagi, jam kepulangan, jadwal skenario, dan gerbang Face ID.
        </p>
      </div>
      @include('partials.header_actions')
    </header>

    @if(session('success'))
      <div class="alert-success" style="margin-bottom:18px;">
        <i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="alert-error" style="margin-bottom:18px;">
        <i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i>{{ session('error') }}
      </div>
    @endif

    {{-- ══ 1. HERO STATUS & REAL-TIME CLOCK ══ --}}
    @php
      $currentTime = now()->format('H:i');
      $jamMasuk = substr($jadwalHariIni->jam_masuk_toleransi ?? '07:15', 0, 5);
      $jamPulang = substr($jadwalHariIni->jam_pulang_mulai ?? '15:30', 0, 5);
      $jamTutup = substr($jadwalHariIni->jam_tutup_gerbang ?? '18:00', 0, 5);

      // Determine current active session phase
      $currentPhase = 'tutup';
      if ($currentTime >= '06:00' && $currentTime < $jamMasuk) {
        $currentPhase = 'masuk';
      } elseif ($currentTime >= $jamMasuk && $currentTime < $jamPulang) {
        $currentPhase = 'kbm';
      } elseif ($currentTime >= $jamPulang && $currentTime <= $jamTutup) {
        $currentPhase = 'pulang';
      }
    @endphp

    <div class="hero-sesi-banner">
      <div>
        <div style="display:inline-flex; align-items:center; gap:6px; background:var(--gold-dim); border:1px solid var(--gold); padding:4px 12px; border-radius:20px; font-size:11px; font-weight:800; color:var(--gold); margin-bottom:8px;">
          <span class="pulse-indicator"></span> STATUS OPERASIONAL HARI INI
        </div>
        <h2 style="font-size:22px; font-weight:900; color:var(--text); margin-bottom:4px;">
          {{ $jadwalHariIni->keterangan ?? 'Jadwal Reguler' }}
        </h2>
        <div style="font-size:12.5px; color:var(--text-3);">
          Berlaku untuk: <strong style="color:var(--text);">{{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }}</strong>
          &nbsp;·&nbsp; Tahun Ajaran: <strong style="color:var(--gold);">{{ $taAktif->nama ?? '2026/2027' }}</strong>
        </div>
      </div>

      <div class="digital-clock-box">
        <div style="font-size:10px; font-weight:800; text-transform:uppercase; color:var(--text-3); letter-spacing:0.8px; margin-bottom:4px;">
          <i class="bi bi-broadcast"></i> WAKTU SISTEM SIRANI
        </div>
        <div class="digital-clock-time" id="liveClockDisplay">
          {{ now()->format('H:i:s') }}
        </div>
        <div style="font-size:11px; color:var(--text-3); font-weight:600; margin-top:2px;">
          Zona Waktu: WIB (UTC+7)
        </div>
      </div>
    </div>

    {{-- ══ 2. VISUAL DAY TIMELINE (GARIS WAKTU SESI) ══ --}}
    <div class="timeline-container">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="font-weight:800; font-size:14.5px; color:var(--text); display:flex; align-items:center; gap:8px;">
          <i class="bi bi-segmented-nav" style="color:var(--gold);"></i>
          <span>Garis Waktu &amp; Tahapan Sesi Presensi Harian</span>
        </div>
        <div style="font-size:12px; color:var(--text-3);">
          Sesi Aktif: 
          @if($currentPhase === 'masuk')
            <span class="badge" style="background:var(--gold-dim); color:var(--gold); border:1px solid rgba(202,138,4,0.3); font-weight:800;">Presensi Masuk Pagi</span>
          @elseif($currentPhase === 'kbm')
            <span class="badge" style="background:var(--bg-3); color:var(--text); border:1px solid var(--border); font-weight:800;">Sesi KBM Efektif</span>
          @elseif($currentPhase === 'pulang')
            <span class="badge" style="background:var(--gold-dim); color:var(--gold); border:1px solid rgba(202,138,4,0.3); font-weight:800;">Presensi Pulang</span>
          @else
            <span class="badge" style="background:var(--bg-3); color:var(--text-3); border:1px solid var(--border); font-weight:800;">Gerbang Kunci</span>
          @endif
        </div>
      </div>

      <div class="timeline-track">
        {{-- Sesi 1: Masuk Pagi --}}
        <div class="timeline-phase {{ $currentPhase === 'masuk' ? 'active' : '' }}">
          <div>
            <span class="phase-badge">
              <i class="bi bi-sunrise"></i> Sesi 1: Masuk Pagi
            </span>
            <div class="phase-time">06:00 – {{ $jamMasuk }}</div>
            <div class="phase-desc">Buka gerbang s/d batas kedatangan tepat waktu. Setelah {{ $jamMasuk }} dicatat terlambat.</div>
          </div>
        </div>

        {{-- Sesi 2: KBM --}}
        <div class="timeline-phase {{ $currentPhase === 'kbm' ? 'active' : '' }}">
          <div>
            <span class="phase-badge">
              <i class="bi bi-book"></i> Sesi 2: KBM Efektif
            </span>
            <div class="phase-time">{{ $jamMasuk }} – {{ $jamPulang }}</div>
            <div class="phase-desc">Jam pembelajaran aktif di kelas. Siswa yang keluar wajib mengantongi izin piket.</div>
          </div>
        </div>

        {{-- Sesi 3: Pulang --}}
        <div class="timeline-phase {{ $currentPhase === 'pulang' ? 'active' : '' }}">
          <div>
            <span class="phase-badge">
              <i class="bi bi-sunset"></i> Sesi 3: Presensi Pulang
            </span>
            <div class="phase-time">{{ $jamPulang }} – {{ $jamTutup }}</div>
            <div class="phase-desc">Sesi pemindaian Face ID kepulangan siswa &amp; guru hingga jam batas sore.</div>
          </div>
        </div>

        {{-- Sesi 4: Tutup Malam --}}
        <div class="timeline-phase {{ $currentPhase === 'tutup' ? 'active' : '' }}">
          <div>
            <span class="phase-badge">
              <i class="bi bi-moon"></i> Sesi 4: Gerbang Kunci
            </span>
            <div class="phase-time">{{ $jamTutup }} – 06:00</div>
            <div class="phase-desc">Kiosk gerbang terkunci otomatis. Tap masuk/pulang di luar jam ditolak sistem.</div>
          </div>
        </div>
      </div>
    </div>

    {{-- ══ 3. STUDIO DUA KOLOM: PRESET & FORM EDITOR vs ATURAN OPERASIONAL ══ --}}
    <div class="studio-layout">
      
      {{-- KOLOM KIRI: FORM & PRESET SKENARIO --}}
      <div class="panel" style="padding:22px; border:1px solid var(--border); border-radius:var(--r-md); background:var(--bg-2); box-shadow:var(--shadow-sm);">
        <div style="font-weight:800; font-size:15px; color:var(--text); display:flex; align-items:center; gap:8px; margin-bottom:14px;">
          <i class="bi bi-sliders" style="color:var(--gold);"></i>
          <span>Pilih Skenario &amp; Atur Jam Operasional</span>
        </div>

        {{-- Pilihan Kartu Skenario Preset --}}
        <div style="font-size:11px; font-weight:800; text-transform:uppercase; color:var(--text-3); letter-spacing:0.5px; margin-bottom:8px;">
          Preset Skenario Cepat (1-Klik Terapkan):
        </div>
        <div class="scenario-grid">
          <button type="button" class="scenario-card" onclick="selectScenario('07:15', '15:30', '18:00', 'Jadwal Reguler (Senin - Kamis)', this)">
            <div class="scenario-icon">
              <i class="bi bi-calendar4-week"></i>
            </div>
            <div class="scenario-title">Reguler Senin–Kamis</div>
            <div class="scenario-meta">07:15 &bull; 15:30 &bull; 18:00</div>
          </button>

          <button type="button" class="scenario-card" onclick="selectScenario('07:15', '11:30', '18:00', 'Jadwal Khusus Hari Jumat', this)">
            <div class="scenario-icon">
              <i class="bi bi-sun"></i>
            </div>
            <div class="scenario-title">Khusus Hari Jumat</div>
            <div class="scenario-meta">07:15 &bull; 11:30 &bull; 18:00</div>
          </button>

          <button type="button" class="scenario-card" onclick="selectScenario('07:15', '12:00', '18:00', 'Pekan Penilaian / Ujian Semester', this)">
            <div class="scenario-icon">
              <i class="bi bi-file-earmark-text"></i>
            </div>
            <div class="scenario-title">Pekan Ujian / PTS / PAS</div>
            <div class="scenario-meta">07:15 &bull; 12:00 &bull; 18:00</div>
          </button>

          <button type="button" class="scenario-card" onclick="selectScenario('07:15', '13:00', '18:00', 'Kegiatan Peringatan / Pulang Awal', this)">
            <div class="scenario-icon">
              <i class="bi bi-flag"></i>
            </div>
            <div class="scenario-title">Acara Khusus / Upacara</div>
            <div class="scenario-meta">07:15 &bull; 13:00 &bull; 18:00</div>
          </button>
        </div>

        {{-- Form Input --}}
        <form action="{{ route('admin.jadwal.update') }}" method="POST" id="form-jadwal" style="border-top:1px solid var(--border); padding-top:16px;">
          @csrf
          <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(160px, 1fr)); gap:14px; margin-bottom:14px;">
            <div>
              <label style="display:block; font-size:11.5px; font-weight:800; text-transform:uppercase; color:var(--text-2); margin-bottom:6px;">
                <i class="bi bi-sunrise"></i> Batas Masuk Pagi <span style="color:var(--red);">*</span>
              </label>
              <div class="time-input-wrap">
                <input type="time" name="jam_masuk_toleransi" id="jam_masuk_toleransi" value="{{ $jamMasuk }}" required />
              </div>
            </div>

            <div>
              <label style="display:block; font-size:11.5px; font-weight:800; text-transform:uppercase; color:var(--text-2); margin-bottom:6px;">
                <i class="bi bi-sunset"></i> Mulai Pulang Sore <span style="color:var(--red);">*</span>
              </label>
              <div class="time-input-wrap">
                <input type="time" name="jam_pulang_mulai" id="jam_pulang_mulai" value="{{ $jamPulang }}" required />
              </div>
            </div>

            <div>
              <label style="display:block; font-size:11.5px; font-weight:800; text-transform:uppercase; color:var(--text-2); margin-bottom:6px;">
                <i class="bi bi-moon"></i> Tutup Gerbang <span style="color:var(--red);">*</span>
              </label>
              <div class="time-input-wrap">
                <input type="time" name="jam_tutup_gerbang" id="jam_tutup_gerbang" value="{{ $jamTutup }}" required />
              </div>
            </div>
          </div>

          <div style="margin-bottom:18px;">
            <label style="display:block; font-size:11.5px; font-weight:800; text-transform:uppercase; color:var(--text-2); margin-bottom:6px;">
              <i class="bi bi-chat-left-quote"></i> Keterangan Jadwal Hari Ini
            </label>
            <input type="text" name="keterangan" id="keterangan" value="{{ $jadwalHariIni->keterangan ?? 'Jadwal Reguler' }}" placeholder="Contoh: Jadwal Ujian Semester / Hari Jumat Khusus" class="input-field" style="width:100%; height:40px; font-size:13px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm);" />
          </div>

          <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <div style="font-size:11.5px; color:var(--text-3);">
              <i class="bi bi-info-circle"></i> Jadwal hari berikutnya otomatis kembali ke standar default.
            </div>
            <button type="submit" id="btn-simpan-jadwal" class="btn btn-gold" style="height:42px; padding:0 24px; font-weight:800; font-size:13px; display:inline-flex; align-items:center; gap:8px;">
              <i class="bi bi-check2-circle"></i> Terapkan Jadwal Operasional
            </button>
          </div>
        </form>
      </div>

      {{-- KOLOM KANAN: PANDUAN & KEBIJAKAN OPERASIONAL --}}
      <div style="display:flex; flex-direction:column; gap:16px;">
        <div class="panel" style="padding:20px; border:1px solid var(--border); border-radius:var(--r-md); background:var(--bg-2); box-shadow:var(--shadow-sm);">
          <div style="font-weight:800; font-size:14px; color:var(--text); display:flex; align-items:center; gap:8px; margin-bottom:12px;">
            <i class="bi bi-shield-check" style="color:var(--gold);"></i>
            <span>Logika &amp; Aturan Absensi Sistem</span>
          </div>

          <div style="display:flex; flex-direction:column; gap:10px; font-size:12.5px; color:var(--text-2); line-height:1.45;">
            <div style="display:flex; gap:10px; align-items:flex-start;">
              <div style="width:20px; height:20px; border-radius:50%; background:var(--bg-3); border:1px solid var(--border); color:var(--text); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:11px; font-weight:800;">1</div>
              <div><strong>Hadir Tepat Waktu:</strong> Tap wajah sebelum atau tepat pada batas masuk (&le; <span style="color:var(--text); font-family:var(--font-mono); font-weight:800;">{{ $jamMasuk }} WIB</span>).</div>
            </div>

            <div style="display:flex; gap:10px; align-items:flex-start;">
              <div style="width:20px; height:20px; border-radius:50%; background:var(--bg-3); border:1px solid var(--border); color:var(--text); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:11px; font-weight:800;">2</div>
              <div><strong>Status Terlambat:</strong> Hadir di atas <span style="color:var(--text); font-family:var(--font-mono); font-weight:800;">{{ $jamMasuk }} WIB</span>. Dicatat terlambat dan menambah akumulasi Buku Kasus.</div>
            </div>

            <div style="display:flex; gap:10px; align-items:flex-start;">
              <div style="width:20px; height:20px; border-radius:50%; background:var(--bg-3); border:1px solid var(--border); color:var(--text); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:11px; font-weight:800;">3</div>
              <div><strong>Pulang Normal:</strong> Mulai pukul <span style="color:var(--text); font-family:var(--font-mono); font-weight:800;">{{ $jamPulang }} WIB</span>. Tap sebelum jam ini wajib memiliki izin piket.</div>
            </div>

            <div style="display:flex; gap:10px; align-items:flex-start;">
              <div style="width:20px; height:20px; border-radius:50%; background:var(--bg-3); border:1px solid var(--border); color:var(--text); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:11px; font-weight:800;">4</div>
              <div><strong>Tutup Gerbang Malam:</strong> Setelah pukul <span style="color:var(--text); font-family:var(--font-mono); font-weight:800;">{{ $jamTutup }} WIB</span>, Kiosk mengunci sesi hingga pagi hari.</div>
            </div>
          </div>
        </div>

        {{-- Card Quick Links --}}
        <div class="panel" style="padding:16px 20px; border:1px solid var(--border); border-radius:var(--r-md); background:var(--surface); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
          <div>
            <div style="font-size:13px; font-weight:800; color:var(--text);">Jadwal Petugas Guru Piket</div>
            <div style="font-size:11.5px; color:var(--text-3);">Kelola giliran guru piket gerbang harian</div>
          </div>
          <a href="{{ route('jadwal-piket.index') }}" class="btn btn-outline" style="font-size:12px; font-weight:800;">
            <i class="bi bi-calendar2-check"></i> Buka Jadwal Piket
          </a>
        </div>
      </div>

    </div>

    {{-- ══ 4. RIWAYAT PERUBAHAN JADWAL (TABEL TERPADU) ══ --}}
    <div class="panel" style="padding:0; overflow:hidden; border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-sm); background:var(--bg-2); margin-bottom:24px;">
      <div style="padding:14px 18px; border-bottom:1px solid var(--border); background:var(--surface); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="font-size:15px; font-weight:800; color:var(--text); display:flex; align-items:center; gap:8px;">
          <i class="bi bi-clock-history" style="color:var(--gold);"></i>
          <span>Riwayat Jadwal Operasional (10 Hari Terakhir)</span>
        </div>
      </div>

      @if($riwayatJadwal->isEmpty())
        <div style="text-align:center; padding:32px; color:var(--text-3); font-size:13px;">
          <i class="bi bi-calendar-x" style="font-size:32px; display:block; margin-bottom:8px; opacity:0.6;"></i>
          Belum ada riwayat jadwal tersimpan.
        </div>
      @else
        <div class="table-responsive" style="overflow-x:auto;">
          <table class="data-table" style="width:100%; border-collapse:collapse;">
            <thead>
              <tr style="background:var(--bg-3);">
                <th style="padding:12px 14px;">Tanggal</th>
                <th style="padding:12px 14px;">Hari</th>
                <th style="padding:12px 14px; text-align:center;">Batas Masuk</th>
                <th style="padding:12px 14px; text-align:center;">Mulai Pulang</th>
                <th style="padding:12px 14px; text-align:center;">Tutup Gerbang</th>
                <th style="padding:12px 14px;">Keterangan</th>
                <th style="padding:12px 14px;">Diubah Oleh</th>
              </tr>
            </thead>
            <tbody>
              @foreach($riwayatJadwal as $rj)
                @php $isToday = $rj->tanggal === $today; @endphp
                <tr style="border-bottom:1px solid var(--border); {{ $isToday ? 'background:rgba(202,138,4,0.04);' : '' }}">
                  <td style="padding:12px 14px;">
                    <span style="font-family:var(--font-mono); font-weight:700; color:{{ $isToday ? 'var(--gold)' : 'var(--text)' }};">
                      {{ \Carbon\Carbon::parse($rj->tanggal)->format('d/m/Y') }}
                    </span>
                    @if($isToday)
                      &nbsp;<span class="badge" style="background:var(--gold-dim); color:var(--gold); font-size:10px; font-weight:800; border:1px solid rgba(202,138,4,0.3);">HARI INI</span>
                    @endif
                  </td>
                  <td style="padding:12px 14px; color:var(--text-2); font-weight:600;">
                    {{ \Carbon\Carbon::parse($rj->tanggal)->translatedFormat('l') }}
                  </td>
                  <td style="padding:12px 14px; text-align:center;">
                    <span class="badge" style="background:rgba(34,197,94,0.12); color:#16A34A; border:1px solid rgba(34,197,94,0.3); font-family:var(--font-mono); font-weight:800; font-size:11px;">
                      {{ substr($rj->jam_masuk_toleransi, 0, 5) }}
                    </span>
                  </td>
                  <td style="padding:12px 14px; text-align:center;">
                    <span class="badge" style="background:rgba(234,179,8,0.15); color:#CA8A04; border:1px solid rgba(234,179,8,0.3); font-family:var(--font-mono); font-weight:800; font-size:11px;">
                      {{ substr($rj->jam_pulang_mulai, 0, 5) }}
                    </span>
                  </td>
                  <td style="padding:12px 14px; text-align:center;">
                    <span class="badge" style="background:rgba(239,68,68,0.12); color:#DC2626; border:1px solid rgba(239,68,68,0.3); font-family:var(--font-mono); font-weight:800; font-size:11px;">
                      {{ substr($rj->jam_tutup_gerbang ?? '18:00', 0, 5) }}
                    </span>
                  </td>
                  <td style="padding:12px 14px; color:var(--text); font-size:12.5px; font-weight:600;">
                    {{ $rj->keterangan ?? '—' }}
                  </td>
                  <td style="padding:12px 14px; color:var(--text-3); font-size:11.5px; font-family:var(--font-mono);">
                    {{ $rj->diubah_oleh ?? 'Sistem' }}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>

  </main>
</div>

<script>
  // Live Clock Updater
  setInterval(function() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    const s = String(now.getSeconds()).padStart(2, '0');
    const el = document.getElementById('liveClockDisplay');
    if (el) el.innerText = h + ':' + m + ':' + s;
  }, 1000);

  // Scenario Selector Function
  function selectScenario(masuk, pulang, tutup, keterangan, cardEl) {
    document.getElementById('jam_masuk_toleransi').value = masuk;
    document.getElementById('jam_pulang_mulai').value = pulang;
    document.getElementById('jam_tutup_gerbang').value = tutup;
    document.getElementById('keterangan').value = keterangan;

    // Highlight card
    document.querySelectorAll('.scenario-card').forEach(c => c.classList.remove('selected'));
    if (cardEl) cardEl.classList.add('selected');

    // Highlight inputs momentarily
    ['jam_masuk_toleransi', 'jam_pulang_mulai', 'jam_tutup_gerbang', 'keterangan'].forEach(id => {
      const el = document.getElementById(id);
      if (el) {
        el.style.borderColor = 'var(--gold)';
        setTimeout(() => el.style.borderColor = '', 900);
      }
    });
  }

  // Form submit state
  document.getElementById('form-jadwal').addEventListener('submit', function () {
    const btn = document.getElementById('btn-simpan-jadwal');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menerapkan Jadwal…';
  });
</script>

</body>
</html>
