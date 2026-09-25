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
            <form action="{{ route('jadwal-piket.sync-akademik') }}" method="POST" style="margin:0;">
              @csrf
              <button type="submit" class="btn btn-sm btn-outline" style="height:32px; padding:0 12px; font-size:11.5px; font-weight:800; display:inline-flex; align-items:center; gap:5px; border-radius:6px; cursor:pointer;" title="Tarik pembaruan penugasan piket dari Waka Kurikulum (DCC Akademik)">
                <i class="bi bi-arrow-repeat text-success"></i>
                <span>Sinkron dari Akademik</span>
              </button>
            </form>
            @if($currentUser && ($currentUser->isAdmin() || $currentUser->isWakaKurikulum()))
              <a href="{{ route('akademik.jadwal.index', ['tab' => 'piket']) }}" class="btn btn-sm" style="height:32px; padding:0 12px; font-size:11.5px; font-weight:800; display:inline-flex; align-items:center; gap:5px; border-radius:6px; background:#16a34a; color:#fff; text-decoration:none;" title="Kelola penugasan piket di DCC Akademik (Waka Kurikulum)">
                <i class="bi bi-pencil-square"></i>
                <span>Kelola di Akademik &rarr;</span>
              </a>
            @endif
            <button type="button" onclick="toggleModalModeUjian(true)" class="btn btn-sm" style="height:32px; padding:0 14px; font-size:11.5px; font-weight:800; display:inline-flex; align-items:center; border-radius:6px; cursor:pointer; background:linear-gradient(135deg, #4f46e5, #7c3aed); color:#fff; border:none; box-shadow:0 2px 6px rgba(79, 70, 229, 0.25);">
              <span>Mode Sumatif</span>
            </button>
          @endif
          @include('partials.header_actions')
        </div>
      </div>
    </div>

    {{-- Banner Mode Sumatif Aktif jika ada --}}
    @if($modeUjianAktif)
      <div class="panel no-print" style="margin-bottom:14px; background:linear-gradient(135deg, rgba(79, 70, 229, 0.08) 0%, rgba(124, 58, 237, 0.05) 100%); border:1px solid rgba(99, 102, 241, 0.28); border-radius:12px; padding:12px 18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; box-shadow:0 2px 10px -2px rgba(99, 102, 241, 0.08);">
        <div style="display:flex; align-items:center; gap:12px; min-width:280px; flex:1;">
          <div style="width:38px; height:38px; border-radius:10px; background:linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); display:flex; align-items:center; justify-content:center; font-size:18px; color:#ffffff; flex-shrink:0; box-shadow:0 3px 8px rgba(79, 70, 229, 0.28);">
            <i class="bi bi-mortarboard-fill"></i>
          </div>
          <div style="display:flex; flex-direction:column; gap:2px;">
            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
              <span style="font-size:10.5px; font-weight:800; text-transform:uppercase; letter-spacing:0.4px; padding:2px 8px; border-radius:6px; background:#4f46e5; color:#ffffff; display:inline-flex; align-items:center; gap:4px;">
                <span style="width:5px; height:5px; border-radius:50%; background:#4ade80;"></span>
                {{ $modeUjianAktif->tipe ?? 'SUMATIF' }} AKTIF
              </span>
              <span style="font-size:13.5px; font-weight:800; color:var(--text, #1e1b4b); letter-spacing:-0.2px;">
                {{ $modeUjianAktif->nama_ujian }}
              </span>
            </div>
            <div style="font-size:11.5px; color:var(--text-3, #64748b); display:flex; align-items:center; gap:6px;">
              <i class="bi bi-calendar-range"></i>
              <span>{{ \Carbon\Carbon::parse($modeUjianAktif->tanggal_mulai)->locale('id')->isoFormat('D MMM') }} – {{ \Carbon\Carbon::parse($modeUjianAktif->tanggal_selesai)->locale('id')->isoFormat('D MMM Y') }}</span>
              <span style="color:var(--border-2, #cbd5e1);">•</span>
              <span><i class="bi bi-people-fill text-indigo-500"></i> Panitia: <strong style="color:var(--text, #0f172a);">{{ $modeUjianAktif->daftar_panitia->count() }} Guru</strong></span>
            </div>
          </div>
        </div>

        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
          {{-- Highlight Jam Pulang Sumatif --}}
          <div style="display:inline-flex; align-items:center; gap:9px; background:var(--bg-2, #ffffff); border:1px solid rgba(99, 102, 241, 0.25); padding:6px 14px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.03);">
            <div style="width:26px; height:26px; border-radius:7px; background:rgba(79, 70, 229, 0.12); color:#4f46e5; display:flex; align-items:center; justify-content:center; font-size:13px;">
              <i class="bi bi-clock-fill"></i>
            </div>
            <div>
              <div style="font-size:9.5px; font-weight:800; text-transform:uppercase; color:var(--text-3, #64748b); letter-spacing:0.4px;">Jam Pulang Siswa</div>
              <div style="font-size:13.5px; font-weight:900; color:#4338ca; font-family:var(--font-mono, monospace); line-height:1.1;">
                {{ substr($modeUjianAktif->jam_pulang_mulai, 0, 5) }} <span style="font-size:10px; font-weight:700;">WIB</span>
              </div>
            </div>
          </div>

          @if($canManagePiket)
            <button type="button" onclick="toggleModalModeUjian(true)" class="btn btn-sm btn-outline" style="font-size:11.5px; font-weight:700; height:38px; display:inline-flex; align-items:center; gap:6px;">
              <i class="bi bi-pencil-square"></i> Kelola
            </button>
          @endif
        </div>
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

    {{-- Banner Mode Read-Only: Dikelola oleh Waka Kurikulum --}}
    <div class="panel no-print" style="margin-bottom:20px; background:linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border:1.5px solid #86efac; border-radius:var(--r-md); padding:14px 18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:38px; height:38px; border-radius:10px; background:#16a34a; display:flex; align-items:center; justify-content:center; font-size:18px; color:#fff; flex-shrink:0;">
          <i class="bi bi-shield-lock-fill"></i>
        </div>
        <div>
          <div style="font-size:13.5px; font-weight:800; color:#14532d; display:flex; align-items:center; gap:6px;">
            <span>Jadwal Piket Terpusat (Mode Baca / Read-Only)</span>
            <span style="font-size:10.5px; padding:2px 7px; border-radius:6px; background:#16a34a; color:#fff; font-weight:800;">WAKAKUR ONLY</span>
          </div>
          <div style="font-size:12px; color:#166534; margin-top:2px;">
            Penetapan, perubahan, dan plotting guru piket dikelola penuh oleh <b>Waka Kurikulum</b> melalui modul <b>Akademik &amp; KBM (Langkah 4)</b>.
          </div>
        </div>
      </div>
      <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
        <form action="{{ route('jadwal-piket.sync-akademik') }}" method="POST" style="margin:0;">
          @csrf
          <button type="submit" class="btn btn-sm" style="height:34px; padding:0 12px; font-size:11.5px; font-weight:800; display:inline-flex; align-items:center; gap:5px; border-radius:6px; background:#fff; border:1px solid #86efac; color:#15803d; cursor:pointer;" title="Tarik pembaruan penugasan piket dari DCC Akademik">
            <i class="bi bi-arrow-repeat"></i> Sinkronkan Ulang
          </button>
        </form>
        @if($currentUser && ($currentUser->isAdmin() || $currentUser->isWakaKurikulum()))
          <a href="{{ route('akademik.jadwal.index', ['tab' => 'piket']) }}" class="btn btn-sm" style="height:34px; padding:0 14px; font-size:11.5px; font-weight:800; display:inline-flex; align-items:center; gap:6px; border-radius:6px; background:#16a34a; color:#fff; text-decoration:none; box-shadow:0 2px 8px rgba(22,163,74,0.3);">
            <i class="bi bi-pencil-square"></i> Kelola di DCC Akademik &rarr;
          </a>
        @endif
      </div>
    </div>

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

                <div style="flex-shrink:0;">
                  @if(stripos($jp->keterangan ?? '', 'koordinator') !== false || stripos($jp->keterangan ?? '', 'waka') !== false)
                    <span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:10px; font-weight:800; padding:3px 8px; border-radius:6px; white-space:nowrap;">
                      <i class="bi bi-shield-check"></i> Waka
                    </span>
                  @else
                    <span class="badge" style="background:var(--bg-3); color:var(--text-3); font-size:10px; font-weight:700; padding:3px 7px; border-radius:6px; white-space:nowrap;">
                      Petugas
                    </span>
                  @endif
                </div>
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
<div class="modal-overlay" id="modalModeUjianJadwal" onclick="if(event.target===this) toggleModalModeUjian(false)" style="display:none; position:fixed; inset:0; width:100%; height:100%; background:rgba(10, 15, 29, 0.82); z-index:99999; align-items:center; justify-content:center; padding:20px; box-sizing:border-box; backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px);">
  <div class="modal-card" style="background:var(--bg-2); border-radius:12px; width:92%; max-width:680px; padding:24px; box-shadow:0 25px 70px rgba(0,0,0,0.6); max-height:90vh; overflow-y:auto; border:1px solid var(--border);">
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
      if (show) {
        modal.classList.add('active', 'open');
        modal.style.display = 'flex';
        modal.style.opacity = '1';
        modal.style.visibility = 'visible';
        modal.style.pointerEvents = 'auto';
        document.body.style.overflow = 'hidden';
      } else {
        modal.classList.remove('active', 'open');
        modal.style.display = 'none';
        modal.style.opacity = '0';
        modal.style.visibility = 'hidden';
        modal.style.pointerEvents = 'none';
        document.body.style.overflow = '';
      }
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

  }
</script>

</body>
</html>
