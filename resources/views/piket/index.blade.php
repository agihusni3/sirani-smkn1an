<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Meja Piket Harian — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <style>
    /* ── Hero Meja Piket ── */
    .piket-hero {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-xl);
      padding: 22px 24px;
      margin-bottom: 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 16px;
      box-shadow: var(--shadow-sm);
    }
    .piket-clock-card {
      background: var(--bg-3);
      border: 1px solid var(--border-2);
      border-radius: var(--r-md);
      padding: 12px 18px;
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 4px;
      min-width: 180px;
    }
    .piket-clock-time {
      font-family: var(--font-mono);
      font-size: 24px;
      font-weight: 900;
      color: var(--text);
      letter-spacing: 1px;
    }

    /* ── KPI Stat Cards ── */
    .piket-stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
      gap: 14px;
      margin-bottom: 24px;
    }
    .piket-stat-card {
      background: var(--bg-3);
      border: 1px solid var(--border-2);
      border-radius: var(--r-md);
      padding: 16px 18px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      gap: 8px;
      transition: all .2s;
    }
    .piket-stat-card:hover {
      border-color: var(--border);
      transform: translateY(-2px);
    }
    .piket-stat-head {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 11.5px;
      font-weight: 700;
      color: var(--text-2);
    }
    .piket-stat-val {
      font-size: 28px;
      font-weight: 900;
      line-height: 1.1;
      font-family: var(--font-mono);
      color: var(--text);
    }

    /* ── Presensi Manual Quick Bar ── */
    .pm-panel {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-xl);
      padding: 22px 24px;
      margin-bottom: 24px;
      box-shadow: var(--shadow-sm);
    }
    .pm-panel:hover {
      border-color: var(--gold);
    }
    .pm-search-dropdown {
      position: absolute;
      top: calc(100% + 4px);
      left: 0;
      right: 0;
      background: var(--bg-2);
      border: 1.5px solid var(--border-2);
      border-radius: var(--r-sm);
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
      z-index: 1050;
      overflow: hidden;
    }
    .pm-item {
      padding: 8px 12px;
      border-bottom: 1px solid var(--border);
      cursor: pointer;
      transition: background .15s ease;
      text-align: left;
    }
    .pm-item:last-child {
      border-bottom: none;
    }
    .pm-item:hover, .pm-item.active {
      background: var(--gold-dim);
    }

    /* ── Tabs Styling ── */
    .piket-nav-tabs {
      display: flex;
      gap: 6px;
      border-bottom: 2px solid var(--border);
      margin-bottom: 20px;
      overflow-x: auto;
      padding-bottom: 2px;
    }
    .piket-tab-btn {
      background: none;
      border: none;
      border-bottom: 3px solid transparent;
      margin-bottom: -4px;
      padding: 10px 18px;
      font-size: 13px;
      font-weight: 700;
      color: var(--text-2);
      cursor: pointer;
      border-radius: var(--r-sm) var(--r-sm) 0 0;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      white-space: nowrap;
      transition: all .15s ease;
    }
    .piket-tab-btn:hover {
      color: var(--text);
      background: var(--bg-3);
    }
    .piket-tab-btn.active {
      color: var(--text);
      font-weight: 800;
      border-bottom-color: var(--text);
      background: none;
    }
    .piket-tab-pane { display: none; }
    .piket-tab-pane.active { display: block; }

    /* ── Filter Pills ── */
    .filter-pills {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      margin-bottom: 14px;
    }
    .filter-pill {
      background: var(--bg-2);
      border: 1px solid var(--border);
      color: var(--text-2);
      padding: 6px 14px;
      border-radius: var(--r-sm);
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
      transition: all .15s;
    }
    .filter-pill:hover {
      color: var(--text);
      border-color: var(--border-2);
    }
    .filter-pill.active {
      background: var(--text);
      border-color: var(--text);
      color: var(--bg);
      font-weight: 800;
    }

    /* ── Day Column Grid ── */
    .piket-day-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
      gap: 14px;
    }
    .piket-day-col {
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      border-radius: var(--r-md);
      padding: 14px;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    .piket-day-col.today {
      border-color: var(--gold);
      background: linear-gradient(180deg, rgba(202,138,4,0.06) 0%, var(--bg-2) 100%);
      box-shadow: 0 0 16px var(--gold-glow);
    }
  </style>
</head>
<body>
<div class="app-container">
  @include('partials.sidebar')

  <main class="main-content">
    
    {{-- HEADER UTAMA --}}
    <header class="header no-print" style="margin-bottom:20px;">
      <div class="header-title">
        <h1 style="margin:0; font-size:22px; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-person-badge-fill" style="color:var(--gold);"></i> Meja Piket Harian
        </h1>
        <p style="margin-top:2px; font-size:13px; color:var(--text-3);">
          Pusat kendali piket harian, monitoring kehadiran real-time, presensi manual siswa &amp; guru, serta tindak lanjut ketertiban.
        </p>
      </div>
      @include('partials.header_actions')
    </header>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
      <div class="alert-success" style="margin-bottom:18px;">
        <i class="bi bi-check-circle-fill" style="margin-right:6px;"></i> {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="alert-error" style="margin-bottom:18px;">
        <i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i> {{ session('error') }}
      </div>
    @endif

    {{-- HERO MEJA PIKET --}}
    <div class="piket-hero">
      <div>
        <div style="font-size:11.5px; font-weight:800; color:var(--text-3); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px;">
          Operasional Meja Piket Harian
        </div>
        <h2 style="font-size:22px; font-weight:900; color:var(--text); margin:0 0 6px 0;">
          {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
        </h2>
        <div style="font-size:13px; color:var(--text-2); display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
          <span>
            Batas Masuk: <strong style="color:var(--text); font-family:var(--font-mono);">{{ substr($jadwal->jam_masuk_toleransi ?? '07:15', 0, 5) }} WIB</strong>
          </span>
          <span>·</span>
          <span>
            Mulai Pulang: <strong style="color:var(--text); font-family:var(--font-mono);">{{ substr($jadwal->jam_pulang_mulai ?? '15:30', 0, 5) }} WIB</strong>
          </span>
          <span>·</span>
          <span>
            Tutup Gerbang: <strong style="color:var(--text); font-family:var(--font-mono);">{{ substr($jadwal->jam_tutup_gerbang ?? '18:00', 0, 5) }} WIB</strong>
          </span>
          @if($isLibur)
            <span>·</span>
            <span style="color:var(--text); font-weight:800;">
              HARI LIBUR: {{ $liburDetail->keterangan ?? 'Libur' }}
            </span>
          @endif
        </div>
      </div>

      <div class="piket-clock-card">
        <div style="font-size:10px; font-weight:700; color:var(--text-3); text-transform:uppercase; letter-spacing:0.5px; display:flex; align-items:center; gap:5px;">
          <span class="pulse-dot" style="background:#22C55E;"></span> JAM SISTEM REAL-TIME
        </div>
        <div class="piket-clock-time" id="livePiketClock">
          {{ now()->format('H:i:s') }}
        </div>
        <div style="font-size:11px; color:var(--text-3); font-family:var(--font-mono);">
          Zona Waktu: WIB (UTC+7)
        </div>
      </div>
    </div>

    {{-- RIBBON PETUGAS PIKET HARI INI --}}
    <div class="panel" style="background:var(--bg-2); border:1px solid var(--border); padding:14px 18px; margin-bottom:20px;">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:10px;">
          <div style="width:36px; height:36px; border-radius:8px; background:rgba(0,0,0,0.06); color:#000000; border:1px solid rgba(0,0,0,0.12); display:flex; align-items:center; justify-content:center; font-size:18px;">
            <i class="bi bi-shield-check" style="color:#000000;"></i>
          </div>
          <div>
            <div style="font-size:12px; font-weight:800; text-transform:uppercase; color:#000000; letter-spacing:0.5px;">Petugas Guru Piket Hari Ini:</div>
            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-top:2px;">
              @forelse($guruPiketHariIni as $gp)
                <span class="badge" style="background:var(--bg-3); border:1px solid var(--border); color:#000000; font-size:12px; font-weight:700; padding:4px 10px; display:inline-flex; align-items:center; gap:6px;">
                  <strong style="color:#000000;">{{ $gp->guru->nama ?? 'Guru' }}</strong>
                  <span style="font-size:10.5px; color:#000000;">({{ $gp->keterangan ?: ($gp->guru->jabatan ?? 'Guru Piket') }})</span>
                </span>
              @empty
                <span style="color:#000000; font-size:12.5px; font-style:italic;">Belum ada jadwal penugasan guru piket untuk hari ini.</span>
              @endforelse
            </div>
          </div>
        </div>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          {{-- Kontrol Otorisasi Sesi Gerbang --}}
          <form action="{{ route('piket.toggle-gerbang') }}" method="POST" style="margin:0;">
            @csrf
            @if($jadwal->is_sesi_buka)
              <div style="display:inline-flex; align-items:center; gap:8px; background:rgba(34,197,94,0.1); border:1px solid #22C55E; padding:4px 10px; border-radius:8px;">
                <span style="font-size:12px; font-weight:800; color:#16A34A; display:flex; align-items:center; gap:5px;">
                  <span class="pulse-dot" style="background:#22C55E;"></span> Gerbang Aktif
                </span>
                <button type="submit" name="status" value="tutup" class="btn btn-sm btn-outline" style="font-size:11px; font-weight:800; color:#DC2626; border-color:#DC2626; padding:2px 8px;" title="Tutup pemindaian Face ID gerbang">
                  <i class="bi bi-door-closed-fill"></i> Tutup Sesi
                </button>
              </div>
            @else
              <div style="display:inline-flex; align-items:center; gap:8px; background:rgba(239,68,68,0.1); border:1px solid #EF4444; padding:4px 10px; border-radius:8px;">
                <span style="font-size:12px; font-weight:800; color:#DC2626; display:flex; align-items:center; gap:5px;">
                  <i class="bi bi-lock-fill"></i> Gerbang Ditutup
                </span>
                <button type="submit" name="status" value="buka" class="btn btn-sm" style="font-size:11px; font-weight:800; background:#16A34A; color:#fff; border:none; padding:3px 10px;" title="Buka sesi pemindaian Face ID gerbang">
                  <i class="bi bi-door-open-fill"></i> Buka Sesi Gerbang
                </button>
              </div>
            @endif
          </form>

          <button type="button" class="btn btn-sm btn-gold" onclick="openModal('modalPresensiManual')" style="font-size:11.5px; font-weight:800; display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-clipboard-plus-fill"></i> Presensi Manual
          </button>
          <a href="{{ route('izin-siswa.index') }}" class="btn btn-sm btn-outline" style="font-size:11.5px; font-weight:800; color:#000000; display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-envelope-paper-fill" style="color:#000000;"></i> Catat Izin
          </a>
          <a href="{{ route('jadwal-piket.index') }}" class="btn btn-sm btn-outline" style="font-size:11.5px; font-weight:800; color:#000000; display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-calendar-week" style="color:#000000;"></i> Jadwal Mingguan
          </a>
        </div>
      </div>
    </div>

    {{-- KPI STATS GRID: 4 STATUS KEHADIRAN MUTUALLY EXCLUSIVE --}}
    @php
      $belumHadirCount = max(0, $totalSiswaAktif - $hadirTotal - $izinCount);
    @endphp
    <div class="piket-stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));">
      
      {{-- 1. Hadir Tepat --}}
      <div class="piket-stat-card">
        <div class="piket-stat-head">
          <span style="font-weight:800; color:var(--text);">Hadir Tepat Waktu</span>
        </div>
        <div class="piket-stat-val" style="color:var(--text);">{{ $hadirTepat }}</div>
        <div style="font-size:11px; color:var(--text-3);">Masuk &le; {{ substr($jadwal->jam_masuk_toleransi ?? '07:15', 0, 5) }} WIB</div>
      </div>

      {{-- 2. Terlambat --}}
      <div class="piket-stat-card">
        <div class="piket-stat-head">
          <span style="font-weight:800; color:var(--text);">Terlambat</span>
        </div>
        <div class="piket-stat-val" style="color:var(--text);">{{ $terlambat }}</div>
        <div style="font-size:11px; color:var(--text-3);">Masuk &gt; {{ substr($jadwal->jam_masuk_toleransi ?? '07:15', 0, 5) }} WIB</div>
      </div>

      {{-- 3. Izin / Sakit --}}
      <div class="piket-stat-card">
        <div class="piket-stat-head">
          <span style="font-weight:800; color:var(--text);">Izin &amp; Sakit</span>
        </div>
        <div class="piket-stat-val" style="color:var(--text);">{{ $izinCount }}</div>
        <div style="font-size:11px; color:var(--text-3);">Surat izin tervalidasi</div>
      </div>

      {{-- 4. Belum Hadir / Alpha --}}
      <div class="piket-stat-card">
        <div class="piket-stat-head">
          <span style="font-weight:800; color:var(--text);">Belum Hadir</span>
        </div>
        <div class="piket-stat-val" style="color:var(--text);">{{ $belumHadirCount }}</div>
        <div style="font-size:11px; color:var(--text-3);">Belum ada catatan presensi</div>
      </div>
    </div>

    {{-- PROGRESS BAR & RINGKASAN REKAP --}}
    <div style="background:var(--bg-2); border:1px solid var(--border); border-radius:var(--r-sm); padding:14px 18px; margin-bottom:24px;">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:8px; font-size:12.5px;">
        <span style="font-weight:800; color:var(--text);">
          <i class="bi bi-pie-chart-fill" style="margin-right:4px;"></i> Total {{ $totalSiswaAktif }} Siswa Terdaftar:
        </span>
        <div style="font-size:12px; color:var(--text-2); display:flex; align-items:center; gap:12px;">
          <span>Tingkat Kehadiran: <strong style="color:var(--text); font-family:var(--font-mono); font-size:13px;">{{ $persenKehadiran }}%</strong> ({{ $hadirTotal }} Masuk)</span>
          <span>·</span>
          <span>Sudah Pulang: <strong style="color:var(--text); font-family:var(--font-mono); font-size:13px;">{{ $sudahPulang }}</strong> siswa</span>
        </div>
      </div>
      <div style="width:100%; height:8px; background:var(--bg-3); border-radius:10px; overflow:hidden; display:flex;">
        @php
          $pctHadir = $totalSiswaAktif > 0 ? round(($hadirTepat / $totalSiswaAktif) * 100, 1) : 0;
          $pctTelat = $totalSiswaAktif > 0 ? round(($terlambat / $totalSiswaAktif) * 100, 1) : 0;
          $pctIzin  = $totalSiswaAktif > 0 ? round(($izinCount / $totalSiswaAktif) * 100, 1) : 0;
        @endphp
        <div style="width:{{ $pctHadir }}%; background:var(--green);" title="Hadir Tepat ({{ $hadirTepat }})"></div>
        <div style="width:{{ $pctTelat }}%; background:var(--amber);" title="Terlambat ({{ $terlambat }})"></div>
        <div style="width:{{ $pctIzin }}%; background:var(--blue);" title="Izin/Sakit ({{ $izinCount }})"></div>
      </div>
    </div>



    {{-- WORKSPACE MULTI-TAB --}}
    <div class="piket-nav-tabs">
      <button class="piket-tab-btn active" onclick="switchPiketTab('tab-siswa', this)">
        <i class="bi bi-people-fill"></i> Presensi Siswa ({{ $absensiHariIni->count() }})
      </button>
      <button class="piket-tab-btn" onclick="switchPiketTab('tab-guru', this)">
        <i class="bi bi-person-badge-fill"></i> Presensi Guru &amp; Pegawai ({{ $absensiGuruHariIni->count() }})
      </button>
      <button class="piket-tab-btn" onclick="switchPiketTab('tab-izin', this)">
        <i class="bi bi-envelope-paper-fill"></i> Izin &amp; Sakit ({{ $izinCount }})
      </button>
      <button class="piket-tab-btn" onclick="switchPiketTab('tab-followup', this)">
        <i class="bi bi-person-x-fill"></i> Siswa Belum Hadir ({{ $siswaBelumHadirList->count() }})
      </button>
      <button class="piket-tab-btn" onclick="switchPiketTab('tab-jadwal', this)">
        <i class="bi bi-calendar-week-fill"></i> Jadwal Piket Sepekan
      </button>
    </div>

    {{-- ── TAB 1: PRESENSI SISWA HARI INI ── --}}
    <div id="tab-siswa" class="piket-tab-pane active">
      <div class="panel" style="padding:0; overflow:hidden;">
        
        {{-- Toolbar Filter & Search --}}
        <div style="padding:14px 18px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
          <div class="filter-pills" style="margin-bottom:0;">
            <button type="button" class="filter-pill active" onclick="filterSiswaTable('all', this)">Semua ({{ $absensiHariIni->count() }})</button>
            <button type="button" class="filter-pill" onclick="filterSiswaTable('hadir', this)">Hadir Tepat ({{ $hadirTepat }})</button>
            <button type="button" class="filter-pill" onclick="filterSiswaTable('terlambat', this)">Terlambat ({{ $terlambat }})</button>
            <button type="button" class="filter-pill" onclick="filterSiswaTable('bolos', this)">Bolos ({{ $absensiHariIni->where('status', 'bolos')->count() }})</button>
            <button type="button" class="filter-pill" onclick="filterSiswaTable('pulang', this)">Sudah Pulang ({{ $sudahPulang }})</button>
          </div>
          <div style="position:relative; width:100%; max-width:280px;">
            <i class="bi bi-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-3); font-size:12px;"></i>
            <input type="text" id="searchSiswaPiket" onkeyup="searchSiswaPiketTable()" placeholder="Cari nama, NIS, kelas..." style="width:100%; height:36px; padding-left:32px; font-size:12.5px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); color:var(--text);" />
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table" id="tableSiswaPiket">
            <thead>
              <tr>
                <th style="width:40px; text-align:center;">No</th>
                <th style="width:45px; text-align:center;">Foto</th>
                <th>NIS</th>
                <th>Nama Peserta Didik</th>
                <th>Kelas / Rombel</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th>Status</th>
                <th>Sumber Input</th>
                <th style="text-align:center; width:90px;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($absensiHariIni as $idx => $ab)
                @php
                  $rombel = $ab->siswaRombel?->rombel?->nama_rombel ?? '—';
                @endphp
                <tr class="row-siswa-absen" data-status="{{ $ab->status }}" data-pulang="{{ $ab->jam_pulang ? 'pulang' : 'belum' }}">
                  <td style="text-align:center; font-weight:700; color:var(--text-3); font-size:12px;">{{ $idx + 1 }}</td>
                  <td style="text-align:center; vertical-align:middle;">
                    <img src="{{ $ab->siswa?->foto_url }}" alt="{{ $ab->siswa?->nama }}" style="width:34px; height:34px; border-radius:50%; object-fit:cover; border:2px solid rgba(202,138,4,0.25);" />
                  </td>
                  <td style="font-family:var(--font-mono); font-weight:700; color:var(--text); font-size:12px;">{{ $ab->siswa?->nis ?? '-' }}</td>
                  <td>
                    <strong style="color:var(--text); font-size:13.5px;">{{ $ab->siswa?->nama ?? '—' }}</strong>
                  </td>
                  <td>
                    <span style="font-weight:800; color:var(--text); font-size:12.5px;">{{ $rombel }}</span>
                  </td>
                  <td style="font-family:var(--font-mono); font-size:12.5px; font-weight:700; color:var(--text);">
                    @if($ab->jam_masuk)
                      {{ substr($ab->jam_masuk, 0, 5) }}
                    @else
                      —
                    @endif
                  </td>
                  <td style="font-family:var(--font-mono); font-size:12.5px; font-weight:700; color:var(--text);">
                    @if($ab->jam_pulang)
                      {{ substr($ab->jam_pulang, 0, 5) }}
                    @else
                      <span style="color:var(--text-3); font-style:italic; font-size:11px;">Belum Tap Pulang</span>
                    @endif
                  </td>
                  <td>
                    @if($ab->status === 'hadir')
                      <span style="font-weight:800; font-size:12px; color:var(--text);">Hadir Tepat</span>
                    @elseif($ab->status === 'terlambat')
                      <span style="font-weight:800; font-size:12px; color:var(--text);">Terlambat</span>
                    @elseif($ab->status === 'bolos')
                      <span style="font-weight:800; font-size:12px; color:var(--text);">Bolos</span>
                    @elseif($ab->status === 'alpha')
                      <span style="font-weight:800; font-size:12px; color:var(--text);">Alpha</span>
                    @else
                      <span style="font-weight:800; font-size:12px; color:var(--text);">{{ ucfirst($ab->status) }}</span>
                    @endif
                  </td>
                  <td>
                    @php
                      $matchingIzin = $izinHariIni->firstWhere('siswa_id', $ab->pemilik_id ?: $ab->siswaRombel?->siswa_id);
                    @endphp
                    @if(in_array($ab->status, ['izin', 'sakit', 'dispen']) || $matchingIzin)
                      <div>
                        <span class="badge" style="background:rgba(59,130,246,0.12); color:#2563EB; font-size:10.5px; font-weight:800;">
                          <i class="bi bi-file-earmark-check-fill"></i> Izin Resmi
                        </span>
                        <div style="font-size:11px; color:var(--text); font-weight:700; margin-top:2px;">
                          <i class="bi bi-person-check-fill" style="color:#10B981; margin-right:2px;"></i>{{ $matchingIzin?->disetujui_oleh ?: ($ab->keterangan ?: 'Guru Piket') }}
                        </div>
                      </div>
                    @elseif($ab->sumber_absen === 'kios_rfid' || $ab->sumber_absen === 'kios_wajah' || $ab->sumber_absen === 'face_kiosk' || $ab->sumber_absen === 'kios_biometrik' || empty($ab->sumber_absen))
                      <span class="badge" style="background:var(--bg-3); color:var(--text-2); font-size:10.5px;">
                        <i class="bi bi-camera-video-fill" style="color:var(--gold);"></i> Smart Gate (Face ID)
                      </span>
                    @elseif($ab->sumber_absen === 'koreksi_piket_manual')
                      <div>
                        <span class="badge" style="background:rgba(202,138,4,0.15); color:var(--gold); font-size:10.5px; font-weight:800;">
                          <i class="bi bi-pencil-square"></i> Koreksi Piket
                        </span>
                        <div style="font-size:11px; color:var(--text); font-weight:600; margin-top:2px;" title="{{ $ab->keterangan }}">
                          {{ Str::limit($ab->keterangan ?: 'Petugas Piket', 26) }}
                        </div>
                      </div>
                    @elseif($ab->sumber_absen === 'manual_piket' || $ab->sumber_absen === 'manual_izin_piket')
                      <div>
                        <span class="badge" style="background:rgba(16,185,129,0.12); color:#10B981; font-size:10.5px; font-weight:800;">
                          <i class="bi bi-clipboard-check"></i> Manual Piket
                        </span>
                        <div style="font-size:11px; color:var(--text); font-weight:600; margin-top:2px;" title="{{ $ab->keterangan }}">
                          {{ Str::limit($ab->keterangan ?: 'Petugas Piket', 26) }}
                        </div>
                      </div>
                    @elseif($ab->sumber_absen === 'evaluasi_alpha')
                      <span class="badge" style="background:rgba(239,68,68,0.12); color:#DC2626; font-size:10.5px; font-weight:800;">
                        <i class="bi bi-robot"></i> Sistem Otomatis
                      </span>
                    @else
                      <div>
                        <span class="badge" style="background:var(--bg-3); color:var(--text-2); font-size:10.5px;">
                          {{ $ab->sumber_absen ?: 'Piket' }}
                        </span>
                        @if($ab->keterangan)
                          <div style="font-size:10.5px; color:var(--text-3); margin-top:2px;">{{ Str::limit($ab->keterangan, 22) }}</div>
                        @endif
                      </div>
                    @endif
                  </td>
                  <td style="text-align:center;">
                    <button type="button" class="btn btn-sm btn-outline" style="padding:3px 8px; font-size:11px; font-weight:700; color:var(--gold); border-color:rgba(202,138,4,0.3);" onclick="openKoreksiPiketModal({{ $ab->id }}, '{{ addslashes($ab->siswa?->nama ?? 'Siswa') }}', '{{ $ab->status }}', '{{ $ab->jam_masuk ? substr($ab->jam_masuk,0,5) : '' }}', '{{ $ab->jam_pulang ? substr($ab->jam_pulang,0,5) : '' }}', '{{ addslashes($ab->keterangan ?? '') }}')">
                      <i class="bi bi-pencil-square"></i> Koreksi
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="10" style="text-align:center; padding:36px; color:var(--text-3);">
                    <i class="bi bi-inbox" style="font-size:32px; opacity:0.4; display:block; margin-bottom:6px;"></i>
                    Belum ada rekaman absensi siswa hari ini.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- ── TAB 2: PRESENSI GURU & STAF ── --}}
    <div id="tab-guru" class="piket-tab-pane">
      <div class="panel" style="padding:0; overflow:hidden;">
        <div class="panel-title" style="padding:14px 18px; margin:0; border-bottom:1px solid var(--border);">
          <span><i class="bi bi-person-badge-fill" style="color:var(--gold); margin-right:6px;"></i> Kehadiran Dewan Guru &amp; Pegawai Hari Ini</span>
          <span class="badge" style="background:var(--bg-3); color:var(--text); font-size:11.5px; font-weight:700;">{{ $absensiGuruHariIni->count() }} Guru Sudah Scan</span>
        </div>
        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th style="width:40px; text-align:center;">No</th>
                <th style="width:45px; text-align:center;">Foto</th>
                <th>NIP / ID</th>
                <th>Nama Guru / Pegawai</th>
                <th>Jabatan</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th>Status</th>
                <th>Sumber</th>
                <th style="text-align:center; width:90px;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($absensiGuruHariIni as $idx => $ag)
                <tr>
                  <td style="text-align:center; font-weight:700; color:var(--text-3); font-size:12px;">{{ $idx + 1 }}</td>
                  <td style="text-align:center; vertical-align:middle;">
                    <img src="{{ $ag->guru?->foto_url }}" alt="{{ $ag->guru?->nama }}" style="width:34px; height:34px; border-radius:50%; object-fit:cover;" />
                  </td>
                  <td style="font-family:var(--font-mono); font-weight:700; color:var(--text); font-size:12px;">{{ $ag->guru?->nip ?: '-' }}</td>
                  <td><strong style="color:var(--text); font-size:13.5px;">{{ $ag->guru?->nama ?? '—' }}</strong></td>
                  <td style="color:var(--text-2); font-size:12.5px;">{{ $ag->guru?->jabatan ?? 'Guru' }}</td>
                  <td style="font-family:var(--font-mono); font-size:12.5px; font-weight:700; color:var(--text);">
                    {{ $ag->jam_masuk ? substr($ag->jam_masuk,0,5) : '—' }}
                  </td>
                  <td style="font-family:var(--font-mono); font-size:12.5px; font-weight:700; color:var(--text);">
                    {{ $ag->jam_pulang ? substr($ag->jam_pulang,0,5) : '—' }}
                  </td>
                  <td>
                    @if($ag->status === 'hadir')
                      <span style="font-weight:800; font-size:12px; color:var(--text);">Hadir</span>
                    @elseif($ag->status === 'terlambat')
                      <span style="font-weight:800; font-size:12px; color:var(--text);">Terlambat</span>
                    @else
                      <span style="font-weight:800; font-size:12px; color:var(--text);">{{ ucfirst($ag->status) }}</span>
                    @endif
                  </td>
                  <td>
                    @if($ag->sumber_absen === 'kios_rfid' || $ag->sumber_absen === 'kios_wajah' || $ag->sumber_absen === 'face_kiosk' || $ag->sumber_absen === 'kios_biometrik' || empty($ag->sumber_absen))
                      <span class="badge" style="background:var(--bg-3); color:var(--text-2); font-size:10.5px;">
                        <i class="bi bi-camera-video-fill" style="color:var(--gold);"></i> Smart Gate (Face ID)
                      </span>
                    @elseif($ag->sumber_absen === 'manual_izin_piket' || in_array($ag->status, ['izin', 'sakit', 'cuti']))
                      <div>
                        <span class="badge" style="background:rgba(59,130,246,0.12); color:#2563EB; font-size:10.5px; font-weight:800;">
                          <i class="bi bi-file-earmark-check-fill"></i> Izin Dinas / Sakit
                        </span>
                        <div style="font-size:11px; color:var(--text); font-weight:600; margin-top:2px;">
                          {{ Str::limit($ag->keterangan ?: 'Guru Piket', 26) }}
                        </div>
                      </div>
                    @elseif($ag->sumber_absen === 'koreksi_piket_manual')
                      <div>
                        <span class="badge" style="background:rgba(202,138,4,0.15); color:var(--gold); font-size:10.5px; font-weight:800;">
                          <i class="bi bi-pencil-square"></i> Koreksi Piket
                        </span>
                        <div style="font-size:11px; color:var(--text); font-weight:600; margin-top:2px;">
                          {{ Str::limit($ag->keterangan ?: 'Petugas Piket', 26) }}
                        </div>
                      </div>
                    @else
                      <span class="badge" style="background:var(--bg-3); color:var(--text-2); font-size:10.5px;">
                        {{ $ag->sumber_absen }}
                      </span>
                    @endif
                  </td>
                  <td style="text-align:center;">
                    <button type="button" class="btn btn-sm btn-outline" style="padding:3px 8px; font-size:11px; font-weight:700; color:var(--gold); border-color:rgba(202,138,4,0.3);" onclick="openKoreksiPiketModal({{ $ag->id }}, '{{ addslashes($ag->guru?->nama ?? 'Guru') }}', '{{ $ag->status }}', '{{ $ag->jam_masuk ? substr($ag->jam_masuk,0,5) : '' }}', '{{ $ag->jam_pulang ? substr($ag->jam_pulang,0,5) : '' }}', '{{ addslashes($ag->keterangan ?? '') }}')">
                      <i class="bi bi-pencil-square"></i> Koreksi
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="10" style="text-align:center; padding:36px; color:var(--text-3);">
                    Belum ada data scan guru hari ini.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- ── TAB 3: IZIN & DISPENSASI ── --}}
    <div id="tab-izin" class="piket-tab-pane">
      <div class="panel" style="padding:0; overflow:hidden;">
        <div class="panel-title" style="padding:14px 18px; margin:0; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
          <span><i class="bi bi-envelope-paper-fill" style="color:var(--gold); margin-right:6px;"></i> Rekap Izin &amp; Sakit Siswa Hari Ini</span>
          <a href="{{ route('izin-siswa.index') }}" class="btn btn-sm btn-gold" style="font-size:11.5px; font-weight:800;">
            <i class="bi bi-plus-lg"></i> + Catat Izin Baru
          </a>
        </div>
        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th style="width:40px; text-align:center;">No</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Jenis Izin</th>
                <th>Keterangan / Alasan</th>
                <th>Pemberi Izin</th>
                <th style="text-align:right;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($izinHariIni as $idx => $iz)
                @php $rombel = $iz->siswa?->siswaRombels?->where('status_keanggotaan', 'aktif')->first()?->rombel?->nama_rombel ?? '-'; @endphp
                <tr>
                  <td style="text-align:center; font-weight:700; color:var(--text-3); font-size:12px;">{{ $idx + 1 }}</td>
                  <td style="font-family:var(--font-mono); font-weight:700; color:var(--text); font-size:12px;">{{ $iz->siswa?->nis ?? '-' }}</td>
                  <td><strong style="color:var(--text); font-size:13.5px;">{{ $iz->siswa?->nama ?? '—' }}</strong></td>
                  <td><span style="font-weight:800; color:var(--text); font-size:12.5px;">{{ $rombel }}</span></td>
                  <td>
                    @if($iz->jenis === 'sakit')
                      <span style="font-weight:800; font-size:12px; color:var(--text);">Sakit</span>
                    @elseif($iz->jenis === 'izin')
                      <span style="font-weight:800; font-size:12px; color:var(--text);">Izin</span>
                    @elseif($iz->jenis === 'dispen')
                      <span style="font-weight:800; font-size:12px; color:var(--text);">Dispensasi</span>
                    @elseif($iz->jenis === 'pulang_cepat')
                      <span style="font-weight:800; font-size:12px; color:var(--text);">Pulang Cepat</span>
                    @else
                      <span style="font-weight:800; font-size:12px; color:var(--text);">{{ ucfirst($iz->jenis) }}</span>
                    @endif
                  </td>
                  <td style="color:var(--text-2); font-size:12.5px;">{{ $iz->keterangan ?: '—' }}</td>
                  <td style="font-size:12px; font-weight:700; color:var(--text);">
                    <i class="bi bi-person-check-fill" style="color:#10B981; margin-right:4px;"></i>{{ $iz->disetujui_oleh ?: 'Guru Piket' }}
                  </td>
                  <td style="text-align:right;">
                    @if($iz->bukti_surat)
                      <a href="{{ asset('storage/' . $iz->bukti_surat) }}" target="_blank" class="btn btn-sm btn-outline" style="font-size:11px; padding:3px 8px;" title="Lihat Lampiran Surat">
                        <i class="bi bi-file-earmark-image"></i> Surat
                      </a>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" style="text-align:center; padding:36px; color:var(--text-3);">
                    Tidak ada siswa yang mengajukan izin hari ini.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- ── TAB 4: FOLLOW-UP ORTU (SISWA TERLAMBAT & BELUM HADIR) ── --}}
    <div id="tab-followup" class="piket-tab-pane">
      <div class="panel" style="padding:0; overflow:hidden;">
        <div class="panel-title" style="padding:14px 18px; margin:0; border-bottom:1px solid var(--border);">
          <span><i class="bi bi-whatsapp" style="color:#22C55E; margin-right:6px;"></i> Tindak Lanjut Siswa Terlambat &amp; Belum Tap Hari Ini</span>
          <span style="font-size:12px; color:var(--text-3);">Hubungi wali murid via WhatsApp dengan 1-klik untuk konfirmasi kehadiran.</span>
        </div>
        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th style="width:40px; text-align:center;">No</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Kondisi Kehadiran</th>
                <th>Nama Orang Tua / No WA</th>
                <th style="text-align:right;">Aksi Cepat</th>
              </tr>
            </thead>
            <tbody>
              @php $noFollow = 1; @endphp
              
              {{-- 1. Siswa Terlambat --}}
              @foreach($siswaTerlambatList as $st)
                @php
                  $s = $st->siswa;
                  $rombel = $st->siswaRombel?->rombel?->nama_rombel ?? '-';
                  $hp = preg_replace('/[^0-9]/', '', $s->no_hp_ortu ?? '');
                  if (str_starts_with($hp, '0')) $hp = '62' . substr($hp, 1);
                  $waMsg = rawurlencode("Yth. Bapak/Ibu Wali Murid dari ananda {$s->nama} ({$rombel}), kami dari Tim Guru Piket SMKN 1 Air Naningan menginformasikan bahwa ananda tiba TERLAMBAT di sekolah pada pukul " . substr($st->jam_masuk,0,5) . " WIB. Mohon bimbingannya agar ananda dapat hadir tepat waktu. Terima kasih.");
                @endphp
                <tr>
                  <td style="text-align:center; font-weight:700; color:var(--text-3); font-size:12px;">{{ $noFollow++ }}</td>
                  <td style="font-family:var(--font-mono); font-weight:700; color:var(--gold); font-size:12px;">{{ $s->nis }}</td>
                  <td><strong style="color:var(--text); font-size:13.5px;">{{ $s->nama }}</strong></td>
                  <td><span style="font-weight:800; color:var(--text); font-size:12.5px;">{{ $rombel }}</span></td>
                  <td>
                    <span class="badge" style="background:rgba(245,158,11,0.15); color:#D97706; font-weight:800;">
                      TERLAMBAT ({{ substr($st->jam_masuk,0,5) }})
                    </span>
                  </td>
                  <td>
                    <div style="font-size:12.5px; font-weight:700; color:var(--text);">{{ $s->nama_ortu ?: 'Wali Murid' }}</div>
                    <div style="font-size:11px; color:var(--text-3); font-family:var(--font-mono);">{{ $s->no_hp_ortu ?: 'Tidak ada No WA' }}</div>
                  </td>
                  <td style="text-align:right; white-space:nowrap;">
                    @if($hp)
                      <a href="https://wa.me/{{ $hp }}?text={{ $waMsg }}" target="_blank" class="btn btn-sm btn-outline" style="font-size:11px; padding:3px 10px; color:#16A34A; border-color:rgba(34,197,94,0.4); font-weight:800;">
                        <i class="bi bi-whatsapp"></i> Chat Ortu
                      </a>
                    @else
                      <span style="color:var(--text-3); font-size:11px;">-</span>
                    @endif
                    <a href="{{ url('/surat/cetak?siswa_id=' . $s->id . '&kategori=panggilan_ortu') }}" target="_blank" class="btn btn-sm btn-outline" style="font-size:11px; padding:3px 8px; font-weight:700; color:var(--gold); margin-left:4px;" title="Cetak Surat">
                      <i class="bi bi-printer"></i>
                    </a>
                  </td>
                </tr>
              @endforeach

              {{-- 2. Siswa Belum Hadir --}}
              @foreach($siswaBelumHadirList->take(30) as $sb)
                @php
                  $rombel = $sb->siswaRombels->where('status_keanggotaan', 'aktif')->first()?->rombel?->nama_rombel ?? '-';
                  $hp = preg_replace('/[^0-9]/', '', $sb->no_hp_ortu ?? '');
                  if (str_starts_with($hp, '0')) $hp = '62' . substr($hp, 1);
                  $waMsg = rawurlencode("Yth. Bapak/Ibu Wali Murid dari ananda {$sb->nama} ({$rombel}), kami dari Tim Guru Piket SMKN 1 Air Naningan mengonfirmasi bahwa ananda BELUM MELAKUKAN TAP PRESENSI di sekolah hari ini (" . now()->locale('id')->isoFormat('D MMMM Y') . "). Mohon konfirmasi keberadaan ananda. Terima kasih.");
                @endphp
                <tr>
                  <td style="text-align:center; font-weight:700; color:var(--text-3); font-size:12px;">{{ $noFollow++ }}</td>
                  <td style="font-family:var(--font-mono); font-weight:700; color:var(--gold); font-size:12px;">{{ $sb->nis }}</td>
                  <td><strong style="color:var(--text); font-size:13.5px;">{{ $sb->nama }}</strong></td>
                  <td><span style="font-weight:800; color:var(--text); font-size:12.5px;">{{ $rombel }}</span></td>
                  <td>
                    <span class="badge" style="background:rgba(239,68,68,0.12); color:#DC2626; font-weight:800;">
                      BELUM TAP / ALPHA
                    </span>
                  </td>
                  <td>
                    <div style="font-size:12.5px; font-weight:700; color:var(--text);">{{ $sb->nama_ortu ?: 'Wali Murid' }}</div>
                    <div style="font-size:11px; color:var(--text-3); font-family:var(--font-mono);">{{ $sb->no_hp_ortu ?: 'Tidak ada No WA' }}</div>
                  </td>
                  <td style="text-align:right; white-space:nowrap;">
                    @if($hp)
                      <a href="https://wa.me/{{ $hp }}?text={{ $waMsg }}" target="_blank" class="btn btn-sm btn-outline" style="font-size:11px; padding:3px 10px; color:#16A34A; border-color:rgba(34,197,94,0.4); font-weight:800;">
                        <i class="bi bi-whatsapp"></i> Chat Ortu
                      </a>
                    @else
                      <span style="color:var(--text-3); font-size:11px;">-</span>
                    @endif
                    <a href="{{ url('/surat/cetak?siswa_id=' . $sb->id . '&kategori=panggilan_ortu') }}" target="_blank" class="btn btn-sm btn-outline" style="font-size:11px; padding:3px 8px; font-weight:700; color:var(--gold); margin-left:4px;" title="Cetak Surat">
                      <i class="bi bi-printer"></i>
                    </a>
                  </td>
                </tr>
              @endforeach

              @if($noFollow === 1)
                <tr>
                  <td colspan="7" style="text-align:center; padding:36px; color:var(--text-3);">
                    <i class="bi bi-check-circle-fill" style="font-size:32px; color:#22C55E; display:block; margin-bottom:6px;"></i>
                    Semua siswa telah hadir tepat waktu atau memiliki surat izin yang sah hari ini!
                  </td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- ── TAB 5: JADWAL PIKET SEPEKAN ── --}}
    <div id="tab-jadwal" class="piket-tab-pane">
      <div class="piket-day-grid">
        @foreach($hariList as $hari)
          @php
            $isToday = ($hari === $hariHariIni);
            $listJadwal = $jadwalPiketSeminggu->get($hari, collect());
          @endphp
          <div class="piket-day-col {{ $isToday ? 'today' : '' }}">
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); padding-bottom:8px;">
              <span style="font-size:14px; font-weight:800; color:var(--text);">Hari {{ $hari }}</span>
              @if($isToday)
                <span class="badge" style="background:#10B981; color:#fff; font-size:10.5px; font-weight:800;">HARI INI</span>
              @else
                <span style="font-size:11px; color:var(--text-3); font-family:var(--font-mono);">{{ $listJadwal->count() }} Guru</span>
              @endif
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
              @forelse($listJadwal as $lj)
                <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:8px 10px; display:flex; align-items:center; gap:8px;">
                  <img src="{{ $lj->guru->foto_url ?? '' }}" alt="{{ $lj->guru->nama ?? '-' }}" style="width:30px; height:30px; border-radius:50%; object-fit:cover;" />
                  <div style="min-width:0; flex:1;">
                    <div style="font-weight:700; font-size:12px; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                      {{ $lj->guru->nama ?? 'Guru' }}
                    </div>
                    <div style="font-size:10px; color:var(--text-3); font-family:var(--font-mono);">
                      {{ $lj->keterangan ?: ($lj->guru->jabatan ?? 'Guru Piket') }}
                    </div>
                  </div>
                </div>
              @empty
                <div style="text-align:center; padding:18px 8px; color:var(--text-3); font-size:11.5px; border:1px dashed var(--border-2); border-radius:var(--r-sm);">
                  Belum ada guru piket
                </div>
              @endforelse
            </div>
          </div>
        @endforeach
      </div>
    </div>

  </main>
