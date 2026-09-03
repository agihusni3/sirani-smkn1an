<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Detail Audit Log #{{ $log->id }} — SIRANI</title>
  @include('partials.styles')
  <style>
    .meta-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 14px;
      margin-bottom: 24px;
    }
    .meta-item {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-md);
      padding: 14px 18px;
      box-shadow: var(--shadow-sm);
    }
    .meta-item .k {
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      color: var(--text-3);
      margin-bottom: 6px;
    }
    .meta-item .v {
      font-size: 14px;
      font-weight: 700;
      color: var(--text);
      word-break: break-word;
    }

    .diff-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 13px;
    }
    .diff-table th {
      text-align: left;
      padding: 12px 18px;
      background: var(--bg-3);
      border-bottom: 1.5px solid var(--border-2);
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      color: var(--text-2);
      width: 33%;
    }
    .diff-table td {
      padding: 12px 18px;
      border-bottom: 1px solid var(--border);
      vertical-align: top;
      color: var(--text);
    }
    .diff-table tr:hover td {
      background: var(--surface);
    }
    .diff-table .field-key {
      font-family: var(--font-mono);
      font-size: 12.5px;
      color: var(--text);
      font-weight: 700;
    }

    .val-old {
      color: #DC2626;
      background: rgba(239, 68, 68, 0.06);
      padding: 2px 6px;
      border-radius: 4px;
      font-family: var(--font-mono);
      font-size: 12px;
    }
    .val-new {
      color: #16A34A;
      background: rgba(34, 197, 94, 0.08);
      padding: 2px 6px;
      border-radius: 4px;
      font-family: var(--font-mono);
      font-size: 12px;
    }
    [data-theme="dark"] .val-old {
      color: #F87171 !important;
      background: rgba(248, 113, 113, 0.15) !important;
    }
    [data-theme="dark"] .val-new {
      color: #4ADE80 !important;
      background: rgba(74, 222, 128, 0.15) !important;
    }
    .val-same {
      color: var(--text-3);
      font-family: var(--font-mono);
      font-size: 12px;
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
  </style>
</head>
<body>
<div class="app-container">
  @include('partials.sidebar')

  <main class="main-content">
    {{-- Header --}}
    <header class="header" style="margin-bottom: 20px;">
      <div class="header-title">
        <div style="font-size: 12px; margin-bottom: 6px;">
          <a href="{{ route('audit.index') }}" style="color: var(--text-3); text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
            <i class="bi bi-arrow-left"></i> Kembali ke Audit Trail
          </a>
        </div>
        <h1 style="margin:0; font-size:22px; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-shield-lock-fill"></i> Detail Rekam Audit Log #{{ $log->id }}
        </h1>
        <p style="margin-top:2px; font-size:13px; color:var(--text-3);">{{ $log->deskripsi }}</p>
      </div>
      @include('partials.header_actions')
    </header>

    {{-- Meta Grid --}}
    <div class="meta-grid">
      <div class="meta-item">
        <div class="k">Aksi Sistem</div>
        <div class="v">
          <span class="badge-aksi {{ $log->badgeClass() }}">{{ $log->aksiLabel() }}</span>
        </div>
      </div>
      <div class="meta-item">
        <div class="k">Modul</div>
        <div class="v" style="text-transform: uppercase;">{{ $log->modul }}</div>
      </div>
      <div class="meta-item">
        <div class="k">Target Objek</div>
        <div class="v">
          {{ $log->target_type ?? '—' }}
          @if($log->target_id) <span style="color:var(--text-3); font-family:var(--font-mono);">#{{ $log->target_id }}</span>@endif
        </div>
      </div>
      <div class="meta-item">
        <div class="k">Pelaku / Pengguna</div>
        <div class="v">{{ $log->user?->name ?? 'Sistem Otomatis' }}</div>
      </div>
      <div class="meta-item">
        <div class="k">Waktu Eksekusi</div>
        <div class="v" style="font-family: var(--font-mono); font-size: 13px;">
          {{ $log->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i:s') }} WIB
        </div>
      </div>
      <div class="meta-item">
        <div class="k">IP Address</div>
        <div class="v" style="font-family: var(--font-mono); font-size: 13px;">{{ $log->ip_address ?? '—' }}</div>
      </div>
    </div>

    {{-- Diff Data --}}
    @if($log->data_lama || $log->data_baru)
      <div class="panel" style="overflow:hidden; padding:0; margin-bottom:24px; border:1px solid var(--border); border-radius:var(--r-md); background:var(--bg-2); box-shadow:var(--shadow-sm);">
        <div style="padding:16px 20px; border-bottom:1px solid var(--border); font-weight:800; font-size:14px; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-arrow-left-right"></i>
          Perubahan Data Rinci (Payload Diff)
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
                <th>Nama Field / Kolom</th>
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
                  <td>
                    <span class="{{ $berubah ? 'val-old' : 'val-same' }}">
                      {{ is_array($valLama) ? json_encode($valLama) : ($valLama ?? '—') }}
                    </span>
                  </td>
                  <td>
                    <span class="{{ $berubah ? 'val-new' : 'val-same' }}">
                      {{ is_array($valBaru) ? json_encode($valBaru) : ($valBaru ?? '—') }}
                    </span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @endif

    <div style="margin-top:20px;">
      <a href="{{ route('audit.index') }}" class="btn btn-outline" style="font-weight:800; display:inline-flex; align-items:center; gap:6px;">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Log
      </a>
    </div>

  </main>
</div>
</body>
</html>
