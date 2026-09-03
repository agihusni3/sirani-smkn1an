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
      border-color: var(--text);
      box-shadow: var(--shadow-sm);
      background: var(--bg-2);
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
      border-color: var(--text);
    }
    .kpi-mini-card {
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      border-radius: var(--r-sm);
      padding: 14px 16px;
      display: flex;
      flex-direction: column;
      gap: 4px;
    }
    .kpi-mini-val {
      font-size: 24px;
      font-weight: 800;
      font-family: var(--font-mono);
      color: var(--text);
      line-height: 1;
    }
    .kpi-mini-lbl {
      font-size: 11.5px;
      font-weight: 700;
      color: var(--text-3);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    @media (max-width: 768px) {
      .day-column {
        padding: 10px 12px !important;
      }
      .kpi-mini-card {
        padding: 8px 10px !important;
      }
      .kpi-mini-val {
        font-size: 18px !important;
      }
    }
  </style>
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
            <button type="button" id="btnToggleFormPiket" onclick="toggleFormPiket()" class="btn btn-sm btn-gold" style="height:32px; padding:0 12px; font-size:11.5px; font-weight:800; display:inline-flex; align-items:center; gap:5px; border-radius:6px; cursor:pointer;">
              <i class="bi bi-person-plus-fill" id="iconTogglePiket"></i>
              <span id="textTogglePiket">Tambah Penugasan</span>
            </button>
          @endif
          @include('partials.header_actions')
        </div>
      </div>
    </div>

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

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- BAGIAN 2: LAPORAN KEHADIRAN PETUGAS GURU PIKET -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="panel" style="margin-bottom:24px; padding:0; overflow:hidden;">
      <div class="panel-title" style="padding:16px 20px; margin:0; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; align-items:center; gap:8px;">
          <i class="bi bi-clipboard2-check-fill" style="color:#000000; font-size:16px;"></i>
          <span style="font-size:14.5px; font-weight:800; color:var(--text);">Laporan Kehadiran Petugas Piket</span>
        </div>

        {{-- Filter Pemilihan Tanggal --}}
        <form method="GET" action="{{ route('jadwal-piket.index') }}" style="display:flex; align-items:center; gap:8px; margin:0;">
          <label style="font-size:12px; font-weight:700; color:var(--text-2); white-space:nowrap;">Pilih Tanggal:</label>
          <input type="date" name="tanggal" value="{{ $filterTanggal }}" onchange="this.form.submit()" style="height:34px; font-size:12px; font-family:var(--font-mono); font-weight:700; color:var(--text); padding:0 10px; border-radius:6px; border:1.5px solid #000000; background:var(--bg-2);" />
          <button type="submit" class="btn btn-sm" style="background:#000000; color:#FFFFFF; border:1.5px solid #000000; height:34px; font-size:11.5px; font-weight:800; padding:0 12px; border-radius:6px; cursor:pointer;">
            Filter
          </button>
          @if($filterTanggal !== now()->toDateString())
            <a href="{{ route('jadwal-piket.index') }}" class="btn btn-sm btn-outline" style="height:34px; font-size:11.5px; font-weight:800; color:#000000; border:1.5px solid #000000; padding:0 10px; border-radius:6px; display:inline-flex; align-items:center;">
              Hari Ini
            </a>
          @endif
        </form>
      </div>

      {{-- KPI Ringkasan Kehadiran Guru Piket --}}
      <div style="padding:16px 20px; background:var(--surface); border-bottom:1px solid var(--border);">
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap:12px;">
          <div class="kpi-mini-card">
            <div class="kpi-mini-lbl">Petugas Terjadwal</div>
            <div class="kpi-mini-val">{{ $totalTugas }} <span style="font-size:12px; font-weight:600; color:var(--text-3);">Guru</span></div>
          </div>
          <div class="kpi-mini-card">
            <div class="kpi-mini-lbl">Hadir Bertugas</div>
            <div class="kpi-mini-val">{{ $totalHadir }} <span style="font-size:12px; font-weight:600; color:var(--text-3);">Guru</span></div>
          </div>
          <div class="kpi-mini-card">
            <div class="kpi-mini-lbl">Terlambat</div>
            <div class="kpi-mini-val">{{ $totalTerlambat }} <span style="font-size:12px; font-weight:600; color:var(--text-3);">Guru</span></div>
          </div>
          <div class="kpi-mini-card">
            <div class="kpi-mini-lbl">Belum Hadir / Scan</div>
            <div class="kpi-mini-val">{{ $totalBelumHadir }} <span style="font-size:12px; font-weight:600; color:var(--text-3);">Guru</span></div>
          </div>
          <div class="kpi-mini-card">
            <div class="kpi-mini-lbl">Status Smart Gate</div>
            <div style="margin-top:4px;">
              @if($jadwalHarian && $jadwalHarian->is_sesi_buka)
                <div style="font-size:13px; font-weight:800; color:var(--text); text-transform:uppercase;">AKTIF</div>
                <div style="font-size:11px; font-weight:600; color:var(--text-3); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $jadwalHarian->dibuka_oleh ?: 'Petugas' }}">
                  {{ $jadwalHarian->dibuka_oleh ?: 'Petugas' }}
                </div>
              @else
                <div style="font-size:13px; font-weight:800; color:var(--text-3); text-transform:uppercase;">BELUM DIBUKA</div>
              @endif
            </div>
          </div>
        </div>
      </div>


      {{-- Tabel Rincian Kehadiran Guru Piket --}}
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th style="width:36px; text-align:center;">No</th>
              <th style="text-align:left;">Petugas Guru Piket</th>
              <th style="width:160px; text-align:left;">Tugas / Posisi</th>
              <th style="width:90px; text-align:center;">Hari Piket</th>
              <th style="width:85px; text-align:center;">Jam Masuk</th>
              <th style="width:85px; text-align:center;">Jam Pulang</th>
              <th style="width:120px; text-align:center;">Status Kehadiran</th>
              <th style="text-align:left;">Catatan Otorisasi Gerbang</th>
            </tr>
          </thead>
          <tbody>
            @forelse($petugasPiket as $i => $p)
              @php
                $abs = $absensiMap->get($p->guru_id);
                $guru = $p->guru;
                $isPembukaGerbang = ($jadwalHarian && $jadwalHarian->dibuka_oleh && str_contains(strtolower($jadwalHarian->dibuka_oleh), strtolower($guru->nama ?? '')));
              @endphp
              <tr>
                <td style="text-align:center; font-weight:700; color:var(--text); font-family:var(--font-mono); font-size:12px;">
                  {{ $i + 1 }}
                </td>
                <td style="text-align:left;">
                  <div style="display:flex; align-items:center; gap:10px;">
                    <div class="avatar-circle avatar-sm">
                      <img src="{{ $guru->foto_url ?? '/img/logo.png' }}" alt="{{ $guru->nama ?? 'Guru' }}" class="avatar-img" />
                    </div>
                    <div>
                      <strong style="color:var(--text); font-size:13.5px; display:block;">{{ $guru->nama ?? 'Guru' }}</strong>
                      <div style="font-size:11px; color:var(--text-3); font-family:var(--font-mono); margin-top:1px;">
                        {{ $guru->nip ? 'NIP: ' . $guru->nip : 'Non-NIP' }} &bull; {{ $guru->jabatan ?? 'Tenaga Pendidik' }}
                      </div>
                    </div>
                  </div>
                </td>
                <td style="text-align:left; font-size:12px; font-weight:600; color:var(--text);">
                  {{ $p->keterangan ?: 'Piket Umum' }}
                </td>
                <td style="text-align:center; font-weight:700; font-size:12px; color:var(--text);">
                  Hari {{ $p->hari }}
                </td>
                <td style="text-align:center; font-family:var(--font-mono); font-size:12px; font-weight:700; color:var(--text);">
                  {{ $abs->jam_masuk ?? '-' }}
                </td>
                <td style="text-align:center; font-family:var(--font-mono); font-size:12px; font-weight:700; color:var(--text);">
                  {{ $abs->jam_pulang ?? '-' }}
                </td>
                <td style="text-align:center; white-space:nowrap;">
                  @if($abs)
                    @if($abs->status === 'hadir')
                      <span style="font-weight:800; font-size:11.5px; text-transform:uppercase; color:var(--text);">HADIR TEPAT</span>
                    @elseif($abs->status === 'terlambat')
                      <span style="font-weight:800; font-size:11.5px; text-transform:uppercase; color:#CA8A04;">TERLAMBAT</span>
                    @else
                      <span style="font-weight:800; font-size:11.5px; text-transform:uppercase; color:var(--text-2);">{{ strtoupper($abs->status) }}</span>
                    @endif
                  @else
                    <span style="font-weight:700; font-size:11.5px; text-transform:uppercase; color:var(--text-3);">BELUM HADIR</span>
                  @endif
                </td>
                <td style="text-align:left; font-size:11.5px;">
                  @if($isPembukaGerbang)
                    <span style="font-weight:700; color:var(--text);">Membuka Sesi Gerbang</span>
                    @if($jadwalHarian->waktu_buka_sesi)
                      <span style="color:var(--text-3); font-family:var(--font-mono);">({{ \Carbon\Carbon::parse($jadwalHarian->waktu_buka_sesi)->format('H:i') }})</span>
                    @endif
                  @elseif($abs)
                    <span style="color:var(--text-3);">Presensi Smart Gate terverifikasi</span>
                  @else
                    <span style="color:var(--text-3);">-</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" style="text-align:center; padding:32px; color:var(--text-3);">
                  <i class="bi bi-calendar-x" style="font-size:32px; opacity:0.4;"></i>
                  <div style="font-weight:700; margin-top:8px; font-size:13.5px; color:var(--text);">Tidak ada jadwal penugasan guru piket pada hari {{ $hariPilihan }}.</div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>

<script>
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
