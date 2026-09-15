<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pengawasan Kinerja &amp; Monitoring Guru — SIRANI SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ filemtime(public_path('css/dashboard.css')) }}">
  <style>
    .pengawasan-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 16px;
      margin-bottom: 20px;
      padding-bottom: 16px;
      border-bottom: 1px solid var(--border);
    }
    .pengawasan-title-box h1 {
      font-size: 22px;
      font-weight: 900;
      color: var(--text);
      letter-spacing: -0.5px;
      margin: 0 0 4px 0;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .pengawasan-title-box p {
      margin: 0;
      font-size: 12.5px;
      color: var(--text-3);
    }
    .date-filter-box {
      display: flex;
      align-items: center;
      gap: 8px;
      background: var(--surface);
      border: 1px solid var(--border-2);
      padding: 6px 12px;
      border-radius: var(--r-sm);
    }
    .date-filter-box input[type="date"] {
      background: transparent;
      border: none;
      color: var(--text);
      font-weight: 700;
      font-size: 13px;
      outline: none;
      font-family: inherit;
    }

    /* KPI Grid */
    .pengawasan-kpi-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
      gap: 12px;
      margin-bottom: 24px;
    }
    .kpi-stat-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--r-md);
      padding: 14px 16px;
      box-shadow: var(--shadow-sm);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: transform .2s ease;
    }
    .kpi-stat-card:hover {
      transform: translateY(-2px);
    }
    .kpi-stat-head {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 6px;
    }
    .kpi-stat-title {
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--text-3);
    }
    .kpi-stat-icon {
      width: 28px;
      height: 28px;
      border-radius: 7px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
    }
    .kpi-stat-val {
      font-size: 26px;
      font-weight: 900;
      font-family: var(--font-mono);
      line-height: 1.1;
      margin-bottom: 4px;
    }
    .kpi-stat-sub {
      font-size: 11.5px;
      color: var(--text-3);
    }

    /* Table & Matrix Styling */
    .matrix-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--r-md);
      overflow: hidden;
      margin-bottom: 24px;
      box-shadow: var(--shadow-sm);
    }
    .matrix-head {
      padding: 14px 18px;
      background: var(--bg-2);
      border-bottom: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
    }
    .matrix-title {
      font-size: 14px;
      font-weight: 800;
      color: var(--text);
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .matrix-search {
      display: flex;
      align-items: center;
      gap: 6px;
      background: var(--surface);
      border: 1px solid var(--border-2);
      padding: 5px 12px;
      border-radius: var(--r-sm);
      font-size: 12px;
    }
    .matrix-search input {
      background: transparent;
      border: none;
      outline: none;
      font-size: 12px;
      color: var(--text);
      width: 170px;
    }

    .table-custom {
      width: 100%;
      border-collapse: collapse;
      font-size: 12.5px;
    }
    .table-custom th {
      background: var(--bg-3);
      padding: 10px 14px;
      text-align: left;
      font-weight: 800;
      font-size: 11px;
      color: var(--text-3);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border-bottom: 1px solid var(--border);
      white-space: nowrap;
    }
    .table-custom td {
      padding: 12px 14px;
      border-bottom: 1px solid var(--border);
      vertical-align: middle;
    }
    .table-custom tr:hover td {
      background: rgba(0,0,0,0.015);
    }

    /* Teacher Cell */
    .teacher-cell {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .teacher-thumb {
      width: 34px;
      height: 34px;
      border-radius: 8px;
      object-fit: cover;
      object-position: center 20%;
      background: #0f172a;
      border: 1px solid var(--border-2);
      flex-shrink: 0;
    }
    .teacher-name {
      font-weight: 800;
      color: var(--text);
      font-size: 13px;
      line-height: 1.2;
    }
    .teacher-nip {
      font-size: 11px;
      color: var(--text-3);
      margin-top: 2px;
      font-family: var(--font-mono);
    }

    /* Attendance Pills in Table */
    .absen-mini-grid {
      display: flex;
      gap: 5px;
      align-items: center;
      flex-wrap: wrap;
    }
    .absen-mini-pill {
      display: inline-flex;
      align-items: center;
      gap: 3px;
      padding: 2px 7px;
      border-radius: 6px;
      font-size: 11px;
      font-weight: 800;
      white-space: nowrap;
    }
    .absen-mini-pill.hadir { background: rgba(16, 185, 129, 0.12); color: #059669; }
    .absen-mini-pill.terlambat { background: rgba(245, 158, 11, 0.12); color: #b45309; }
    .absen-mini-pill.izin { background: rgba(59, 130, 246, 0.12); color: #2563eb; }
    .absen-mini-pill.alpha { background: rgba(239, 68, 68, 0.14); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.3); }

    /* Status Badges */
    .status-badge-lg {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 4px 10px;
      border-radius: 7px;
      font-size: 11.5px;
      font-weight: 800;
      white-space: nowrap;
    }
    .status-badge-lg.aktif {
      background: rgba(16, 185, 129, 0.14);
      color: #059669;
      border: 1px solid rgba(16, 185, 129, 0.35);
    }
    .status-badge-lg.bahaya {
      background: rgba(239, 68, 68, 0.12);
      color: #dc2626;
      border: 1px solid rgba(239, 68, 68, 0.35);
      animation: pulse-border 2s infinite ease-in-out;
    }
    .status-badge-lg.belum {
      background: rgba(100, 116, 139, 0.12);
      color: #475569;
      border: 1px solid rgba(100, 116, 139, 0.25);
    }

    /* Action Buttons */
    .btn-action-wa {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 5px 10px;
      border-radius: 7px;
      font-size: 11.5px;
      font-weight: 800;
      background: #25D366;
      color: #ffffff;
      text-decoration: none;
      border: none;
      cursor: pointer;
      transition: all .2s;
      box-shadow: 0 2px 6px rgba(37, 211, 102, 0.25);
    }
    .btn-action-wa:hover {
      background: #1eb956;
      transform: translateY(-1px);
      color: #ffffff;
    }
    .btn-action-detail {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 5px 9px;
      border-radius: 7px;
      font-size: 11.5px;
      font-weight: 700;
      background: var(--bg-3);
      color: var(--text);
      border: 1px solid var(--border-2);
      cursor: pointer;
      transition: all .2s;
    }
    .btn-action-detail:hover {
      background: var(--surface);
      border-color: var(--text);
    }

    /* Timeline Log Item */
    .log-item {
      display: flex;
      gap: 12px;
      padding: 10px 14px;
      border-bottom: 1px solid var(--border);
      font-size: 12px;
      align-items: flex-start;
    }
    .log-item:last-child {
      border-bottom: none;
    }
    .log-time {
      font-family: var(--font-mono);
      font-weight: 800;
      color: var(--text-3);
      font-size: 11px;
      white-space: nowrap;
      padding-top: 1px;
    }
    .log-badge {
      font-size: 10px;
      font-weight: 800;
      padding: 2px 6px;
      border-radius: 5px;
      text-transform: uppercase;
      background: var(--bg-3);
      color: var(--text);
      border: 1px solid var(--border-2);
      white-space: nowrap;
    }

    /* Modal Styling */
    .pengawasan-modal {
      display: none;
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(15, 23, 42, 0.65);
      backdrop-filter: blur(4px);
      z-index: 9999;
      align-items: center;
      justify-content: center;
      padding: 16px;
    }
    .pengawasan-modal.active {
      display: flex;
    }
    .pengawasan-modal-content {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--r-md);
      max-width: 680px;
      width: 100%;
      max-height: 85vh;
      display: flex;
      flex-direction: column;
      box-shadow: var(--shadow-lg);
      overflow: hidden;
    }
    .pengawasan-modal-head {
      padding: 14px 18px;
      background: var(--bg-2);
      border-bottom: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .pengawasan-modal-body {
      padding: 16px;
      overflow-y: auto;
      flex: 1;
    }
  </style>
</head>
<body>

<div class="app-container">
  @include('partials.sidebar')

  <main class="main-content">
    <header class="header no-print">
      <div class="header-title">
        <h1>SIRANI</h1>
        <p>Sistem Informasi Responsif Absensi SMKN 1 Air Naningan</p>
      </div>
      @include('partials.header_actions')
    </header>

    {{-- ══ 1. HEADER HALAMAN PENGAWASAN KEPSEK ══ --}}
    <div class="pengawasan-header no-print">
      <div class="pengawasan-title-box">
        <h1>
          <i class="bi bi-award-fill" style="color:#d97706;"></i>
          Pengawasan Kinerja &amp; Monitoring Guru
        </h1>
        <p>
          Pantauan Eksekutif Kepala Sekolah terhadap kedisiplinan dan monitoring kehadiran peserta didik oleh Wali Kelas.
        </p>
      </div>
      <div>
        <form method="GET" action="/pengawasan-guru" class="date-filter-box" id="formFilterTanggal">
          <i class="bi bi-calendar-event" style="color:var(--text-3);"></i>
          <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="document.getElementById('formFilterTanggal').submit();" title="Pilih Tanggal Pengawasan" />
          @if(!$isHariIni)
            <a href="/pengawasan-guru" style="font-size:11px; font-weight:800; color:var(--cyan); text-decoration:none; margin-left:4px;">Hari Ini</a>
          @endif
        </form>
      </div>
    </div>

    {{-- ══ 2. KPI ANALYTICS GRID ══ --}}
    <div class="pengawasan-kpi-grid no-print">
      <!-- 1. Total Wali Kelas -->
      <div class="kpi-stat-card">
        <div class="kpi-stat-head">
          <span class="kpi-stat-title">Total Rombel / Wali</span>
          <div class="kpi-stat-icon" style="background:#f1f5f9; color:#475569;">
            <i class="bi bi-person-badge-fill"></i>
          </div>
        </div>
        <div class="kpi-stat-val" style="color:var(--text);">{{ $totalWaliKelas }} <span style="font-size:13px; font-weight:600; color:var(--text-3);">Kelas</span></div>
        <div class="kpi-stat-sub">9 Rombel aktif di SMK</div>
      </div>

      <!-- 2. Wali Kelas Sudah Login & Memantau -->
      <div class="kpi-stat-card" style="border-color:rgba(16, 185, 129, 0.35);">
        <div class="kpi-stat-head">
          <span class="kpi-stat-title" style="color:#059669;">Aktif Memantau</span>
          <div class="kpi-stat-icon" style="background:#ecfdf5; color:#059669;">
            <i class="bi bi-check-circle-fill"></i>
          </div>
        </div>
        <div class="kpi-stat-val" style="color:#059669;">{{ $waliKelasAktif }} <span style="font-size:13px; font-weight:600; color:#059669;">Wali Kelas</span></div>
        <div class="kpi-stat-sub">Telah login &amp; memantau SIRANI hari ini</div>
      </div>

      <!-- 3. Wali Kelas Belum Login / Belum Memantau -->
      <div class="kpi-stat-card" style="border-color:{{ $waliKelasBelumAktif > 0 ? 'rgba(239, 68, 68, 0.4)' : 'var(--border)' }};">
        <div class="kpi-stat-head">
          <span class="kpi-stat-title" style="color:{{ $waliKelasBelumAktif > 0 ? '#dc2626' : 'var(--text-3)' }};">Belum Login / Memantau</span>
          <div class="kpi-stat-icon" style="background:#fef2f2; color:#dc2626;">
            <i class="bi bi-exclamation-octagon-fill"></i>
          </div>
        </div>
        <div class="kpi-stat-val" style="color:{{ $waliKelasBelumAktif > 0 ? '#dc2626' : 'var(--text)' }};">
          {{ $waliKelasBelumAktif }} <span style="font-size:13px; font-weight:600; color:var(--text-3);">Wali Kelas</span>
        </div>
        <div class="kpi-stat-sub">{{ $waliKelasBelumAktif > 0 ? 'Perlu perhatian / teguran' : 'Semua wali kelas aktif' }}</div>
      </div>

      <!-- 4. Kehadiran Siswa Sekolah -->
      <div class="kpi-stat-card">
        <div class="kpi-stat-head">
          <span class="kpi-stat-title">Kehadiran Siswa</span>
          <div class="kpi-stat-icon" style="background:#f0f9ff; color:#0284c7;">
            <i class="bi bi-pie-chart-fill"></i>
          </div>
        </div>
        <div class="kpi-stat-val" style="color:#0284c7;">{{ $persenSekolah }}%</div>
        <div class="kpi-stat-sub">{{ $totalHadirSekolah }} dari {{ $totalSiswaSekolah }} siswa hadir ({{ $totalAlphaSekolah }} Alpha)</div>
      </div>
    </div>

    {{-- ══ 3. MATRIKS PENGAWASAN WALI KELAS ══ --}}
    <div class="matrix-card">
      <div class="matrix-head">
        <div class="matrix-title">
          <i class="bi bi-table" style="color:#d97706;"></i>
          <span>Matriks Pengawasan Wali Kelas (Kelas Binaan &amp; Status Monitoring)</span>
        </div>
        <div class="matrix-search">
          <i class="bi bi-search" style="color:var(--text-3);"></i>
          <input type="text" id="filterMatrixInput" placeholder="Cari nama guru / kelas..." onkeyup="filterMatrixRows()" />
        </div>
      </div>

      <div style="overflow-x:auto;">
        <table class="table-custom" id="matrixTable">
          <thead>
            <tr>
              <th style="width:40px; text-align:center;">No</th>
              <th>Wali Kelas</th>
              <th>Kelas Binaan</th>
              <th>Status Monitoring Hari Ini</th>
              <th>Kehadiran Siswa Kelas</th>
              <th>Login Terakhir</th>
              <th style="text-align:right;">Tindakan Kepala Sekolah</th>
            </tr>
          </thead>
          <tbody>
            @forelse($rekapWaliKelas as $idx => $item)
              <tr class="matrix-row" data-search="{{ strtolower($item['nama_guru'] . ' ' . $item['nama_rombel'] . ' ' . $item['nip']) }}">
                <td style="text-align:center; color:var(--text-3); font-family:var(--font-mono); font-size:11.5px;">{{ $idx + 1 }}</td>
                
                <!-- Wali Kelas -->
                <td>
                  <div class="teacher-cell">
                    <img src="{{ $item['foto'] }}" alt="{{ $item['nama_guru'] }}" class="teacher-thumb" onerror="this.src='/img/user-default.png'" />
                    <div>
                      <div class="teacher-name">{{ $item['nama_guru'] }}</div>
                      <div class="teacher-nip">NIP: {{ $item['nip'] }}</div>
                    </div>
                  </div>
                </td>

                <!-- Kelas Binaan -->
                <td>
                  <span style="display:inline-flex; align-items:center; gap:5px; font-weight:800; font-size:13px; color:var(--text);">
                    <i class="bi bi-door-closed" style="color:#d97706;"></i> {{ $item['nama_rombel'] }}
                  </span>
                  <div style="font-size:11px; color:var(--text-3); margin-top:2px;">{{ $item['total_siswa'] }} Peserta Didik</div>
                </td>

                <!-- Status Monitoring -->
                <td>
                  <span class="status-badge-lg {{ $item['status_badge'] }}" title="{{ $item['status_desc'] }}">
                    @if($item['status_badge'] === 'aktif')
                      <i class="bi bi-check-circle-fill"></i>
                    @elseif($item['status_badge'] === 'bahaya')
                      <i class="bi bi-exclamation-triangle-fill"></i>
                    @else
                      <i class="bi bi-clock-history"></i>
                    @endif
                    <span>{{ $item['status_label'] }}</span>
                  </span>
                  @if($item['total_aktivitas'] > 0)
                    <div style="font-size:10.5px; color:var(--text-3); margin-top:3px;">
                      {{ $item['total_aktivitas'] }} aksi tercatat di log
                    </div>
                  @endif
                </td>

                <!-- Rekap Siswa Kelas -->
                <td>
                  <div class="absen-mini-grid">
                    <span class="absen-mini-pill hadir" title="Hadir Tepat Waktu">
                      <i class="bi bi-check2"></i> {{ $item['hadir'] }} Hadir
                    </span>
                    @if($item['terlambat'] > 0)
                      <span class="absen-mini-pill terlambat" title="Terlambat Gerbang">
                        <i class="bi bi-clock"></i> {{ $item['terlambat'] }}
                      </span>
                    @endif
                    @if($item['izin'] > 0)
                      <span class="absen-mini-pill izin" title="Izin / Sakit Sah">
                        <i class="bi bi-file-earmark-text"></i> {{ $item['izin'] }}
                      </span>
                    @endif
                    @if($item['alpha'] > 0)
                      <span class="absen-mini-pill alpha" title="Alpha / Belum Hadir">
                        <i class="bi bi-x-circle"></i> {{ $item['alpha'] }} Alpha
                      </span>
                    @endif
                  </div>
                  <div style="font-size:11px; color:var(--text-3); margin-top:3px;">
                    Kehadiran: <strong>{{ $item['persen_hadir'] }}%</strong>
                  </div>
                </td>

                <!-- Login Terakhir -->
                <td>
                  <div style="font-size:12px; font-weight:700; color:var(--text);">
                    {{ $item['login_terakhir'] }}
                  </div>
                  @if($item['aktivitas_terakhir'])
                    <div style="font-size:10.5px; color:#059669; margin-top:2px;">
                      <i class="bi bi-broadcast"></i> Terakhir akses: {{ $item['aktivitas_terakhir'] }} WIB
                    </div>
                  @endif
                </td>

                <!-- Tindakan Kepala Sekolah -->
                <td style="text-align:right;">
                  <div style="display:inline-flex; gap:6px; align-items:center;">
                    @if($item['wa_link'])
                      <a href="{{ $item['wa_link'] }}" target="_blank" class="btn-action-wa" title="Kirim Pengingat Resmi via WhatsApp ke Wali Kelas">
                        <i class="bi bi-whatsapp"></i>
                        <span>Ingatkan WA</span>
                      </a>
                    @else
                      <span class="btn-action-detail" style="opacity:0.5; cursor:not-allowed;" title="Nomor HP belum terdaftar">
                        <i class="bi bi-telephone-x"></i> No WA -
                      </span>
                    @endif
                    <button type="button" class="btn-action-detail" onclick="openDetailAktivitasGuru({{ $item['guru_id'] }})" title="Lihat Riwayat Jejak Log Aktivitas Guru">
                      <i class="bi bi-clock-history"></i> Log
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" style="text-align:center; padding:36px; color:var(--text-3);">
                  Belum ada data wali kelas yang terdaftar.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- ══ 4. JEJAK LOG AKTIVITAS SELURUH GURU HARI INI (AUDIT TRAIL) ══ --}}
    <div class="matrix-card">
      <div class="matrix-head">
        <div class="matrix-title">
          <i class="bi bi-activity" style="color:#0284c7;"></i>
          <span>Kronologi Log Aktivitas Guru di SIRANI ({{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }})</span>
        </div>
        <div style="font-size:12px; color:var(--text-3);">
          Menampilkan {{ $aktivitasGuruList->count() }} aktivitas terekam
        </div>
      </div>

      <div style="max-height:420px; overflow-y:auto;">
        @forelse($aktivitasGuruList as $log)
          <div class="log-item">
            <span class="log-time">{{ $log->created_at->format('H:i:s') }}</span>
            <span class="log-badge">{{ $log->modul }}:{{ $log->aksi }}</span>
            <div style="flex:1;">
              <strong style="color:var(--text);">{{ $log->user?->name ?? 'Guru' }}</strong>:
              <span style="color:var(--text-2);">{{ $log->deskripsi }}</span>
            </div>
            <span style="font-size:10.5px; color:var(--text-3); font-family:var(--font-mono);">
              {{ $log->created_at->diffForHumans() }}
            </span>
          </div>
        @empty
          <div style="text-align:center; padding:36px; color:var(--text-3); font-size:12.5px;">
            <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:6px; opacity:.4;"></i>
            Belum ada log aktivitas guru yang terekam pada tanggal ini.
          </div>
        @endforelse
      </div>
    </div>

  </main>
</div>

{{-- ══ MODAL DETAIL LOG AKTIVITAS 1 GURU ══ --}}
<div class="pengawasan-modal" id="modalDetailAktivitas" onclick="closeModalOnBackdrop(event)">
  <div class="pengawasan-modal-content">
    <div class="pengawasan-modal-head">
      <div style="display:flex; align-items:center; gap:10px;">
        <img id="modalGuruFoto" src="/img/user-default.png" style="width:36px; height:36px; border-radius:8px; object-fit:cover;" />
        <div>
          <div id="modalGuruNama" style="font-weight:900; font-size:14px; color:var(--text);">Nama Guru</div>
          <div id="modalGuruSub" style="font-size:11.5px; color:var(--text-3);">NIP / Jabatan</div>
        </div>
      </div>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModalAktivitas()" style="padding:4px 8px; border-radius:6px;">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <div class="pengawasan-modal-body" id="modalLogListContainer">
      <div style="text-align:center; padding:30px; color:var(--text-3);">Memuat log aktivitas...</div>
    </div>
  </div>
</div>

<script>
  function filterMatrixRows() {
    const input = document.getElementById('filterMatrixInput');
    const filter = (input ? input.value : '').toLowerCase().trim();
    const rows = document.querySelectorAll('.matrix-row');

    rows.forEach(r => {
      const searchData = r.getAttribute('data-search') || '';
      if (searchData.includes(filter)) {
        r.style.display = '';
      } else {
        r.style.display = 'none';
      }
    });
  }

  async function openDetailAktivitasGuru(guruId) {
    const modal = document.getElementById('modalDetailAktivitas');
    const container = document.getElementById('modalLogListContainer');
    const namaEl = document.getElementById('modalGuruNama');
    const subEl = document.getElementById('modalGuruSub');
    const fotoEl = document.getElementById('modalGuruFoto');

    if (!modal || !container) return;

    modal.classList.add('active');
    container.innerHTML = '<div style="text-align:center; padding:30px; color:var(--text-3);"><i class="bi bi-arrow-repeat spin" style="font-size:20px; display:block; margin-bottom:8px;"></i>Memuat riwayat log...</div>';

    try {
      const res = await fetch(`/pengawasan-guru/${guruId}/aktivitas`, {
        headers: { 'Accept': 'application/json' }
      });
      const data = await res.json();

      if (!data.success) {
        container.innerHTML = `<div style="text-align:center; padding:30px; color:#dc2626;">${data.message || 'Gagal memuat log.'}</div>`;
        return;
      }

      if (namaEl) namaEl.textContent = data.guru?.nama || 'Guru';
      if (subEl) subEl.textContent = `NIP: ${data.guru?.nip || '-'} • ${data.guru?.jabatan || 'Guru'}`;
      if (fotoEl) fotoEl.src = data.guru?.foto || '/img/user-default.png';

      if (!data.logs || data.logs.length === 0) {
        container.innerHTML = '<div style="text-align:center; padding:30px; color:var(--text-3); font-size:12.5px;"><i class="bi bi-clock-history" style="font-size:24px; display:block; margin-bottom:6px; opacity:.4;"></i>Belum ada rekaman riwayat aktivitas untuk guru ini.</div>';
        return;
      }

      let html = '';
      data.logs.forEach(log => {
        html += `
          <div class="log-item">
            <span class="log-time">${log.tanggal} ${log.waktu}</span>
            <span class="log-badge">${log.modul}:${log.aksi}</span>
            <div style="flex:1;">
              <span style="color:var(--text);">${escapeHtml(log.deskripsi)}</span>
            </div>
            <span style="font-size:10.5px; color:var(--text-3); font-family:var(--font-mono);">${log.relatif}</span>
          </div>
        `;
      });
      container.innerHTML = html;
    } catch (e) {
      console.error(e);
      container.innerHTML = '<div style="text-align:center; padding:30px; color:#dc2626;">Terjadi kesalahan saat memuat data riwayat.</div>';
    }
  }

  function closeModalAktivitas() {
    const modal = document.getElementById('modalDetailAktivitas');
    if (modal) modal.classList.remove('active');
  }

  function closeModalOnBackdrop(e) {
    if (e.target.id === 'modalDetailAktivitas') {
      closeModalAktivitas();
    }
  }

  function escapeHtml(text) {
    if (!text) return '';
    return String(text)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }
</script>

</body>
</html>
