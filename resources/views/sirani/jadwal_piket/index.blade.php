<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Jadwal Guru Piket — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/jadwal-piket.css') }}?v={{ filemtime(public_path('css/jadwal-piket.css')) }}">
</head>
<body>

<div class="app-container">
  @include('partials.sidebar')

  <main class="main-content">
    @php
      $currentUser = auth()->user();
      $canManagePiket = $currentUser && ($currentUser->isAdmin() || $currentUser->isWakaKesiswaan() || $currentUser->isWakaKurikulum());
    @endphp

    {{-- ULTRA COMPACT SLIM HEADER BAR --}}
    <div class="panel no-print" style="background:var(--bg-2); border:1px solid var(--border); padding:10px 16px; margin-bottom:12px; border-radius:var(--r-md); box-shadow:var(--shadow-sm);">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          <h1 style="margin:0; font-size:16px; font-weight:900; color:var(--text); display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-calendar-week-fill" style="color:#000000; font-size:16px;"></i> Jadwal Penugasan Guru Piket
          </h1>
          <span style="color:var(--border-2); font-weight:300;">|</span>
          <span style="font-size:11.5px; color:var(--text-3);">
            Plotting penugasan berkala Senin s/d Jumat
          </span>
        </div>

        <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
          @if($canManagePiket)
            <button type="button" onclick="toggleModalModeUjian(true)" class="btn btn-sm" style="height:32px; padding:0 12px; font-size:11.5px; font-weight:800; display:inline-flex; align-items:center; gap:5px; border-radius:6px; cursor:pointer; background:linear-gradient(135deg, #4f46e5, #7c3aed); color:#fff; border:none; box-shadow:0 2px 6px rgba(79, 70, 229, 0.25);">
              <i class="bi bi-mortarboard-fill"></i>
              <span>Mode Sumatif</span>
            </button>
            <button type="button" id="btnToggleFormPiket" onclick="toggleFormPiket()" class="btn btn-sm btn-gold" style="height:32px; padding:0 12px; font-size:11.5px; font-weight:800; display:inline-flex; align-items:center; gap:5px; border-radius:6px; cursor:pointer;">
              <i class="bi bi-person-plus-fill" id="iconTogglePiket"></i>
              <span id="textTogglePiket">Tambah Penugasan</span>
            </button>
          @endif
          @include('partials.header_actions')
        </div>
      </div>
    </div>

    {{-- Banner Mode Sumatif Aktif jika ada --}}
    @if($modeUjianAktif)
      <div class="panel no-print" style="margin-bottom:14px; background:linear-gradient(135deg, rgba(79, 70, 229, 0.08) 0%, rgba(147, 51, 234, 0.06) 100%); border:1.5px solid rgba(99, 102, 241, 0.3); border-radius:var(--r-md); padding:14px 18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; align-items:center; gap:12px;">
          <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, #4f46e5, #7c3aed); display:flex; align-items:center; justify-content:center; font-size:20px; color:#fff; flex-shrink:0;">
            <i class="bi bi-mortarboard-fill"></i>
          </div>
          <div>
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:2px; flex-wrap:wrap;">
              <strong style="font-size:14px; color:#3730a3;">
                MODE PEKAN SUMATIF AKTIF: {{ $modeUjianAktif->nama_ujian }}
              </strong>
              <span style="font-size:11px; padding:1px 7px; border-radius:5px; background:#4f46e5; color:#fff; font-weight:700;">
                {{ $modeUjianAktif->tipe }}
              </span>
              <span style="font-size:12px; color:#4338ca;">
                ({{ \Carbon\Carbon::parse($modeUjianAktif->tanggal_mulai)->locale('id')->isoFormat('D MMM') }} - {{ \Carbon\Carbon::parse($modeUjianAktif->tanggal_selesai)->locale('id')->isoFormat('D MMM Y') }})
              </span>
            </div>
            <div style="font-size:12px; color:var(--text-2);">
              <strong>Jam Pulang Sumatif: {{ substr($modeUjianAktif->jam_pulang_mulai, 0, 5) }} WIB</strong>
              • Jadwal piket reguler dinonaktifkan sementara dan digantikan oleh <span style="color:#4338ca; font-weight:700;">Panitia Sumatif ({{ $modeUjianAktif->daftar_panitia->count() }} Guru)</span> tanpa merusak plotting semester.
            </div>
          </div>
        </div>
        @if($canManagePiket)
          <div style="display:flex; gap:8px;">
            <button type="button" onclick="toggleModalModeUjian(true)" class="btn btn-sm btn-outline" style="font-size:11.5px; font-weight:700;">
              <i class="bi bi-pencil-square"></i> Kelola Mode Sumatif
            </button>
          </div>
        @endif
      </div>
    @endif

    @if(session('success'))
      <div class="alert-success" style="margin-bottom:12px;">
        <i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="alert-error" style="margin-bottom:12px;">
        <i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i>{{ session('error') }}
      </div>
    @endif

    <!-- Form Tambah Penugasan Piket (Collapsible / Toggle) -->
    @if($canManagePiket)
    <div class="panel" id="panelFormPiket" style="display:none; margin-bottom:24px; animation:fadeIn 0.25s ease;">
      <div class="panel-title" style="display:flex; justify-content:space-between; align-items:center;">
        <span><i class="bi bi-person-plus-fill" style="color:#000000; margin-right:6px;"></i>Tambah Guru ke Jadwal Piket</span>
        <button type="button" onclick="toggleFormPiket(false)" class="btn btn-outline" style="height:30px; width:30px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:6px; color:var(--text-3);" title="Tutup Form">
          <i class="bi bi-x-lg"></i>
        </button>
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
          <div style="align-self: flex-end; display:flex; gap:8px;">
            <button type="button" onclick="toggleFormPiket(false)" class="btn btn-outline" style="height:42px; padding:0 14px; font-weight:700;">
              Batal
            </button>
            <button type="submit" class="btn" style="background:#000000; color:#FFFFFF; border:1.5px solid #000000; flex:1; height:42px; display:inline-flex; align-items:center; justify-content:center; gap:8px; font-weight:800; border-radius:6px; cursor:pointer;">
              <i class="bi bi-save-fill" style="color:#FFFFFF;"></i>Tugaskan Piket
            </button>
          </div>
        </div>
      </form>
    </div>
    @endif

    <!-- Board Penugasan 5 Hari (Senin s/d Jumat) -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin-bottom:32px;">
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
                <span class="pulse-dot" style="margin-left:6px; background:#000000;" title="Hari Ini Aktif Bertugas"></span>
              @endif
            </div>
            <span style="font-family:var(--font-mono); font-size:11.5px; font-weight:700; color:var(--text); background:var(--bg-3); border:1px solid var(--border-2); padding:2px 8px; border-radius:6px;">
              {{ $listPiket->count() }} Guru
            </span>
          </div>

          <div class="piket-list" style="display:flex; flex-direction:column; gap:8px; min-height:80px;">
            @forelse($listPiket as $jp)
              <div class="piket-card-item">
                <div style="display:flex; align-items:center; gap:8px; flex:1; min-width:0;">
                  <div class="avatar-circle avatar-sm">
                    <img src="{{ $jp->guru->foto_url ?? '/img/logo.png' }}" alt="{{ $jp->guru->nama ?? '-' }}" class="avatar-img" />
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
                    <i class="bi bi-trash3"></i>
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

