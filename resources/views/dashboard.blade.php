<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SIRANI — Dasbor Utama Absensi SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <style>
    .clickable-card {
      cursor: pointer;
      position: relative;
      user-select: none;
      transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }
    .clickable-card:hover {
      transform: translateY(-3px);
      box-shadow: var(--shadow-md);
      border-color: #000000;
    }

    .section-divider {
      display: flex;
      align-items: center;
      gap: 12px;
      margin: 28px 0 16px 0;
    }
    .section-divider h2 {
      font-size: 16px;
      font-weight: 800;
      letter-spacing: -0.01em;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .section-divider-line {
      flex: 1;
      height: 1px;
      background: var(--border);
    }

    /* Tab Switcher */
    .tab-pills {
      display: flex;
      gap: 8px;
      margin-bottom: 16px;
    }
    .tab-pill-btn {
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      color: var(--text-2);
      padding: 8px 18px;
      border-radius: var(--r-sm);
      font-family: var(--font);
      font-size: 13px;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all .2s;
    }
    .tab-pill-btn.active {
      background: #000000 !important;
      color: #FFFFFF !important;
      border-color: #000000 !important;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.25);
    }
    .tab-pill-btn.active i {
      color: #FFFFFF !important;
    }

    /* Modules Grid Hub */
    .modules-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
      gap: 14px;
      margin-bottom: 24px;
    }
    .module-card {
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      border-radius: var(--r-md);
      padding: 14px 16px;
      text-decoration: none;
      color: var(--text);
      display: flex;
      align-items: center;
      gap: 12px;
      transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease, background .2s ease;
      box-shadow: var(--shadow-sm);
    }
    .module-card:hover {
      transform: translateY(-3px);
      border-color: #000000;
      box-shadow: var(--shadow-md);
      background: var(--bg-3);
    }
    .module-icon-wrap {
      width: 42px;
      height: 42px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      flex-shrink: 0;
    }
    .module-title {
      font-size: 13.5px;
      font-weight: 800;
      line-height: 1.3;
      color: var(--text);
    }
    .module-desc {
      font-size: 11px;
      color: var(--text-2);
      margin-top: 2px;
    }

    /* Mobile Responsive Dashboard Layout */
    @media (max-width: 768px) {
      .header {
        margin-bottom: 12px !important;
        padding-bottom: 8px !important;
      }
      .header-title h1 {
        font-size: 18px !important;
      }
      .header-title p {
        font-size: 11.5px !important;
      }
      .exec-hero {
        padding: 14px 16px !important;
        gap: 12px !important;
        margin-bottom: 14px !important;
      }
      .exec-date-card {
        padding: 10px 12px !important;
      }
      .db-kpi-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 8px !important;
        margin-bottom: 14px !important;
      }
      .db-kpi-card {
        padding: 10px 12px !important;
      }
      .db-kpi-val {
        font-size: 20px !important;
      }
      .db-kpi-title {
        font-size: 10px !important;
      }
      .db-kpi-icon {
        width: 28px !important;
        height: 28px !important;
        font-size: 14px !important;
      }
      .tab-pills {
        overflow-x: auto !important;
        flex-wrap: nowrap !important;
        -webkit-overflow-scrolling: touch !important;
        scrollbar-width: none !important;
        gap: 6px !important;
        margin-bottom: 12px !important;
      }
      .tab-pill-btn {
        padding: 6px 12px !important;
        font-size: 11.5px !important;
        white-space: nowrap !important;
        flex-shrink: 0 !important;
      }
      .modules-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 8px !important;
        margin-bottom: 16px !important;
      }
      .module-card {
        padding: 10px 12px !important;
        gap: 8px !important;
      }
      .module-icon-wrap {
        width: 34px !important;
        height: 34px !important;
        font-size: 16px !important;
        border-radius: 8px !important;
      }
      .module-title {
        font-size: 12px !important;
      }
      .module-desc {
        display: none !important;
      }
      .section-divider {
        margin: 18px 0 10px 0 !important;
      }
      .section-divider h2 {
        font-size: 14px !important;
      }
    }
  </style>
</head>
<body>

