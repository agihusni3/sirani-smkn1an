<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kalender Akademik — SIRANI</title>
  @include('partials.styles')
  <style>
    .cal-header-nav {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 12px;
      margin-bottom: 20px;
    }
    .cal-nav-btn {
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      color: var(--text);
      padding: 8px 16px;
      border-radius: var(--r-sm);
      font-weight: 700;
      font-size: 13px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      cursor: pointer;
      text-decoration: none;
      transition: all .2s ease;
    }
    .cal-nav-btn:hover {
      background: var(--bg-3);
      border-color: #000000;
      color: #000000;
    }

    /* Grid Kalender Bulanan */
    .calendar-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 8px;
      margin-bottom: 28px;
    }
    .cal-day-header {
      text-align: center;
      font-weight: 800;
      font-size: 11.5px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 10px 4px;
      color: var(--text-2);
      background: var(--bg-3);
      border-radius: var(--r-sm);
    }
    .cal-day-header.weekend-hdr {
      color: #EF4444;
    }
    .cal-cell {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-sm);
      min-height: 105px;
      padding: 8px 10px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: transform .15s ease, border-color .15s ease, box-shadow .15s ease;
      position: relative;
    }
    .cal-cell:hover {
      border-color: #000000;
      box-shadow: var(--shadow-sm);
      transform: translateY(-2px);
    }
    .cal-cell.other-month {
      opacity: 0.35;
      background: var(--bg);
    }
    .cal-cell.is-today {
      border: 2px solid #000000;
      box-shadow: 0 0 10px rgba(0,0,0,0.15);
    }
    .cal-cell.is-weekend {
      background: rgba(239, 68, 68, 0.03);
    }
    .cal-cell.is-holiday {
      background: linear-gradient(135deg, rgba(239, 68, 68, 0.12), rgba(239, 68, 68, 0.04));
      border-color: rgba(239, 68, 68, 0.4);
    }
    .cal-date-number {
      font-family: var(--font-mono);
      font-weight: 800;
      font-size: 14px;
      color: var(--text);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .cal-cell.is-weekend .cal-date-number {
      color: #EF4444;
    }
    .holiday-tag {
      background: #EF4444;
      color: #fff;
      font-size: 10px;
      font-weight: 800;
      padding: 3px 6px;
      border-radius: 6px;
      line-height: 1.25;
      margin-top: 4px;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
      box-shadow: 0 2px 6px rgba(239, 68, 68, 0.3);
    }
    .today-pill {
      background: #000000;
      color: #FFFFFF;
      font-size: 9px;
      font-weight: 900;
      padding: 1px 5px;
      border-radius: 4px;
      text-transform: uppercase;
    }
    @media (max-width: 768px) {
      .calendar-grid {
        gap: 3px !important;
        margin-bottom: 16px !important;
      }
      .cal-cell {
        min-height: 55px !important;
        padding: 4px 5px !important;
        border-radius: 4px !important;
      }
      .cal-day-header {
        font-size: 10px !important;
        padding: 5px 2px !important;
      }
      .cal-date-number {
        font-size: 11.5px !important;
      }
      .holiday-tag {
        font-size: 8.5px !important;
        padding: 1px 3px !important;
        border-radius: 3px !important;
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
      $canManageLibur = $currentUser && ($currentUser->isAdmin() || $currentUser->isWakaKurikulum());
    @endphp

    {{-- ULTRA COMPACT SLIM HEADER BAR --}}
    <div class="panel no-print" style="background:var(--bg-2); border:1px solid var(--border); padding:10px 16px; margin-bottom:12px; border-radius:var(--r-md); box-shadow:var(--shadow-sm);">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          <h1 style="margin:0; font-size:16px; font-weight:900; color:var(--text); display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-calendar-event-fill" style="color:#000000; font-size:16px;"></i> Kalender Akademik &amp; Libur
          </h1>
          <span style="color:var(--border-2); font-weight:300;">|</span>
          <span style="font-size:11.5px; color:var(--text-3);">
            Periode: <strong style="color:#000000;">{{ $namaBulan }}</strong>
          </span>
        </div>

        <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
          @if($canManageLibur)
            <button type="button" class="btn btn-sm btn-gold" onclick="openModalTambahLibur()" style="height:32px; padding:0 12px; font-size:11.5px; font-weight:800; display:inline-flex; align-items:center; gap:5px; border-radius:6px; cursor:pointer;">
              <i class="bi bi-plus-circle-fill"></i> Tambah Libur
            </button>

            <form action="{{ route('admin.hari-libur.preset') }}" method="POST" onsubmit="return confirm('Tambahkan hari libur nasional & cuti bersama standar untuk tahun {{ $tahun }}?')" style="margin:0;">
              @csrf
              <input type="hidden" name="tahun" value="{{ $tahun }}" />
              <button type="submit" class="btn btn-sm btn-outline" style="height:32px; padding:0 10px; font-size:11.5px; font-weight:800; color:#000000; border:1px solid var(--border-2); background:var(--bg-2); display:inline-flex; align-items:center; gap:4px; border-radius:6px;">
                <i class="bi bi-magic" style="color:#000000;"></i> Preset Libur
              </button>
            </form>
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

    {{-- FILTER NAVIGASI BULAN & TAHUN --}}
    <div class="cal-header-nav" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; margin-bottom:12px;">
      @php
        $prevDate = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->subMonth();
        $nextDate = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->addMonth();
      @endphp
      <a href="{{ route('admin.hari-libur.index', ['bulan' => $prevDate->month, 'tahun' => $prevDate->year]) }}" class="btn btn-sm btn-outline" style="height:32px; padding:0 12px; font-size:12px; font-weight:700; border-radius:6px; display:inline-flex; align-items:center; gap:6px; text-decoration:none; color:var(--text);">
        <i class="bi bi-chevron-left"></i> {{ $prevDate->translatedFormat('F Y') }}
      </a>
      <div style="font-size:14.5px; font-weight:900; color:var(--text); display:flex; align-items:center; gap:6px;">
        <i class="bi bi-calendar-check" style="color:#000000;"></i> {{ $namaBulan }}
      </div>
      <a href="{{ route('admin.hari-libur.index', ['bulan' => $nextDate->month, 'tahun' => $nextDate->year]) }}" class="btn btn-sm btn-outline" style="height:32px; padding:0 12px; font-size:12px; font-weight:700; border-radius:6px; display:inline-flex; align-items:center; gap:6px; text-decoration:none; color:var(--text);">
        {{ $nextDate->translatedFormat('F Y') }} <i class="bi bi-chevron-right"></i>
      </a>
    </div>

    {{-- KALENDER MATRIX --}}
    <div class="calendar-grid">
      <div class="cal-day-header">Senin</div>
      <div class="cal-day-header">Selasa</div>
      <div class="cal-day-header">Rabu</div>
      <div class="cal-day-header">Kamis</div>
      <div class="cal-day-header">Jumat</div>
      <div class="cal-day-header weekend-hdr">Sabtu</div>
      <div class="cal-day-header weekend-hdr">Minggu</div>

      @foreach($calendarDays as $cDay)
        @php
          $classes = ['cal-cell'];
          if (!$cDay['isCurrent']) $classes[] = 'other-month';
          if ($cDay['isWeekend'])  $classes[] = 'weekend';
          if ($cDay['holiday'])    $classes[] = 'holiday';
          if ($cDay['isToday'])    $classes[] = 'today';
        @endphp
        <div class="{{ implode(' ', $classes) }}">
          <div class="cal-cell-top">
            <span class="cal-day-num">{{ $cDay['day'] }}</span>
            @if($cDay['isToday'])
              <span class="cal-badge-today">HARI INI</span>
            @endif
          </div>
          @if($cDay['holiday'])
            <div class="cal-holiday-tag" title="{{ $cDay['holiday']->nama_libur }} ({{ $cDay['holiday']->jenis }})">
              {{ $cDay['holiday']->nama_libur }}
            </div>
          @elseif($cDay['isWeekend'])
            <div style="font-size:10px; color:#EF4444; font-weight:700; margin-top:4px;">Akhir Pekan</div>
          @endif
        </div>
      @endforeach
    </div>

    {{-- TABEL DAFTAR HARI LIBUR TAHUNAN --}}
    <div class="panel" style="padding:0; overflow:hidden; border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-sm); background:var(--bg-2); margin-bottom:24px;">
      <div class="panel-title" style="padding:14px 18px; margin:0; border-bottom:1px solid var(--border); background:var(--surface); display:flex; justify-content:space-between; align-items:center;">
        <div style="font-weight:800; font-size:15px; color:var(--text); display:flex; align-items:center; gap:8px;">
          <i class="bi bi-list-check" style="color:#000000;"></i>
          <span>Daftar Hari Libur Tahun {{ $tahun }}</span>
        </div>
        <span class="badge" style="background:var(--bg-3); color:var(--text); font-weight:800; font-size:11.5px; border:1px solid var(--border-2);">
          {{ $semuaLiburTahun->count() }} Jadwal Terdaftar
        </span>
      </div>

      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th style="width:40px; text-align:center;">#</th>
              <th>Nama Hari Libur</th>
              <th>Rentang Tanggal</th>
              <th>Durasi</th>
              <th>Kategori</th>
              <th>Keterangan</th>
              <th>Dicatat Oleh</th>
              @if($canManageLibur)
                <th style="width:90px; text-align:center;">Aksi</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @forelse($semuaLiburTahun as $idx => $libur)
              @php
                $mulai = \Carbon\Carbon::parse($libur->tanggal_mulai);
                $selesai = \Carbon\Carbon::parse($libur->tanggal_selesai);
                $durasi = $mulai->diffInDays($selesai) + 1;
              @endphp
              <tr>
                <td style="text-align:center; font-family:var(--font-mono); color:var(--text-3);">{{ $idx + 1 }}</td>
                <td>
                  <strong style="color:var(--text); font-size:13.5px;">{{ $libur->nama_libur }}</strong>
                </td>
                <td>
                  <div style="font-family:var(--font-mono); font-size:12.5px; font-weight:700; color:#000000;">
                    @if($libur->tanggal_mulai == $libur->tanggal_selesai)
                      {{ $mulai->translatedFormat('d F Y') }}
                    @else
                      {{ $mulai->translatedFormat('d M Y') }} s/d {{ $selesai->translatedFormat('d M Y') }}
                    @endif
                  </div>
                </td>
                <td>
                  <span class="badge" style="background:var(--bg-3); font-size:11px; font-weight:700;">
                    {{ $durasi }} Hari
                  </span>
                </td>
                <td>{!! $libur->jenis_badge !!}</td>
                <td style="font-size:12px; color:var(--text-2);">{{ $libur->keterangan ?: '-' }}</td>
                <td style="font-size:11.5px; color:var(--text-3);">{{ $libur->created_by ?: 'Admin' }}</td>
                @if($canManageLibur)
                  <td style="text-align:center;">
                    <form action="{{ route('admin.hari-libur.destroy', $libur->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal hari libur {{ $libur->nama_libur }}?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger" title="Hapus Jadwal Libur">
                        <i class="bi bi-trash3-fill"></i>
                      </button>
                    </form>
                  </td>
                @endif
              </tr>
            @empty
              <tr>
                <td colspan="{{ $canManageLibur ? 8 : 7 }}" style="text-align:center; padding:36px; color:var(--text-3);">
                  <div style="font-size:36px; margin-bottom:8px; opacity:.6;">🏖️</div>
                  <div style="font-weight:700; font-size:14px; color:var(--text);">Belum ada jadwal hari libur di tahun {{ $tahun }}</div>
                  @if($canManageLibur)
                    <p style="font-size:12px; margin-top:4px;">Gunakan tombol "+ Tambah Hari Libur" atau "Isi Preset Libur Nasional" di atas.</p>
                  @else
                    <p style="font-size:12px; margin-top:4px;">Hubungi Waka Kurikulum / Administrator untuk pengelolaan jadwal hari libur.</p>
                  @endif
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>

@if($canManageLibur)
  {{-- MODAL TAMBAH HARI LIBUR --}}
  <div class="modal-overlay" id="modalTambahLibur">
    <div class="modal-card" style="max-width:520px; padding:24px;">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
        <h3 style="font-size:17px; font-weight:900; color:var(--text); margin:0; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-calendar-plus-fill" style="color:#000000;"></i> Tambah Jadwal Hari Libur
        </h3>
        <button type="button" class="btn btn-sm btn-outline" onclick="closeModalTambahLibur()">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>

      <form action="{{ route('admin.hari-libur.store') }}" method="POST">
        @csrf
        <div style="margin-bottom:14px;">
          <label class="form-label" style="font-weight:700; font-size:12.5px; margin-bottom:4px; display:block;">Nama Hari Libur / Peristiwa <span style="color:var(--red);">*</span></label>
          <input type="text" name="nama_libur" class="input-field" placeholder="Contoh: Hari Kemerdekaan RI / Libur Semester" required style="width:100%;" />
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
          <div>
            <label class="form-label" style="font-weight:700; font-size:12.5px; margin-bottom:4px; display:block;">Tanggal Mulai <span style="color:var(--red);">*</span></label>
            <input type="date" name="tanggal_mulai" class="input-field" value="{{ \Carbon\Carbon::today()->toDateString() }}" required style="width:100%;" />
          </div>
          <div>
            <label class="form-label" style="font-weight:700; font-size:12.5px; margin-bottom:4px; display:block;">Tanggal Selesai (Opsional)</label>
            <input type="date" name="tanggal_selesai" class="input-field" placeholder="Kosongkan jika 1 hari" style="width:100%;" />
          </div>
        </div>

        <div style="margin-bottom:14px;">
          <label class="form-label" style="font-weight:700; font-size:12.5px; margin-bottom:4px; display:block;">Kategori / Jenis Libur <span style="color:var(--red);">*</span></label>
          <select name="jenis" class="input-field" style="width:100%;" required>
            <option value="libur_nasional">Libur Nasional</option>
            <option value="cuti_bersama">Cuti Bersama</option>
            <option value="libur_semester">Libur Semester</option>
            <option value="khusus_sekolah">Khusus / Hari Besar Sekolah</option>
          </select>
        </div>

        <div style="margin-bottom:20px;">
          <label class="form-label" style="font-weight:700; font-size:12.5px; margin-bottom:4px; display:block;">Catatan / Keterangan (Opsional)</label>
          <input type="text" name="keterangan" class="input-field" placeholder="Keterangan tambahan jika diperlukan" style="width:100%;" />
        </div>

        <div style="display:flex; justify-content:flex-end; gap:8px;">
          <button type="button" class="btn btn-outline" onclick="closeModalTambahLibur()">Batal</button>
          <button type="submit" class="btn btn-gold">
            <i class="bi bi-save-fill"></i> Simpan Jadwal Libur
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openModalTambahLibur() {
      document.getElementById('modalTambahLibur')?.classList.add('active');
    }
    function closeModalTambahLibur() {
      document.getElementById('modalTambahLibur')?.classList.remove('active');
    }
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeModalTambahLibur();
    });
    document.getElementById('modalTambahLibur')?.addEventListener('click', (e) => {
      if (e.target.id === 'modalTambahLibur') closeModalTambahLibur();
    });
  </script>
@endif

</body>
</html>
