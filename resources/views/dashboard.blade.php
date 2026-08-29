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
      border-color: var(--gold);
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
      border-color: var(--gold);
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

      $roleTitle = match(true) {
        $isKepalaSekolah => 'EXECUTIVE KEPALA SEKOLAH',
        $isWakasis       => 'WAKA KESISWAAN MONITORING',
        $isWakaKurikulum => 'WAKA KURIKULUM MONITORING',
        $isGuruBk        => 'GURU BIMBINGAN & KONSELING (BK)',
        $isWaliKelas     => 'WALI KELAS MONITORING',
        $isStafTu        => 'STAF TATA USAHA (TU)',
        $isGuruPiket     => 'GURU PIKET OPERATIONAL',
        $isAdmin         => 'ADMINISTRATOR MONITORING',
        default          => 'PORTAL GURU & PEGAWAI',
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
        <div style="display:inline-flex; align-items:center; gap:6px; background:rgba(0,0,0,0.06); border:1px solid rgba(0,0,0,0.12); padding:3px 10px; border-radius:20px; font-size:11px; font-weight:800; color:#000000; margin-bottom:8px;">
          <i class="bi {{ $roleIcon }}" style="color:#000000;"></i> {{ $roleTitle }}
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
    <!-- VISUALISASI GRAFIK MULTI-DIMENSI (ADMIN, KEPSEK, WAKASIS, BK, WALI) -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    @if($isAdmin || $isKepalaSekolah || $isWakasis || $isGuruBk || $isWaliKelas)
      <div class="section-divider">
        <h2><i class="bi bi-pie-chart-fill" style="color:#000000;"></i> Grafik &amp; Analisis Visual Presensi</h2>
        <span style="font-family:var(--font-mono); font-size:12px; color:var(--text-3); font-weight:700;">
          (Visualisasi Real-Time Siswa &amp; Kedisiplinan)
        </span>
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
      @if($isAdmin || $isKepalaSekolah)
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
  <div class="modal-card" style="max-width:850px; width:92%;">
    <div class="modal-header" style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); padding-bottom:14px; margin-bottom:16px;">
      <h3 id="modalTitle" style="font-size:16px; font-weight:800; color:var(--text); margin:0; display:flex; align-items:center;">
        <i class="bi bi-people-fill" style="color:var(--gold); margin-right:8px;"></i> Detail Presensi Hari Ini
      </h3>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeDetailModal()" style="font-size:12px; padding:4px 10px;">
        <i class="bi bi-x-lg"></i> Tutup
      </button>
    </div>

    <div style="margin-bottom:14px;">
      <input type="text" id="modalSearch" onkeyup="filterModalTable()" class="form-control" placeholder="Cari nama, NIS, NIP, atau rombel..." style="font-size:12.5px; padding:8px 14px;">
    </div>

    <div style="max-height:420px; overflow-y:auto;">
      <table class="data-table" style="font-size:12.5px;">
        <thead>
          <tr style="background:var(--bg-3);">
            <th style="width:36px; text-align:center;">#</th>
            <th>Nama &amp; Identitas</th>
            <th>Kelas / Jabatan</th>
            <th style="text-align:center;">Status</th>
            <th style="text-align:center;">Jam Masuk</th>
            <th style="text-align:center;">Jam Pulang</th>
            <th style="text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody id="modalTableBody">
          {{-- Populated by JS --}}
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  // Data absensi hari ini untuk modal drilldown
  const rawAbsensiSiswa = @json($absensiSiswaHariIni);
  const rawAbsensiGuru  = @json($absensiGuruHariIni);
  const rawSemuaGuru    = @json($semuaGuruList);
  const rawSemuaSiswa   = @json($semuaSiswaList);

  function openDetailModal(type, filterStatus, title) {
    const modal = document.getElementById('detailModal');
    const modalTitle = document.getElementById('modalTitle');
    const searchInput = document.getElementById('modalSearch');

    searchInput.value = '';
    modalTitle.innerHTML = `<i class="bi ${type === 'siswa' ? 'bi-people-fill' : 'bi-person-badge-fill'}" style="color:#000000; margin-right:8px;"></i> ${title}`;

    let list = [];

    if (type === 'siswa') {
      if (filterStatus === 'semua') {
        list = rawAbsensiSiswa.map(item => ({
          type: 'siswa',
          siswaId: item.siswa ? item.siswa.id : null,
          idNumber: item.siswa ? item.siswa.nis : '-',
          nama: item.siswa ? item.siswa.nama : '-',
          sub: item.siswa_rombel && item.siswa_rombel.rombel ? item.siswa_rombel.rombel.nama_rombel : '-',
          jamMasuk: item.jam_masuk || '-',
          jamPulang: item.jam_pulang || '-',
          status: item.status
        }));
      } else if (filterStatus === 'pkl') {
        list = rawSemuaSiswa.filter(s => s.status_pkl === 'aktif_pkl' || s.status_pkl === 'pkl' || s.is_pkl).map(s => {
          const rombelNama = (s.siswa_rombels && s.siswa_rombels.length > 0 && s.siswa_rombels[0].rombel) ? s.siswa_rombels[0].rombel.nama_rombel : '-';
          return {
            type: 'siswa',
            siswaId: s.id,
            idNumber: s.nis || '-',
            nama: s.nama || '-',
            sub: rombelNama,
            jamMasuk: 'PKL',
            jamPulang: 'PKL',
            status: 'pkl'
          };
        });
      } else if (filterStatus === 'izin') {
        list = rawAbsensiSiswa.filter(item => item.status === 'sakit' || item.status === 'izin').map(item => ({
          type: 'siswa',
          siswaId: item.siswa ? item.siswa.id : null,
          idNumber: item.siswa ? item.siswa.nis : '-',
          nama: item.siswa ? item.siswa.nama : '-',
          sub: item.siswa_rombel && item.siswa_rombel.rombel ? item.siswa_rombel.rombel.nama_rombel : '-',
          jamMasuk: item.jam_masuk || '-',
          jamPulang: item.jam_pulang || '-',
          status: item.status
        }));
      } else if (filterStatus === 'alpha') {
        const absenMap = {};
        rawAbsensiSiswa.forEach(a => { absenMap[a.pemilik_id] = a; });

        list = rawSemuaSiswa.filter(s => !absenMap[s.id] || absenMap[s.id].status === 'alpha').map(s => {
          const a = absenMap[s.id];
          const rombelNama = (s.siswa_rombels && s.siswa_rombels.length > 0 && s.siswa_rombels[0].rombel) ? s.siswa_rombels[0].rombel.nama_rombel : '-';
          return {
            type: 'siswa',
            siswaId: s.id,
            idNumber: s.nis || '-',
            nama: s.nama || '-',
            sub: rombelNama,
            jamMasuk: a ? (a.jam_masuk || '-') : '-',
            jamPulang: a ? (a.jam_pulang || '-') : '-',
            status: 'alpha'
          };
        });
      } else {
        list = rawAbsensiSiswa.filter(item => item.status === filterStatus).map(item => ({
          type: 'siswa',
          siswaId: item.siswa ? item.siswa.id : null,
          idNumber: item.siswa ? item.siswa.nis : '-',
          nama: item.siswa ? item.siswa.nama : '-',
          sub: item.siswa_rombel && item.siswa_rombel.rombel ? item.siswa_rombel.rombel.nama_rombel : '-',
          jamMasuk: item.jam_masuk || '-',
          jamPulang: item.jam_pulang || '-',
          status: item.status
        }));
      }
    } else {
      const scannedGuruIds = new Set(rawAbsensiGuru.map(a => a.pemilik_id));

      if (filterStatus === 'semua') {
        list = rawAbsensiGuru.map(item => ({
          type: 'guru',
          idNumber: item.guru ? (item.guru.nip || '-') : '-',
          nama: item.guru ? item.guru.nama : '-',
          sub: item.guru ? item.guru.jabatan : '-',
          jamMasuk: item.jam_masuk || '-',
          jamPulang: item.jam_pulang || '-',
          status: item.status
        }));
      } else if (filterStatus === 'belum') {
        list = rawSemuaGuru.filter(g => !scannedGuruIds.has(g.id)).map(g => ({
          type: 'guru',
          idNumber: g.nip || '-',
          nama: g.nama || '-',
          sub: g.jabatan || 'Guru',
          jamMasuk: '-',
          jamPulang: '-',
          status: 'belum_hadir'
        }));
      } else if (filterStatus === 'izin') {
        list = rawAbsensiGuru.filter(item => item.status === 'sakit' || item.status === 'izin').map(item => ({
          type: 'guru',
          idNumber: item.guru ? (item.guru.nip || '-') : '-',
          nama: item.guru ? item.guru.nama : '-',
          sub: item.guru ? item.guru.jabatan : '-',
          jamMasuk: item.jam_masuk || '-',
          jamPulang: item.jam_pulang || '-',
          status: item.status
        }));
      } else {
        list = rawAbsensiGuru.filter(item => item.status === filterStatus).map(item => ({
          type: 'guru',
          idNumber: item.guru ? (item.guru.nip || '-') : '-',
          nama: item.guru ? item.guru.nama : '-',
          sub: item.guru ? item.guru.jabatan : '-',
          jamMasuk: item.jam_masuk || '-',
          jamPulang: item.jam_pulang || '-',
          status: item.status
        }));
      }
    }

    currentModalData = list;
    renderModalRows(list);
    modal.classList.add('active');
  }

  function closeDetailModal() {
    document.getElementById('detailModal').classList.remove('active');
  }

  function renderModalRows(list) {
    const tbody = document.getElementById('modalTableBody');

    if (list.length === 0) {
      tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; color:var(--text-3); padding:32px;"><i class="bi bi-inbox" style="font-size:26px; display:block; margin-bottom:6px; opacity:.5;"></i>Tidak ada data yang sesuai.</td></tr>`;
      return;
    }

    tbody.innerHTML = list.map((item, idx) => {
      let statusBadge = '';
      const st = item.status;
      if (st === 'hadir') {
        statusBadge = `<span class="badge" style="background:rgba(34,197,94,0.12); color:#16A34A; border:1px solid rgba(34,197,94,0.25); font-weight:800; font-size:10.5px; padding:3px 8px;">HADIR</span>`;
      } else if (st === 'terlambat') {
        statusBadge = `<span class="badge" style="background:rgba(245,158,11,0.12); color:#D97706; border:1px solid rgba(245,158,11,0.25); font-weight:800; font-size:10.5px; padding:3px 8px;">TERLAMBAT</span>`;
      } else if (st === 'izin') {
        statusBadge = `<span class="badge" style="background:rgba(59,130,246,0.12); color:#2563EB; border:1px solid rgba(59,130,246,0.25); font-weight:800; font-size:10.5px; padding:3px 8px;">IZIN</span>`;
      } else if (st === 'sakit') {
        statusBadge = `<span class="badge" style="background:rgba(168,85,247,0.12); color:#9333EA; border:1px solid rgba(168,85,247,0.25); font-weight:800; font-size:10.5px; padding:3px 8px;">SAKIT</span>`;
      } else if (st === 'pkl') {
        statusBadge = `<span class="badge" style="background:rgba(59,130,246,0.15); color:#1D4ED8; border:1px solid rgba(59,130,246,0.3); font-weight:800; font-size:10.5px; padding:3px 8px;">MAGANG / PKL</span>`;
      } else if (st === 'belum_hadir') {
        statusBadge = `<span class="badge" style="background:var(--bg-3); color:var(--text-3); border:1px solid var(--border); font-weight:800; font-size:10.5px; padding:3px 8px;">BELUM TAP</span>`;
      } else {
        statusBadge = `<span class="badge" style="background:rgba(239,68,68,0.12); color:#DC2626; border:1px solid rgba(239,68,68,0.25); font-weight:800; font-size:10.5px; padding:3px 8px;">ALPHA</span>`;
      }

      let actionHtml = '-';
      if (item.type === 'siswa' && item.idNumber && item.idNumber !== '-') {
        actionHtml = `
          <div style="display:inline-flex; gap:4px; align-items:center;">
            <a href="/presensi-siswa/${item.idNumber}" target="_blank" class="btn btn-sm btn-outline" style="font-size:11px; padding:3px 8px;" title="Buka Portal Rekap Presensi">
              <i class="bi bi-person-lines-fill"></i> Portal
            </a>
            ${item.siswaId ? `
              <a href="/surat/cetak?siswa_id=${item.siswaId}&kategori=panggilan_ortu" target="_blank" class="btn btn-sm btn-outline" style="font-size:11px; padding:3px 8px; color:#000000; border-color:#000000;" title="Cetak Lembar Surat A4">
                <i class="bi bi-printer-fill" style="color:#000000;"></i> Surat
              </a>
            ` : ''}
          </div>
        `;
      }

      return `
        <tr style="border-bottom:1px solid var(--border);">
          <td style="text-align:center; color:var(--text-3); font-family:var(--font-mono); font-size:12px; padding:12px 14px;">${idx + 1}</td>
          <td style="padding:12px 14px;">
            <div style="font-weight:700; color:var(--text); font-size:13px; line-height:1.35; white-space:nowrap;">${item.nama}</div>
            <div style="font-family:var(--font-mono); font-size:11.5px; color:var(--text-3); margin-top:2px;">${item.idNumber}</div>
          </td>
          <td style="font-size:12px; font-weight:600; color:var(--text-2); padding:12px 14px;">${item.sub}</td>
          <td style="text-align:center; padding:12px 14px;">${statusBadge}</td>
          <td style="text-align:center; font-family:var(--font-mono); font-size:12px; padding:12px 14px;">${item.jamMasuk}</td>
          <td style="text-align:center; font-family:var(--font-mono); font-size:12px; padding:12px 14px;">${item.jamPulang}</td>
          <td style="text-align:right; padding:12px 14px;">${actionHtml}</td>
        </tr>
      `;
    }).join('');
  }

  function filterModalTable() {
    const q = document.getElementById('modalSearch').value.toLowerCase();
    const filtered = currentModalData.filter(item => {
      return (item.nama && item.nama.toLowerCase().includes(q)) ||
             (item.idNumber && item.idNumber.toLowerCase().includes(q)) ||
             (item.sub && item.sub.toLowerCase().includes(q));
    });
    renderModalRows(filtered);
  }

  // ═══════════════════════════════════════════════════════════════════
  // INISIALISASI CHART.JS DENGAN DESAIN MODERN SUITE
  // ═══════════════════════════════════════════════════════════════════
  document.addEventListener("DOMContentLoaded", function() {
    Chart.defaults.font.family = "Outfit, system-ui, -apple-system, sans-serif";

    // 1. Grafik Tren Siswa 30 Hari (Line Chart)
    const ctxTrend = document.getElementById('trendChart');
    if (ctxTrend) {
      const labels = @json($chartLabels);
      const dataHadir = @json($chartPersentase);

      new Chart(ctxTrend, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [{
            label: '% Kehadiran Siswa',
            data: dataHadir,
            borderColor: '#000000',
            backgroundColor: 'rgba(0, 0, 0, 0.08)',
            fill: true,
            tension: 0.35,
            borderWidth: 2.5,
            pointRadius: 3,
            pointHoverRadius: 6,
            pointBackgroundColor: '#000000'
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
              ticks: { callback: v => v + '%' }
            },
            x: {
              ticks: { maxRotation: 45, font: { size: 10 } }
            }
          }
        }
      });
    }

    // 2. Grafik Donut Status Siswa Hari Ini
    const ctxDonutSiswa = document.getElementById('donutSiswaChart');
    if (ctxDonutSiswa) {
      new Chart(ctxDonutSiswa, {
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
            borderWidth: 2,
            borderColor: 'var(--bg-2)'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
          },
          cutout: '68%'
        }
      });
    }

    // 3. Grafik Tren Guru 30 Hari (Line Chart)
    const ctxTrendGuru = document.getElementById('trendGuruChart');
    if (ctxTrendGuru) {
      const labelsG = @json($chartLabels);
      const dataHadirG = @json($chartGuruPersentase);

      new Chart(ctxTrendGuru, {
        type: 'line',
        data: {
          labels: labelsG,
          datasets: [{
            label: '% Kehadiran Guru',
            data: dataHadirG,
            borderColor: '#000000',
            backgroundColor: 'rgba(0, 0, 0, 0.08)',
            fill: true,
            tension: 0.35,
            borderWidth: 2.5,
            pointRadius: 3,
            pointHoverRadius: 6,
            pointBackgroundColor: '#000000'
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
              ticks: { callback: v => v + '%' }
            },
            x: {
              ticks: { maxRotation: 45, font: { size: 10 } }
            }
          }
        }
      });
    }

    // 4. Grafik Donut Status Guru Hari Ini
    const ctxDonutGuru = document.getElementById('donutGuruChart');
    if (ctxDonutGuru) {
      new Chart(ctxDonutGuru, {
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
            backgroundColor: ['#16A34A', '#D97706', '#CA8A04', '#64748B'],
            borderWidth: 2,
            borderColor: 'var(--bg-2)'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
          },
          cutout: '68%'
        }
      });
    }
  });
</script>

</body>
</html>