</div>

{{-- SCRIPT INTERAKTIF MEJA PIKET --}}
<script>
  // ── Clock Digital ──
  function updateLiveClock() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    const s = String(now.getSeconds()).padStart(2, '0');
    const el = document.getElementById('livePiketClock');
    if (el) el.textContent = `${h}:${m}:${s}`;
  }
  setInterval(updateLiveClock, 1000);

  // ── Switch Tabs ──
  function switchPiketTab(tabId, btn) {
    document.querySelectorAll('.piket-tab-pane').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.piket-tab-btn').forEach(el => el.classList.remove('active'));
    
    const target = document.getElementById(tabId);
    if (target) target.classList.add('active');
    if (btn) btn.classList.add('active');
  }

  // ── Presensi Manual Live Search Logic ──
  function onPmKategoriChange() {
    const kat = document.getElementById('pmKategori').value;
    const label = document.getElementById('pmLabelNama');
    const input = document.getElementById('pmSearchInput');
    
    if (kat === 'siswa') {
      label.innerHTML = 'PILIH SISWA <span style="color:var(--red);">*</span>';
      input.placeholder = 'Ketik nama, NIS, atau kelas...';
    } else {
      label.innerHTML = 'PILIH GURU <span style="color:var(--red);">*</span>';
      input.placeholder = 'Ketik nama, NIP, atau jabatan...';
    }

    clearPmSelection();
  }

  function filterPmSearch(val) {
    const kat = document.getElementById('pmKategori').value;
    const q = (val || '').toLowerCase().trim();
    const dropdown = document.getElementById('pmDropdownMenu');
    
    // Jangan munculkan dropdown jika kolom input kosong / belum mengetik
    if (!q || q.length === 0) {
      dropdown.style.display = 'none';
      return;
    }

    const items = document.querySelectorAll('.pm-search-item');
    let visibleCount = 0;

    items.forEach(el => {
      const itemType = el.getAttribute('data-type');
      if (itemType !== kat) {
        el.style.display = 'none';
        return;
      }

      const name = (el.getAttribute('data-name') || '').toLowerCase();
      const sub = (el.getAttribute('data-sub') || '').toLowerCase();
      const match = name.includes(q) || sub.includes(q);

      el.style.display = match ? 'block' : 'none';
      if (match) visibleCount++;
    });

    const emptyMsg = document.getElementById('pmEmptyMsg');
    if (emptyMsg) emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
    
    dropdown.style.display = 'block';
  }

  function selectPmItem(type, id, name, sub) {
    document.getElementById('pmPemilikId').value = id;
    document.getElementById('pmSearchInput').value = name + ' (' + sub + ')';
    document.getElementById('pmClearBtn').style.display = 'block';
    const dropdown = document.getElementById('pmDropdownMenu');
    if (dropdown) dropdown.style.display = 'none';
  }

  function clearPmSelection() {
    document.getElementById('pmPemilikId').value = '';
    document.getElementById('pmSearchInput').value = '';
    document.getElementById('pmClearBtn').style.display = 'none';
    const dropdown = document.getElementById('pmDropdownMenu');
    if (dropdown) dropdown.style.display = 'none';
  }

  // Close dropdown when clicking outside
  document.addEventListener('click', function(e) {
    const wrap = document.getElementById('pmSearchWrap');
    if (wrap && !wrap.contains(e.target)) {
      const menu = document.getElementById('pmDropdownMenu');
      if (menu) menu.style.display = 'none';
    }
  });

  document.addEventListener('DOMContentLoaded', () => {
    onPmKategoriChange();
  });

  // ── Filter Tabel Siswa Piket (Semua / Hadir / Terlambat / Pulang) ──
  function filterSiswaTable(status, btn) {
    document.querySelectorAll('.filter-pill').forEach(el => el.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const rows = document.querySelectorAll('#tableSiswaPiket tbody tr.row-siswa-absen');
    rows.forEach(r => {
      const rowStatus = r.dataset.status;
      const rowPulang = r.dataset.pulang;

      if (status === 'all') {
        r.style.display = '';
      } else if (status === 'hadir' && rowStatus === 'hadir') {
        r.style.display = '';
      } else if (status === 'terlambat' && rowStatus === 'terlambat') {
        r.style.display = '';
      } else if (status === 'bolos' && rowStatus === 'bolos') {
        r.style.display = '';
      } else if (status === 'pulang' && rowPulang === 'pulang') {
        r.style.display = '';
      } else {
        r.style.display = 'none';
      }
    });
  }

  // ── Search Input Filter Real-time ──
  function searchSiswaPiketTable() {
    const query = document.getElementById('searchSiswaPiket').value.toLowerCase();
    const rows = document.querySelectorAll('#tableSiswaPiket tbody tr.row-siswa-absen');

    rows.forEach(r => {
      const text = r.innerText.toLowerCase();
      r.style.display = text.includes(query) ? '' : 'none';
    });
  }

  // ── Modal Helper Global Meja Piket ──
  function openModal(id) {
    const el = document.getElementById(id);
    if (el) {
      el.classList.add('active');
      el.style.display = 'flex';
    }
  }

  function closeModal(id) {
    const el = document.getElementById(id);
    if (el) {
      el.classList.remove('active');
      el.style.display = 'none';
    }
  }

  // ── Modal Koreksi Presensi Meja Piket ──
  function openKoreksiPiketModal(id, nama, status, jamMasuk, jamPulang, keterangan) {
    const form = document.getElementById('formKoreksiPiket');
    if (form) form.action = '/piket/absensi/' + id;
    
    const elNama = document.getElementById('koreksiPiketNama');
    if (elNama) elNama.value = nama || '';

    const elStatus = document.getElementById('koreksiPiketStatus');
    if (elStatus) elStatus.value = status || 'hadir';

    const elMasuk = document.getElementById('koreksiPiketJamMasuk');
    if (elMasuk) elMasuk.value = jamMasuk ? jamMasuk.substring(0, 5) : '';

    const elPulang = document.getElementById('koreksiPiketJamPulang');
    if (elPulang) elPulang.value = jamPulang ? jamPulang.substring(0, 5) : '';

    const elKet = document.getElementById('koreksiPiketKeterangan');
    if (elKet) elKet.value = keterangan || '';

    openModal('modalKoreksiPiket');
  }

  // Close modal when clicking on backdrop
  document.addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('modal-overlay')) {
      closeModal(e.target.id);
    }
  });

  document.addEventListener('DOMContentLoaded', () => {
    updatePmSelect();
  });
