<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Detail Audit Log #{{ $log->id }} — SIRANI</title>
  @include('partials.styles')
  <style>
    .diff-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .diff-table th {
      text-align: left;
      padding: 9px 14px;
      background: var(--bg-3);
      border-bottom: 1px solid var(--border-2);
      font-size: 10.5px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--text-2);
      width: 33%;
    }
    .diff-table td {
      padding: 10px 14px;
      border-bottom: 1px solid var(--border);
      vertical-align: top;
    }
    .diff-table .field-key {
      font-family: monospace;
      font-size: 12.5px;
      color: var(--text-2);
      font-weight: 700;
    }
    .val-old { color: #DC2626; }
    .val-new  { color: #16A34A; }
    .val-same { color: var(--text-2); }
    .badge-aksi {
      display: inline-block;
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.4px;
      padding: 4px 12px;
      border-radius: 20px;
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
    .meta-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 12px;
      margin-bottom: 24px;
    }
    .meta-item {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-sm);
      padding: 12px 16px;
    }
    .meta-item .k {
      font-size: 10.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--text-3);
      margin-bottom: 4px;
    }
    .meta-item .v {
      font-size: 14px;
      font-weight: 700;
      color: var(--text);
    }
  </style>
</head>
<body>
<div class="app-container">
  @include('partials.sidebar')

  <main class="main-content">
    {{-- Header --}}
    <header class="header" style="margin-bottom:20px;">
      <div class="header-title">
        <div style="font-size:12px; margin-bottom:4px;">
          <a href="{{ route('audit.index') }}" style="color:var(--text-3); text-decoration:none; font-weight:700;">
            <i class="bi bi-arrow-left"></i> Kembali ke Audit Trail
          </a>
        </div>
        <h1 style="margin:0; font-size:22px; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-shield-lock-fill" style="color:var(--gold);"></i> Detail Log #{{ $log->id }}
        </h1>
        <p style="margin-top:2px; font-size:13px; color:var(--text-3);">{{ $log->deskripsi }}</p>
      </div>
      @include('partials.header_actions')
    </header>

    {{-- Meta Grid --}}
    <div class="meta-grid">
      <div class="meta-item">
        <div class="k">Aksi</div>
        <div class="v">
          <span class="badge-aksi {{ $log->badgeClass() }}">{{ $log->aksiLabel() }}</span>
        </div>
      </div>
      <div class="meta-item">
        <div class="k">Modul</div>
        <div class="v" style="text-transform:uppercase; font-size:13px;">{{ $log->modul }}</div>
      </div>
      <div class="meta-item">
        <div class="k">Target</div>
        <div class="v" style="font-size:13px;">
          {{ $log->target_type ?? '—' }}
          @if($log->target_id) <span style="color:var(--text-3);">#{{ $log->target_id }}</span>@endif
        </div>
      </div>
      <div class="meta-item">
        <div class="k">Pengguna</div>
        <div class="v" style="font-size:13px;">{{ $log->user?->name ?? 'Sistem / Presensi AI' }}</div>
      </div>
      <div class="meta-item">
        <div class="k">Waktu</div>
        <div class="v" style="font-size:13px;">
          {{ $log->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i:s') }} WIB
        </div>
      </div>
      <div class="meta-item">
        <div class="k">IP Address</div>
        <div class="v" style="font-size:13px; font-family:monospace;">{{ $log->ip_address ?? '—' }}</div>
      </div>
    </div>

    {{-- Diff Data --}}
    @if($log->data_lama || $log->data_baru)
      <div class="panel" style="overflow:hidden; padding:0; margin-bottom:24px;">
        <div style="padding:14px 20px; border-bottom:1px solid var(--border); font-weight:700; font-size:14px;">
          <i class="bi bi-arrow-left-right" style="color:var(--gold); margin-right:6px;"></i>
          Perubahan Data Rinci
        </div>
        @php
          $lama = $log->data_lama ?? [];
          $baru = $log->data_baru ?? [];
          $keys = array_unique(array_merge(array_keys($lama), array_keys($baru)));
          sort($keys);
        @endphp
        <div style="overflow-x:auto;">
          <table class="diff-table">
            <thead>
              <tr>
                <th>Nama Field</th>
                <th style="color:#DC2626;">Data Sebelum</th>
                <th style="color:#16A34A;">Data Sesudah</th>
              </tr>
            </thead>
            <tbody>
              @foreach($keys as $key)
                @php
                  $valLama = $lama[$key] ?? null;
                  $valBaru = $baru[$key] ?? null;
                  $berubah = $valLama !== $valBaru;
                @endphp
                <tr>
                  <td class="field-key">{{ $key }}</td>
                  <td class="{{ $berubah ? 'val-old' : 'val-same' }}">
                    {{ is_array($valLama) ? json_encode($valLama) : ($valLama ?? '—') }}
                  </td>
                  <td class="{{ $berubah ? 'val-new' : 'val-same' }}">
                    {{ is_array($valBaru) ? json_encode($valBaru) : ($valBaru ?? '—') }}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @endif

    <div style="margin-top:16px;">
      <a href="{{ route('audit.index') }}" class="btn btn-outline" style="font-weight:700;">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Log
      </a>
    </div>

  </main>
</div>
</body>
</html>
