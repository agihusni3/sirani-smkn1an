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
      border-radius: var(--r-md);
      padding: 12px 16px;
      margin-bottom: 12px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
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
      height: 2.5px;
      background: #000000;
    }

    /* Live Digital Clock Card */
    .digital-clock-box {
      background: var(--bg-3);
      border: 1px solid var(--border-2);
      border-radius: var(--r-sm);
      padding: 6px 14px;
      text-align: center;
      min-width: 160px;
    }
    .digital-clock-time {
      font-size: 20px;
      font-weight: 900;
      font-family: var(--font-mono);
      color: #000000;
      letter-spacing: 1px;
      line-height: 1.1;
    }
    .pulse-indicator {
      display: inline-block;
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: #000000;
      animation: pulseAnim 1.8s infinite;
      margin-right: 4px;
    }
    @keyframes pulseAnim {
      0% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(0,0,0,0.4); }
      70% { transform: scale(1.1); box-shadow: 0 0 0 6px rgba(0,0,0,0); }
      100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(0,0,0,0); }
    }

    /* ── Visual Day Timeline ── */
    .timeline-container {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-md);
      padding: 12px 14px;
      margin-bottom: 12px;
      box-shadow: var(--shadow-sm);
    }
    .timeline-track {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 8px;
      margin-top: 10px;
      position: relative;
    }
    .timeline-phase {
      background: var(--bg-3);
      border: 1px solid var(--border-2);
      border-radius: var(--r-sm);
      padding: 8px 10px;
      position: relative;
      transition: all .15s ease;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      gap: 3px;
    }
    .timeline-phase.active {
      border-color: #000000;
      background: var(--bg-2);
      box-shadow: 0 0 0 1px #000000;
    }
    .phase-badge {
      font-size: 9.5px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.4px;
      padding: 2px 6px;
      border-radius: 4px;
      display: inline-flex;
      align-items: center;
      gap: 4px;
      width: fit-content;
      margin-bottom: 2px;
      background: var(--bg-2);
      border: 1px solid var(--border);
      color: var(--text-2);
    }
    .timeline-phase.active .phase-badge {
      border-color: #000000;
      color: #FFFFFF;
      background: #000000;
    }
    .phase-time {
      font-size: 14px;
      font-weight: 900;
      font-family: var(--font-mono);
      margin: 2px 0 1px 0;
      color: #000000;
    }
    .phase-desc {
      font-size: 10.5px;
      color: var(--text-3);
      line-height: 1.25;
    }

    /* ── Preset Scenario Cards Grid ── */
    .scenario-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 8px;
      margin-bottom: 14px;
    }
    .scenario-card {
      background: var(--bg-3);
      border: 1px solid var(--border-2);
      border-radius: var(--r-sm);
      padding: 10px 12px;
      cursor: pointer;
      transition: all .15s ease;
      text-align: left;
      position: relative;
    }
    .scenario-card:hover {
      border-color: #000000;
      background: var(--bg-2);
    }
    .scenario-card.selected {
      border-color: #000000;
      background: #000000;
      color: #FFFFFF !important;
    }
    .scenario-card.selected .scenario-title,
    .scenario-card.selected .scenario-meta,
    .scenario-card.selected i {
      color: #FFFFFF !important;
    }
    .scenario-icon {
      width: 24px;
      height: 24px;
      border-radius: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      margin-bottom: 6px;
      background: var(--bg-2);
      color: #000000;
      border: 1px solid var(--border);
    }
    .scenario-title {
      font-size: 12px;
      font-weight: 800;
      color: var(--text);
      margin-bottom: 2px;
    }
    .scenario-meta {
      font-size: 10px;
      font-family: var(--font-mono);
      color: var(--text-3);
      font-weight: 700;
    }

    /* Form Styles */
    .studio-layout {
      display: grid;
      grid-template-columns: 1.3fr 1fr;
      gap: 14px;
    }
    .time-inputs-row {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 10px;
      margin-bottom: 12px;
    }
    .time-input-wrap {
      position: relative;
    }
    .time-input-wrap input {
      width: 100%;
      height: 38px;
      font-size: 14px;
      font-weight: 800;
      font-family: var(--font-mono);
      padding: 0 12px;
      background: var(--bg-3);
      border: 1px solid var(--border-2);
      border-radius: var(--r-sm);
      color: #000000;
      transition: border-color .15s ease;
    }
    .time-input-wrap input:focus {
      outline: none;
      border-color: #000000;
    }

    /* Mobile Responsive */
    @media (max-width: 900px) {
      .studio-layout {
        grid-template-columns: 1fr;
      }
    }
    @media (max-width: 768px) {
      .hero-sesi-banner {
        flex-direction: column !important;
        align-items: stretch !important;
        padding: 10px 12px !important;
      }
      .digital-clock-box {
        width: 100% !important;
        padding: 6px 10px !important;
      }
      .timeline-track {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 6px !important;
      }
      .timeline-phase {
        padding: 6px 8px !important;
      }
      .phase-time {
        font-size: 12.5px !important;
      }
      .phase-desc {
        display: none !important;
      }
      .scenario-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 6px !important;
      }
      .scenario-card {
        padding: 8px 10px !important;
      }
      .time-inputs-row {
        grid-template-columns: 1fr !important;
        gap: 8px !important;
      }
      .time-input-wrap input {
        height: 34px !important;
        font-size: 13px !important;
      }
    }
  </style>
