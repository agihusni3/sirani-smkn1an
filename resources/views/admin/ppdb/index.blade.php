<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Panitia PPDB Online 2026 — SIRANI SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/admin-ppdb.css') }}?v={{ filemtime(public_path('css/admin-ppdb.css')) }}">
</head>
<body>
<div class="app-container">
  @include('partials.sidebar')
  <main class="main-content">
    
    {{-- HEADER BAR --}}
    <div class="panel no-print" style="background:var(--bg-2); border:1px solid var(--border); padding:12px 18px; margin-bottom:14px; border-radius:var(--r-md); box-shadow:var(--shadow-sm);">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          <h1 style="margin:0; font-size:17px; font-weight:900; color:var(--text); display:inline-flex; align-items:center; gap:8px;">
            <i class="bi bi-mortarboard-fill" style="color:#6366f1; font-size:18px;"></i> Panitia PPDB Online 2026/2027
          </h1>
          <span style="color:var(--border-2); font-weight:300;">|</span>
          <span style="font-size:12px; color:var(--text-3);">
            Penerimaan Calon Peserta Didik Baru &amp; Mutasi Terpadu SIRANI
          </span>
        </div>

        <div style="display:flex; align-items:center; gap:8px;">
          <a href="{{ route('ppdb.formulir') }}" target="_blank" class="btn btn-sm" style="background:#6366f1; color:#fff; font-weight:700; border-radius:6px; font-size:12px; padding:6px 14px; display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-box-arrow-up-right"></i> Buka Form Pendaftaran
          </a>
        </div>
      </div>
    </div>

    @if(session('success'))
      <div class="panel" style="background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; padding:12px 16px; margin-bottom:14px; border-radius:var(--r-sm); font-size:13px; font-weight:700;">
        <i class="bi bi-check-circle-fill" style="margin-right:6px;"></i> {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="panel" style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:12px 16px; margin-bottom:14px; border-radius:var(--r-sm); font-size:13px; font-weight:700;">
        <i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i> {{ session('error') }}
      </div>
    @endif

    {{-- STATISTIK PENDAFTAR --}}
    <div class="ppdb-stat-grid">
      <div class="ppdb-stat-card">
        <div style="width:36px; height:36px; border-radius:8px; background:#e0e7ff; color:#4338ca; display:flex; align-items:center; justify-content:center; font-size:18px;">
          <i class="bi bi-people-fill"></i>
        </div>
        <div>
          <div class="ppdb-stat-val" style="color:#4338ca;">{{ $counts['total'] }}</div>
          <div style="font-size:11px; color:var(--text-3); font-weight:700;">Total Pendaftar</div>
        </div>
      </div>

      <div class="ppdb-stat-card">
        <div style="width:36px; height:36px; border-radius:8px; background:#fef3c7; color:#b45309; display:flex; align-items:center; justify-content:center; font-size:18px;">
          <i class="bi bi-hourglass-split"></i>
        </div>
        <div>
          <div class="ppdb-stat-val" style="color:#b45309;">{{ $counts['menunggu'] }}</div>
          <div style="font-size:11px; color:var(--text-3); font-weight:700;">Menunggu Verifikasi</div>
        </div>
      </div>

      <div class="ppdb-stat-card">
        <div style="width:36px; height:36px; border-radius:8px; background:#e0f2fe; color:#0369a1; display:flex; align-items:center; justify-content:center; font-size:18px;">
          <i class="bi bi-file-earmark-check-fill"></i>
        </div>
        <div>
          <div class="ppdb-stat-val" style="color:#0369a1;">{{ $counts['berkas_valid'] }}</div>
          <div style="font-size:11px; color:var(--text-3); font-weight:700;">Berkas Valid</div>
        </div>
      </div>

      <div class="ppdb-stat-card">
        <div style="width:36px; height:36px; border-radius:8px; background:#dcfce7; color:#15803d; display:flex; align-items:center; justify-content:center; font-size:18px;">
          <i class="bi bi-check-all"></i>
        </div>
        <div>
          <div class="ppdb-stat-val" style="color:#15803d;">{{ $counts['diterima'] }}</div>
          <div style="font-size:11px; color:var(--text-3); font-weight:700;">Diterima</div>
        </div>
      </div>

      <div class="ppdb-stat-card">
        <div style="width:36px; height:36px; border-radius:8px; background:#fee2e2; color:#b91c1c; display:flex; align-items:center; justify-content:center; font-size:18px;">
          <i class="bi bi-x-circle-fill"></i>
        </div>
        <div>
          <div class="ppdb-stat-val" style="color:#b91c1c;">{{ $counts['ditolak'] }}</div>
          <div style="font-size:11px; color:var(--text-3); font-weight:700;">Ditolak</div>
        </div>
      </div>
    </div>

    {{-- FILTER & PENCARIAN --}}
    <div class="panel" style="background:var(--bg-2); border:1px solid var(--border); padding:12px 16px; margin-bottom:14px; border-radius:var(--r-sm);">
      <form method="GET" action="{{ route('admin.ppdb.index') }}" style="display:flex; flex-wrap:wrap; gap:10px; align-items:center; justify-content:space-between;">
        <div style="display:flex; flex-wrap:wrap; gap:8px; align-items:center; flex:1;">
          <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama siswa, NISN, no. pendaftaran, atau asal sekolah..." style="min-width:280px; flex:1; padding:7px 12px; font-size:12.5px; border-radius:6px; border:1px solid var(--border); background:var(--surface); color:var(--text);">
          
          <select name="status" onchange="this.form.submit()" style="padding:7px 12px; font-size:12px; border-radius:6px; border:1px solid var(--border); background:var(--surface); color:var(--text);">
            <option value="">-- Semua Status --</option>
            <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
            <option value="berkas_valid" {{ request('status') == 'berkas_valid' ? 'selected' : '' }}>Berkas Valid</option>
            <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
          </select>

          <select name="jurusan_id" onchange="this.form.submit()" style="padding:7px 12px; font-size:12px; border-radius:6px; border:1px solid var(--border); background:var(--surface); color:var(--text);">
            <option value="">-- Semua Jurusan --</option>
            @foreach($jurusans as $j)
              <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->kode }} - {{ $j->nama_jurusan }}</option>
            @endforeach
          </select>

          <button type="submit" class="btn btn-sm btn-primary" style="padding:7px 14px; font-size:12px; font-weight:700; border-radius:6px;">
            <i class="bi bi-search"></i> Cari
          </button>
          @if(request()->hasAny(['cari', 'status', 'jurusan_id']))
            <a href="{{ route('admin.ppdb.index') }}" class="btn btn-sm" style="background:var(--border); color:var(--text); padding:7px 12px; font-size:12px; border-radius:6px;">Reset</a>
          @endif
        </div>
      </form>
    </div>

    {{-- TABEL DATA PENDAFTAR --}}
    <div class="panel" style="background:var(--bg-2); border:1px solid var(--border); border-radius:var(--r-sm); overflow:hidden;">
      <div style="overflow-x:auto;">
        <table class="table" style="width:100%; border-collapse:collapse; font-size:12px; margin:0;">
          <thead>
            <tr style="background:var(--surface); border-bottom:2px solid var(--border); text-align:left;">
              <th style="padding:10px 12px;">No. Pendaftaran</th>
              <th style="padding:10px 12px;">Calon Siswa</th>
              <th style="padding:10px 12px;">Asal Sekolah</th>
              <th style="padding:10px 12px;">Pilihan Jurusan</th>
              <th style="padding:10px 12px;">Jalur</th>
              <th style="padding:10px 12px; text-align:center;">Status</th>
              <th style="padding:10px 12px; text-align:center;">Mutasi SIRANI</th>
              <th style="padding:10px 12px; text-align:center;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($pendaftars as $p)
              <tr style="border-bottom:1px solid var(--border); vertical-align:middle;">
                <td style="padding:10px 12px; font-family:var(--font-mono); font-weight:800; color:#4338ca;">
                  <a href="{{ route('admin.ppdb.show', $p->id) }}" style="color:inherit; text-decoration:underline;">
                    {{ $p->nomor_pendaftaran }}
                  </a>
                </td>
                <td style="padding:10px 12px;">
                  <div style="font-weight:800; font-size:13px; color:var(--text);">{{ $p->nama_lengkap }}</div>
                  <div style="font-size:11px; color:var(--text-3);">NISN: {{ $p->nisn }} | HP: {{ $p->no_hp_ortu }}</div>
                </td>
                <td style="padding:10px 12px; color:var(--text-2);">
                  {{ $p->asal_sekolah }} ({{ $p->tahun_lulus }})
                </td>
                <td style="padding:10px 12px;">
                  <div><strong style="color:#0284c7;">1. {{ $p->jurusanPilihan1->kode ?? '-' }}</strong> - {{ $p->jurusanPilihan1->nama_jurusan ?? '-' }}</div>
                  @if($p->jurusanPilihan2)
                    <div style="font-size:11px; color:var(--text-3);">2. {{ $p->jurusanPilihan2->kode }}</div>
                  @endif
                </td>
                <td style="padding:10px 12px; text-transform:uppercase; font-weight:700; font-size:11px;">
                  {{ $p->jalur_pendaftaran }}
                </td>
                <td style="padding:10px 12px; text-align:center;">
                  <span class="badge-status badge-{{ $p->status_pendaftaran }}">
                    {{ str_replace('_', ' ', $p->status_pendaftaran) }}
                  </span>
                </td>
                <td style="padding:10px 12px; text-align:center;">
                  @if($p->siswa_id)
                    <span style="font-size:11px; color:#15803d; font-weight:800;">
                      <i class="bi bi-check-circle-fill"></i> Siswa Aktif
                    </span>
                  @elseif($p->status_pendaftaran == 'diterima')
                    {{-- Form Mutasi 1-Klik --}}
                    <form method="POST" action="{{ route('admin.ppdb.mutasi', $p->id) }}" style="display:inline-flex; align-items:center; gap:4px;">
                      @csrf
                      <select name="rombel_id" required style="padding:4px 6px; font-size:11px; border-radius:4px; border:1px solid #86efac; background:#f0fdf4;">
                        <option value="">-- Pilih Kelas X --</option>
                        @foreach($rombels as $r)
                          <option value="{{ $r->id }}">{{ $r->nama_rombel }}</option>
                        @endforeach
                      </select>
                      <button type="submit" class="btn btn-sm" style="background:#16a34a; color:#fff; padding:4px 8px; font-size:11px; font-weight:700; border-radius:4px;" title="Mutasi ke Siswa Aktif SIRANI">
                        Mutasi
                      </button>
                    </form>
                  @else
                    <span style="font-size:11px; color:var(--text-3);">-</span>
                  @endif
                </td>
                <td style="padding:10px 12px; text-align:center; white-space:nowrap;">
                  <a href="{{ route('admin.ppdb.show', $p->id) }}" class="btn btn-sm" style="background:var(--surface); border:1px solid var(--border); color:var(--text); padding:4px 8px; font-size:11px; border-radius:4px;" title="Detail & Verifikasi">
                    <i class="bi bi-eye-fill"></i> Periksa
                  </a>
                  <a href="{{ route('ppdb.cetak', $p->nomor_pendaftaran) }}" target="_blank" class="btn btn-sm" style="background:var(--surface); border:1px solid var(--border); color:#0284c7; padding:4px 8px; font-size:11px; border-radius:4px;" title="Cetak Kartu">
                    <i class="bi bi-printer-fill"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" style="padding:30px; text-align:center; color:var(--text-3);">
                  Belum ada data pendaftar calon siswa yang sesuai dengan filter.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div style="padding:12px; border-top:1px solid var(--border);">
        {{ $pendaftars->withQueryString()->links() }}
      </div>
    </div>

  </main>
</div>
</body>
</html>
