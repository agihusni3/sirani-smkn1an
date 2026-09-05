<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Riwayat &amp; Log Aktivitas — PPDB Online 2026</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/audit.css') }}?v={{ filemtime(public_path('css/audit.css')) }}">
</head>
<body>
<div class="app-container">
  @include('partials.sidebar_ppdb')

  <main class="main-content">
    {{-- Header Bar PPDB Log --}}
    <header class="topbar no-print" style="margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
      <div style="display:flex; align-items:center; gap:8px;">
        <span style="font-size:12px; font-weight:700; color:var(--text-3);">
          <a href="{{ route('admin.portal') }}" style="color:var(--text-3); text-decoration:none;"><i class="bi bi-command"></i> DCC</a>
          <span style="margin:0 4px;">/</span>
          <a href="{{ route('admin.ppdb.index') }}" style="color:var(--text-3); text-decoration:none;">PPDB 2026</a>
          <span style="margin:0 4px;">/</span>
          <span style="color:#d97706;">Log Verifikasi Panitia</span>
        </span>
      </div>
      @include('partials.header_actions')
    </header>

    {{-- Banner Ringkasan Ruang Log PPDB --}}
    <div style="background:linear-gradient(135deg, #78350f 0%, #b45309 50%, #d97706 100%); border-radius:16px; padding:20px 24px; color:#ffffff; margin-bottom:20px; box-shadow:0 8px 24px rgba(217,119,6,0.2); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; border:1px solid rgba(255,255,255,0.15);">
      <div>
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:4px;">
          <h1 style="margin:0; font-size:19px; font-weight:900; letter-spacing:-0.02em; display:flex; align-items:center; gap:8px;">
            <i class="bi bi-mortarboard-fill" style="color:#fde68a;"></i> Audit Trail Modul PPDB 2026
          </h1>
          <span style="font-size:10px; font-weight:800; background:rgba(255,255,255,0.2); padding:3px 9px; border-radius:999px; text-transform:uppercase; letter-spacing:0.05em;">Panitia Penerimaan</span>
        </div>
        <p style="margin:0; font-size:12.5px; color:rgba(255,255,255,0.9);">
          Rekam jejak verifikasi berkas, perubahan status pendaftar calon siswa (menunggu, berkas valid, diterima, ditolak), serta migrasi siswa aktif ke sistem utama.
        </p>
      </div>

      <div style="display:flex; align-items:center; gap:8px;">
        <a href="{{ route('admin.ppdb.index') }}" class="btn" style="background:rgba(255,255,255,0.15); color:#ffffff; border:1px solid rgba(255,255,255,0.3); font-size:12px; font-weight:700; padding:8px 14px; border-radius:8px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
          <i class="bi bi-speedometer2"></i> Dasbor PPDB
        </a>
        @if(auth()->user()->isAdmin() || auth()->user()->isKepalaSekolah())
          <a href="{{ route('audit.index') }}" class="btn" style="background:#ffffff; color:#78350f; font-size:12px; font-weight:800; padding:8px 14px; border-radius:8px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-shield-shaded"></i> Master Telemetri DCC
          </a>
        @endif
      </div>
    </div>

    {{-- 3 KPI Cards Log PPDB --}}
    <div class="audit-kpi-grid">
      <div class="audit-kpi-card">
        <div class="audit-kpi-info">
          <div class="audit-kpi-lbl">Verifikasi Hari Ini</div>
          <div class="audit-kpi-val">{{ number_format($counts['hari_ini']) }}</div>
        </div>
        <div class="audit-kpi-icon" style="color:#d97706; background:rgba(217,119,6,0.1); border-color:rgba(217,119,6,0.25);">
          <i class="bi bi-calendar2-day-fill"></i>
        </div>
      </div>

      <div class="audit-kpi-card">
        <div class="audit-kpi-info">
          <div class="audit-kpi-lbl">Verifikasi Minggu Ini</div>
          <div class="audit-kpi-val">{{ number_format($counts['minggu_ini']) }}</div>
        </div>
        <div class="audit-kpi-icon" style="color:#b45309; background:rgba(180,83,9,0.1); border-color:rgba(180,83,9,0.25);">
          <i class="bi bi-calendar2-week-fill"></i>
        </div>
      </div>

      <div class="audit-kpi-card">
        <div class="audit-kpi-info">
          <div class="audit-kpi-lbl">Total Riwayat Log PPDB</div>
          <div class="audit-kpi-val">{{ number_format($counts['total']) }}</div>
        </div>
        <div class="audit-kpi-icon" style="color:#78350f; background:rgba(120,53,15,0.1); border-color:rgba(120,53,15,0.25);">
          <i class="bi bi-database-fill-check"></i>
        </div>
      </div>
    </div>

    {{-- Panel Utama Riwayat PPDB --}}
    <div class="audit-panel">
      <div class="audit-panel-header">
        <div class="audit-panel-title">
          <i class="bi bi-list-columns-reverse" style="color:#d97706;"></i>
          <span>Log Riwayat Seleksi &amp; Panitia PPDB</span>
        </div>
        <div style="font-size:12px; color:var(--text-3); font-family:var(--font-mono);">
          Menampilkan <strong>{{ $logs->count() }}</strong> dari <strong>{{ $logs->total() }}</strong> catatan
        </div>
      </div>

      {{-- Toolbar Filter Terpadu --}}
      <form method="GET" action="{{ route('admin.ppdb.log') }}" class="audit-filter-bar">
        <div>
          <input 
            type="text" 
            name="cari" 
            placeholder="Cari nama calon siswa, no pendaftaran, atau keterangan..." 
            value="{{ $filters['cari'] ?? '' }}" 
            class="audit-filter-input" 
          />
        </div>

        <div>
          <select name="aksi" class="audit-filter-input" onchange="this.form.submit()">
            <option value="">Semua Aksi</option>
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
          <button type="submit" class="btn btn-primary" style="height:38px; padding:0 14px; font-size:12.5px; font-weight:800; display:inline-flex; align-items:center; gap:6px; background:#d97706; border-color:#d97706;">
            <i class="bi bi-search"></i> Filter
          </button>

          @if(!empty($filters['cari']) || !empty($filters['aksi']) || !empty($filters['dari']) || !empty($filters['sampai']))
            <a href="{{ route('admin.ppdb.log') }}" class="btn btn-outline" style="height:38px; padding:0 12px; font-size:12.5px; font-weight:700; color:var(--red); border-color:rgba(239,68,68,0.4);" title="Reset Filter">
              <i class="bi bi-x-circle"></i>
            </a>
          @endif
        </div>
      </form>

      {{-- Tabel Log PPDB --}}
      <div style="overflow-x:auto;">
        <table class="audit-table">
          <thead>
            <tr>
              <th style="width: 150px;">Waktu &amp; Tanggal</th>
              <th style="width: 140px;">Aksi</th>
              <th style="width: 110px;">Modul</th>
              <th>Deskripsi Aktivitas Panitia</th>
              <th style="width: 220px;">Panitia / Operator</th>
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
                  <span class="modul-capsule" style="background:#fef3c7; color:#d97706; border-color:#fde68a;">
                    PPDB 2026
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
                      Panitia PPDB Online
                    </div>
                    <div style="font-family: var(--font-mono); font-size: 10.5px; color: var(--text-3);">
                      Formulir Publik
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
                  <i class="bi bi-mortarboard" style="font-size: 40px; display: block; margin-bottom: 12px; color: var(--text-3);"></i>
                  <div style="font-weight: 700; font-size: 14px; color: var(--text-2); margin-bottom: 4px;">Tidak Ada Log PPDB</div>
                  <p style="font-size: 12px; color: var(--text-3); margin: 0;">Belum ada riwayat aktivitas panitia PPDB yang tercatat dengan filter saat ini.</p>
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