{{-- MODAL PENGATURAN MODE SUMATIF (STS / SAS) --}}
@if($canManagePiket)
<div class="modal-overlay" id="modalModeUjianJadwal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
  <div class="modal-card" style="background:var(--bg-2); border-radius:12px; width:92%; max-width:680px; padding:24px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.3); max-height:90vh; overflow-y:auto; border:1px solid var(--border);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--border); padding-bottom:10px;">
      <div>
        <h3 style="font-size:16px; font-weight:900; color:var(--text); margin:0; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-mortarboard-fill" style="color:#4f46e5;"></i> Konfigurasi Mode Pekan Sumatif (STS / SAS)
        </h3>
        <div style="font-size:11.5px; color:var(--text-3); margin-top:2px;">Atur tanggal ujian, jam pulang Smart Gate, dan penugasan Panitia Sumatif</div>
      </div>
      <button type="button" class="btn btn-sm btn-outline" onclick="toggleModalModeUjian(false)"><i class="bi bi-x-lg"></i></button>
    </div>

    <form action="{{ route('mode-ujian.simpan') }}" method="POST">
      @csrf
      @if($modeUjianAktif)
        <input type="hidden" name="id" value="{{ $modeUjianAktif->id }}">
      @endif

      <div style="display:grid; grid-template-columns: 2fr 1fr; gap:14px; margin-bottom:14px;">
        <div class="form-group">
          <label style="font-size:11.5px; font-weight:800; text-transform:uppercase; color:var(--text); margin-bottom:5px; display:block;">
            Nama Pelaksanaan Ujian <span style="color:var(--red);">*</span>
          </label>
          <input type="text" name="nama_ujian" value="{{ $modeUjianAktif->nama_ujian ?? 'Sumatif Tengah Semester (STS) Ganjil TP 2026/2027' }}" required class="form-control" style="width:100%; height:38px; border-radius:6px; border:1px solid var(--border-2); padding:0 10px; font-size:13px;" />
        </div>
        <div class="form-group">
          <label style="font-size:11.5px; font-weight:800; text-transform:uppercase; color:var(--text); margin-bottom:5px; display:block;">
            Tipe Ujian <span style="color:var(--red);">*</span>
          </label>
          <select name="tipe" class="form-control" style="width:100%; height:38px; border-radius:6px; border:1px solid var(--border-2); padding:0 10px; font-size:13px;">
            @php $curTipe = $modeUjianAktif->tipe ?? 'STS'; @endphp
            <option value="STS" {{ $curTipe === 'STS' ? 'selected' : '' }}>STS (Sumatif Tengah Semester)</option>
            <option value="SAS" {{ $curTipe === 'SAS' ? 'selected' : '' }}>SAS (Sumatif Akhir Semester)</option>
            <option value="SAT" {{ $curTipe === 'SAT' ? 'selected' : '' }}>SAT (Sumatif Akhir Tahun)</option>
            <option value="USBK" {{ $curTipe === 'USBK' ? 'selected' : '' }}>USBK / US</option>
            <option value="Lainnya" {{ $curTipe === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
          </select>
        </div>
      </div>

      <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:14px; margin-bottom:14px;">
        <div class="form-group">
          <label style="font-size:11.5px; font-weight:800; text-transform:uppercase; color:var(--text); margin-bottom:5px; display:block;">
            Tanggal Mulai <span style="color:var(--red);">*</span>
          </label>
          <input type="date" name="tanggal_mulai" value="{{ $modeUjianAktif->tanggal_mulai ?? '2026-09-21' }}" required class="form-control" style="width:100%; height:38px; border-radius:6px; border:1px solid var(--border-2); padding:0 10px; font-size:13px;" />
        </div>
        <div class="form-group">
          <label style="font-size:11.5px; font-weight:800; text-transform:uppercase; color:var(--text); margin-bottom:5px; display:block;">
            Tanggal Selesai <span style="color:var(--red);">*</span>
          </label>
          <input type="date" name="tanggal_selesai" value="{{ $modeUjianAktif->tanggal_selesai ?? '2026-09-25' }}" required class="form-control" style="width:100%; height:38px; border-radius:6px; border:1px solid var(--border-2); padding:0 10px; font-size:13px;" />
        </div>
        <div class="form-group">
          <label style="font-size:11.5px; font-weight:800; text-transform:uppercase; color:#4f46e5; margin-bottom:5px; display:block;">
            <i class="bi bi-door-open-fill"></i> Jam Pulang Ujian <span style="color:var(--red);">*</span>
          </label>
          <input type="time" name="jam_pulang_mulai" value="{{ substr($modeUjianAktif->jam_pulang_mulai ?? '11:30', 0, 5) }}" required class="form-control" style="width:100%; height:38px; border-radius:6px; border:1.5px solid #6366f1; padding:0 10px; font-size:14px; font-weight:800; color:#4f46e5;" />
        </div>
      </div>

      <div style="background:rgba(79, 70, 229, 0.05); border:1px solid rgba(99, 102, 241, 0.2); border-radius:8px; padding:10px 14px; margin-bottom:14px;">
        <label style="display:flex; align-items:center; gap:8px; margin:0; cursor:pointer; font-size:12.5px; font-weight:700; color:var(--text);">
          <input type="checkbox" name="nonaktifkan_piket_reguler" value="1" {{ ($modeUjianAktif?->nonaktifkan_piket_reguler ?? true) ? 'checked' : '' }} style="width:16px; height:16px; accent-color:#4f46e5;">
          <span>Nonaktifkan Jadwal Guru Piket Reguler (Meja piket dialihkan penuh ke Panitia Pelaksana Sumatif)</span>
        </label>
      </div>

      {{-- Checklist Panitia Pelaksana Sumatif --}}
      <div style="margin-bottom:16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
          <label style="font-size:11.5px; font-weight:800; text-transform:uppercase; color:var(--text); margin:0;">
            Pilih Personel Panitia Sumatif (Akses Operasional Meja Piket):
          </label>
          <input type="text" id="filterPanitiaGtkJadwal" placeholder="Cari guru..." oninput="filterChecklistPanitiaJadwal(this.value)" style="height:28px; width:160px; font-size:11.5px; border-radius:6px; border:1px solid var(--border-2); padding:0 8px;">
        </div>

        @php
          $selectedPanitiaIdsJadwal = $modeUjianAktif->panitia_guru_ids ?? [];
        @endphp
        <div style="max-height:180px; overflow-y:auto; border:1px solid var(--border-2); border-radius:8px; padding:8px 12px; background:var(--bg-3); display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:6px;" id="listPanitiaChecklistJadwal">
          @foreach($gurus as $g)
            @php $checked = in_array($g->id, $selectedPanitiaIdsJadwal); @endphp
            <label class="panitia-item-label" style="display:flex; align-items:center; gap:8px; padding:4px 6px; border-radius:6px; cursor:pointer; font-size:12px; background:{{ $checked ? 'rgba(79, 70, 229, 0.08)' : 'transparent' }};">
              <input type="checkbox" name="panitia_guru_ids[]" value="{{ $g->id }}" {{ $checked ? 'checked' : '' }} style="accent-color:#4f46e5;">
              <span class="panitia-nama" style="font-weight:{{ $checked ? '700' : '500' }}; color:var(--text);">{{ $g->nama }}</span>
            </label>
          @endforeach
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid var(--border); padding-top:14px;">
        <button type="button" class="btn btn-outline" onclick="toggleModalModeUjian(false)">Batal</button>
        <button type="submit" class="btn" style="background:#4f46e5; color:#fff; font-weight:800; padding:8px 18px; border-radius:6px; border:none; display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
          <i class="bi bi-check2-circle"></i> Simpan Pengaturan Sumatif
        </button>
      </div>
    </form>
  </div>
</div>
@endif

<script>
  function toggleModalModeUjian(show) {
    const modal = document.getElementById('modalModeUjianJadwal');
    if (modal) {
      modal.style.display = show ? 'flex' : 'none';
    }
  }

  function filterChecklistPanitiaJadwal(query) {
    const q = (query || '').toLowerCase();
    const items = document.querySelectorAll('#listPanitiaChecklistJadwal .panitia-item-label');
    items.forEach(el => {
      const name = el.querySelector('.panitia-nama')?.innerText.toLowerCase() || '';
      el.style.display = name.includes(q) ? 'flex' : 'none';
    });
  }

  function toggleFormPiket(forceState) {
    const panel = document.getElementById('panelFormPiket');
    const text = document.getElementById('textTogglePiket');
    const icon = document.getElementById('iconTogglePiket');
    const btn = document.getElementById('btnToggleFormPiket');
    if (!panel) return;

    const isVisible = (panel.style.display !== 'none' && panel.style.display !== '');
    const targetState = (forceState !== undefined) ? forceState : !isVisible;

    if (targetState) {
      panel.style.display = 'block';
      if (text) text.innerText = 'Tutup Form';
      if (icon) icon.className = 'bi bi-x-lg';
      if (btn) btn.classList.add('active');
      panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
      panel.style.display = 'none';
      if (text) text.innerText = 'Tambah Penugasan Guru Piket';
      if (icon) icon.className = 'bi bi-person-plus-fill';
      if (btn) btn.classList.remove('active');
    }
  }
</script>

</body>
</html>