<div class="app-container">
  @include('partials.sidebar')

  <main class="main-content">
    <header class="header">
      <div class="header-title">
        <h1>SIRANI</h1>
        <p>Sistem Informasi Responsif Absensi SMKN 1 Air Naningan</p>
      </div>
      @include('partials.header_actions')
    </header>

    <!-- Hero Card Berdasarkan Role Pengguna -->
    @php
      $currentUser = auth()->user();
      $isAdmin = $currentUser ? $currentUser->isAdmin() : false;
      $isKepalaSekolah = $currentUser ? $currentUser->isKepalaSekolah() : false;
      $isWakasis = $currentUser ? $currentUser->isWakaKesiswaan() : false;
      $isWakaKurikulum = $currentUser ? $currentUser->isWakaKurikulum() : false;
      $isGuruBk = $currentUser ? $currentUser->isGuruBk() : false;
      $isWaliKelas = $currentUser ? $currentUser->isWaliKelas() : false;
      $isStafTu = $currentUser ? $currentUser->isStafTu() : false;
      $isGuruPiket = $currentUser ? $currentUser->isGuruPiket() : false;
      $isGuru = $currentUser ? $currentUser->isGuru() : false;
      $isWaliSedangPiket = $isWaliSedangPiket ?? false;

      $roleTitle = match(true) {
        $isKepalaSekolah                 => 'EXECUTIVE KEPALA SEKOLAH',
        $isWakasis                       => 'WAKA KESISWAAN MONITORING',
        $isWakaKurikulum                 => 'WAKA KURIKULUM MONITORING',
        $isGuruBk                        => 'GURU BIMBINGAN & KONSELING (BK)',
        ($isWaliKelas && $isWaliSedangPiket) => 'WALI KELAS MONITORING',
        $isWaliKelas                     => 'WALI KELAS MONITORING',
        $isStafTu                        => 'STAF TATA USAHA (TU)',
        $isGuruPiket                     => 'GURU PIKET OPERATIONAL',
        $isAdmin                         => 'ADMINISTRATOR MONITORING',
        default                          => 'PORTAL GURU & PEGAWAI',
      };
      $roleIcon = match(true) {
        $isKepalaSekolah => 'bi-award-fill',
        $isWakasis       => 'bi-shield-shaded',
        $isWakaKurikulum => 'bi-calendar-range-fill',
        $isGuruBk        => 'bi-heart-pulse-fill',
        $isWaliKelas     => 'bi-mortarboard-fill',
        $isStafTu        => 'bi-folder-symlink-fill',
        $isGuruPiket     => 'bi-shield-fill-check',
        $isAdmin         => 'bi-shield-check',
        default          => 'bi-person-badge-fill',
      };
    @endphp
    <div class="exec-hero">
      <div>
        <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap; margin-bottom:8px;">
          <span style="font-size:12px; font-weight:900; color:#000000; letter-spacing:0.5px; text-transform:uppercase;">
            {{ $roleTitle }}
          </span>
          @if($isWaliSedangPiket)
            <span style="font-size:11px; font-weight:900; color:#ffffff; letter-spacing:0.5px; text-transform:uppercase; background:#16A34A; border-radius:6px; padding:1px 8px; display:inline-flex; align-items:center; gap:4px;">
              <i class="bi bi-shield-fill-check"></i> GURU PIKET AKTIF
            </span>
          @elseif(($isGuruPiket || (isset($isPiketHariIni) && $isPiketHariIni)) && !$currentUser->isKepalaSekolah())
            <span style="font-size:12px; font-weight:900; color:#000000; letter-spacing:0.5px; text-transform:uppercase;">
              · BERTUGAS PIKET HARI INI
            </span>
          @endif
        </div>
        <h2 style="font-size:22px; font-weight:900; color:var(--text); margin-bottom:4px;">
          {{ $currentUser->name ?? 'Pengguna SIRANI' }}
        </h2>
        <div style="font-size:13px; color:var(--text-2);">
          Peran: <strong style="color:var(--text);">{{ $currentUser ? $currentUser->role_display_name : '-' }}</strong>
          @if($currentUser && $currentUser->guru && $currentUser->guru->jabatan)
            · Jabatan: <strong style="color:var(--text);">{{ $currentUser->guru->jabatan }}</strong>
          @endif
          · Tahun Ajaran: <strong style="color:#000000;">{{ $taAktif->nama ?? '2026/2027' }}</strong>
        </div>
      </div>

      <div class="exec-date-card">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px;">
          <div style="font-size:10px; color:var(--text-3); font-weight:800; text-transform:uppercase; letter-spacing:0.8px;">TANGGAL SISTEM</div>
          @if($isLiburHariIni)
            <span class="badge" style="background:rgba(239,68,68,0.12); color:#DC2626; border:1px solid rgba(239,68,68,0.25); font-size:10px; font-weight:800; padding:2px 8px; border-radius:10px;">HARI LIBUR</span>
          @else
            <span class="badge" style="background:rgba(0,0,0,0.06); color:#000000; border:1px solid rgba(0,0,0,0.12); font-size:10px; font-weight:800; padding:2px 8px; border-radius:10px;">EFEKTIF BELAJAR</span>
          @endif
        </div>
        <div style="font-size:17px; font-weight:900; color:var(--text); font-family:var(--font-mono); margin:4px 0 2px;">
          {{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }}
        </div>
        <div style="font-size:11.5px; color:var(--text-3);">
          @if($hariLiburAktif)
            <strong style="color:var(--text);">{{ $hariLiburAktif->nama_libur }}</strong> ({!! $hariLiburAktif->jenis_badge !!})
          @elseif(\App\Models\HariLibur::isWeekend($today))
            <span>Libur Akhir Pekan (Sabtu/Minggu)</span>
          @else
            Batas Masuk: <strong style="color:var(--text); font-family:var(--font-mono);">{{ substr($jadwalHariIni->jam_masuk_toleransi ?? '07:15', 0, 5) }}</strong> · 
            Jam Pulang: <strong style="color:var(--text); font-family:var(--font-mono);">{{ substr($jadwalHariIni->jam_pulang_mulai ?? '15:30', 0, 5) }}</strong>
          @endif
        </div>
      </div>
    </div>

    @if(session('success'))<div class="alert-success"><i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error"><i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i>{{ session('error') }}</div>@endif

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- 1. RINGKASAN EKSEKUTIF KEHADIRAN (KPI STRIP) -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    @if($isWakaKurikulum)
      <div class="db-kpi-grid">
        <!-- Persentase Kehadiran Guru -->
        <div class="db-kpi-card" onclick="openDetailModal('guru', 'semua', 'Seluruh Kehadiran Guru & Pegawai Hari Ini')">
          <div class="db-kpi-head">
            <span class="db-kpi-title">Kehadiran Guru</span>
            <div class="db-kpi-icon" style="background:var(--bg-3); color:#000000; border:1px solid var(--border-2);">
              <i class="bi bi-person-badge-fill"></i>
            </div>
          </div>
          <div class="db-kpi-val" style="color:#000000;">{{ $persenGuru }}%</div>
          <div class="db-kpi-sub">{{ $guruTotalScan }} dari {{ $totalGuruActive }} Hadir</div>
        </div>

        <!-- Guru Hadir Tepat -->
        <div class="db-kpi-card" onclick="openDetailModal('guru', 'hadir', 'Guru Hadir Tepat Waktu')">
          <div class="db-kpi-head">
            <span class="db-kpi-title">Guru Tepat Waktu</span>
            <div class="db-kpi-icon" style="background:var(--bg-3); color:#000000; border:1px solid var(--border-2);">
              <i class="bi bi-check2-circle"></i>
            </div>
          </div>
          <div class="db-kpi-val" style="color:#000000;">{{ $guruHadir }} <span style="font-size:12px; font-weight:600; color:var(--text-3);">Guru</span></div>
          <div class="db-kpi-sub">Siap mengajar KBM</div>
        </div>

        <!-- Guru Terlambat -->
        <div class="db-kpi-card" onclick="openDetailModal('guru', 'terlambat', 'Guru Terlambat Hari Ini')">
          <div class="db-kpi-head">
            <span class="db-kpi-title">Guru Terlambat</span>
            <div class="db-kpi-icon" style="background:var(--bg-3); color:#000000; border:1px solid var(--border-2);">
              <i class="bi bi-clock-history"></i>
            </div>
          </div>
          <div class="db-kpi-val" style="color:#000000;">{{ $guruTerlambat }} <span style="font-size:12px; font-weight:600; color:var(--text-3);">Guru</span></div>
          <div class="db-kpi-sub">Setelah bel masuk</div>
        </div>

        <!-- Guru Izin / Dinas / Sakit -->
        <div class="db-kpi-card" onclick="openDetailModal('guru', 'izin', 'Guru Izin / Dinas Luar Hari Ini')">
          <div class="db-kpi-head">
            <span class="db-kpi-title">Guru Izin / Dinas</span>
            <div class="db-kpi-icon" style="background:var(--bg-3); color:#000000; border:1px solid var(--border-2);">
              <i class="bi bi-envelope-paper-fill"></i>
            </div>
          </div>
          <div class="db-kpi-val" style="color:#000000;">{{ $guruIzin }} <span style="font-size:12px; font-weight:600; color:var(--text-3);">Guru</span></div>
          <div class="db-kpi-sub">Perlu guru infal</div>
        </div>

        <!-- Tingkat Kehadiran Siswa -->
        <div class="db-kpi-card" onclick="openDetailModal('siswa', 'semua', 'Seluruh Kehadiran Siswa Hari Ini')">
          <div class="db-kpi-head">
            <span class="db-kpi-title">Kehadiran Siswa</span>
            <div class="db-kpi-icon" style="background:var(--bg-3); color:#000000; border:1px solid var(--border-2);">
              <i class="bi bi-people-fill"></i>
            </div>
          </div>
          <div class="db-kpi-val" style="color:#000000;">{{ $persenSekolah }}%</div>
          <div class="db-kpi-sub">Target KBM: &ge; 90%</div>
        </div>

        <!-- Siswa PKL -->
        <div class="db-kpi-card" onclick="openDetailModal('siswa', 'pkl', 'Siswa Sedang PKL / Prakerin')">
          <div class="db-kpi-head">
            <span class="db-kpi-title">Siswa PKL</span>
            <div class="db-kpi-icon" style="background:var(--bg-3); color:#000000; border:1px solid var(--border-2);">
              <i class="bi bi-briefcase-fill"></i>
            </div>
          </div>
          <div class="db-kpi-val" style="color:#000000;">{{ $totalSiswaPkl }} <span style="font-size:12px; font-weight:600; color:var(--text-3);">Siswa</span></div>
          <div class="db-kpi-sub">Prakerin industri</div>
        </div>
      </div>

      <!-- Panel Khusus Operasional Kurikulum & Kalender Akademik -->
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 18px; margin-bottom: 24px;">
        <!-- Card 1: Jam Sekolah & Sesi Belajar Hari Ini -->
        <div class="panel" style="margin-bottom:0; padding:0; overflow:hidden; border:1px solid var(--border); border-radius:var(--r-md); background:var(--bg-2);">
          <div style="padding:14px 18px; border-bottom:1px solid var(--border); background:var(--surface); display:flex; justify-content:space-between; align-items:center;">
            <span style="font-weight:800; font-size:13.5px; color:var(--text); display:flex; align-items:center; gap:8px;">
              <i class="bi bi-clock-history"></i> Jam Belajar &amp; Sesi Smart Gate
            </span>
            <a href="/jadwal-sekolah" class="btn btn-outline" style="font-size:11px; padding:3px 10px; font-weight:800; text-decoration:none;">
              <i class="bi bi-pencil"></i> Atur Sesi
            </a>
          </div>
          <div style="padding:16px 18px;">
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:14px;">
              <div style="background:var(--bg-3); padding:12px; border-radius:var(--r-sm); border:1px solid var(--border-2);">
                <div style="font-size:10.5px; font-weight:800; text-transform:uppercase; color:var(--text-3);">Jam Masuk</div>
                <div style="font-size:18px; font-weight:900; font-family:var(--font-mono); color:var(--text); margin-top:2px;">
                  {{ $jadwalHariIni->jam_masuk ?? '07:00' }} WIB
                </div>
                <div style="font-size:10.5px; color:var(--text-3); margin-top:2px;">Toleransi: s/d {{ $jadwalHariIni->toleransi_keterlambatan ?? '07:15' }}</div>
              </div>
              <div style="background:var(--bg-3); padding:12px; border-radius:var(--r-sm); border:1px solid var(--border-2);">
                <div style="font-size:10.5px; font-weight:800; text-transform:uppercase; color:var(--text-3);">Jam Pulang</div>
                <div style="font-size:18px; font-weight:900; font-family:var(--font-mono); color:var(--text); margin-top:2px;">
                  {{ $jadwalHariIni->jam_pulang ?? '15:30' }} WIB
                </div>
                <div style="font-size:10.5px; color:var(--text-3); margin-top:2px;">Hari {{ $hariHariIni ?? 'Ini' }}</div>
              </div>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; background:var(--surface); padding:10px 14px; border-radius:var(--r-sm); border:1px solid var(--border);">
              <span style="font-size:12px; font-weight:700; color:var(--text-2);">Status Gerbang Pagi:</span>
              @if(isset($jadwalHarianSesi) && $jadwalHarianSesi && $jadwalHarianSesi->status_gerbang === 'buka')
                <span class="badge" style="background:rgba(34,197,94,0.12); color:#16A34A; border:1px solid rgba(34,197,94,0.25); font-weight:800; font-size:11px;">
                  <i class="bi bi-door-open-fill"></i> SESI TERBUKA
                </span>
              @else
                <span class="badge" style="background:rgba(239,68,68,0.12); color:#DC2626; border:1px solid rgba(239,68,68,0.25); font-weight:800; font-size:11px;">
                  <i class="bi bi-door-closed-fill"></i> SESI DITUTUP
                </span>
              @endif
            </div>
          </div>
        </div>

        <!-- Card 2: Kalender Libur & Agenda Akademik Terdekat -->
        <div class="panel" style="margin-bottom:0; padding:0; overflow:hidden; border:1px solid var(--border); border-radius:var(--r-md); background:var(--bg-2);">
          <div style="padding:14px 18px; border-bottom:1px solid var(--border); background:var(--surface); display:flex; justify-content:space-between; align-items:center;">
            <span style="font-weight:800; font-size:13.5px; color:var(--text); display:flex; align-items:center; gap:8px;">
              <i class="bi bi-calendar2-week-fill"></i> Kalender Akademik &amp; Agenda Terdekat
            </span>
            <a href="/hari-libur" class="btn btn-outline" style="font-size:11px; padding:3px 10px; font-weight:800; text-decoration:none;">
              <i class="bi bi-calendar-event"></i> Buka Kalender
            </a>
          </div>
          <div style="padding:14px 18px;">
            @if(isset($upcomingHolidays) && $upcomingHolidays->count() > 0)
              <div style="display:flex; flex-direction:column; gap:8px;">
                @foreach($upcomingHolidays as $hLibur)
                  <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 12px; background:var(--bg-3); border-radius:var(--r-sm); border:1px solid var(--border-2);">
                    <div>
                      <div style="font-weight:800; font-size:12.5px; color:var(--text);">{{ $hLibur->nama_libur }}</div>
                      <div style="font-size:11px; color:var(--text-3); font-family:var(--font-mono);">
                        {{ \Carbon\Carbon::parse($hLibur->tanggal_mulai)->translatedFormat('d M Y') }}
                        @if($hLibur->tanggal_mulai !== $hLibur->tanggal_selesai)
                          s/d {{ \Carbon\Carbon::parse($hLibur->tanggal_selesai)->translatedFormat('d M Y') }}
                        @endif
                      </div>
                    </div>
                    <div>{!! $hLibur->jenis_badge !!}</div>
                  </div>
                @endforeach
              </div>
            @else
              <div style="text-align:center; padding:24px 10px; color:var(--text-3); font-size:12px;">
                <i class="bi bi-calendar-check" style="font-size:24px; display:block; margin-bottom:6px; opacity:.5;"></i>
                Tidak ada agenda libur terdekat yang terdaftar.
              </div>
            @endif
          </div>
        </div>
      </div>
    @else
      <div class="db-kpi-grid">
        <!-- Persentase Kehadiran -->
        <div class="db-kpi-card" onclick="openDetailModal('siswa', 'semua', 'Seluruh Kehadiran Siswa Hari Ini')">
          <div class="db-kpi-head">
            <span class="db-kpi-title">Tingkat Kehadiran</span>
            <div class="db-kpi-icon" style="background:var(--bg-3); color:#000000; border:1px solid var(--border-2);">
              <i class="bi bi-pie-chart-fill"></i>
            </div>
          </div>
          <div class="db-kpi-val" style="color:#000000;">{{ $persenSekolah }}%</div>
          <div class="db-kpi-sub">Target: &ge; 90%</div>
        </div>

        <!-- Siswa Hadir Tepat -->
        <div class="db-kpi-card" onclick="openDetailModal('siswa', 'hadir', 'Siswa Hadir Tepat Waktu')">
          <div class="db-kpi-head">
            <span class="db-kpi-title">Hadir Tepat Waktu</span>
            <div class="db-kpi-icon" style="background:var(--bg-3); color:#000000; border:1px solid var(--border-2);">
              <i class="bi bi-check2-circle"></i>
            </div>
          </div>
          <div class="db-kpi-val" style="color:#000000;">{{ $siswaHadir }} <span style="font-size:12px; font-weight:600; color:var(--text-3);">Siswa</span></div>
          <div class="db-kpi-sub">Scan sebelum batas</div>
        </div>

        <!-- Siswa Terlambat -->
        <div class="db-kpi-card" onclick="openDetailModal('siswa', 'terlambat', 'Siswa Terlambat Hari Ini')">
          <div class="db-kpi-head">
            <span class="db-kpi-title">Terlambat Gerbang</span>
            <div class="db-kpi-icon" style="background:var(--bg-3); color:#000000; border:1px solid var(--border-2);">
              <i class="bi bi-clock-history"></i>
            </div>
          </div>
          <div class="db-kpi-val" style="color:#000000;">{{ $siswaTerlambat }} <span style="font-size:12px; font-weight:600; color:var(--text-3);">Siswa</span></div>
          <div class="db-kpi-sub">Pos guru piket</div>
        </div>

        <!-- Siswa Izin / Sakit -->
        <div class="db-kpi-card" onclick="openDetailModal('siswa', 'izin', 'Siswa Izin / Sakit Hari Ini')">
          <div class="db-kpi-head">
            <span class="db-kpi-title">Izin / Sakit</span>
            <div class="db-kpi-icon" style="background:var(--bg-3); color:#000000; border:1px solid var(--border-2);">
              <i class="bi bi-envelope-paper-fill"></i>
            </div>
          </div>
          <div class="db-kpi-val" style="color:#000000;">{{ $siswaIzin }} <span style="font-size:12px; font-weight:600; color:var(--text-3);">Siswa</span></div>
          <div class="db-kpi-sub">Keterangan sah</div>
        </div>

        <!-- Siswa PKL -->
        <div class="db-kpi-card" onclick="openDetailModal('siswa', 'pkl', 'Siswa Sedang PKL / Prakerin')">
          <div class="db-kpi-head">
            <span class="db-kpi-title">Siswa PKL</span>
            <div class="db-kpi-icon" style="background:var(--bg-3); color:#000000; border:1px solid var(--border-2);">
              <i class="bi bi-briefcase-fill"></i>
            </div>
          </div>
          <div class="db-kpi-val" style="color:#000000;">{{ $totalSiswaPkl }} <span style="font-size:12px; font-weight:600; color:var(--text-3);">Siswa</span></div>
          <div class="db-kpi-sub">Prakerin industri</div>
        </div>

        <!-- Siswa Alpha / Belum Hadir -->
        @php
          $alphaCount = max(0, $totalSiswaActive - ($siswaHadir + $siswaTerlambat + $siswaIzin + $totalSiswaPkl));
        @endphp
        <div class="db-kpi-card" onclick="openDetailModal('siswa', 'alpha', 'Siswa Alpha / Belum Scan Hari Ini')">
          <div class="db-kpi-head">
            <span class="db-kpi-title">Alpha / Belum Scan</span>
            <div class="db-kpi-icon" style="background:var(--bg-3); color:#000000; border:1px solid var(--border-2);">
              <i class="bi bi-exclamation-octagon-fill"></i>
            </div>
          </div>
          <div class="db-kpi-val" style="color:#000000;">{{ $alphaCount }} <span style="font-size:12px; font-weight:600; color:var(--text-3);">Siswa</span></div>
          <div class="db-kpi-sub">Follow up wali kelas</div>
        </div>

        {{-- KPI: Belum Scan Pulang --}}
        @php $jamTutupLabel = $sudahLewatJamTutup ? 'Dianggap Bolos' : 'Sementara'; @endphp
        <a href="/jadwal-piket" style="text-decoration:none;">
          <div class="db-kpi-card" style="border:1.5px solid {{ $sudahLewatJamTutup ? '#000000' : 'var(--border)' }};">
            <div class="db-kpi-head">
              <span class="db-kpi-title">Belum Scan Pulang</span>
              <div class="db-kpi-icon" style="background:var(--bg-3); color:#000000; border:1px solid var(--border-2);">
                <i class="bi bi-door-open-fill"></i>
              </div>
            </div>
            <div class="db-kpi-val" style="color:#000000;">{{ $siswaBelumPulangCount }} <span style="font-size:12px; font-weight:600; color:var(--text-3);">Siswa</span></div>
            <div class="db-kpi-sub" style="color:{{ $sudahLewatJamTutup ? '#000000' : 'var(--text-3)' }}; font-weight:{{ $sudahLewatJamTutup ? '800' : '600' }};">{{ $jamTutupLabel }}</div>
          </div>
        </a>
      </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- PANEL OPERASIONAL GURU PIKET HARI INI (AKTIF SAAT JADWAL PIKET) -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    @if(($isGuruPiket || (isset($isPiketHariIni) && $isPiketHariIni) || (isset($isWaliSedangPiket) && $isWaliSedangPiket)) && !$currentUser->isKepalaSekolah())
      <div class="panel" style="margin-bottom:24px; padding:0; overflow:hidden; border:2px solid #000000; border-radius:var(--r-md); background:var(--bg-2); box-shadow:var(--shadow-sm);">
        <div style="padding:14px 18px; border-bottom:1px solid var(--border); background:var(--surface); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
          <div style="font-size:14px; font-weight:800; color:#000000; display:flex; align-items:center; gap:8px;">
            <span>Meja Operasional Guru Piket Hari Ini</span>
            <span style="font-size:12px; font-weight:800; color:#000000;">
              (SEDANG BERTUGAS)
            </span>
          </div>
          <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
            <a href="/izin-siswa" class="btn btn-sm btn-gold" style="font-size:12px; font-weight:800; padding:6px 14px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
              <i class="bi bi-file-earmark-plus-fill"></i> + Input Izin / Dispen Siswa
            </a>
            <a href="/piket" class="btn btn-sm btn-outline" style="font-size:12px; font-weight:800; padding:6px 14px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
              <i class="bi bi-camera-fill"></i> Presensi Manual Gerbang
            </a>
          </div>
        </div>

        <div style="padding:18px;">
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom:16px;">
            <!-- Mini KPI 1: Siswa Terlambat Gerbang -->
            <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:12px 14px;">
              <div style="font-size:10.5px; font-weight:800; text-transform:uppercase; color:var(--text-3); letter-spacing:0.5px;">Siswa Terlambat Gerbang</div>
              <div style="font-size:22px; font-weight:900; font-family:var(--font-mono); color:#000000; margin-top:2px;">
                {{ $siswaTerlambat }} <span style="font-size:11.5px; font-weight:600; color:var(--text-3);">Siswa</span>
              </div>
              <div style="font-size:11px; color:var(--text-3); margin-top:2px;">Dicatat di Pos Piket</div>
            </div>

            <!-- Mini KPI 2: Siswa Izin / Dispen -->
            <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:12px 14px;">
              <div style="font-size:10.5px; font-weight:800; text-transform:uppercase; color:var(--text-3); letter-spacing:0.5px;">Perizinan / Dispen Aktif</div>
              <div style="font-size:22px; font-weight:900; font-family:var(--font-mono); color:#000000; margin-top:2px;">
                {{ $siswaIzin }} <span style="font-size:11.5px; font-weight:600; color:var(--text-3);">Surat</span>
              </div>
              <div style="font-size:11px; color:var(--text-3); margin-top:2px;">Keluar/Masuk Gerbang</div>
            </div>

            <!-- Mini KPI 3: Siswa Belum Scan -->
            <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:12px 14px;">
              <div style="font-size:10.5px; font-weight:800; text-transform:uppercase; color:var(--text-3); letter-spacing:0.5px;">Belum Scan Gerbang</div>
              <div style="font-size:22px; font-weight:900; font-family:var(--font-mono); color:#000000; margin-top:2px;">
                {{ $piketBelumHadirCount ?? 0 }} <span style="font-size:11.5px; font-weight:600; color:var(--text-3);">Siswa</span>
              </div>
              <div style="font-size:11px; color:var(--text-3); margin-top:2px;">Estimasi belum tap RFID / Barcode</div>
            </div>

            <!-- Mini KPI 4: Siswa Belum Scan Pulang -->
            @php $labelPulang = $sudahLewatJamTutup ? 'Dianggap Bolos' : 'Belum Scan Pulang'; @endphp
            <a href="/jadwal-piket" style="text-decoration:none;">
              <div style="background:var(--bg-3); border:{{ $sudahLewatJamTutup ? '1.5px solid #000000' : '1px solid var(--border-2)' }}; border-radius:var(--r-sm); padding:12px 14px; cursor:pointer; transition:all .15s;">
                <div style="font-size:10.5px; font-weight:800; text-transform:uppercase; color:var(--text-3); letter-spacing:0.5px;">{{ $labelPulang }}</div>
                <div style="font-size:22px; font-weight:900; font-family:var(--font-mono); color:#000000; margin-top:2px;">
                  {{ $siswaBelumPulangCount }} <span style="font-size:11.5px; font-weight:600; color:var(--text-3);">Siswa</span>
                </div>
                <div style="font-size:11px; color:{{ $sudahLewatJamTutup ? '#000000' : 'var(--text-3)' }}; font-weight:{{ $sudahLewatJamTutup ? '800' : '600' }}; margin-top:2px;">
                  {{ $sudahLewatJamTutup ? 'Lihat di Jadwal Piket →' : 'Sesi masih berlangsung' }}
                </div>
              </div>
            </a>

            <!-- Mini KPI 5: Sesi Smart Gate -->
            <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:12px 14px;">
              <div style="font-size:10.5px; font-weight:800; text-transform:uppercase; color:var(--text-3); letter-spacing:0.5px;">Sesi Smart Gate</div>
              <div style="margin-top:4px;">
                @if(isset($jadwalHarianSesi) && $jadwalHarianSesi && $jadwalHarianSesi->status_gerbang === 'buka')
                  <span class="badge" style="background:var(--bg-2); color:#000000; border:1px solid #000000; font-weight:800; font-size:11.5px; padding:3px 8px; border-radius:6px;">
                    SESI TERBUKA
                  </span>
                @else
                  <span class="badge" style="background:var(--bg-2); color:#000000; border:1px solid var(--border-2); font-weight:800; font-size:11.5px; padding:3px 8px; border-radius:6px;">
                    SESI DITUTUP
                  </span>
                @endif
              </div>
              <div style="font-size:11px; color:var(--text-3); margin-top:4px;">Gerbang Kiosk Depan</div>
            </div>
          </div>

          {{-- Quick Table Siswa Terlambat Hari Ini --}}
          @if(isset($piketSiswaTerlambatList) && $piketSiswaTerlambatList->count() > 0)
            <div style="border-top:1px solid var(--border); padding-top:14px;">
              <div style="font-weight:800; font-size:12.5px; color:var(--text); margin-bottom:8px; display:flex; justify-content:space-between; align-items:center;">
                <span><i class="bi bi-clock-history" style="color:#D97706;"></i> Siswa Terlambat Hari Ini (Perlu Verifikasi Piket):</span>
                <a href="/izin-siswa" style="font-size:11.5px; font-weight:700; color:var(--text-2); text-decoration:underline;">Lihat Semua Izin &amp; Dispen</a>
              </div>
              <div style="overflow-x:auto;">
                <table class="data-table" style="font-size:12px; margin:0; width:100%;">
                  <thead>
                    <tr style="background:var(--bg-3);">
                      <th>Nama Siswa</th>
                      <th>Kelas</th>
                      <th style="text-align:center;">Jam Masuk</th>
                      <th style="text-align:center;">Status</th>
                      <th style="text-align:right;">Tindakan</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($piketSiswaTerlambatList->take(5) as $terlambat)
                      @php $sw = $terlambat->siswa; @endphp
                      <tr>
                        <td>
                          <strong>{{ $sw->nama ?? '-' }}</strong>
                          <div style="font-size:10.5px; color:var(--text-3); font-family:var(--font-mono);">NISN: {{ $sw->nisn ?? '-' }}</div>
                        </td>
                        <td>{{ $sw->rombel->nama_rombel ?? ($sw->siswaRombels->first()?->rombel->nama_rombel ?? '-') }}</td>
                        <td style="text-align:center; font-family:var(--font-mono); font-weight:700;">
                          {{ $terlambat->jam_masuk ? substr($terlambat->jam_masuk, 0, 5) . ' WIB' : '-' }}
                        </td>
                        <td style="text-align:center;">
                          <span class="badge" style="background:rgba(245,158,11,0.12); color:#D97706; border:1px solid rgba(245,158,11,0.25); font-weight:800; font-size:10.5px; padding:2px 8px; border-radius:6px;">
                            TERLAMBAT
                          </span>
                        </td>
                        <td style="text-align:right;">
                          <a href="/izin-siswa?siswa_id={{ $sw->id }}" class="btn btn-sm btn-outline" style="font-size:11px; font-weight:800; padding:2px 8px;">
                            <i class="bi bi-file-earmark-plus"></i> Beri Izin/Dispen
                          </a>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          @endif
        </div>
      </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- WIDGET RINGKASAN KELAS BINAAN (KHUSUS WALI KELAS YANG SEDANG PIKET) --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    @if(isset($isWaliSedangPiket) && $isWaliSedangPiket && isset($waliRombel) && $waliRombel)
      <div class="panel" style="margin-bottom:24px; padding:0; overflow:hidden; border:1.5px solid #16A34A; border-radius:var(--r-md); background:var(--bg-2);">
        <div style="padding:12px 18px; border-bottom:1px solid rgba(22,163,74,0.2); background:rgba(22,163,74,0.06); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
          <div style="font-size:13.5px; font-weight:800; color:#15803D; display:flex; align-items:center; gap:8px;">
            <i class="bi bi-mortarboard-fill"></i>
            <span>📌 Ringkasan Kelas Binaan Anda: <strong>{{ $waliRombel->nama_rombel }}</strong></span>
          </div>
          <div style="display:flex; gap:8px;">
            <a href="/siswa" class="btn btn-sm" style="font-size:11.5px; font-weight:800; padding:4px 12px; text-decoration:none; background:#15803D; color:#fff; border-radius:6px; display:inline-flex; align-items:center; gap:5px;">
              <i class="bi bi-people-fill"></i> Data Siswa
            </a>
            <a href="/disiplin" class="btn btn-sm btn-outline" style="font-size:11.5px; font-weight:800; padding:4px 12px; text-decoration:none; display:inline-flex; align-items:center; gap:5px; border-color:#16A34A; color:#15803D;">
              <i class="bi bi-journal-text"></i> Buku Disiplin
            </a>
          </div>
        </div>
        <div style="padding:16px 18px;">
          <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(140px, 1fr)); gap:12px;">
            <div style="background:rgba(22,163,74,0.06); border:1px solid rgba(22,163,74,0.2); border-radius:var(--r-sm); padding:12px;">
              <div style="font-size:10.5px; font-weight:800; text-transform:uppercase; color:#15803D; letter-spacing:0.5px;">Total Siswa</div>
              <div style="font-size:22px; font-weight:900; font-family:var(--font-mono); color:#15803D; margin-top:2px;">{{ $waliTotalSiswa }} <span style="font-size:11px; font-weight:600; color:var(--text-3);">Siswa</span></div>
            </div>
            <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:12px;">
              <div style="font-size:10.5px; font-weight:800; text-transform:uppercase; color:var(--text-3); letter-spacing:0.5px;">Hadir Tepat</div>
              <div style="font-size:22px; font-weight:900; font-family:var(--font-mono); color:#000; margin-top:2px;">{{ $waliHadir }} <span style="font-size:11px; font-weight:600; color:var(--text-3);">Siswa</span></div>
            </div>
            <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:12px;">
              <div style="font-size:10.5px; font-weight:800; text-transform:uppercase; color:var(--text-3); letter-spacing:0.5px;">Terlambat</div>
              <div style="font-size:22px; font-weight:900; font-family:var(--font-mono); color:#D97706; margin-top:2px;">{{ $waliTerlambat }} <span style="font-size:11px; font-weight:600; color:var(--text-3);">Siswa</span></div>
            </div>
            <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:12px;">
              <div style="font-size:10.5px; font-weight:800; text-transform:uppercase; color:var(--text-3); letter-spacing:0.5px;">Izin / Sakit</div>
              <div style="font-size:22px; font-weight:900; font-family:var(--font-mono); color:#2563EB; margin-top:2px;">{{ $waliIzin }} <span style="font-size:11px; font-weight:600; color:var(--text-3);">Siswa</span></div>
            </div>
            <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:12px;">
              <div style="font-size:10.5px; font-weight:800; text-transform:uppercase; color:var(--text-3); letter-spacing:0.5px;">Alpha</div>
              <div style="font-size:22px; font-weight:900; font-family:var(--font-mono); color:#DC2626; margin-top:2px;">{{ $waliAlpha }} <span style="font-size:11px; font-weight:600; color:var(--text-3);">Siswa</span></div>
            </div>
            <div style="background:rgba(22,163,74,0.08); border:1px solid rgba(22,163,74,0.2); border-radius:var(--r-sm); padding:12px;">
              <div style="font-size:10.5px; font-weight:800; text-transform:uppercase; color:#15803D; letter-spacing:0.5px;">% Kehadiran</div>
              <div style="font-size:22px; font-weight:900; font-family:var(--font-mono); color:#15803D; margin-top:2px;">{{ $waliPersen }}%</div>
            </div>
          </div>
          @if(isset($waliKasusTahap1List) && $waliKasusTahap1List->count() > 0)
            <div style="margin-top:14px; padding-top:12px; border-top:1px solid rgba(22,163,74,0.2); font-size:12px; font-weight:700; color:#DC2626; display:flex; align-items:center; gap:6px;">
              <i class="bi bi-exclamation-triangle-fill"></i>
              {{ $waliKasusTahap1List->count() }} siswa kelas {{ $waliRombel->nama_rombel }} perlu tindak lanjut pembinaan.
              <a href="/disiplin" style="color:#DC2626; text-decoration:underline; font-weight:800;">Buka Buku Disiplin →</a>
            </div>
          @endif
        </div>
      </div>
    @endif



    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- 4. VISUALISASI GRAFIK MULTI-DIMENSI (ADMIN, KEPSEK, WAKASIS, BK, WALI) -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    @if($isAdmin || $isKepalaSekolah || $isWakasis || $isGuruBk || $isWaliKelas || $isWakaKurikulum)
      <div class="section-divider">
        <h2><i class="bi bi-pie-chart-fill" style="color:#000000;"></i> Grafik &amp; Analisis Visual Presensi</h2>
        <div class="section-divider-line"></div>
      </div>

      <!-- ROW 1: TREN 30 HARI & KOMPOSISI DONUT HARI INI -->
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 18px; margin-bottom: 20px;">
        <!-- Grafik 1: Tren Kehadiran 30 Hari (1 Bulan) -->
        <div class="panel" style="margin-bottom: 0; display:flex; flex-direction:column;">
          <div class="panel-title" style="margin-bottom: 14px; display:flex; justify-content:space-between; align-items:center;">
            <span style="font-size:13.5px; font-weight:800; display:flex; align-items:center; gap:8px; color:var(--text);">
              <i class="bi bi-graph-up-arrow" style="color:#000000;"></i> Tren Kehadiran Siswa (30 Hari / 1 Bulan)
            </span>
            <span class="badge" style="background:rgba(0,0,0,0.06); color:#000000; border:1px solid rgba(0,0,0,0.12); font-size:10.5px; padding:2px 8px; border-radius:12px; font-weight:800;">
              30 Hari Terakhir
            </span>
          </div>
          <div style="position:relative; flex:1; min-height:240px; width:100%;">
            <canvas id="trendChart"></canvas>
          </div>
        </div>

        <!-- Grafik 2: Komposisi Status Kehadiran Hari Ini (Donut) -->
        <div class="panel" style="margin-bottom: 0; display:flex; flex-direction:column;">
          <div class="panel-title" style="margin-bottom: 14px; display:flex; justify-content:space-between; align-items:center;">
            <span style="font-size:13.5px; font-weight:800; display:flex; align-items:center; gap:8px; color:var(--text);">
              <i class="bi bi-pie-chart-fill" style="color:#000000;"></i> Komposisi Status Siswa Hari Ini
            </span>
            <span class="badge" style="background:rgba(0,0,0,0.06); color:#000000; border:1px solid rgba(0,0,0,0.12); font-size:10.5px; padding:2px 8px; border-radius:12px; font-weight:800;">
              {{ $totalSiswaActive }} Siswa
            </span>
          </div>
          <div style="position:relative; flex:1; min-height:240px; width:100%; display:flex; align-items:center; justify-content:center;">
            <canvas id="donutSiswaChart"></canvas>
          </div>
        </div>
      </div>

      <!-- ROW 2: TREN KEHADIRAN GURU & KOMPOSISI GURU HARI INI (ADMIN & KEPSEK) -->
      @if($isAdmin || $isKepalaSekolah || $isWakaKurikulum)
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 18px; margin-bottom: 24px;">
          <!-- Grafik 3: Tren Kehadiran Guru & Pegawai (30 Hari / 1 Bulan) -->
          <div class="panel" style="margin-bottom: 0; display:flex; flex-direction:column;">
            <div class="panel-title" style="margin-bottom: 14px; display:flex; justify-content:space-between; align-items:center;">
              <span style="font-size:13.5px; font-weight:800; display:flex; align-items:center; gap:8px; color:var(--text);">
                <i class="bi bi-graph-up-arrow" style="color:#000000;"></i> Tren Kehadiran Guru &amp; Pegawai (30 Hari / 1 Bulan)
              </span>
              <span class="badge" style="background:rgba(0,0,0,0.06); color:#000000; border:1px solid rgba(0,0,0,0.12); font-size:10.5px; padding:2px 8px; border-radius:12px; font-weight:800;">
                30 Hari Terakhir
              </span>
            </div>
            <div style="position:relative; flex:1; min-height:240px; width:100%;">
              <canvas id="trendGuruChart"></canvas>
            </div>
          </div>

          <!-- Grafik 4: Komposisi Status Guru & Pegawai Hari Ini (Donut) -->
          <div class="panel" style="margin-bottom: 0; display:flex; flex-direction:column;">
            <div class="panel-title" style="margin-bottom: 14px; display:flex; justify-content:space-between; align-items:center;">
              <span style="font-size:13.5px; font-weight:800; display:flex; align-items:center; gap:8px; color:var(--text);">
                <i class="bi bi-pie-chart-fill" style="color:#000000;"></i> Komposisi Status Guru &amp; Pegawai Hari Ini
              </span>
              <span class="badge" style="background:rgba(0,0,0,0.06); color:#000000; border:1px solid rgba(0,0,0,0.12); font-size:10.5px; padding:2px 8px; border-radius:12px; font-weight:800;">
                {{ $totalGuruActive }} Guru
              </span>
            </div>
            <div style="position:relative; flex:1; min-height:240px; width:100%; display:flex; align-items:center; justify-content:center;">
              <canvas id="donutGuruChart"></canvas>
            </div>
          </div>
        </div>
      @endif
    @endif



  </main>