</head>
<body>

<div class="app-container">
  @include('partials.sidebar')

  <main class="main-content">
    @php
      $currentTime = now()->format('H:i');
      $jamMasuk = substr($jadwalHariIni->jam_masuk_toleransi ?? '07:15', 0, 5);
      $jamPulang = substr($jadwalHariIni->jam_pulang_mulai ?? '15:30', 0, 5);
      $jamTutup = substr($jadwalHariIni->jam_tutup_gerbang ?? '17:00', 0, 5);


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

    {{-- ULTRA COMPACT SLIM HEADER BAR --}}
    <div class="panel no-print" style="background:var(--bg-2); border:1px solid var(--border); padding:10px 16px; margin-bottom:12px; border-radius:var(--r-md); box-shadow:var(--shadow-sm);">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          <h1 style="margin:0; font-size:16px; font-weight:900; color:var(--text); display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-clock-history" style="color:#000000; font-size:16px;"></i> Jam Sekolah &amp; Sesi Operasional
          </h1>
          <span style="color:var(--border-2); font-weight:300;">|</span>
          <span style="font-size:11.5px; color:var(--text-3);">
            {{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }} · T.A. <strong style="color:#000000;">{{ $taAktif->nama ?? '2026/2027' }}</strong>
          </span>
        </div>

        <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
          <span style="background:var(--bg-3); border:1px solid var(--border-2); color:#000000; font-family:var(--font-mono); font-size:12px; font-weight:800; padding:4px 10px; border-radius:6px; display:inline-flex; align-items:center; gap:5px;">
            <i class="bi bi-broadcast" style="color:#000000; font-size:11px;"></i>
            <span id="liveClockDisplay">{{ now()->format('H:i:s') }}</span> WIB
          </span>

          <span style="background:var(--bg-3); border:1px solid var(--border-2); color:#000000; font-size:11.5px; font-weight:800; padding:4px 10px; border-radius:6px; display:inline-flex; align-items:center; gap:5px;">
            <span class="pulse-indicator" style="width:6px; height:6px;"></span>
            {{ $jadwalHariIni->keterangan ?? 'Reguler' }}
          </span>

          @include('partials.header_actions')
        </div>
      </div>
    </div>

    @if(session('success'))
      <div class="alert-success" style="margin-bottom:12px;">
        <i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="alert-error" style="margin-bottom:12px;">
        <i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i>{{ session('error') }}
      </div>
    @endif

    {{-- ══ 2. VISUAL DAY TIMELINE (GARIS WAKTU SESI) ══ --}}
    <div class="timeline-container">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="font-weight:800; font-size:14.5px; color:var(--text); display:flex; align-items:center; gap:8px;">
          <i class="bi bi-segmented-nav" style="color:#000000;"></i>
          <span>Garis Waktu &amp; Tahapan Sesi Presensi Harian</span>
        </div>
        <div style="font-size:12px; color:var(--text-3);">
          Sesi Aktif: 
          @if($currentPhase === 'masuk')
            <span class="badge" style="background:rgba(0,0,0,0.06); color:#000000; border:1px solid rgba(0,0,0,0.12); font-weight:800;">Presensi Masuk Pagi</span>
          @elseif($currentPhase === 'kbm')
            <span class="badge" style="background:var(--bg-3); color:var(--text); border:1px solid var(--border); font-weight:800;">Sesi KBM Efektif</span>
          @elseif($currentPhase === 'pulang')
            <span class="badge" style="background:rgba(0,0,0,0.06); color:#000000; border:1px solid rgba(0,0,0,0.12); font-weight:800;">Presensi Pulang</span>
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
            <div class="phase-desc">Siswa/guru tap pulang di Face ID gerbang. Jam pulang sah terhitung di sistem.</div>
          </div>
        </div>

        {{-- Sesi 4: Tutup Gerbang --}}
        <div class="timeline-phase {{ $currentPhase === 'tutup' ? 'active' : '' }}">
          <div>
            <span class="phase-badge">
              <i class="bi bi-lock"></i> Sesi 4: Gerbang Kunci
            </span>
            <div class="phase-time">> {{ $jamTutup }} WIB</div>
            <div class="phase-desc">Operasional presensi hari ini ditutup. Sistem mengunci rekapitulasi harian.</div>
          </div>
        </div>
      </div>
    </div>

    @php
      $currentUser = auth()->user();
      $canManageJadwal = $currentUser && ($currentUser->isAdmin() || $currentUser->isWakaKurikulum() || $currentUser->isWakaKesiswaan() || $currentUser->isKepalaSekolah() || $currentUser->isStafTu());
    @endphp


    {{-- ══ 3. STUDIO DUA KOLOM: PRESET & FORM EDITOR vs ATURAN OPERASIONAL ══ --}}
    <div class="studio-layout">
      
      {{-- KOLOM KIRI: FORM & PRESET SKENARIO --}}
      <div class="panel" style="padding:22px; border:1px solid var(--border); border-radius:var(--r-md); background:var(--bg-2); box-shadow:var(--shadow-sm);">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:14px;">
          <div style="font-weight:800; font-size:15px; color:var(--text); display:flex; align-items:center; gap:8px;">
            <i class="bi bi-sliders" style="color:#000000;"></i>
            <span>Jam Operasional &amp; Skenario Sekolah</span>
          </div>
          @if(!$canManageJadwal)
            <span class="badge" style="background:var(--bg-3); color:var(--text-2); border:1px solid var(--border-2); font-size:11px; font-weight:800; padding:4px 10px; border-radius:100px; display:inline-flex; align-items:center; gap:4px;">
              <i class="bi bi-eye-fill"></i> Read Only
            </span>
          @endif
        </div>

        @if($canManageJadwal)
        {{-- Pilihan Kartu Skenario Preset --}}
        <div style="font-size:11px; font-weight:800; text-transform:uppercase; color:var(--text-3); letter-spacing:0.5px; margin-bottom:8px;">
          Preset Skenario Cepat (1-Klik Terapkan):
        </div>
        <div class="scenario-grid">
          <button type="button" class="scenario-card" onclick="selectScenario('07:15', '15:30', '17:00', 'Jadwal Reguler (Senin - Kamis)', this)">
            <div class="scenario-icon">
              <i class="bi bi-calendar4-week"></i>
            </div>
            <div class="scenario-title">Reguler Senin–Kamis</div>
            <div class="scenario-meta">07:15 &bull; 15:30 &bull; 17:00</div>
          </button>

          <button type="button" class="scenario-card" onclick="selectScenario('07:15', '11:30', '17:00', 'Jadwal Khusus Hari Jumat', this)">
            <div class="scenario-icon">
              <i class="bi bi-sun"></i>
            </div>
            <div class="scenario-title">Khusus Hari Jumat</div>
            <div class="scenario-meta">07:15 &bull; 11:30 &bull; 17:00</div>
          </button>

          <button type="button" class="scenario-card" onclick="selectScenario('07:15', '12:00', '17:00', 'Pekan Penilaian / Ujian Semester', this)">
            <div class="scenario-icon">
              <i class="bi bi-file-earmark-text"></i>
            </div>
            <div class="scenario-title">Pekan Ujian / PTS / PAS</div>
            <div class="scenario-meta">07:15 &bull; 12:00 &bull; 17:00</div>
          </button>

          <button type="button" class="scenario-card" onclick="selectScenario('07:15', '13:00', '17:00', 'Kegiatan Peringatan / Pulang Awal', this)">
            <div class="scenario-icon">
              <i class="bi bi-flag"></i>
            </div>
            <div class="scenario-title">Acara Khusus / Upacara</div>
            <div class="scenario-meta">07:15 &bull; 13:00 &bull; 17:00</div>
          </button>
        </div>
        @endif


        {{-- Form Input --}}
        <form action="{{ route('admin.jadwal.update') }}" method="POST" id="form-jadwal" style="border-top:1px solid var(--border); padding-top:14px;">
          @csrf
          <div class="time-inputs-row">
            <div>
              <label style="display:block; font-size:11px; font-weight:800; text-transform:uppercase; color:var(--text-2); margin-bottom:4px;">
                <i class="bi bi-sunrise"></i> Batas Masuk Pagi <span style="color:var(--red);">*</span>
              </label>
              <div class="time-input-wrap">
                <input type="time" name="jam_masuk_toleransi" id="input_jam_masuk" value="{{ substr($jadwalHariIni->jam_masuk_toleransi ?? '07:15', 0, 5) }}" required {{ !$canManageJadwal ? 'disabled' : '' }} />
              </div>
            </div>

            <div>
              <label style="display:block; font-size:11px; font-weight:800; text-transform:uppercase; color:var(--text-2); margin-bottom:4px;">
                <i class="bi bi-sunset"></i> Mulai Pulang Sore <span style="color:var(--red);">*</span>
              </label>
              <div class="time-input-wrap">
                <input type="time" name="jam_pulang_mulai" id="input_jam_pulang" value="{{ substr($jadwalHariIni->jam_pulang_mulai ?? '15:30', 0, 5) }}" required {{ !$canManageJadwal ? 'disabled' : '' }} />
              </div>
            </div>

            <div>
              <label style="display:block; font-size:11px; font-weight:800; text-transform:uppercase; color:var(--text-2); margin-bottom:4px;">
                <i class="bi bi-moon-stars"></i> Tutup Gerbang <span style="color:var(--red);">*</span>
              </label>
              <div class="time-input-wrap">
                <input type="time" name="jam_tutup_gerbang" id="input_jam_tutup" value="{{ substr($jadwalHariIni->jam_tutup_gerbang ?? '17:00', 0, 5) }}" required {{ !$canManageJadwal ? 'disabled' : '' }} />

              </div>
            </div>
          </div>

          <div style="margin-bottom:14px;">
            <label style="display:block; font-size:11px; font-weight:800; text-transform:uppercase; color:var(--text-2); margin-bottom:4px;">
              <i class="bi bi-chat-left-quote"></i> Keterangan Jadwal Hari Ini
            </label>
            <input type="text" name="keterangan" id="keterangan" value="{{ $jadwalHariIni->keterangan ?? 'Jadwal Reguler' }}" {{ !$canManageJadwal ? 'disabled' : '' }} placeholder="Contoh: Jadwal Ujian Semester / Hari Jumat Khusus" class="input-field" style="width:100%; height:36px; font-size:12.5px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px;" />
          </div>

          @if($canManageJadwal)
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
              <div style="font-size:11px; color:var(--text-3);">
                <i class="bi bi-info-circle"></i> Jadwal hari berikutnya otomatis kembali ke standar default.
              </div>
              <button type="submit" id="btn-simpan-jadwal" class="btn btn-sm btn-gold" style="height:36px; padding:0 18px; font-weight:800; font-size:12px; display:inline-flex; align-items:center; gap:6px; border-radius:6px;">
                <i class="bi bi-check2-circle"></i> Terapkan Jadwal Operasional
              </button>
            </div>
          @else
            <div style="font-size:11.5px; font-weight:600; color:var(--text-3); background:var(--bg-3); padding:8px 12px; border-radius:6px; display:flex; align-items:center; gap:6px;">
              <i class="bi bi-info-circle-fill"></i> Mode pemantauan eksekutif. Perubahan jam operasional dikelola oleh Waka Kurikulum &amp; Administrator.
            </div>
          @endif
        </form>
      </div>

      {{-- KOLOM KANAN: PANDUAN & KEBIJAKAN OPERASIONAL --}}
      <div style="display:flex; flex-direction:column; gap:12px;">
        <div class="panel" style="padding:14px 16px; border:1px solid var(--border); border-radius:var(--r-md); background:var(--bg-2); box-shadow:var(--shadow-sm);">
          <div style="font-weight:800; font-size:13px; color:var(--text); display:flex; align-items:center; gap:6px; margin-bottom:10px;">
            <i class="bi bi-shield-check" style="color:#000000;"></i>
            <span>Logika &amp; Aturan Absensi Sistem</span>
          </div>

          <div style="display:flex; flex-direction:column; gap:8px; font-size:11.5px; color:var(--text-2); line-height:1.4;">
            <div style="display:flex; gap:8px; align-items:flex-start;">
              <div style="width:18px; height:18px; border-radius:50%; background:var(--bg-3); border:1px solid var(--border); color:#000000; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:10px; font-weight:800;">1</div>
              <div><strong>Hadir Tepat Waktu:</strong> Tap kartu / scan barcode sebelum/tepat batas masuk (&le; <span style="color:#000000; font-family:var(--font-mono); font-weight:800;">{{ $jamMasuk }} WIB</span>).</div>
            </div>

            <div style="display:flex; gap:8px; align-items:flex-start;">
              <div style="width:18px; height:18px; border-radius:50%; background:var(--bg-3); border:1px solid var(--border); color:#000000; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:10px; font-weight:800;">2</div>
              <div><strong>Status Terlambat:</strong> Hadir di atas <span style="color:#000000; font-family:var(--font-mono); font-weight:800;">{{ $jamMasuk }} WIB</span>. Dicatat terlambat dan menambah akumulasi Buku Kasus.</div>
            </div>

            <div style="display:flex; gap:8px; align-items:flex-start;">
              <div style="width:18px; height:18px; border-radius:50%; background:var(--bg-3); border:1px solid var(--border); color:#000000; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:10px; font-weight:800;">3</div>
              <div><strong>Presensi Pulang:</strong> Dibuka mulai pukul <span style="color:#000000; font-family:var(--font-mono); font-weight:800;">{{ $jamPulang }} WIB</span>. Tap sebelum jam tersebut tidak dihitung pulang resmi.</div>
            </div>

            <div style="display:flex; gap:8px; align-items:flex-start; padding-top:4px; border-top:1px dashed var(--border);">
              <div style="width:18px; height:18px; border-radius:50%; background:var(--bg-3); border:1px solid var(--border); color:#000000; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:10px; font-weight:800;">4</div>
              <div style="flex:1;">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:6px;">
                  <div>
                    <div style="font-size:11.5px; font-weight:800; color:var(--text);">Jadwal Petugas Guru Piket</div>
                    <div style="font-size:10.5px; color:var(--text-3);">Kelola giliran piket gerbang harian</div>
                  </div>
                  <a href="{{ route('jadwal-piket.index') }}" class="btn btn-sm btn-outline" style="height:28px; font-size:11px; font-weight:800; padding:0 8px; display:inline-flex; align-items:center; gap:4px; border-radius:6px; text-decoration:none;">
                    <i class="bi bi-calendar2-check"></i> Buka Jadwal Piket
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    {{-- ══ 4. JAM OPERASIONAL MINGGUAN RUTIN (SENIN - JUMAT) ══ --}}
    <div class="panel" style="padding:0; overflow:hidden; border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-sm); background:var(--bg-2); margin-bottom:20px;">
      <div style="padding:16px 20px; border-bottom:1px solid var(--border); background:var(--surface); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div>
          <div style="font-size:15px; font-weight:900; color:var(--text); display:flex; align-items:center; gap:8px;">
            <i class="bi bi-calendar-week-fill" style="color:#000000; font-size:16px;"></i>
            <span>Jam Operasional Mingguan Rutin (Senin s/d Jumat)</span>
          </div>
          <div style="font-size:12px; color:var(--text-3); margin-top:2px;">
            Konfigurasi standar jadwal per hari sekolah. Jadwal ini otomatis diterapkan berulang setiap pekannya oleh sistem Smart Gate SIRANI.
          </div>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
          <span style="background:rgba(0,0,0,0.06); color:#000000; font-size:11px; font-weight:800; padding:4px 10px; border-radius:100px; border:1px solid var(--border-2); display:inline-flex; align-items:center; gap:4px;">
            <i class="bi bi-arrow-repeat"></i> Berlaku Berulang Otomatis
          </span>
        </div>
      </div>

      <form action="{{ route('admin.jadwal.mingguan.update') }}" method="POST" style="margin:0;">
        @csrf
        <div class="table-responsive" style="overflow-x:auto;">
          <table class="data-table" style="width:100%; border-collapse:collapse;">
            <thead>
              <tr style="background:var(--bg-3);">
                <th style="padding:12px 16px; color:#000000; font-weight:800; width:130px;">Hari</th>
                <th style="padding:12px 16px; color:#000000; font-weight:800; text-align:center; width:160px;">
                  <i class="bi bi-sunrise"></i> Batas Masuk Pagi
                </th>
                <th style="padding:12px 16px; color:#000000; font-weight:800; text-align:center; width:160px;">
                  <i class="bi bi-sunset"></i> Mulai Pulang Sore
                </th>
                <th style="padding:12px 16px; color:#000000; font-weight:800; text-align:center; width:160px;">
                  <i class="bi bi-moon-stars"></i> Tutup Gerbang
                </th>
                <th style="padding:12px 16px; color:#000000; font-weight:800;">
                  <i class="bi bi-chat-left-text"></i> Keterangan Kegiatan
                </th>
                <th style="padding:12px 16px; color:#000000; font-weight:800; text-align:center; width:100px;">
                  Status
                </th>
              </tr>
            </thead>
            <tbody>
              @foreach($jadwalMingguanList as $jm)
                @php
                  $isTodayDay = strtolower($jm->hari) === strtolower(\Carbon\Carbon::parse($today)->locale('id')->isoFormat('dddd'));
                @endphp
                <tr style="border-bottom:1px solid var(--border); background:{{ $isTodayDay ? 'rgba(0,0,0,0.02)' : 'transparent' }};">
                  <td style="padding:12px 16px; vertical-align:middle;">
                    <div style="display:flex; align-items:center; gap:8px;">
                      <span style="font-weight:900; font-size:13.5px; color:#000000;">{{ $jm->hari }}</span>
                      @if($isTodayDay)
                        <span style="background:#000000; color:#FFFFFF; font-size:9.5px; font-weight:800; padding:2px 6px; border-radius:4px;">HARI INI</span>
                      @endif
                    </div>
                  </td>
                  <td style="padding:10px 16px; text-align:center; vertical-align:middle;">
                    <div class="time-input-wrap" style="max-width:140px; margin:0 auto;">
                      <input type="time" name="mingguan[{{ $jm->hari }}][jam_masuk_toleransi]" value="{{ substr($jm->jam_masuk_toleransi, 0, 5) }}" required {{ !$canManageJadwal ? 'disabled' : '' }} style="text-align:center; font-weight:800; font-family:var(--font-mono); font-size:13px;" />
                    </div>
                  </td>
                  <td style="padding:10px 16px; text-align:center; vertical-align:middle;">
                    <div class="time-input-wrap" style="max-width:140px; margin:0 auto;">
                      <input type="time" name="mingguan[{{ $jm->hari }}][jam_pulang_mulai]" value="{{ substr($jm->jam_pulang_mulai, 0, 5) }}" required {{ !$canManageJadwal ? 'disabled' : '' }} style="text-align:center; font-weight:800; font-family:var(--font-mono); font-size:13px;" />
                    </div>
                  </td>
                  <td style="padding:10px 16px; text-align:center; vertical-align:middle;">
                    <div class="time-input-wrap" style="max-width:140px; margin:0 auto;">
                      <input type="time" name="mingguan[{{ $jm->hari }}][jam_tutup_gerbang]" value="{{ substr($jm->jam_tutup_gerbang ?? '17:00', 0, 5) }}" required {{ !$canManageJadwal ? 'disabled' : '' }} style="text-align:center; font-weight:800; font-family:var(--font-mono); font-size:13px;" />
                    </div>
                  </td>
                  <td style="padding:10px 16px; vertical-align:middle;">
                    <input type="text" name="mingguan[{{ $jm->hari }}][keterangan]" value="{{ $jm->keterangan }}" placeholder="Keterangan hari {{ $jm->hari }}" {{ !$canManageJadwal ? 'disabled' : '' }} style="width:100%; height:34px; font-size:12px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:6px; padding:0 10px;" />
                  </td>
                  <td style="padding:10px 16px; text-align:center; vertical-align:middle;">
                    <input type="hidden" name="mingguan[{{ $jm->hari }}][is_aktif]" value="1" />
                    <span style="display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:800; color:#16A34A; background:rgba(22,163,74,0.1); padding:3px 8px; border-radius:6px;">
                      <i class="bi bi-check-circle-fill"></i> Aktif
                    </span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        @if($canManageJadwal)
          <div style="padding:14px 20px; border-top:1px solid var(--border); background:var(--surface); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <label style="display:flex; align-items:center; gap:8px; cursor:pointer; margin:0; font-size:12.5px; font-weight:700; color:var(--text);">
              <input type="checkbox" name="terapkan_hari_ini" value="1" checked style="width:16px; height:16px; cursor:pointer;" />
              <span>Terapkan juga ke jadwal hari ini ({{ \Carbon\Carbon::parse($today)->locale('id')->isoFormat('dddd') }}) secara langsung</span>
            </label>
            <button type="submit" class="btn btn-primary" style="background:#000000; color:#FFFFFF; height:38px; padding:0 22px; font-weight:800; font-size:12.5px; border-radius:8px; display:inline-flex; align-items:center; gap:6px;">
              <i class="bi bi-floppy-fill"></i> Simpan Jam Operasional Mingguan
            </button>
          </div>
        @else
          <div style="padding:12px 20px; border-top:1px solid var(--border); background:var(--bg-3); font-size:12px; color:var(--text-3); font-weight:600;">
            <i class="bi bi-lock-fill"></i> Pengubahan jam operasional mingguan dibatasi untuk Admin, Kepala Sekolah, Waka Kurikulum, dan Waka Kesiswaan.
          </div>
        @endif
      </form>
    </div>

    {{-- ══ 5. RIWAYAT PERUBAHAN JADWAL (TABEL TERPADU) ══ --}}
    <div class="panel" style="padding:0; overflow:hidden; border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-sm); background:var(--bg-2); margin-bottom:24px;">
      <div style="padding:14px 18px; border-bottom:1px solid var(--border); background:var(--surface); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="font-size:15px; font-weight:800; color:#000000; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-clock-history" style="color:#000000;"></i>
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
                <th style="padding:12px 14px; color:#000000; font-weight:800;">Tanggal</th>
                <th style="padding:12px 14px; color:#000000; font-weight:800;">Hari</th>
                <th style="padding:12px 14px; text-align:center; color:#000000; font-weight:800;">Batas Masuk</th>
                <th style="padding:12px 14px; text-align:center; color:#000000; font-weight:800;">Mulai Pulang</th>
                <th style="padding:12px 14px; text-align:center; color:#000000; font-weight:800;">Tutup Gerbang</th>
                <th style="padding:12px 14px; color:#000000; font-weight:800;">Keterangan</th>
                <th style="padding:12px 14px; color:#000000; font-weight:800;">Diubah Oleh</th>
              </tr>
            </thead>
            <tbody>
              @foreach($riwayatJadwal as $rj)
                @php $isToday = $rj->tanggal === $today; @endphp
                <tr style="border-bottom:1px solid var(--border);">
                  <td style="padding:12px 14px;">
                    <span style="font-family:var(--font-mono); font-weight:700; color:#000000;">
                      {{ \Carbon\Carbon::parse($rj->tanggal)->format('d/m/Y') }}
                    </span>
                    @if($isToday)
                      &nbsp;<span style="color:#000000; font-size:10px; font-weight:800;">HARI INI</span>
                    @endif
                  </td>
                  <td style="padding:12px 14px; color:#000000; font-weight:700;">
                    {{ \Carbon\Carbon::parse($rj->tanggal)->translatedFormat('l') }}
                  </td>
                  <td style="padding:12px 14px; text-align:center;">
                    <span style="font-family:var(--font-mono); font-weight:800; font-size:13px; color:#000000;">
                      {{ substr($rj->jam_masuk_toleransi, 0, 5) }}
                    </span>
                  </td>
                  <td style="padding:12px 14px; text-align:center;">
                    <span style="font-family:var(--font-mono); font-weight:800; font-size:13px; color:#000000;">
                      {{ substr($rj->jam_pulang_mulai, 0, 5) }}
                    </span>
                  </td>
                  <td style="padding:12px 14px; text-align:center;">
                    <span style="font-family:var(--font-mono); font-weight:800; font-size:13px; color:#000000;">
                      {{ substr($rj->jam_tutup_gerbang ?? '17:00', 0, 5) }}

                    </span>
                  </td>
                  <td style="padding:12px 14px; color:#000000; font-size:12.5px; font-weight:600;">
                    {{ $rj->keterangan ?? '—' }}
                  </td>
                  <td style="padding:12px 14px; color:#000000; font-size:11.5px; font-family:var(--font-mono); font-weight:700;">
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
        el.style.borderColor = '#000000';
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