</script>

{{-- MODAL PRESENSI MANUAL MEJA PIKET --}}
<div class="modal-overlay" id="modalPresensiManual">
  <div class="modal-card" style="max-width:540px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:36px; height:36px; border-radius:8px; background:var(--gold-dim); color:var(--gold); display:flex; align-items:center; justify-content:center; font-size:18px;">
          <i class="bi bi-clipboard-plus-fill"></i>
        </div>
        <div>
          <h3 style="font-size:16px; font-weight:900; color:var(--text); margin:0;">Presensi Manual Meja Piket</h3>
          <div style="font-size:11.5px; color:var(--text-3);">Catat kehadiran jika terkendala Face ID atau verifikasi manual</div>
        </div>
      </div>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('modalPresensiManual')"><i class="bi bi-x-lg"></i></button>
    </div>

    <form method="POST" action="{{ route('piket.presensi-manual.store') }}">
      @csrf
      <div style="display:flex; flex-direction:column; gap:14px;">
        
        {{-- Kategori --}}
        <div class="form-group" style="margin-bottom:0;">
          <label style="font-size:11.5px; font-weight:700; text-transform:uppercase; color:var(--text-2); margin-bottom:4px; display:block;">Kategori <span style="color:var(--red);">*</span></label>
          <select name="kategori" id="pmKategori" onchange="onPmKategoriChange()" required style="width:100%; height:40px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px; color:var(--text); font-size:13px; font-weight:700;">
            <option value="siswa">Peserta Didik (Siswa)</option>
            <option value="guru">Guru / Tenaga Pendidik</option>
          </select>
        </div>

        {{-- Pilih Siswa / Guru (Live Search Input) --}}
        <div class="form-group" style="margin-bottom:0; position:relative;">
          <label id="pmLabelNama" style="font-size:11.5px; font-weight:700; text-transform:uppercase; color:var(--text-2); margin-bottom:4px; display:block;">PILIH SISWA <span style="color:var(--red);">*</span></label>
          <div style="position:relative;" id="pmSearchWrap">
            <i class="bi bi-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-3); font-size:13px; pointer-events:none;"></i>
            <input 
              type="text" 
              id="pmSearchInput" 
              placeholder="Ketik nama, NIS, atau kelas..." 
              autocomplete="off"
              oninput="filterPmSearch(this.value)"
              required
              style="width:100%; height:40px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 34px 0 34px; color:var(--text); font-size:13px; font-weight:600;" 
            />
            <button 
              type="button" 
              id="pmClearBtn" 
              onclick="clearPmSelection()" 
              style="display:none; position:absolute; right:8px; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--text-3); cursor:pointer; padding:4px 6px; font-size:14px;"
              title="Hapus pilihan"
            >
              <i class="bi bi-x-circle-fill"></i>
            </button>

            <input type="hidden" name="pemilik_id" id="pmPemilikId" required />

            {{-- Live Search Dropdown Panel --}}
            <div id="pmDropdownMenu" class="pm-search-dropdown" style="display:none; position:absolute; top:calc(100% + 4px); left:0; right:0; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-md); box-shadow:var(--shadow-lg); z-index:1100;">
              <div id="pmItemsContainer" style="max-height:220px; overflow-y:auto;">
                {{-- Siswa Items --}}
                @foreach($semuaSiswa as $s)
                  @php 
                    $rombel = $s->siswaRombels->where('status_keanggotaan', 'aktif')->first()?->rombel?->nama_rombel ?? 'Tanpa Rombel'; 
                  @endphp
                  <div 
                    class="pm-search-item" 
                    data-type="siswa"
                    data-id="{{ $s->id }}"
                    data-name="{{ $s->nama }}"
                    data-sub="NIS: {{ $s->nis ?? '-' }} · {{ $rombel }}"
                    onclick="selectPmItem('siswa', '{{ $s->id }}', '{{ addslashes($s->nama) }}', 'NIS: {{ $s->nis ?? '-' }} · {{ addslashes($rombel) }}')"
                  >
                    <div style="font-weight:700; color:var(--text);">{{ $s->nama }}</div>
                    <div style="font-size:11.5px; color:var(--text-3); font-family:var(--font-mono);">NIS: {{ $s->nis ?? '-' }} · {{ $rombel }}</div>
                  </div>
                @endforeach

                {{-- Guru Items --}}
                @foreach($semuaGuru as $g)
                  <div 
                    class="pm-search-item" 
                    data-type="guru"
                    data-id="{{ $g->id }}"
                    data-name="{{ $g->nama }}"
                    data-sub="{{ $g->jabatan ?? 'Guru' }} · NIP: {{ $g->nip ?: '-' }}"
                    style="display:none;"
                    onclick="selectPmItem('guru', '{{ $g->id }}', '{{ addslashes($g->nama) }}', '{{ addslashes($g->jabatan ?? 'Guru') }} · NIP: {{ $g->nip ?: '-' }}')"
                  >
                    <div style="font-weight:700; color:var(--text);">{{ $g->nama }}</div>
                    <div style="font-size:11.5px; color:var(--text-3); font-family:var(--font-mono);">{{ $g->jabatan ?? 'Guru' }} · NIP: {{ $g->nip ?: '-' }}</div>
                  </div>
                @endforeach

                <div id="pmEmptyMsg" style="display:none; padding:12px; text-align:center; color:var(--text-3); font-size:12.5px;">
                  <i class="bi bi-search" style="margin-right:4px;"></i> Data tidak ditemukan
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Sesi --}}
        <div class="form-group" style="margin-bottom:0;">
          <label style="font-size:11.5px; font-weight:700; text-transform:uppercase; color:var(--text-2); margin-bottom:4px; display:block;">Sesi Presensi <span style="color:var(--red);">*</span></label>
          <select name="sesi" required style="width:100%; height:40px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px; color:var(--text); font-size:13px; font-weight:700;">
            <option value="masuk">Sesi Masuk Pagi</option>
            <option value="pulang">Sesi Pulang Siang/Sore</option>
          </select>
        </div>

        {{-- Keterangan --}}
        <div class="form-group" style="margin-bottom:0;">
          <label style="font-size:11.5px; font-weight:700; text-transform:uppercase; color:var(--text-2); margin-bottom:4px; display:block;">Keterangan</label>
          <input type="text" name="keterangan" placeholder="Contoh: Lupa Kartu / Izin Lisan" style="width:100%; height:40px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px; color:var(--text); font-size:13px;" />
        </div>

        {{-- Action Buttons --}}
        <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:8px;">
          <button type="button" class="btn btn-outline" onclick="closeModal('modalPresensiManual')">Batal</button>
          <button type="submit" class="btn btn-gold" style="font-weight:800; display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-check2-circle"></i> Catat Presensi
          </button>
        </div>

      </div>
    </form>
  </div>
