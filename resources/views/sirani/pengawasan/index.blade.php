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

    /* Attendance Numbers in Table (Clean, No Border, No BG, Black Font) */
    .absen-mini-grid {
      display: flex;
      gap: 6px;
      align-items: center;
      flex-wrap: wrap;
      font-size: 12.5px;
      color: #0f172a;
    }
    .absen-mini-item {
      display: inline-flex;
      align-items: center;
      gap: 2px;
      color: #0f172a;
      font-weight: 600;
    }
    .absen-mini-item strong {
      color: #0f172a;
      font-weight: 800;
    }

    /* Status Text (Clean, No Border, No BG, Black Font) */
    .status-text-row {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      font-size: 12.5px;
      font-weight: 700;
      color: #0f172a;
      white-space: nowrap;
    }
    .status-dot {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      flex-shrink: 0;
    }
    .status-dot.aktif { background: #10b981; }
    .status-dot.bahaya { background: #ef4444; }
    .status-dot.belum { background: #94a3b8; }

    /* Action Buttons (Simplified, Compact Icon Buttons with Hover Tooltip) */
    .action-btn-group {
      display: inline-flex;
      gap: 6px;
      align-items: center;
      justify-content: flex-end;
    }
    .action-tooltip-box {
      position: relative;
      display: inline-flex;
    }
    .action-tooltip-box .tooltip-text {
      visibility: hidden;
      opacity: 0;
      position: absolute;
      bottom: calc(100% + 7px);
      right: 50%;
      transform: translateX(50%) translateY(4px);
      background: #0f172a;
      color: #ffffff;
      font-size: 11px;
      font-weight: 600;
      padding: 5px 9px;
      border-radius: 6px;
      white-space: nowrap;
      pointer-events: none;
      z-index: 1000;
      box-shadow: 0 4px 12px rgba(15,23,42,0.2);
      transition: opacity 0.18s ease, transform 0.18s ease;
    }
    .action-tooltip-box .tooltip-text::after {
      content: "";
      position: absolute;
      top: 100%;
      left: 50%;
      transform: translateX(-50%);
      border-width: 4px;
      border-style: solid;
      border-color: #0f172a transparent transparent transparent;
    }
    .action-tooltip-box:hover .tooltip-text {
      visibility: visible;
      opacity: 1;
      transform: translateX(50%) translateY(0);
    }
    
    .btn-action-icon {
      width: 32px;
      height: 32px;
      border-radius: 8px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      border: 1px solid var(--border-2);
      background: var(--surface);
      color: #0f172a;
      cursor: pointer;
      text-decoration: none;
      transition: all .15s ease;
    }
    .btn-action-icon:hover {
      background: #0f172a;
      color: #ffffff;
      border-color: #0f172a;
      transform: translateY(-1px);
    }
    .btn-action-icon.wa {
      color: #16a34a;
      border-color: #bbf7d0;
      background: #f0fdf4;
    }
    .btn-action-icon.wa:hover {
      background: #16a34a;
      color: #ffffff;
      border-color: #16a34a;
    }
    .btn-action-icon.disabled {
      opacity: 0.4;
      cursor: not-allowed;
      pointer-events: auto;
      background: var(--bg-2);
      border-color: var(--border);
      color: #94a3b8;
    }
    .btn-action-icon.disabled:hover {
      background: var(--bg-2);
      color: #94a3b8;
      transform: none;
    }

    /* Timeline Log Item (Clean, No BG/Border on badge, Black Font) */
    .log-item {
      display: flex;
      gap: 12px;
      padding: 10px 14px;
      border-bottom: 1px solid var(--border);
      font-size: 12.5px;
      align-items: flex-start;
      color: #0f172a;
    }
    .log-item:last-child {
      border-bottom: none;
    }
    .log-time {
      font-family: var(--font-mono);
      font-weight: 700;
      color: #64748b;
      font-size: 11px;
      white-space: nowrap;
      padding-top: 1px;
    }
    .log-badge-clean {
      font-size: 11px;
      font-weight: 800;
      color: #0f172a;
      text-transform: uppercase;
      white-space: nowrap;
      letter-spacing: 0.3px;
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
          <span class="kpi-stat-title">Total Wali Kelas</span>
          <div class="kpi-stat-icon" style="background:#f1f5f9; color:#475569;">
            <i class="bi bi-person-badge-fill"></i>
          </div>
        </div>
        <div class="kpi-stat-val" style="color:#0f172a;">{{ $totalWaliKelas }} <span style="font-size:13px; font-weight:600; color:#475569;">Guru</span></div>
        <div class="kpi-stat-sub">Membina rombel aktif di sekolah</div>
      </div>

      <!-- 2. Wali Kelas Sudah Login & Memantau -->
      <div class="kpi-stat-card">
        <div class="kpi-stat-head">
          <span class="kpi-stat-title">Aktif Memantau</span>
          <div class="kpi-stat-icon" style="background:#f1f5f9; color:#0f172a;">
            <i class="bi bi-check2-circle"></i>
          </div>
        </div>
        <div class="kpi-stat-val" style="color:#0f172a;">{{ $waliKelasAktif }} <span style="font-size:13px; font-weight:600; color:#475569;">Wali Kelas</span></div>
        <div class="kpi-stat-sub">Telah login &amp; memantau SIRANI hari ini</div>
      </div>

      <!-- 3. Wali Kelas Belum Login / Belum Memantau -->
      <div class="kpi-stat-card">
        <div class="kpi-stat-head">
          <span class="kpi-stat-title">Belum Memantau</span>
          <div class="kpi-stat-icon" style="background:#f1f5f9; color:#0f172a;">
            <i class="bi bi-exclamation-circle"></i>
          </div>
        </div>
        <div class="kpi-stat-val" style="color:#0f172a;">
          {{ $waliKelasBelumAktif }} <span style="font-size:13px; font-weight:600; color:#475569;">Wali Kelas</span>
        </div>
        <div class="kpi-stat-sub">{{ $waliKelasBelumAktif > 0 ? 'Perlu perhatian / teguran' : 'Semua wali kelas aktif' }}</div>
      </div>

      <!-- 4. Kehadiran Siswa Sekolah -->
      <div class="kpi-stat-card">
        <div class="kpi-stat-head">
          <span class="kpi-stat-title">Kehadiran Siswa</span>
          <div class="kpi-stat-icon" style="background:#f1f5f9; color:#0f172a;">
            <i class="bi bi-pie-chart"></i>
          </div>
        </div>
        <div class="kpi-stat-val" style="color:#0f172a;">{{ $persenSekolah }}%</div>
        <div class="kpi-stat-sub">{{ $totalHadirSekolah }} dari {{ $totalSiswaSekolah }} siswa hadir ({{ $totalAlphaSekolah }} Alpha)</div>
      </div>
    </div>

    {{-- ══ 3. MATRIKS PENGAWASAN WALI KELAS ══ --}}
    <div class="matrix-card">
      <div class="matrix-head">
        <div class="matrix-title">
          <i class="bi bi-table" style="color:#0f172a;"></i>
          <span style="color:#0f172a;">Matriks Pengawasan Wali Kelas (Kelas Binaan &amp; Status Monitoring)</span>
        </div>
        <div class="matrix-search">
          <i class="bi bi-search" style="color:#64748b;"></i>
          <input type="text" id="filterMatrixInput" placeholder="Cari nama guru / kelas..." onkeyup="filterMatrixRows()" />
        </div>
      </div>

      <div style="overflow-x:auto;">
        <table class="table-custom" id="matrixTable">
          <thead>
            <tr>
              <th style="width:40px; text-align:center; color:#0f172a;">No</th>
              <th style="color:#0f172a;">Wali Kelas</th>
              <th style="color:#0f172a;">Kelas Binaan</th>
              <th style="color:#0f172a;">Status Monitoring Hari Ini</th>
              <th style="color:#0f172a;">Kehadiran Siswa Kelas</th>
              <th style="color:#0f172a;">Login Terakhir</th>
              <th style="text-align:right; color:#0f172a;">Tindakan</th>
            </tr>
          </thead>
          <tbody>
            @forelse($rekapWaliKelas as $idx => $item)
              <tr class="matrix-row" data-search="{{ strtolower($item['nama_guru'] . ' ' . $item['nama_rombel'] . ' ' . $item['nip']) }}">
                <td style="text-align:center; color:#475569; font-family:var(--font-mono); font-size:12px;">{{ $idx + 1 }}</td>
                
                <!-- Wali Kelas -->
                <td>
                  <div class="teacher-cell">
                    <img src="{{ $item['foto'] }}" alt="{{ $item['nama_guru'] }}" class="teacher-thumb" onerror="this.src='/img/user-default.png'" />
                    <div>
                      <div class="teacher-name" style="color:#0f172a;">{{ $item['nama_guru'] }}</div>
                      <div class="teacher-nip" style="color:#64748b;">NIP: {{ $item['nip'] }}</div>
                    </div>
                  </div>
                </td>

                <!-- Kelas Binaan -->
                <td>
                  <span style="display:inline-flex; align-items:center; gap:5px; font-weight:800; font-size:13px; color:#0f172a;">
                    <i class="bi bi-door-closed" style="color:#64748b;"></i> {{ $item['nama_rombel'] }}
                  </span>
                  <div style="font-size:11px; color:#475569; margin-top:2px;">{{ $item['total_siswa'] }} Peserta Didik</div>
                </td>

                <!-- Status Monitoring -->
                <td>
                  <div class="status-text-row" title="{{ $item['status_desc'] }}">
                    <span class="status-dot {{ $item['status_badge'] }}"></span>
                    <span style="color:#0f172a; font-weight:700;">{{ $item['status_label'] }}</span>
                  </div>
                  @if($item['total_aktivitas'] > 0)
                    <div style="font-size:11px; color:#64748b; margin-top:2px;">
                      {{ $item['total_aktivitas'] }} aksi di sistem
                    </div>
                  @endif
                </td>

                <!-- Rekap Siswa Kelas -->
                <td>
                  <div class="absen-mini-grid">
                    <span class="absen-mini-item">
                      <strong>{{ $item['hadir'] }}</strong> Hadir
                    </span>
                    @if($item['terlambat'] > 0)
                      <span style="color:#94a3b8;">·</span>
                      <span class="absen-mini-item">
                        <strong>{{ $item['terlambat'] }}</strong> Telat
                      </span>
                    @endif
                    @if($item['izin'] > 0)
                      <span style="color:#94a3b8;">·</span>
                      <span class="absen-mini-item">
                        <strong>{{ $item['izin'] }}</strong> Izin
                      </span>
                    @endif
                    @if($item['alpha'] > 0)
                      <span style="color:#94a3b8;">·</span>
                      <span class="absen-mini-item">
                        <strong>{{ $item['alpha'] }}</strong> Alpha
                      </span>
                    @endif
                  </div>
                  <div style="font-size:11px; color:#475569; margin-top:3px;">
                    Kehadiran: <strong style="color:#0f172a;">{{ $item['persen_hadir'] }}%</strong>
                  </div>
                </td>

                <!-- Login Terakhir (Non-redundant) -->
                <td>
                  @if($item['is_aktif_hari_ini'] && $item['aktivitas_terakhir'])
                    <div style="font-size:12.5px; font-weight:700; color:#0f172a;">
                      Hari ini, {{ $item['aktivitas_terakhir'] }} WIB
                    </div>
                  @else
                    <div style="font-size:12.5px; font-weight:700; color:#0f172a;">
                      {{ $item['login_terakhir'] }}
                    </div>
                  @endif
                </td>

                <!-- Tindakan Kepala Sekolah (Simplified Buttons with Hover Tooltip) -->
                <td style="text-align:right;">
                  <div class="action-btn-group">
                    @if($item['wa_link'])
                      <div class="action-tooltip-box">
                        <a href="{{ $item['wa_link'] }}" target="_blank" class="btn-action-icon wa" title="Kirim Pengingat WhatsApp" aria-label="Kirim Pengingat WhatsApp">
                          <i class="bi bi-whatsapp"></i>
                        </a>
                        <span class="tooltip-text">Kirim Pesan WhatsApp</span>
                      </div>
                    @else
                      <div class="action-tooltip-box">
                        <span class="btn-action-icon disabled" title="Nomor WhatsApp belum terdaftar">
                          <i class="bi bi-whatsapp"></i>
                        </span>
                        <span class="tooltip-text">Nomor WA Belum Ada</span>
                      </div>
                    @endif

                    <div class="action-tooltip-box">
                      <button type="button" class="btn-action-icon" onclick="openDetailAktivitasGuru({{ $item['guru_id'] }})" title="Lihat Riwayat Log Aktivitas" aria-label="Lihat Riwayat Log">
                        <i class="bi bi-clock-history"></i>
                      </button>
                      <span class="tooltip-text">Lihat Riwayat Log</span>
                    </div>
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
            <span class="log-badge-clean">{{ strtoupper($log->modul) }}:{{ strtoupper($log->aksi) }}</span>
            <div style="flex:1;">
              <strong style="color:#0f172a;">{{ $log->user?->name ?? 'Guru' }}:</strong>
              <span style="color:#0f172a;">{{ $log->deskripsi }}</span>
            </div>
            <span style="font-size:10.5px; color:#64748b; font-family:var(--font-mono);">
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
          <div id="modalGuruNama" style="font-weight:900; font-size:14px; color:#0f172a;">Nama Guru</div>
          <div id="modalGuruSub" style="font-size:11.5px; color:#64748b;">NIP / Jabatan</div>
        </div>
      </div>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModalAktivitas()" style="padding:4px 8px; border-radius:6px;">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <div class="pengawasan-modal-body" id="modalLogListContainer">
      <div style="text-align:center; padding:30px; color:#64748b;">Memuat log aktivitas...</div>
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
    container.innerHTML = '<div style="text-align:center; padding:30px; color:#64748b;"><i class="bi bi-arrow-repeat spin" style="font-size:20px; display:block; margin-bottom:8px;"></i>Memuat riwayat log...</div>';

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
        container.innerHTML = '<div style="text-align:center; padding:30px; color:#64748b; font-size:12.5px;"><i class="bi bi-clock-history" style="font-size:24px; display:block; margin-bottom:6px; opacity:.4;"></i>Belum ada rekaman riwayat aktivitas untuk guru ini.</div>';
        return;
      }

      let html = '';
      data.logs.forEach(log => {
        html += `
          <div class="log-item">
            <span class="log-time">${log.tanggal} ${log.waktu}</span>
            <span class="log-badge-clean">${log.modul}:${log.aksi}</span>
            <div style="flex:1;">
              <span style="color:#0f172a; font-weight:500;">${escapeHtml(log.deskripsi)}</span>
            </div>
            <span style="font-size:10.5px; color:#64748b; font-family:var(--font-mono);">${log.relatif}</span>
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
