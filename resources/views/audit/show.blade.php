<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Detail Audit Log #{{ $log->id }} — SIRANI</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/audit.css') }}?v={{ filemtime(public_path('css/audit.css')) }}">
</head>
<body>
<div class="app-container">
  @include('partials.sidebar')

  <main class="main-content">
    {{-- Header --}}
    <header class="header" style="margin-bottom: 20px;">
      <div class="header-title">
        <div style="font-size: 12px; margin-bottom: 6px;">
          @php
            $prevUrl = url()->previous();
            $backText = 'Kembali ke Audit Trail Global';
            if (str_contains($prevUrl, 'situan')) {
                $backText = 'Kembali ke Log Modul SITUAN';
            } elseif (str_contains($prevUrl, 'ppdb')) {
                $backText = 'Kembali ke Log Modul PPDB';
            } elseif (str_contains($prevUrl, 'berita') || str_contains($prevUrl, 'banner')) {
                $backText = 'Kembali ke Log Modul Web Humas';
            }
          @endphp
          <a href="{{ $prevUrl ?: route('audit.index') }}" style="color: var(--text-3); text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
            <i class="bi bi-arrow-left"></i> {{ $backText }}
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
