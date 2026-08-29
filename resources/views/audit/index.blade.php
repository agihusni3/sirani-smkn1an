<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Audit Trail — SIRANI</title>
  @include('partials.styles')
  <style>
    .audit-filters {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-md);
      padding: 18px 20px;
      margin-bottom: 24px;
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      align-items: flex-end;
    }
    .audit-filters .f-group {
      display: flex;
      flex-direction: column;
      gap: 5px;
      flex: 1;
      min-width: 150px;
    }
    .audit-filters label {
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--text-2);
    }
    .audit-filters select,
    .audit-filters input {
      background: var(--bg-3);
      border: 1px solid var(--border-2);
      color: var(--text);
      padding: 8px 12px;
      border-radius: var(--r-sm);
      font-size: 13px;
    }
    .audit-table { width: 100%; border-collapse: collapse; }
    .audit-table th {
      text-align: left;
      font-size: 10.5px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--text-2);
      padding: 10px 14px;
      border-bottom: 1px solid var(--border-2);
      background: var(--bg-3);
    }
    .audit-table td {
      padding: 11px 14px;
      border-bottom: 1px solid var(--border);
      font-size: 13px;
      vertical-align: top;
    }
    .audit-table tr:hover td { background: var(--surface); }

    /* Badge Aksi */
    .badge-aksi {
      display: inline-block;
      font-size: 10.5px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.4px;
      padding: 3px 9px;
      border-radius: 20px;
      white-space: nowrap;
    }
    .badge-create   { background: rgba(22,163,74,0.12);  color: #16A34A; }
    .badge-update   { background: rgba(59,130,246,0.12); color: #2563EB; }
    .badge-delete   { background: rgba(220,38,38,0.1);   color: #DC2626; }
    .badge-transisi { background: rgba(139,92,246,0.12); color: #7C3AED; }
    .badge-koreksi  { background: rgba(245,158,11,0.12); color: #D97706; }
    .badge-scan     { background: rgba(20,184,166,0.12); color: #0D9488; }
    .badge-login    { background: rgba(202,138,4,0.12);  color: #CA8A04; }
    .badge-logout   { background: rgba(100,116,139,0.1); color: #64748B; }
    .badge-default  { background: var(--bg-3); color: var(--text-2); }

    .modul-badge {
      display: inline-block;
      font-size: 10px;
      font-weight: 700;
      padding: 2px 8px;
      border-radius: 6px;
      background: var(--bg-3);
      color: var(--text-2);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .detail-link {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      font-size: 12px;
      color: var(--gold);
      text-decoration: none;
      font-weight: 600;
    }
    .detail-link:hover { text-decoration: underline; }

    .stat-strip {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin-bottom: 20px;
    }
    .stat-chip {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-sm);
      padding: 10px 16px;
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 13px;
    }
    .stat-chip .val {
      font-size: 20px;
      font-weight: 900;
      color: var(--gold);
    }
    .stat-chip .lbl { color: var(--text-2); font-size: 12px; }
  </style>
<body>
<div class="app-container">
  @include('partials.sidebar')

  <main class="main-content">
    {{-- Header --}}
    <header class="header" style="margin-bottom:20px;">
      <div class="header-title">
        <h1 style="margin:0; font-size:22px; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-shield-lock-fill" style="color:var(--gold);"></i> Audit Trail &amp; Log Aktivitas
        </h1>
        <p style="margin-top:2px; font-size:13px; color:var(--text-3);">
          Riwayat seluruh perubahan data, transisi akademik, koreksi absensi, dan aktivitas sistem.
        </p>
      </div>
      @include('partials.header_actions')
    </header>

    {{-- Stat Strip --}}
    @php
      $totalHariIni = \App\Models\AuditLog::whereDate('created_at', today())->count();
      $totalMingguIni = \App\Models\AuditLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
      $totalAll = \App\Models\AuditLog::count();
    @endphp
    <div class="stat-strip">
      <div class="stat-chip">
        <i class="bi bi-calendar-day" style="color:var(--gold); font-size:18px;"></i>
        <div>
          <div class="val">{{ $totalHariIni }}</div>
          <div class="lbl">Log hari ini</div>
        </div>
      </div>
      <div class="stat-chip">
        <i class="bi bi-calendar-week" style="color:#3B82F6; font-size:18px;"></i>
        <div>
          <div class="val">{{ $totalMingguIni }}</div>
          <div class="lbl">Minggu ini</div>
        </div>
      </div>
      <div class="stat-chip">
        <i class="bi bi-database" style="color:#10B981; font-size:18px;"></i>
        <div>
          <div class="val">{{ number_format($totalAll) }}</div>
          <div class="lbl">Total log</div>
        </div>
      </div>
    </div>

    {{-- Tabel Log & Filter Terpadu --}}
    <div class="panel" style="overflow:hidden; padding:0; border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-sm); background:var(--bg-2); margin-bottom:24px;">
      <div style="padding:14px 18px; border-bottom:1px solid var(--border); background:var(--surface); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="font-weight:800; font-size:15px; color:var(--text); display:flex; align-items:center; gap:8px;">
          <i class="bi bi-shield-lock-fill" style="color:var(--gold);"></i>
          <span>Daftar Riwayat Aktivitas &amp; Audit Trail</span>
        </div>
      </div>

      {{-- Toolbar Filter Terpadu --}}
      <div style="padding:12px 18px; border-bottom:1px solid var(--border); background:var(--surface);">
        <form method="GET" action="{{ route('audit.index') }}" style="display:flex; flex-wrap:wrap; gap:8px; align-items:center;">
          <div style="flex:1.5; min-width:180px;">
            <input type="text" name="cari" placeholder="Cari deskripsi log..." value="{{ $filters['cari'] ?? '' }}" class="input-field" style="width:100%; height:36px; font-size:12px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px;" />
          </div>

          <div style="min-width:130px;">
            <select name="modul" class="input-field" style="width:100%; height:36px; font-size:12px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm);" onchange="this.form.submit()">
              <option value="">Semua Modul</option>
              @foreach($modulOptions as $m)
                <option value="{{ $m }}" @selected(($filters['modul'] ?? '') === $m)>{{ ucfirst($m) }}</option>
              @endforeach
            </select>
          </div>

          <div style="min-width:130px;">
            <select name="aksi" class="input-field" style="width:100%; height:36px; font-size:12px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm);" onchange="this.form.submit()">
              <option value="">Semua Aksi</option>
              @foreach($aksiOptions as $a)
                <option value="{{ $a }}" @selected(($filters['aksi'] ?? '') === $a)>{{ ucfirst($a) }}</option>
              @endforeach
            </select>
          </div>

          <div style="min-width:140px;">
            <select name="user_id" class="input-field" style="width:100%; height:36px; font-size:12px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm);" onchange="this.form.submit()">
              <option value="">Semua Pengguna</option>
              @foreach($users as $u)
                <option value="{{ $u->id }}" @selected(($filters['user_id'] ?? '') == $u->id)>{{ $u->name }}</option>
              @endforeach
            </select>
          </div>

          <button type="submit" class="btn btn-outline" style="height:36px; padding:0 12px; font-size:12px; font-weight:700;">
            <i class="bi bi-search"></i> Cari
          </button>

          @if(!empty($filters['cari']) || !empty($filters['modul']) || !empty($filters['aksi']) || !empty($filters['user_id']) || !empty($filters['dari']) || !empty($filters['sampai']))
            <a href="{{ route('audit.index') }}" class="btn btn-outline" style="height:36px; padding:0 10px; font-size:12px; color:var(--red); border-color:rgba(239,68,68,0.4);" title="Reset Filter">
              Reset
            </a>
          @endif
        </form>
      </div>

      <div style="overflow-x:auto;">
        <table class="audit-table">
          <thead>
            <tr>
              <th style="width:160px;">Waktu</th>
              <th style="width:100px;">Aksi</th>
              <th style="width:90px;">Modul</th>
              <th>Deskripsi</th>
              <th style="width:140px;">Pengguna</th>
              <th style="width:60px;"></th>
            </tr>
          </thead>
          <tbody>
            @forelse($logs as $log)
              <tr>
                <td style="white-space:nowrap; color:var(--text-2); font-size:12px;">
                  {{ $log->created_at->timezone('Asia/Jakarta')->format('d M Y') }}<br>
                  <span style="color:var(--text-3);">{{ $log->created_at->timezone('Asia/Jakarta')->format('H:i:s') }} WIB</span>
                </td>
                <td>
                  <span class="badge-aksi {{ $log->badgeClass() }}">{{ $log->aksiLabel() }}</span>
                </td>
                <td>
                  <span class="modul-badge">{{ $log->modul }}</span>
                </td>
                <td style="color:var(--text); line-height:1.5;">
                  {{ $log->deskripsi }}
                  @if($log->target_type && $log->target_id)
                    <br><span style="font-size:11px; color:var(--text-3);">{{ $log->target_type }} #{{ $log->target_id }}</span>
                  @endif
                </td>
                <td>
                  @if($log->user)
                    <div style="font-size:13px; font-weight:600;">{{ $log->user->name }}</div>
                    <div style="font-size:11px; color:var(--text-3);">{{ $log->ip_address }}</div>
                  @else
                    <span style="color:var(--text-3); font-size:12px;">Sistem / Face ID</span>
                  @endif
                </td>
                <td>
                  @if($log->data_lama || $log->data_baru)
                    <a href="{{ route('audit.show', $log->id) }}" class="detail-link" title="Lihat perubahan data">
                      <i class="bi bi-eye"></i>
                    </a>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="text-align:center; padding:40px; color:var(--text-3);">
                  <i class="bi bi-inbox" style="font-size:32px; display:block; margin-bottom:8px;"></i>
                  Tidak ada log yang sesuai filter.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if($logs->hasPages())
        <div style="padding:16px 20px; border-top:1px solid var(--border);">
          {{ $logs->links('partials.pagination') }}
        </div>
      @endif
    </div>

  </main>
</div>
</body>
</html>
