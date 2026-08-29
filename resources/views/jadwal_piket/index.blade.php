<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Jadwal Guru Piket — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <style>
    .day-column {
      background: var(--bg-2);
      border: 1.5px solid var(--border-2);
      border-radius: var(--r-md);
      padding: 16px;
      display: flex;
      flex-direction: column;
      gap: 12px;
      transition: all .2s;
    }
    .day-column.today-column {
      border-color: var(--gold);
      box-shadow: 0 0 16px var(--gold-glow);
      background: linear-gradient(180deg, rgba(202,138,4,0.06) 0%, var(--bg-2) 100%);
    }
    .day-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-bottom: 10px;
      border-bottom: 1px solid var(--border);
    }
    .day-name {
      font-size: 15px;
      font-weight: 800;
      color: var(--text);
    }
    .piket-card-item {
      background: var(--bg-3);
      border: 1px solid var(--border-2);
      border-radius: var(--r-sm);
      padding: 10px 12px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      transition: transform .15s;
    }
    .piket-card-item:hover {
      transform: translateY(-1px);
      border-color: var(--gold);
    }
  </style>
</head>
<body>

<div class="app-container">
  @include('partials.sidebar')

  <main class="main-content">
    <header class="header no-print">
      <div class="header-title">
        <h1>
          <i class="bi bi-calendar-week-fill" style="color:var(--gold); margin-right:8px;"></i>Jadwal Penugasan Guru Piket
        </h1>
        <p>Atur penugasan harian guru piket. Hanya guru yang bertugas pada hari tersebut yang berwenang mengelola meja piket harian.</p>
      </div>
      @include('partials.header_actions')
    </header>

    @if(session('success'))
      <div class="alert-success" style="margin-bottom:16px;">
        <i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="alert-error" style="margin-bottom:16px;">
        <i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i>{{ session('error') }}
      </div>
    @endif

    @php
      $currentUser = auth()->user();
      $canManagePiket = $currentUser && ($currentUser->isAdmin() || $currentUser->isWakaKesiswaan());
    @endphp

    <!-- Form Tambah Penugasan Piket (Hanya Admin & Waka Kesiswaan) -->
    @if($canManagePiket)
    <div class="panel" style="margin-bottom:24px;">
      <div class="panel-title">
        <span><i class="bi bi-person-plus-fill" style="color:var(--green); margin-right:6px;"></i>Tambah Guru ke Jadwal Piket</span>
      </div>
      <form action="/jadwal-piket" method="POST">
        @csrf
        <div class="form-row" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px 20px;">
          <div class="form-group">
            <label>Hari Bertugas <span style="color:var(--red);">*</span></label>
            <select name="hari" required style="width:100%; height:42px;">
              @foreach($hariList as $h)
                <option value="{{ $h }}" {{ $h === $hariHariIni ? 'selected' : '' }}>
                  Hari {{ $h }} {{ $h === $hariHariIni ? '(Hari Ini)' : '' }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Pilih Guru / Pegawai <span style="color:var(--red);">*</span></label>
            <select name="guru_id" required style="width:100%; height:42px;">
              <option value="">-- Pilih Guru --</option>
              @foreach($gurus as $g)
                <option value="{{ $g->id }}">{{ $g->nama }} ({{ $g->jabatan }})</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Keterangan / Peran</label>
            <input type="text" name="keterangan" placeholder="Opsional (Koordinator / Anggota)" style="width:100%; height:42px;" />
          </div>
          <div style="align-self: flex-end;">
            <button type="submit" class="btn btn-gold" style="width:100%; height:42px; display:inline-flex; align-items:center; justify-content:center; gap:8px; font-weight:800;">
              <i class="bi bi-save-fill"></i>Tugaskan Piket
            </button>
          </div>
        </div>
      </form>
    </div>
    @endif

    <!-- Board Penugasan 5 Hari (Senin s/d Jumat) -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
      @foreach($hariList as $hari)
        @php
          $isToday = ($hari === $hariHariIni);
          $listPiket = $jadwalGrouped->get($hari, collect());
        @endphp
        <div class="day-column {{ $isToday ? 'today-column' : '' }}">
          <div class="day-header">
            <div>
              <span class="day-name">Hari {{ $hari }}</span>
              @if($isToday)
                <span class="pulse-dot" style="margin-left:6px;" title="Hari Ini Aktif Bertugas"></span>
              @endif
            </div>
            <span style="font-family:var(--font-mono); font-size:11.5px; font-weight:700; color:var(--text-3); background:var(--bg-3); padding:2px 8px; border-radius:10px;">
              {{ $listPiket->count() }} Guru
            </span>
          </div>

          <div style="display:flex; flex-direction:column; gap:8px; min-height:80px;">
            @forelse($listPiket as $jp)
              <div class="piket-card-item">
                <div style="display:flex; align-items:center; gap:8px; flex:1; min-width:0;">
                  <div class="avatar-circle blue-border avatar-sm">
                    <img src="{{ $jp->guru->foto_url ?? '' }}" alt="{{ $jp->guru->nama ?? '-' }}" class="avatar-img" />
                  </div>
                  <div style="min-width:0; flex:1;">
                    <div style="font-weight:700; font-size:12.5px; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                      {{ $jp->guru->nama ?? '-' }}
                    </div>
                    <div style="font-size:10.5px; color:var(--text-3); font-family:var(--font-mono);">
                      {{ $jp->keterangan ?: ($jp->guru->jabatan ?? 'Guru Piket') }}
                    </div>
                  </div>
                </div>

                @if($canManagePiket)
                <form action="/jadwal-piket/{{ $jp->id }}" method="POST" onsubmit="return confirm('Hapus penugasan piket {{ $jp->guru->nama ?? '' }} hari {{ $hari }}?')" style="margin:0;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-icon btn-icon-danger" style="width:28px; height:28px; font-size:12px;" data-tooltip="Hapus Penugasan">
                    <i class="bi bi-trash3-fill"></i>
                  </button>
                </form>
                @endif
              </div>
            @empty
              <div style="text-align:center; padding:20px 10px; color:var(--text-3); font-size:12px; border:1px dashed var(--border-2); border-radius:var(--r-sm);">
                Belum ada guru piket ditugaskan
              </div>
            @endforelse
          </div>
        </div>
      @endforeach
    </div>

  </main>
</div>

</body>
</html>