</div>

{{-- MODAL KOREKSI PRESENSI MEJA PIKET --}}
<div class="modal-overlay" id="modalKoreksiPiket">
  <div class="modal-card" style="max-width:480px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
      <h3 style="font-size:16px; font-weight:900; color:var(--text); margin:0; display:flex; align-items:center; gap:8px;">
        <i class="bi bi-pencil-square" style="color:var(--gold);"></i> Koreksi Presensi Meja Piket
      </h3>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('modalKoreksiPiket')"><i class="bi bi-x-lg"></i></button>
    </div>

    <form id="formKoreksiPiket" method="POST">
      @csrf
      @method('PUT')

      {{-- Nama Target --}}
      <div class="form-group" style="margin-bottom:14px;">
        <label style="font-size:11.5px; font-weight:700; color:var(--text-2); margin-bottom:4px; display:block;">Nama Peserta / Guru</label>
        <input type="text" id="koreksiPiketNama" readonly style="width:100%; height:38px; background:var(--bg-2); border:1px solid var(--border); border-radius:var(--r-sm); padding:0 12px; font-weight:700; color:var(--text); font-size:13px;" />
      </div>

      {{-- Status Presensi --}}
      <div class="form-group" style="margin-bottom:14px;">
        <label style="font-size:11.5px; font-weight:700; color:var(--text-2); margin-bottom:4px; display:block;">Status Kehadiran <span style="color:var(--red);">*</span></label>
        <select name="status" id="koreksiPiketStatus" required style="width:100%; height:40px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px; font-weight:700; color:var(--text); font-size:13px;">
          <option value="hadir">Hadir Tepat Waktu</option>
          <option value="terlambat">Datang Terlambat</option>
          <option value="izin">Izin</option>
          <option value="sakit">Sakit</option>
          <option value="dispen">Dispensasi</option>
          <option value="bolos">Bolos</option>
          <option value="alpha">Alpha</option>
        </select>
      </div>

      {{-- Waktu Masuk & Pulang --}}
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
        <div>
          <label style="font-size:11.5px; font-weight:700; color:var(--text-2); margin-bottom:4px; display:block;">Jam Masuk</label>
          <input type="time" name="jam_masuk" id="koreksiPiketJamMasuk" style="width:100%; height:38px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 10px; color:var(--text); font-size:13px;" />
        </div>
        <div>
          <label style="font-size:11.5px; font-weight:700; color:var(--text-2); margin-bottom:4px; display:block;">Jam Pulang</label>
          <input type="time" name="jam_pulang" id="koreksiPiketJamPulang" style="width:100%; height:38px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 10px; color:var(--text); font-size:13px;" />
        </div>
      </div>

      {{-- Keterangan Alasan Koreksi --}}
      <div class="form-group" style="margin-bottom:18px;">
        <label style="font-size:11.5px; font-weight:700; color:var(--text-2); margin-bottom:4px; display:block;">Alasan / Catatan Koreksi Piket</label>
        <input type="text" name="keterangan" id="koreksiPiketKeterangan" placeholder="Contoh: Surat izin dokter disusulkan / Koreksi piket" style="width:100%; height:38px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px; color:var(--text); font-size:13px;" />
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalKoreksiPiket')">Batal</button>
        <button type="submit" class="btn btn-gold" style="font-weight:800; display:inline-flex; align-items:center; gap:6px;">
          <i class="bi bi-check2-circle"></i> Simpan Koreksi
        </button>
      </div>
    </form>
  </div>
</div>

</body>
</html>
