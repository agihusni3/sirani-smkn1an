<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Audit Trail &amp; Log Aktivitas — SIRANI</title>
  @include('partials.styles')
  <style>
    /* ── Executive Audit Page Styling ── */
    .audit-kpi-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
      margin-bottom: 24px;
    }

    .audit-kpi-card {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-md);
      padding: 16px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      box-shadow: var(--shadow-sm);
      transition: all .2s ease;
    }
    .audit-kpi-card:hover {
      border-color: var(--border-2);
      transform: translateY(-2px);
    }

    .audit-kpi-info {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }
    .audit-kpi-lbl {
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      color: var(--text-3);
    }
    .audit-kpi-val {
      font-size: 26px;
      font-weight: 900;
      font-family: var(--font-mono);
      color: var(--text);
      line-height: 1.1;
    }
    .audit-kpi-icon {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      background: var(--bg-3);
      border: 1px solid var(--border-2);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      color: var(--text);
      flex-shrink: 0;
    }

    /* ── Main Panel & Toolbar ── */
    .audit-panel {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-lg);
      box-shadow: var(--shadow-sm);
      overflow: hidden;
      margin-bottom: 30px;
    }

    .audit-panel-header {
      padding: 18px 22px;
      background: var(--bg-2);
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 12px;
    }

    .audit-panel-title {
      font-size: 15px;
      font-weight: 800;
      color: var(--text);
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .audit-filter-bar {
      padding: 14px 22px;
      background: var(--surface);
      border-bottom: 1px solid var(--border);
      display: grid;
      grid-template-columns: 2fr 1.2fr 1.2fr 1.4fr auto;
      gap: 10px;
      align-items: center;
    }
    @media (max-width: 1024px) {
      .audit-filter-bar {
        grid-template-columns: 1fr 1fr;
      }
    }
    @media (max-width: 640px) {
      .audit-filter-bar {
        grid-template-columns: 1fr;
      }
    }

    .audit-filter-input {
      width: 100%;
      height: 38px;
      background: var(--bg-2);
      border: 1.5px solid var(--border-2);
      border-radius: var(--r-sm);
      padding: 0 12px;
      font-size: 13px;
      color: var(--text);
      transition: all .15s ease;
    }
    .audit-filter-input:focus {
      border-color: var(--text);
      box-shadow: 0 0 0 2px var(--border-2);
    }

    /* ── Table Styling ── */
    .audit-table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
    }
    .audit-table th {
      background: var(--bg-3);
      color: var(--text-2);
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      padding: 12px 18px;
      border-bottom: 1.5px solid var(--border-2);
      white-space: nowrap;
    }
    .audit-table td {
      padding: 14px 18px;
      border-bottom: 1px solid var(--border);
      font-size: 13px;
      vertical-align: middle;
      color: var(--text);
    }
    .audit-table tr:hover td {
      background: var(--surface);
    }

    /* ── Badges ── */
    .badge-aksi {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 0.3px;
      padding: 4px 10px;
      border-radius: 20px;
      white-space: nowrap;
      border: 1px solid transparent;
    }
    .badge-create        { background: rgba(34, 197, 94, 0.12);  color: #16A34A; border-color: rgba(34, 197, 94, 0.25); }
    .badge-update        { background: rgba(59, 130, 246, 0.12); color: #2563EB; border-color: rgba(59, 130, 246, 0.25); }
    .badge-delete        { background: rgba(239, 68, 68, 0.12);  color: #DC2626; border-color: rgba(239, 68, 68, 0.25); }
    .badge-transisi      { background: rgba(168, 85, 247, 0.12); color: #9333EA; border-color: rgba(168, 85, 247, 0.25); }
    .badge-koreksi       { background: rgba(245, 158, 11, 0.12); color: #D97706; border-color: rgba(245, 158, 11, 0.25); }
    .badge-scan          { background: rgba(6, 182, 212, 0.12);  color: #0891B2; border-color: rgba(6, 182, 212, 0.25); }
    .badge-login         { background: rgba(234, 179, 8, 0.12);  color: #CA8A04; border-color: rgba(234, 179, 8, 0.25); }
    .badge-face          { background: rgba(99, 102, 241, 0.12); color: #4F46E5; border-color: rgba(99, 102, 241, 0.25); }
    .badge-logout        { background: rgba(107, 114, 128, 0.12);color: #4B5563; border-color: rgba(107, 114, 128, 0.25); }
    .badge-gerbang-buka  { background: rgba(16, 185, 129, 0.14); color: #059669; border-color: rgba(16, 185, 129, 0.30); }
    .badge-gerbang-tutup { background: rgba(244, 63, 94, 0.14);  color: #E11D48; border-color: rgba(244, 63, 94, 0.30); }
    .badge-default       { background: var(--bg-3); color: var(--text-2); border-color: var(--border-2); }

    [data-theme="dark"] .badge-create        { color: #4ADE80 !important; background: rgba(74, 222, 128, 0.16) !important; border-color: rgba(74, 222, 128, 0.3) !important; }
    [data-theme="dark"] .badge-update        { color: #60A5FA !important; background: rgba(96, 165, 250, 0.16) !important; border-color: rgba(96, 165, 250, 0.3) !important; }
    [data-theme="dark"] .badge-delete        { color: #F87171 !important; background: rgba(248, 113, 113, 0.16) !important; border-color: rgba(248, 113, 113, 0.3) !important; }
    [data-theme="dark"] .badge-transisi      { color: #C084FC !important; background: rgba(192, 132, 252, 0.16) !important; border-color: rgba(192, 132, 252, 0.3) !important; }
    [data-theme="dark"] .badge-koreksi       { color: #FBBF24 !important; background: rgba(251, 191, 36, 0.16) !important; border-color: rgba(251, 191, 36, 0.3) !important; }
    [data-theme="dark"] .badge-scan          { color: #22D3EE !important; background: rgba(34, 211, 238, 0.16) !important; border-color: rgba(34, 211, 238, 0.3) !important; }
    [data-theme="dark"] .badge-login         { color: #FDE047 !important; background: rgba(253, 224, 71, 0.16) !important; border-color: rgba(253, 224, 71, 0.3) !important; }
    [data-theme="dark"] .badge-face          { color: #818CF8 !important; background: rgba(129, 140, 248, 0.16) !important; border-color: rgba(129, 140, 248, 0.3) !important; }
    [data-theme="dark"] .badge-logout        { color: #9CA3AF !important; background: rgba(156, 163, 175, 0.16) !important; border-color: rgba(156, 163, 175, 0.3) !important; }
    [data-theme="dark"] .badge-gerbang-buka  { color: #34D399 !important; background: rgba(52, 211, 153, 0.16) !important; border-color: rgba(52, 211, 153, 0.3) !important; }
    [data-theme="dark"] .badge-gerbang-tutup { color: #FB7185 !important; background: rgba(251, 113, 133, 0.16) !important; border-color: rgba(251, 113, 133, 0.3) !important; }

    .modul-capsule {
      display: inline-flex;
      align-items: center;
      padding: 3px 8px;
      border-radius: 6px;
      font-size: 10.5px;
      font-weight: 800;
      font-family: var(--font-mono);
      background: var(--bg-3);
      color: var(--text-2);
      border: 1px solid var(--border-2);
      text-transform: uppercase;
    }

    .btn-detail-icon {
      width: 32px;
      height: 32px;
      border-radius: 8px;
      background: var(--bg-3);
      border: 1px solid var(--border-2);
      color: var(--text);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      transition: all .15s ease;
      font-size: 14px;
    }
    .btn-detail-icon:hover {
      background: var(--text);
      color: var(--bg);
      border-color: var(--text);
      transform: scale(1.05);
    }
  </style>
</head>
<body>
<div class="app-container">
  @include('partials.sidebar')

  <main class="main-content">
    {{-- ULTRA COMPACT SLIM HEADER BAR --}}
    <div class="panel no-print" style="background:var(--bg-2); border:1px solid var(--border); padding:10px 16px; margin-bottom:12px; border-radius:var(--r-md); box-shadow:var(--shadow-sm);">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          <h1 style="margin:0; font-size:16px; font-weight:900; color:var(--text); display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-shield-lock-fill" style="color:#000000; font-size:16px;"></i> Audit Trail &amp; Log Aktivitas
          </h1>
          <span style="color:var(--border-2); font-weight:300;">|</span>
          <span style="font-size:11.5px; color:var(--text-3);">
            Rekam jejak mutasi data, sesi smart gate, &amp; keamanan sistem
          </span>
        </div>

        <div style="display:flex; align-items:center; gap:8px;">
          @include('partials.header_actions')
        </div>
      </div>
    </div>

    {{-- 3 Executive KPI Cards --}}
    @php
      $totalHariIni = \App\Models\AuditLog::whereDate('created_at', today())->count();
      $totalMingguIni = \App\Models\AuditLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
      $totalAll = \App\Models\AuditLog::count();
    @endphp
    <div class="audit-kpi-grid">
      <div class="audit-kpi-card">
        <div class="audit-kpi-info">
          <div class="audit-kpi-lbl">Log Hari Ini</div>
          <div class="audit-kpi-val">{{ number_format($totalHariIni) }}</div>
        </div>
        <div class="audit-kpi-icon">
          <i class="bi bi-calendar2-day-fill"></i>
        </div>
      </div>

      <div class="audit-kpi-card">
        <div class="audit-kpi-info">
          <div class="audit-kpi-lbl">Log Minggu Ini</div>
          <div class="audit-kpi-val">{{ number_format($totalMingguIni) }}</div>
        </div>
        <div class="audit-kpi-icon">
          <i class="bi bi-calendar2-week-fill"></i>
        </div>
      </div>

      <div class="audit-kpi-card">
        <div class="audit-kpi-info">
          <div class="audit-kpi-lbl">Total Riwayat Log</div>
          <div class="audit-kpi-val">{{ number_format($totalAll) }}</div>
        </div>
        <div class="audit-kpi-icon">
          <i class="bi bi-database-fill-check"></i>
        </div>
      </div>
    </div>

    {{-- Panel Utama Audit Trail --}}
    <div class="audit-panel">
      <div class="audit-panel-header">
        <div class="audit-panel-title">
          <i class="bi bi-list-columns-reverse"></i>
          <span>Daftar Riwayat Aktivitas &amp; Integritas Data</span>
        </div>
        <div style="font-size:12px; color:var(--text-3); font-family:var(--font-mono);">
          Menampilkan <strong>{{ $logs->count() }}</strong> dari <strong>{{ $logs->total() }}</strong> entri log
        </div>
      </div>

      {{-- Toolbar Filter Terpadu --}}
      <form method="GET" action="{{ route('audit.index') }}" class="audit-filter-bar">
        <div>
          <input 
            type="text" 
            name="cari" 
            placeholder="Cari kata kunci deskripsi log..." 
            value="{{ $filters['cari'] ?? '' }}" 
            class="audit-filter-input" 
          />
        </div>

        <div>
          <select name="modul" class="audit-filter-input" onchange="this.form.submit()">
            <option value="">Semua Modul</option>
            @foreach($modulOptions as $m)
              <option value="{{ $m }}" @selected(($filters['modul'] ?? '') === $m)>{{ strtoupper($m) }}</option>
            @endforeach
          </select>
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
          <select name="user_id" class="audit-filter-input" onchange="this.form.submit()">
            <option value="">Semua Pengguna</option>
            @foreach($users as $u)
              <option value="{{ $u->id }}" @selected(($filters['user_id'] ?? '') == $u->id)>{{ $u->name }}</option>
            @endforeach
          </select>
        </div>

        <div style="display: flex; gap: 8px;">
          <button type="submit" class="btn btn-primary" style="height:38px; padding:0 14px; font-size:12.5px; font-weight:800; display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-search"></i> Filter
          </button>

          @if(!empty($filters['cari']) || !empty($filters['modul']) || !empty($filters['aksi']) || !empty($filters['user_id']) || !empty($filters['dari']) || !empty($filters['sampai']))
            <a href="{{ route('audit.index') }}" class="btn btn-outline" style="height:38px; padding:0 12px; font-size:12.5px; font-weight:700; color:var(--red); border-color:rgba(239,68,68,0.4);" title="Reset Filter">
              <i class="bi bi-x-circle"></i>
            </a>
          @endif
        </div>
      </form>

      {{-- Tabel Riwayat --}}
      <div style="overflow-x:auto;">
        <table class="audit-table">
          <thead>
            <tr>
              <th style="width: 150px;">Waktu &amp; Tanggal</th>
              <th style="width: 140px;">Aksi</th>
              <th style="width: 110px;">Modul</th>
              <th>Deskripsi Aktivitas</th>
              <th style="width: 220px;">Pengguna &amp; IP Address</th>
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
                  <span class="modul-capsule">{{ $log->modul }}</span>
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
                    <div style="display: inline-flex; align-items: center; gap: 4px; font-family: var(--font-mono); font-size: 10.5px; color: var(--text-3); margin-top: 2px; background: var(--bg-3); padding: 1px 6px; border-radius: 4px; border: 1px solid var(--border-2);">
                      <i class="bi bi-camera-video-fill"></i> Smart Gate Kiosk
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
                  <i class="bi bi-shield-check" style="font-size: 40px; display: block; margin-bottom: 12px; color: var(--text-3);"></i>
                  <div style="font-weight: 700; font-size: 14px; color: var(--text-2); margin-bottom: 4px;">Tidak Ada Log Aktivitas</div>
                  <p style="font-size: 12px; color: var(--text-3); margin: 0;">Tidak ditemukan catatan log dengan kriteria filter saat ini.</p>
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