</div>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- MODAL DRILLDOWN DETAIL DATA KETIKA ANGKA DI-KLIK -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<div class="modal-backdrop" id="detailModal">
  <div class="modal-card" style="max-width:900px; width:94%; padding:0; overflow:hidden; border-radius:var(--r-md); border:1.5px solid var(--border-2); box-shadow:0 20px 60px rgba(0,0,0,0.3); background:var(--bg-2);">
    <div class="modal-header" style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border-2); padding:16px 20px; background:var(--surface);">
      <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
        <h3 id="modalTitle" style="font-size:15.5px; font-weight:800; color:var(--text); margin:0; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-people-fill" style="color:#000000;"></i> Detail Presensi Hari Ini
        </h3>
        <span class="badge" style="background:var(--bg-2); color:var(--text); border:1px solid var(--border-2); font-size:11px; font-weight:800; padding:3px 10px; border-radius:100px; display:inline-flex; align-items:center; gap:4px;">
          <i class="bi bi-eye-fill"></i> Read Only
        </span>
      </div>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeDetailModal()" style="font-size:12px; font-weight:700; padding:6px 14px; border-radius:var(--r-sm); display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
        <i class="bi bi-x-lg"></i> Tutup
      </button>
    </div>

    <div style="padding:20px;">
      <div style="position:relative; margin-bottom:16px;">
        <i class="bi bi-search" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--text-3); font-size:13px;"></i>
        <input type="text" id="modalSearch" onkeyup="filterModalTable()" class="form-control" placeholder="Cari nama siswa/guru, NIS, NIP, atau rombel..." style="width:100%; padding:10px 14px 10px 38px; font-size:13px; border-radius:var(--r-sm); border:1px solid var(--border-2); background:var(--bg-3); color:var(--text); box-sizing:border-box;">
      </div>

      <div style="max-height:450px; overflow-y:auto; border:1px solid var(--border-2); border-radius:var(--r-sm); background:var(--bg-2);">
        <table class="data-table" style="font-size:12.5px; width:100%; border-collapse:collapse; margin:0;">
          <thead>
            <tr style="background:var(--bg-3); border-bottom:1.5px solid var(--border-2);">
              <th style="width:48px; text-align:center; padding:12px 14px; font-size:11px; font-weight:800; letter-spacing:0.04em; color:var(--text-3); text-transform:uppercase;">#</th>
              <th style="text-align:left; min-width:180px; padding:12px 14px; font-size:11px; font-weight:800; letter-spacing:0.04em; color:var(--text-3); text-transform:uppercase;">Nama &amp; Identitas</th>
              <th style="text-align:left; min-width:120px; padding:12px 14px; font-size:11px; font-weight:800; letter-spacing:0.04em; color:var(--text-3); text-transform:uppercase;">Kelas / Jabatan</th>
              <th style="text-align:center; min-width:130px; padding:12px 14px; font-size:11px; font-weight:800; letter-spacing:0.04em; color:var(--text-3); text-transform:uppercase;">Status</th>
              <th style="text-align:center; width:95px; padding:12px 14px; font-size:11px; font-weight:800; letter-spacing:0.04em; color:var(--text-3); text-transform:uppercase;">Jam Masuk</th>
              <th style="text-align:center; width:95px; padding:12px 14px; font-size:11px; font-weight:800; letter-spacing:0.04em; color:var(--text-3); text-transform:uppercase;">Jam Pulang</th>
              <th style="text-align:left; min-width:160px; padding:12px 14px; font-size:11px; font-weight:800; letter-spacing:0.04em; color:var(--text-3); text-transform:uppercase;">Keterangan</th>
            </tr>
          </thead>
          <tbody id="modalTableBody">
            {{-- Populated by JS --}}
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
  // Data absensi hari ini untuk modal drilldown
  const rawAbsensiSiswa = @json($absensiSiswaHariIni ?? []);
  const rawAbsensiGuru  = @json($absensiGuruHariIni ?? []);
  const rawSemuaGuru    = @json($semuaGuruList ?? []);
  const rawSemuaSiswa   = @json($semuaSiswaList ?? []);
  let currentModalData  = [];

  function openDetailModal(type, filterStatus, title) {
    const modal = document.getElementById('detailModal');
    const modalTitle = document.getElementById('modalTitle');
    const searchInput = document.getElementById('modalSearch');

    if (!modal) return;

    if (searchInput) searchInput.value = '';

    let list = [];

    if (type === 'siswa') {
      const absenMap = {};
      rawAbsensiSiswa.forEach(a => {
        if (a && a.pemilik_id) {
          absenMap[a.pemilik_id] = a;
        }
      });

      if (filterStatus === 'semua') {
        // Tampilkan seluruh siswa dengan status masing-masing hari ini
        list = rawSemuaSiswa.map(s => {
          const a = absenMap[s.id];
          const rombels = s.siswa_rombels || s.siswaRombels || [];
          const rombelNama = (rombels.length > 0 && rombels[0].rombel) ? rombels[0].rombel.nama_rombel : '-';
          const isPkl = (s.status_pkl === 'aktif_pkl' || s.status_pkl === 'pkl');
          
          let st = 'alpha';
          if (a) {
            st = a.status;
          } else if (isPkl) {
            st = 'pkl';
          }

          return {
            type: 'siswa',
            siswaId: s.id,
            idNumber: s.nis || '-',
            nama: s.nama || '-',
            sub: rombelNama,
            jamMasuk: a ? (a.jam_masuk ? a.jam_masuk.substring(0, 5) : '-') : (isPkl ? 'PKL' : '-'),
            jamPulang: a ? (a.jam_pulang ? a.jam_pulang.substring(0, 5) : '-') : (isPkl ? 'PKL' : '-'),
            status: st,
            noHp: s.no_hp_ortu || s.nomor_hp_ortu || s.no_hp_siswa || ''
          };
        });
      } else if (filterStatus === 'pkl') {
        list = rawSemuaSiswa.filter(s => s.status_pkl === 'aktif_pkl' || s.status_pkl === 'pkl').map(s => {
          const rombels = s.siswa_rombels || s.siswaRombels || [];
          const rombelNama = (rombels.length > 0 && rombels[0].rombel) ? rombels[0].rombel.nama_rombel : '-';
          return {
            type: 'siswa',
            siswaId: s.id,
            idNumber: s.nis || '-',
            nama: s.nama || '-',
            sub: rombelNama,
            jamMasuk: 'PKL',
            jamPulang: 'PKL',
            status: 'pkl',
            noHp: s.no_hp_ortu || s.nomor_hp_ortu || s.no_hp_siswa || ''
          };
        });
      } else if (filterStatus === 'izin') {
        list = rawAbsensiSiswa.filter(item => ['sakit', 'izin', 'dispen'].includes(item.status)).map(item => {
          const s = item.siswa;
          const rombel = (item.siswa_rombel && item.siswa_rombel.rombel) ? item.siswa_rombel.rombel.nama_rombel : '-';
          return {
            type: 'siswa',
            siswaId: s ? s.id : item.pemilik_id,
            idNumber: s ? s.nis : '-',
            nama: s ? s.nama : 'Siswa',
            sub: rombel,
            jamMasuk: item.jam_masuk ? item.jam_masuk.substring(0, 5) : '-',
            jamPulang: item.jam_pulang ? item.jam_pulang.substring(0, 5) : '-',
            status: item.status,
            noHp: s ? (s.no_hp_ortu || s.nomor_hp_ortu || s.no_hp_siswa || '') : ''
          };
        });
      } else if (filterStatus === 'alpha') {
        list = rawSemuaSiswa.filter(s => {
          const isPkl = (s.status_pkl === 'aktif_pkl' || s.status_pkl === 'pkl');
          if (isPkl) return false;
          const a = absenMap[s.id];
          return !a || a.status === 'alpha';
        }).map(s => {
          const a = absenMap[s.id];
          const rombels = s.siswa_rombels || s.siswaRombels || [];
          const rombelNama = (rombels.length > 0 && rombels[0].rombel) ? rombels[0].rombel.nama_rombel : '-';
          return {
            type: 'siswa',
            siswaId: s.id,
            idNumber: s.nis || '-',
            nama: s.nama || '-',
            sub: rombelNama,
            jamMasuk: a ? (a.jam_masuk ? a.jam_masuk.substring(0, 5) : '-') : '-',
            jamPulang: a ? (a.jam_pulang ? a.jam_pulang.substring(0, 5) : '-') : '-',
            status: 'alpha',
            noHp: s.no_hp_ortu || s.nomor_hp_ortu || s.no_hp_siswa || ''
          };
        });
      } else {
        list = rawAbsensiSiswa.filter(item => item.status === filterStatus).map(item => {
          const s = item.siswa;
          const rombel = (item.siswa_rombel && item.siswa_rombel.rombel) ? item.siswa_rombel.rombel.nama_rombel : '-';
          return {
            type: 'siswa',
            siswaId: s ? s.id : item.pemilik_id,
            idNumber: s ? s.nis : '-',
            nama: s ? s.nama : 'Siswa',
            sub: rombel,
            jamMasuk: item.jam_masuk ? item.jam_masuk.substring(0, 5) : '-',
            jamPulang: item.jam_pulang ? item.jam_pulang.substring(0, 5) : '-',
            status: item.status,
            noHp: s ? (s.no_hp_ortu || s.nomor_hp_ortu || s.no_hp_siswa || '') : ''
          };
        });
      }
    } else {
      // Data Guru
      const guruAbsenMap = {};
      rawAbsensiGuru.forEach(a => {
        if (a && a.pemilik_id) {
          guruAbsenMap[a.pemilik_id] = a;
        }
      });

      if (filterStatus === 'semua') {
        list = rawSemuaGuru.map(g => {
          const a = guruAbsenMap[g.id];
          return {
            type: 'guru',
            guruId: g.id,
            idNumber: g.nip || '-',
            nama: g.nama || '-',
            sub: g.jabatan || 'Guru',
            jamMasuk: a ? (a.jam_masuk ? a.jam_masuk.substring(0, 5) : '-') : '-',
            jamPulang: a ? (a.jam_pulang ? a.jam_pulang.substring(0, 5) : '-') : '-',
            status: a ? a.status : 'belum_hadir',
            noHp: g.no_hp || ''
          };
        });
      } else if (filterStatus === 'belum') {
        list = rawSemuaGuru.filter(g => !guruAbsenMap[g.id]).map(g => ({
          type: 'guru',
          guruId: g.id,
          idNumber: g.nip || '-',
          nama: g.nama || '-',
          sub: g.jabatan || 'Guru',
          jamMasuk: '-',
          jamPulang: '-',
          status: 'belum_hadir',
          noHp: g.no_hp || ''
        }));
      } else if (filterStatus === 'izin') {
        list = rawAbsensiGuru.filter(item => ['sakit', 'izin', 'cuti', 'dispen'].includes(item.status)).map(item => ({
          type: 'guru',
          guruId: item.guru ? item.guru.id : item.pemilik_id,
          idNumber: item.guru ? (item.guru.nip || '-') : '-',
          nama: item.guru ? item.guru.nama : 'Guru',
          sub: item.guru ? item.guru.jabatan : 'Guru',
          jamMasuk: item.jam_masuk ? item.jam_masuk.substring(0, 5) : '-',
          jamPulang: item.jam_pulang ? item.jam_pulang.substring(0, 5) : '-',
          status: item.status,
          noHp: item.guru ? (item.guru.no_hp || '') : ''
        }));
      } else {
        list = rawAbsensiGuru.filter(item => item.status === filterStatus).map(item => ({
          type: 'guru',
          guruId: item.guru ? item.guru.id : item.pemilik_id,
          idNumber: item.guru ? (item.guru.nip || '-') : '-',
          nama: item.guru ? item.guru.nama : 'Guru',
          sub: item.guru ? item.guru.jabatan : 'Guru',
          jamMasuk: item.jam_masuk ? item.jam_masuk.substring(0, 5) : '-',
          jamPulang: item.jam_pulang ? item.jam_pulang.substring(0, 5) : '-',
          status: item.status,
          noHp: item.guru ? (item.guru.no_hp || '') : ''
        }));
      }
    }

    currentModalData = list;
    if (modalTitle) {
      modalTitle.innerHTML = `<i class="bi ${type === 'siswa' ? 'bi-people-fill' : 'bi-person-badge-fill'}" style="color:#000000; font-size:17px;"></i> <span>${title}</span> <span class="badge" style="background:#000000; color:#FFFFFF; font-size:11px; font-weight:800; padding:2px 8px; border-radius:6px; margin-left:6px;">${list.length} Data</span>`;
    }

    renderModalRows(list);
    modal.style.display = 'flex';
    modal.classList.add('active');
  }

  function closeDetailModal() {
    const modal = document.getElementById('detailModal');
    if (modal) {
      modal.classList.remove('active');
      modal.style.display = 'none';
    }
  }

  // Event listener klik luar backdrop untuk menutup modal
  document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('detailModal');
    if (modal) {
      modal.addEventListener('click', function(e) {
        if (e.target === modal) {
          closeDetailModal();
        }
      });
    }
  });

  function renderModalRows(list) {
    const tbody = document.getElementById('modalTableBody');
    if (!tbody) return;

    if (list.length === 0) {
      tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; color:var(--text-3); padding:36px;"><i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px; opacity:.4;"></i>Belum ada data presensi yang sesuai pada kategori ini hari ini.</td></tr>`;
      return;
    }

    tbody.innerHTML = list.map((item, idx) => {
      let statusBadge = '';
      const st = item.status;
      if (st === 'hadir') {
        statusBadge = `<span class="badge" style="background:rgba(34,197,94,0.12); color:#16A34A; border:1px solid rgba(34,197,94,0.25); font-weight:800; font-size:11px; padding:4px 10px; border-radius:6px; white-space:nowrap; display:inline-block;">HADIR</span>`;
      } else if (st === 'terlambat') {
        statusBadge = `<span class="badge" style="background:rgba(245,158,11,0.12); color:#D97706; border:1px solid rgba(245,158,11,0.25); font-weight:800; font-size:11px; padding:4px 10px; border-radius:6px; white-space:nowrap; display:inline-block;">TERLAMBAT</span>`;
      } else if (st === 'izin') {
        statusBadge = `<span class="badge" style="background:rgba(59,130,246,0.12); color:#2563EB; border:1px solid rgba(59,130,246,0.25); font-weight:800; font-size:11px; padding:4px 10px; border-radius:6px; white-space:nowrap; display:inline-block;">IZIN</span>`;
      } else if (st === 'sakit') {
        statusBadge = `<span class="badge" style="background:rgba(168,85,247,0.12); color:#9333EA; border:1px solid rgba(168,85,247,0.25); font-weight:800; font-size:11px; padding:4px 10px; border-radius:6px; white-space:nowrap; display:inline-block;">SAKIT</span>`;
      } else if (st === 'cuti' || st === 'dispen') {
        statusBadge = `<span class="badge" style="background:rgba(59,130,246,0.12); color:#2563EB; border:1px solid rgba(59,130,246,0.25); font-weight:800; font-size:11px; padding:4px 10px; border-radius:6px; white-space:nowrap; display:inline-block;">${st.toUpperCase()}</span>`;
      } else if (st === 'pkl') {
        statusBadge = `<span class="badge" style="background:rgba(59,130,246,0.15); color:#1D4ED8; border:1px solid rgba(59,130,246,0.3); font-weight:800; font-size:11px; padding:4px 10px; border-radius:6px; white-space:nowrap; display:inline-block;">MAGANG / PKL</span>`;
      } else {
        statusBadge = `<span class="badge" style="background:rgba(239,68,68,0.12); color:#DC2626; border:1px solid rgba(239,68,68,0.25); font-weight:800; font-size:11px; padding:4px 10px; border-radius:6px; white-space:nowrap; display:inline-block;">BELUM SCAN</span>`;
      }

      let keteranganText = '-';
      if (st === 'hadir') {
        keteranganText = (item.jamMasuk && item.jamMasuk !== '-') ? 'Tepat Waktu (Smart Gate)' : 'Hadir Terekam';
      } else if (st === 'terlambat') {
        keteranganText = 'Terlambat Masuk Gerbang';
      } else if (st === 'izin') {
        keteranganText = 'Izin Resmi Terdata';
      } else if (st === 'sakit') {
        keteranganText = 'Surat Keterangan Sakit';
      } else if (st === 'pkl') {
        keteranganText = 'Praktik Kerja Lapangan';
      } else if (st === 'dispen' || st === 'cuti') {
        keteranganText = 'Dispensasi / Cuti Sah';
      } else {
        keteranganText = 'Belum Ada Catatan Scan';
      }

      return `
        <tr style="border-bottom:1px solid var(--border); transition:background 0.15s ease;">
          <td style="text-align:center; color:var(--text-3); font-family:var(--font-mono); font-size:12px; padding:12px 14px;">${idx + 1}</td>
          <td style="padding:12px 14px;">
            <div style="font-weight:800; color:var(--text); font-size:13px; line-height:1.35; white-space:nowrap;">${item.nama}</div>
            <div style="font-family:var(--font-mono); font-size:11px; color:var(--text-3); margin-top:2px;">NIS: ${item.idNumber}</div>
          </td>
          <td style="font-size:12.5px; font-weight:700; color:var(--text-2); padding:12px 14px;">${item.sub}</td>
          <td style="text-align:center; padding:12px 14px;">${statusBadge}</td>
          <td style="text-align:center; font-family:var(--font-mono); font-size:12.5px; font-weight:700; color:var(--text); padding:12px 14px;">${item.jamMasuk}</td>
          <td style="text-align:center; font-family:var(--font-mono); font-size:12.5px; font-weight:700; color:var(--text); padding:12px 14px;">${item.jamPulang}</td>
          <td style="font-size:12px; font-weight:600; color:var(--text-3); padding:12px 14px;">${keteranganText}</td>
        </tr>
      `;
    }).join('');
  }

  function filterModalTable() {
    const q = (document.getElementById('modalSearch')?.value || '').toLowerCase();
    const filtered = currentModalData.filter(item => {
      return (item.nama && item.nama.toLowerCase().includes(q)) ||
             (item.idNumber && item.idNumber.toLowerCase().includes(q)) ||
             (item.sub && item.sub.toLowerCase().includes(q));
    });
    renderModalRows(filtered);
  }

  // ═══════════════════════════════════════════════════════════════════
  // ═══════════════════════════════════════════════════════════════════
  // INISIALISASI CHART.JS DENGAN DUKUNGAN ADAPTIF LIGHT & DARK MODE
  // ═══════════════════════════════════════════════════════════════════
  document.addEventListener("DOMContentLoaded", function() {
    function getChartTheme() {
      const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
      return {
        isDark: isDark,
        fontFamily: "'Plus Jakarta Sans', system-ui, -apple-system, sans-serif",
        lineColor: isDark ? '#38BDF8' : '#0F172A',
        lineBg: isDark ? 'rgba(56, 189, 248, 0.16)' : 'rgba(15, 23, 42, 0.08)',
        pointBg: isDark ? '#38BDF8' : '#0F172A',
        gridColor: isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)',
        tickColor: isDark ? '#94A3B8' : '#64748B',
        legendColor: isDark ? '#F8FAFC' : '#0F172A',
      };
    }

    let chartTrendSiswa = null;
    let chartDonutSiswa = null;
    let chartTrendGuru  = null;
    let chartDonutGuru  = null;

    function initOrUpdateCharts() {
      const theme = getChartTheme();
      Chart.defaults.font.family = theme.fontFamily;
      Chart.defaults.color = theme.tickColor;

      // 1. Grafik Tren Siswa 30 Hari (Line Chart)
      const ctxTrend = document.getElementById('trendChart');
      if (ctxTrend) {
        const labels = @json($chartLabels);
        const dataHadir = @json($chartPersentase);

        if (chartTrendSiswa) {
          chartTrendSiswa.data.datasets[0].borderColor = theme.lineColor;
          chartTrendSiswa.data.datasets[0].backgroundColor = theme.lineBg;
          chartTrendSiswa.data.datasets[0].pointBackgroundColor = theme.pointBg;
          chartTrendSiswa.options.scales.x.ticks.color = theme.tickColor;
          chartTrendSiswa.options.scales.x.grid.color = theme.gridColor;
          chartTrendSiswa.options.scales.y.ticks.color = theme.tickColor;
          chartTrendSiswa.options.scales.y.grid.color = theme.gridColor;
          chartTrendSiswa.update();
        } else {
          chartTrendSiswa = new Chart(ctxTrend, {
            type: 'line',
            data: {
              labels: labels,
              datasets: [{
                label: '% Kehadiran Siswa',
                data: dataHadir,
                borderColor: theme.lineColor,
                backgroundColor: theme.lineBg,
                fill: true,
                tension: 0.35,
                borderWidth: 2.5,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: theme.pointBg
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: { display: false },
                tooltip: {
                  callbacks: {
                    label: function(ctx) { return ' Kehadiran: ' + ctx.raw + '%'; }
                  }
                }
              },
              scales: {
                y: {
                  min: 0,
                  max: 100,
                  ticks: { callback: v => v + '%', color: theme.tickColor },
                  grid: { color: theme.gridColor }
                },
                x: {
                  ticks: { maxRotation: 45, font: { size: 10 }, color: theme.tickColor },
                  grid: { color: theme.gridColor }
                }
              }
            }
          });
        }
      }

      // 2. Grafik Donut Status Siswa Hari Ini
      const ctxDonutSiswa = document.getElementById('donutSiswaChart');
      if (ctxDonutSiswa) {
        if (chartDonutSiswa) {
          chartDonutSiswa.options.plugins.legend.labels.color = theme.legendColor;
          chartDonutSiswa.update();
        } else {
          chartDonutSiswa = new Chart(ctxDonutSiswa, {
            type: 'doughnut',
            data: {
              labels: ['Hadir Tepat', 'Terlambat', 'Izin / Sakit', 'Alpha / Belum Tap'],
              datasets: [{
                data: [
                  {{ $siswaHadir }},
                  {{ $siswaTerlambat }},
                  {{ $siswaIzin }},
                  {{ max(0, $totalSiswaActive - $siswaHadir - $siswaTerlambat - $siswaIzin) }}
                ],
                backgroundColor: ['#16A34A', '#D97706', '#2563EB', '#DC2626'],
                borderWidth: 0,
                borderColor: 'transparent'
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 }, color: theme.legendColor } }
              },
              cutout: '68%'
            }
          });
        }
      }

      // 3. Grafik Tren Guru 30 Hari (Line Chart)
      const ctxTrendGuru = document.getElementById('trendGuruChart');
      if (ctxTrendGuru) {
        const labelsG = @json($chartLabels);
        const dataHadirG = @json($chartGuruPersentase);

        if (chartTrendGuru) {
          chartTrendGuru.data.datasets[0].borderColor = theme.lineColor;
          chartTrendGuru.data.datasets[0].backgroundColor = theme.lineBg;
          chartTrendGuru.data.datasets[0].pointBackgroundColor = theme.pointBg;
          chartTrendGuru.options.scales.x.ticks.color = theme.tickColor;
          chartTrendGuru.options.scales.x.grid.color = theme.gridColor;
          chartTrendGuru.options.scales.y.ticks.color = theme.tickColor;
          chartTrendGuru.options.scales.y.grid.color = theme.gridColor;
          chartTrendGuru.update();
        } else {
          chartTrendGuru = new Chart(ctxTrendGuru, {
            type: 'line',
            data: {
              labels: labelsG,
              datasets: [{
                label: '% Kehadiran Guru',
                data: dataHadirG,
                borderColor: theme.lineColor,
                backgroundColor: theme.lineBg,
                fill: true,
                tension: 0.35,
                borderWidth: 2.5,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: theme.pointBg
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: { display: false },
                tooltip: {
                  callbacks: {
                    label: function(ctx) { return ' Kehadiran: ' + ctx.raw + '%'; }
                  }
                }
              },
              scales: {
                y: {
                  min: 0,
                  max: 100,
                  ticks: { callback: v => v + '%', color: theme.tickColor },
                  grid: { color: theme.gridColor }
                },
                x: {
                  ticks: { maxRotation: 45, font: { size: 10 }, color: theme.tickColor },
                  grid: { color: theme.gridColor }
                }
              }
            }
          });
        }
      }

      // 4. Grafik Donut Status Guru Hari Ini
      const ctxDonutGuru = document.getElementById('donutGuruChart');
      if (ctxDonutGuru) {
        if (chartDonutGuru) {
          chartDonutGuru.options.plugins.legend.labels.color = theme.legendColor;
          chartDonutGuru.update();
        } else {
          chartDonutGuru = new Chart(ctxDonutGuru, {
            type: 'doughnut',
            data: {
              labels: ['Hadir Tepat', 'Terlambat', 'Izin / Dinas', 'Belum Hadir'],
              datasets: [{
                data: [
                  {{ $guruHadir }},
                  {{ $guruTerlambat }},
                  {{ $guruIzin }},
                  {{ $guruBelumHadir }}
                ],
                backgroundColor: ['#16A34A', '#D97706', '#CA8A04', '#DC2626'],
                borderWidth: 0,
                borderColor: 'transparent'
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 }, color: theme.legendColor } }
              },
              cutout: '68%'
            }
          });
        }
      }
    }

    // Inisialisasi awal
    initOrUpdateCharts();

    // Observasi perubahan mode tema secara dinamis
    const themeObserver = new MutationObserver(function(mutations) {
      mutations.forEach(function(m) {
        if (m.attributeName === 'data-theme') {
          initOrUpdateCharts();
        }
      });
    });
    themeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
  });
</script>

</body>
</html>
