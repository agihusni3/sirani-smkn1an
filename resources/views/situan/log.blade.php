<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Riwayat &amp; Log Aktivitas — SITUAN SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/audit.css') }}?v={{ filemtime(public_path('css/audit.css')) }}">
</head>
<body>
<div class="app-container">
  @include('partials.sidebar_situan')

  <main class="main-content">
    {{-- Header Bar SITUAN Log --}}
    <header class="topbar no-print" style="margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
      <div style="display:flex; align-items:center; gap:8px;">
        <span style="font-size:12px; font-weight:700; color:var(--text-3);">
          <a href="{{ route('admin.portal') }}" style="color:var(--text-3); text-decoration:none;"><i class="bi bi-command"></i> DCC</a>
          <span style="margin:0 4px;">/</span>
          <a href="{{ route('situan.index') }}" style="color:var(--text-3); text-decoration:none;">SITUAN</a>
          <span style="margin:0 4px;">/</span>
          <span style="color:#0284c7;">Log Aktivitas TU</span>
        </span>
      </div>
      @include('partials.header_actions')
    </header>

    {{-- Banner Ringkasan Ruang Log SITUAN --}}
    <div style="background:linear-gradient(135deg, #0c4a6e 0%, #0369a1 100%); border-radius:16px; padding:20px 24px; color:#ffffff; margin-bottom:20px; box-shadow:0 8px 24px rgba(2,132,199,0.18); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; border:1px solid rgba(255,255,255,0.15);">
      <div>
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:4px;">
          <h1 style="margin:0; font-size:19px; font-weight:900; letter-spacing:-0.02em; display:flex; align-items:center; gap:8px;">
            <i class="bi bi-buildings-fill" style="color:#38bdf8;"></i> Audit Log Modul SITUAN
          </h1>
          <span style="font-size:10px; font-weight:800; background:rgba(255,255,255,0.2); padding:3px 9px; border-radius:999px; text-transform:uppercase; letter-spacing:0.05em;">Tata Usaha</span>
        </div>
        <p style="margin:0; font-size:12.5px; color:rgba(255,255,255,0.85);">
          Pusat rekam jejak mutasi data pokok siswa, pendidik &amp; tenaga kependidikan (PTK), rombel, siklus akademik, dan konfigurasi kelembagaan.
        </p>
      </div>

      <div style="display:flex; align-items:center; gap:8px;">
        <a href="{{ route('situan.index') }}" class="btn" style="background:rgba(255,255,255,0.15); color:#ffffff; border:1px solid rgba(255,255,255,0.3); font-size:12px; font-weight:700; padding:8px 14px; border-radius:8px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
          <i class="bi bi-speedometer2"></i> Dasbor SITUAN
        </a>
        @if(auth()->user()->isAdmin() || auth()->user()->isKepalaSekolah())
          <a href="{{ route('audit.index') }}" class="btn" style="background:#ffffff; color:#0c4a6e; font-size:12px; font-weight:800; padding:8px 14px; border-radius:8px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-shield-shaded"></i> Master Telemetri DCC
          </a>
        @endif
      </div>
    </div>

    {{-- 3 KPI Cards Log SITUAN --}}
    <div class="audit-kpi-grid">
      <div class="audit-kpi-card">
        <div class="audit-kpi-info">
          <div class="audit-kpi-lbl">Mutasi TU Hari Ini</div>
          <div class="audit-kpi-val">{{ number_format($counts['hari_ini']) }}</div>
        </div>
        <div class="audit-kpi-icon" style="color:#0284c7; background:rgba(2,132,199,0.1); border-color:rgba(2,132,199,0.25);">
          <i class="bi bi-calendar2-day-fill"></i>
        </div>
      </div>

      <div class="audit-kpi-card">
        <div class="audit-kpi-info">
          <div class="audit-kpi-lbl">Mutasi TU Minggu Ini</div>
          <div class="audit-kpi-val">{{ number_format($counts['minggu_ini']) }}</div>
        </div>
        <div class="audit-kpi-icon" style="color:#0369a1; background:rgba(3,105,161,0.1); border-color:rgba(3,105,161,0.25);">
          <i class="bi bi-calendar2-week-fill"></i>
        </div>
      </div>

      <div class="audit-kpi-card">
        <div class="audit-kpi-info">
          <div class="audit-kpi-lbl">Total Riwayat Log SITUAN</div>
          <div class="audit-kpi-val">{{ number_format($counts['total']) }}</div>
        </div>
        <div class="audit-kpi-icon" style="color:#0c4a6e; background:rgba(12,74,110,0.1); border-color:rgba(12,74,110,0.25);">
          <i class="bi bi-database-fill-check"></i>
        </div>
      </div>
    </div>

    {{-- Panel Utama Riwayat SITUAN --}}
    <div class="audit-panel">
      <div class="audit-panel-header">
        <div class="audit-panel-title">
          <i class="bi bi-list-columns-reverse" style="color:#0284c7;"></i>
          <span>Log Mutasi Administrasi &amp; Data Pokok</span>
        </div>
        <div style="font-size:12px; color:var(--text-3); font-family:var(--font-mono);">
          Menampilkan <strong>{{ $logs->count() }}</strong> dari <strong>{{ $logs->total() }}</strong> aktivitas
        </div>
      </div>

      {{-- Toolbar Filter Terpadu --}}
      <form method="GET" action="{{ route('situan.log') }}" class="audit-filter-bar">
        <div>
          <input 
            type="text" 
            name="cari" 
            placeholder="Cari nama siswa, nip, rombel, atau uraian mutasi..." 
            value="{{ $filters['cari'] ?? '' }}" 
            class="audit-filter-input" 
          />
        </div>

        <div>
          <select name="modul" class="audit-filter-input" onchange="this.form.submit()">
            <option value="">Semua Sub-Modul TU</option>
            @foreach($subModulOptions as $val => $lbl)
              <option value="{{ $val }}" @selected(($filters['modul'] ?? '') === $val)>{{ $lbl }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <select name="aksi" class="audit-filter-input" onchange="this.form.submit()">
            <option value="">Semua Tipe Aksi</option>
            @foreach($aksiOptions as $a)
              <option value="{{ $a }}" @selected(($filters['aksi'] ?? '') === $a)>{{ ucwords(str_replace('_', ' ', $a)) }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <input 
            type="date" 
            name="dari" 
            value="{{ $filters['dari'] ?? '' }}" 
            class="audit-filter-input" 
            title="Dari Tanggal"
          />
        </div>

        <div>
          <input 
            type="date" 
            name="sampai" 
            value="{{ $filters['sampai'] ?? '' }}" 
            class="audit-filter-input" 
            title="Sampai Tanggal"
          />
        </div>

        <div style="display: flex; gap: 8px;">
          <button type="submit" class="btn btn-primary" style="height:38px; padding:0 14px; font-size:12.5px; font-weight:800; display:inline-flex; align-items:center; gap:6px; background:#0284c7; border-color:#0284c7;">
            <i class="bi bi-search"></i> Filter
          </button>

          @if(!empty($filters['cari']) || !empty($filters['modul']) || !empty($filters['aksi']) || !empty($filters['dari']) || !empty($filters['sampai']))
            <a href="{{ route('situan.log') }}" class="btn btn-outline" style="height:38px; padding:0 12px; font-size:12.5px; font-weight:700; color:var(--red); border-color:rgba(239,68,68,0.4);" title="Reset Filter">
              <i class="bi bi-x-circle"></i>
            </a>
          @endif
        </div>
      </form>

      {{-- Tabel Log SITUAN --}}
      <div style="overflow-x:auto;">
        <table class="audit-table">
          <thead>
            <tr>
              <th style="width: 150px;">Waktu &amp; Tanggal</th>
              <th style="width: 140px;">Aksi</th>
              <th style="width: 120px;">Sub-Modul</th>
              <th>Deskripsi Mutasi Data</th>
              <th style="width: 220px;">Operator Tata Usaha</th>
              <th style="width: 60px; text-align: center;">Detail</th>
            </tr>
          </thead>
          <tbody>
            @forelse($logs as $log)
              <tr>
                <td>
                  <div style="font-weight: 700; font-size: 12.5px; color: var(--text);">
                    {{ $log->created_at->timezone('Asia/Jakarta')->format('d M Y') }}
                  </div>
                  <div style="font-family: var(--font-mono); font-size: 11px; color: var(--text-3); margin-top: 2px;">
                    {{ $log->created_at->timezone('Asia/Jakarta')->format('H:i:s') }} WIB
                  </div>
                </td>
                <td>
                  <span class="badge-aksi {{ $log->badgeClass() }}">
                    {{ $log->aksiLabel() }}
                  </span>
                </td>
                <td>
                  <span class="modul-capsule" style="background:#e0f2fe; color:#0284c7; border-color:#bae6fd;">
                    {{ $subModulOptions[$log->modul] ?? strtoupper($log->modul) }}
                  </span>
                </td>
                <td>
                  <div style="font-size: 13px; color: var(--text); line-height: 1.45;">
                    {{ $log->deskripsi }}
                  </div>
                  @if($log->target_type && $log->target_id)
                    <div style="font-size: 11px; font-family: var(--font-mono); color: var(--text-3); margin-top: 3px;">
                      Ref: {{ $log->target_type }} #{{ $log->target_id }}
                    </div>
                  @endif
                </td>
                <td>
                  @if($log->user)
                    <div style="font-weight: 700; font-size: 12.5px; color: var(--text);">
                      {{ $log->user->name }}
                    </div>
                    <div style="display: inline-flex; align-items: center; gap: 4px; font-family: var(--font-mono); font-size: 10.5px; color: var(--text-3); margin-top: 2px; background: var(--bg-3); padding: 1px 6px; border-radius: 4px; border: 1px solid var(--border-2);">
                      <i class="bi bi-hdd-network"></i> {{ $log->ip_address ?? '127.0.0.1' }}
                    </div>
                  @else
                    <div style="font-weight: 700; font-size: 12.5px; color: var(--text-2);">
                      Sistem Otomatis
                    </div>
                    <div style="font-family: var(--font-mono); font-size: 10.5px; color: var(--text-3);">
                      Batch / Mutasi DB
                    </div>
                  @endif
                </td>
                <td style="text-align: center;">
                  @if($log->data_lama || $log->data_baru)
                    <a href="{{ route('audit.show', $log->id) }}" class="btn-detail-icon" title="Lihat Rekam Perubahan Data">
                      <i class="bi bi-eye-fill"></i>
                    </a>
                  @else
                    <span style="color: var(--text-3); font-size: 12px;">—</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="text-align: center; padding: 48px 20px; color: var(--text-3);">
                  <i class="bi bi-buildings" style="font-size: 40px; display: block; margin-bottom: 12px; color: var(--text-3);"></i>
                  <div style="font-weight: 700; font-size: 14px; color: var(--text-2); margin-bottom: 4px;">Tidak Ada Log Aktivitas TU</div>
                  <p style="font-size: 12px; color: var(--text-3); margin: 0;">Belum ada riwayat perubahan data untuk filter yang dipilih.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      @if($logs->hasPages())
        <div style="padding: 16px 22px; border-top: 1px solid var(--border); background: var(--bg-2); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
          <div style="font-size: 12px; color: var(--text-3);">
            Menampilkan halaman <strong>{{ $logs->currentPage() }}</strong> dari <strong>{{ $logs->lastPage() }}</strong>
          </div>
          {{ $logs->links('partials.pagination') }}
        </div>
      @endif
    </div>

  </main>
</div>
</body>
</html>
