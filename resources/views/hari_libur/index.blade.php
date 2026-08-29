<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kalender Hari Libur &amp; Tanggal Merah — SIRANI</title>
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
      border-color: var(--gold);
      color: var(--gold);
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
      border-color: var(--gold);
      box-shadow: var(--shadow-sm);
      transform: translateY(-2px);
    }
    .cal-cell.other-month {
      opacity: 0.35;
      background: var(--bg);
    }
    .cal-cell.is-today {
      border: 2px solid var(--gold);
      box-shadow: 0 0 14px var(--gold-glow);
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
      background: var(--gold);
      color: #0F172A;
      font-size: 9px;
      font-weight: 900;
      padding: 1px 5px;
      border-radius: 4px;
      text-transform: uppercase;
    }
  </style>
</head>
<body>

<div class="app-container">
  @include('partials.sidebar')

  <main class="main-content">
    <header class="header">
      <div class="header-title">
        <h1>Kalender Hari Libur &amp; Tanggal Merah</h1>
        <p>Pengaturan Hari Bebas Presensi &amp; Kalender Pendidikan SMKN 1 Air Naningan</p>
      </div>
      @include('partials.header_actions')
    </header>

    @if(session('success'))
      <div class="alert-success" style="margin-bottom:18px;">
        <i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="alert-error" style="margin-bottom:18px;">
        <i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i>{{ session('error') }}
      </div>
    @endif

    {{-- HERO / HEADER ACTIONS --}}
    <div class="panel" style="margin-bottom: 24px;">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
        <div>
          <div style="display:inline-flex; align-items:center; gap:6px; background:var(--gold-dim); border:1px solid var(--gold); padding:3px 10px; border-radius:20px; font-size:11px; font-weight:800; color:var(--gold); margin-bottom:8px;">
            <i class="bi bi-calendar2-week-fill"></i> SISTEM OTOMASI HARI LIBUR
          </div>
          <h2 style="font-size:20px; font-weight:900; color:var(--text); margin:0 0 4px 0;">
            Kalender Periode: <span style="color:var(--gold);">{{ $namaBulan }}</span>
          </h2>
          <p style="font-size:12.5px; color:var(--text-2); margin:0;">
            Pada tanggal yang terdaftar sebagai <strong>Hari Libur</strong> atau <strong>Akhir Pekan</strong>, evaluasi alpha otomatis akan di-skip tanpa mencatat alpha bagi siswa dan guru.
          </p>
        </div>

        @if(auth()->user()?->isAdmin())
          <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <button type="button" class="btn btn-gold" onclick="openModalTambahLibur()">
              <i class="bi bi-plus-circle-fill"></i> + Tambah Hari Libur
            </button>

            <form action="{{ route('admin.hari-libur.preset') }}" method="POST" onsubmit="return confirm('Tambahkan hari libur nasional & cuti bersama standar untuk tahun {{ $tahun }}?')">
              @csrf
              <input type="hidden" name="tahun" value="{{ $tahun }}" />
              <button type="submit" class="btn btn-outline" style="border-color:var(--gold); color:var(--gold); font-weight:700;">
                <i class="bi bi-magic"></i> Isi Preset Libur Nasional
              </button>
            </form>
          </div>
        @else
          <div>
            <span class="badge" style="background:rgba(59,130,246,0.12); color:var(--navy); border:1px solid rgba(59,130,246,0.3); font-size:11.5px; padding:6px 12px; border-radius:8px; font-weight:700; display:inline-flex; align-items:center; gap:6px;">
              <i class="bi bi-eye-fill"></i> Akses Tinjau Kalender
            </span>
          </div>
        @endif
      </div>
    </div>

    {{-- FILTER NAVIGASI BULAN & TAHUN --}}
    <div class="cal-header-nav">
      @php
        $prevDate = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->subMonth();
        $nextDate = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->addMonth();
      @endphp
      <a href="{{ route('admin.hari-libur.index', ['bulan' => $prevDate->month, 'tahun' => $prevDate->year]) }}" class="cal-nav-btn">
        <i class="bi bi-chevron-left"></i> {{ $prevDate->translatedFormat('F Y') }}
      </a>

      {{-- Form Jump Dropdown --}}
      <form action="{{ route('admin.hari-libur.index') }}" method="GET" style="display:flex; gap:8px; align-items:center;">
        <select name="bulan" class="input-field" style="padding:6px 12px; font-weight:700;" onchange="this.form.submit()">
          @for($m = 1; $m <= 12; $m++)
            <option value="{{ $m }}" {{ $m == $bulan ? 'selected' : '' }}>
              {{ \Carbon\Carbon::createFromDate($tahun, $m, 1)->translatedFormat('F') }}
            </option>
          @endfor
        </select>
        <select name="tahun" class="input-field" style="padding:6px 12px; font-weight:700;" onchange="this.form.submit()">
          @for($y = $tahun - 2; $y <= $tahun + 2; $y++)
            <option value="{{ $y }}" {{ $y == $tahun ? 'selected' : '' }}>{{ $y }}</option>
          @endfor
        </select>
      </form>

      <a href="{{ route('admin.hari-libur.index', ['bulan' => $nextDate->month, 'tahun' => $nextDate->year]) }}" class="cal-nav-btn">
        {{ $nextDate->translatedFormat('F Y') }} <i class="bi bi-chevron-right"></i>
      </a>
    </div>

    {{-- KALENDER GRID BULANAN --}}
    <div class="calendar-grid">
      {{-- Header Hari --}}
      <div class="cal-day-header">Senin</div>
      <div class="cal-day-header">Selasa</div>
      <div class="cal-day-header">Rabu</div>
      <div class="cal-day-header">Kamis</div>
      <div class="cal-day-header">Jumat</div>
      <div class="cal-day-header weekend-hdr">Sabtu</div>
      <div class="cal-day-header weekend-hdr">Minggu</div>

      {{-- Sel Tanggal --}}
      @foreach($calendarDays as $cell)
        @php
          $classes = ['cal-cell'];
          if (!$cell['isCurrent']) $classes[] = 'other-month';
          if ($cell['isToday'])    $classes[] = 'is-today';
          if ($cell['isWeekend'])  $classes[] = 'is-weekend';
          if ($cell['holiday'])    $classes[] = 'is-holiday';
        @endphp
        <div class="{{ implode(' ', $classes) }}">
          <div class="cal-date-number">
            <span>{{ $cell['day'] }}</span>
            @if($cell['isToday'])
              <span class="today-pill">Hari Ini</span>
            @endif
          </div>

          <div>
            @if($cell['holiday'])
              <div class="holiday-tag" title="{{ $cell['holiday']->nama_libur }} ({{ $cell['holiday']->jenis }})">
                {{ $cell['holiday']->nama_libur }}
              </div>
            @elseif($cell['isWeekend'] && $cell['isCurrent'])
              <span style="font-size:10px; color:#EF4444; font-weight:700;">Libur Akhir Pekan</span>
            @endif
          </div>
        </div>
      @endforeach
    </div>

    {{-- TABEL DAFTAR HARI LIBUR TAHUN INI --}}
    <div class="panel" style="padding:0; overflow:hidden; border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-sm); background:var(--bg-2); margin-bottom:24px;">
      <div class="panel-title" style="padding:14px 18px; margin:0; border-bottom:1px solid var(--border); background:var(--surface); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:8px; font-weight:800; font-size:15px; color:var(--text);">
          <i class="bi bi-calendar-check" style="color:var(--gold);"></i>
          <span>Daftar Tanggal Libur Terdaftar Tahun {{ $tahun }}</span>
        </div>
      </div>

      <div class="table-responsive" style="overflow-x:auto;">
        <table class="data-table">
          <thead>
            <tr>
              <th style="width:40px; text-align:center;">No</th>
              <th>Nama Hari Libur</th>
              <th>Rentang Tanggal</th>
              <th>Durasi</th>
              <th>Kategori</th>
              <th>Keterangan</th>
              <th>Dicatat Oleh</th>
              @if(auth()->user()?->isAdmin())
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
                  <div style="font-family:var(--font-mono); font-size:12.5px; font-weight:700; color:var(--gold);">
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
                @if(auth()->user()?->isAdmin())
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
                <td colspan="{{ auth()->user()?->isAdmin() ? 8 : 7 }}" style="text-align:center; padding:36px; color:var(--text-3);">
                  <div style="font-size:36px; margin-bottom:8px; opacity:.6;">🏖️</div>
                  <div style="font-weight:700; font-size:14px; color:var(--text);">Belum ada jadwal hari libur di tahun {{ $tahun }}</div>
                  @if(auth()->user()?->isAdmin())
                    <p style="font-size:12px; margin-top:4px;">Gunakan tombol "+ Tambah Hari Libur" atau "Isi Preset Libur Nasional" di atas.</p>
                  @else
                    <p style="font-size:12px; margin-top:4px;">Hubungi Administrator untuk pengelolaan jadwal hari libur.</p>
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

@if(auth()->user()?->isAdmin())
  {{-- MODAL TAMBAH HARI LIBUR --}}
  <div class="modal-overlay" id="modalTambahLibur">
    <div class="modal-card" style="max-width:520px; padding:24px;">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
        <h3 style="font-size:17px; font-weight:900; color:var(--text); margin:0; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-calendar-plus-fill" style="color:var(--gold);"></i> Tambah Jadwal Hari Libur
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
