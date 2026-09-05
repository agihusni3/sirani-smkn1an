<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dasbor Panitia PPDB Online 2026 — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/admin-ppdb.css') }}?v={{ filemtime(public_path('css/admin-ppdb.css')) }}">
  <style>
    .ppdb-hero-bar {
      background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #4338ca 100%);
      border-radius: 16px;
      padding: 22px 24px;
      color: #ffffff;
      margin-bottom: 20px;
      box-shadow: 0 10px 25px rgba(49, 46, 129, 0.18);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 16px;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .ppdb-jurusan-strip {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 12px;
      margin-bottom: 20px;
    }
    .ppdb-jurusan-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 14px 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      transition: all 0.15s ease;
    }
    .ppdb-jurusan-card:hover {
      border-color: #d97706;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .status-tab-btn {
      padding: 8px 14px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 700;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      border: 1px solid var(--border);
      background: var(--surface);
      color: var(--text-2);
      transition: all 0.15s ease;
    }
    .status-tab-btn.active {
      background: #4338ca;
      color: #ffffff;
      border-color: #4338ca;
      box-shadow: 0 2px 8px rgba(67, 56, 202, 0.25);
    }
  </style>
</head>
<body>
<div class="app-container">
  @include('partials.sidebar_ppdb')
  
  <main class="main-content">
    
    {{-- PPDB HERO HEADER --}}
    <div class="ppdb-hero-bar no-print">
      <div>
        <div style="display:inline-flex; align-items:center; gap:6px; background:rgba(245,158,11,0.2); color:#fde68a; font-size:11px; font-weight:800; padding:3px 10px; border-radius:20px; margin-bottom:8px; border:1px solid rgba(245,158,11,0.35);">
          <i class="bi bi-mortarboard-fill"></i> WORKSPACE RESMI PPDB 2026/2027
        </div>
        <h1 style="margin:0 0 6px; font-size:22px; font-weight:900; letter-spacing:-0.02em;">
          Dasbor Seleksi Penerimaan Siswa Baru
        </h1>
        <p style="margin:0; font-size:13px; color:#c7d2fe; max-width:640px;">
          Pusat pemantauan berkas, seleksi kuota jurusan, verifikasi persyaratan pendaftar, dan kelulusan calon peserta didik SMKN 1 Air Naningan.
        </p>
      </div>

      <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
        <a href="{{ route('admin.portal') }}" class="btn btn-sm" style="background:rgba(255,255,255,0.12); color:#ffffff; font-weight:800; border-radius:8px; font-size:12px; padding:8px 14px; border:1px solid rgba(255,255,255,0.25); text-decoration:none; display:inline-flex; align-items:center; gap:6px;" title="Buka Digital Command Center SMKN 1 AN">
          <i class="bi bi-command" style="color:#38bdf8;"></i> DCC SMKN 1 AN
        </a>
        <a href="{{ route('ppdb.formulir') }}" target="_blank" class="btn btn-sm" style="background:#f59e0b; color:#000000; font-weight:900; border-radius:8px; font-size:12px; padding:8px 16px; border:none; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
          <i class="bi bi-box-arrow-up-right"></i> Form Pendaftaran Publik
        </a>
      </div>
    </div>

    @if(session('success'))
      <div class="panel" style="background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; padding:12px 16px; margin-bottom:16px; border-radius:var(--r-sm); font-size:13px; font-weight:700;">
        <i class="bi bi-check-circle-fill" style="margin-right:6px;"></i> {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="panel" style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:12px 16px; margin-bottom:16px; border-radius:var(--r-sm); font-size:13px; font-weight:700;">
        <i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i> {{ session('error') }}
      </div>
    @endif

    {{-- STATISTIK FUNNEL SELEKSI PPDB --}}
    <div class="ppdb-stat-grid" style="margin-bottom:20px;">
      <div class="ppdb-stat-card">
        <div style="width:40px; height:40px; border-radius:10px; background:rgba(67,56,202,0.12); color:#4338ca; display:flex; align-items:center; justify-content:center; font-size:20px;">
          <i class="bi bi-people-fill"></i>
        </div>
        <div>
          <div class="ppdb-stat-val" style="color:#4338ca;">{{ $counts['total'] }}</div>
          <div style="font-size:11.5px; color:var(--text-3); font-weight:700;">Total Pendaftar</div>
        </div>
      </div>

      <div class="ppdb-stat-card">
        <div style="width:40px; height:40px; border-radius:10px; background:rgba(217,119,6,0.12); color:#d97706; display:flex; align-items:center; justify-content:center; font-size:20px;">
          <i class="bi bi-clock-history"></i>
        </div>
        <div>
          <div class="ppdb-stat-val" style="color:#d97706;">{{ $counts['menunggu'] }}</div>
          <div style="font-size:11.5px; color:var(--text-3); font-weight:700;">Menunggu Cek Berkas</div>
        </div>
      </div>

      <div class="ppdb-stat-card">
        <div style="width:40px; height:40px; border-radius:10px; background:rgba(2,132,199,0.12); color:#0284c7; display:flex; align-items:center; justify-content:center; font-size:20px;">
          <i class="bi bi-file-earmark-check-fill"></i>
        </div>
        <div>
          <div class="ppdb-stat-val" style="color:#0284c7;">{{ $counts['berkas_valid'] }}</div>
          <div style="font-size:11.5px; color:var(--text-3); font-weight:700;">Berkas Valid</div>
        </div>
      </div>

      <div class="ppdb-stat-card">
        <div style="width:40px; height:40px; border-radius:10px; background:rgba(16,185,129,0.12); color:#10b981; display:flex; align-items:center; justify-content:center; font-size:20px;">
          <i class="bi bi-check-circle-fill"></i>
        </div>
        <div>
          <div class="ppdb-stat-val" style="color:#10b981;">{{ $counts['diterima'] }}</div>
          <div style="font-size:11.5px; color:var(--text-3); font-weight:700;">Siswa Diterima</div>
        </div>
      </div>

      <div class="ppdb-stat-card">
        <div style="width:40px; height:40px; border-radius:10px; background:rgba(239,68,68,0.12); color:#ef4444; display:flex; align-items:center; justify-content:center; font-size:20px;">
          <i class="bi bi-x-circle-fill"></i>
        </div>
        <div>
          <div class="ppdb-stat-val" style="color:#ef4444;">{{ $counts['ditolak'] }}</div>
          <div style="font-size:11.5px; color:var(--text-3); font-weight:700;">Ditolak / Draf</div>
        </div>
      </div>
    </div>

    {{-- PEMINATAN JURUSAN KEJURUAN --}}
    @if(isset($jurusanStats) && $jurusanStats->isNotEmpty())
      <div style="margin-bottom:12px; font-size:12.5px; font-weight:800; color:var(--text); display:flex; align-items:center; gap:6px;">
        <i class="bi bi-diagram-3-fill" style="color:#d97706;"></i> Peminatan Jurusan Kejuruan (Pilihan 1):
      </div>
      <div class="ppdb-jurusan-strip">
        @foreach($jurusanStats as $js)
          <div class="ppdb-jurusan-card">
            <div>
              <div style="font-size:13px; font-weight:900; color:var(--text);">{{ $js['kode'] }}</div>
              <div style="font-size:11px; color:var(--text-3);">{{ Str::limit($js['nama'], 26) }}</div>
            </div>
            <div style="text-align:right;">
              <span style="font-size:16px; font-weight:900; color:#4338ca; font-family:var(--font-mono);">{{ $js['peminat'] }}</span>
              <div style="font-size:10px; color:#10b981; font-weight:700;">{{ $js['diterima'] }} Diterima</div>
            </div>
          </div>
        @endforeach
      </div>
    @endif

    {{-- STATUS TABS & FILTER PENCARIAN --}}
    <div class="panel" style="background:var(--bg-2); border:1px solid var(--border); padding:14px 18px; margin-bottom:16px; border-radius:12px;">
      
      {{-- Tab Navigasi Cepat --}}
      <div style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:14px; padding-bottom:12px; border-bottom:1px solid var(--border-2);">
        <a href="{{ route('admin.ppdb.index') }}" class="status-tab-btn {{ empty(request('status')) ? 'active' : '' }}">
          <i class="bi bi-people"></i> Semua Pendaftar ({{ $counts['total'] }})
        </a>
        <a href="{{ route('admin.ppdb.index', ['status' => 'menunggu']) }}" class="status-tab-btn {{ request('status') === 'menunggu' ? 'active' : '' }}">
          <i class="bi bi-clock"></i> Menunggu Cek ({{ $counts['menunggu'] }})
        </a>
        <a href="{{ route('admin.ppdb.index', ['status' => 'berkas_valid']) }}" class="status-tab-btn {{ request('status') === 'berkas_valid' ? 'active' : '' }}">
          <i class="bi bi-patch-check"></i> Berkas Valid ({{ $counts['berkas_valid'] }})
        </a>
        <a href="{{ route('admin.ppdb.index', ['status' => 'diterima']) }}" class="status-tab-btn {{ request('status') === 'diterima' ? 'active' : '' }}">
          <i class="bi bi-check-circle"></i> Diterima ({{ $counts['diterima'] }})
        </a>
        <a href="{{ route('admin.ppdb.index', ['status' => 'ditolak']) }}" class="status-tab-btn {{ request('status') === 'ditolak' ? 'active' : '' }}">
          <i class="bi bi-x-circle"></i> Ditolak ({{ $counts['ditolak'] }})
        </a>
      </div>

      {{-- Form Filter & Pencarian --}}
      <form method="GET" action="{{ route('admin.ppdb.index') }}" style="display:flex; flex-wrap:wrap; gap:10px; align-items:center; justify-content:space-between;">
        <div style="display:flex; flex-wrap:wrap; gap:8px; align-items:center; flex:1;">
          <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Ketik nama calon siswa, NISN, nomor pendaftaran, atau asal sekolah..." style="min-width:300px; flex:1; height:38px; padding:0 14px; font-size:12.5px; border-radius:8px; border:1px solid var(--border); background:var(--surface); color:var(--text);">
          
          <select name="jurusan_id" onchange="this.form.submit()" style="height:38px; padding:0 12px; font-size:12px; border-radius:8px; border:1px solid var(--border); background:var(--surface); color:var(--text);">
            <option value="">-- Semua Jurusan --</option>
            @foreach($jurusans as $j)
              <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->kode_jurusan }} — {{ $j->nama_jurusan }}</option>
            @endforeach
          </select>

          @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
          @endif

          <button type="submit" class="btn btn-sm" style="background:#4338ca; color:#ffffff; height:38px; padding:0 16px; font-size:12.5px; font-weight:800; border-radius:8px; border:none; cursor:pointer;">
            <i class="bi bi-search"></i> Cari
          </button>

          @if(request()->hasAny(['cari', 'status', 'jurusan_id']))
            <a href="{{ route('admin.ppdb.index') }}" class="btn btn-sm" style="height:38px; padding:0 12px; display:inline-flex; align-items:center; justify-content:center; background:var(--surface); border:1px solid var(--border); color:var(--text); font-size:12px; border-radius:8px; text-decoration:none;">
              Reset Filter
            </a>
          @endif
        </div>
      </form>
    </div>

    {{-- TABEL DATA PENDAFTAR --}}
    <div class="panel" style="background:var(--bg-2); border:1px solid var(--border); border-radius:12px; overflow:hidden;">
      <div style="overflow-x:auto;">
        <table class="table" style="width:100%; border-collapse:collapse; font-size:12px; margin:0;">
          <thead>
            <tr style="background:var(--surface); border-bottom:2px solid var(--border); text-align:left;">
              <th style="padding:12px 14px;">No. Pendaftaran</th>
              <th style="padding:12px 14px;">Calon Siswa</th>
              <th style="padding:12px 14px;">Asal Sekolah</th>
              <th style="padding:12px 14px;">Pilihan Jurusan</th>
              <th style="padding:12px 14px; text-align:center;">Jalur</th>
              <th style="padding:12px 14px; text-align:center;">Status Berkas</th>
              <th style="padding:12px 14px; text-align:center;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($pendaftars as $p)
              <tr style="border-bottom:1px solid var(--border); transition:background 0.1s ease;">
                <td style="padding:12px 14px; font-family:var(--font-mono); font-weight:800; color:#4338ca;">
                  {{ $p->no_pendaftaran ?? $p->nomor_pendaftaran }}
                </td>
                <td style="padding:12px 14px;">
                  <div style="font-weight:800; font-size:13px; color:var(--text);">{{ $p->nama_lengkap }}</div>
                  <div style="font-size:11px; color:var(--text-3); font-family:var(--font-mono); margin-top:2px;">
                    NISN: {{ $p->nisn }} · {{ $p->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                  </div>
                </td>
                <td style="padding:12px 14px; color:var(--text-2);">
                  {{ $p->asal_sekolah }}
                </td>
                <td style="padding:12px 14px;">
                  <div style="font-weight:700; color:var(--text);">
                    1. {{ $p->jurusanPilihan1 ? $p->jurusanPilihan1->kode_jurusan : '-' }}
                  </div>
                  @if($p->jurusanPilihan2)
                    <div style="font-size:10.5px; color:var(--text-3); margin-top:1px;">
                      2. {{ $p->jurusanPilihan2->kode_jurusan }}
                    </div>
                  @endif
                </td>
                <td style="padding:12px 14px; text-align:center;">
                  <span style="font-size:10.5px; font-weight:800; padding:2px 8px; border-radius:6px; background:rgba(0,0,0,0.05); text-transform:uppercase;">
                    {{ $p->jalur_pendaftaran ?? 'Reguler' }}
                  </span>
                </td>
                <td style="padding:12px 14px; text-align:center;">
                  @php
                    $stBadge = match($p->status) {
                      'menunggu', 'menunggu_verifikasi', 'draft' => ['bg' => '#fef3c7', 'color' => '#b45309', 'label' => 'Menunggu Cek'],
                      'berkas_valid', 'terverifikasi'            => ['bg' => '#e0f2fe', 'color' => '#0369a1', 'label' => 'Berkas Valid'],
                      'diterima'                                 => ['bg' => '#dcfce7', 'color' => '#15803d', 'label' => 'Diterima'],
                      'ditolak'                                  => ['bg' => '#fee2e2', 'color' => '#b91c1c', 'label' => 'Ditolak'],
                      default                                    => ['bg' => '#f1f5f9', 'color' => '#64748b', 'label' => $p->status],
                    };
                  @endphp
                  <span style="display:inline-block; font-size:11px; font-weight:800; padding:3px 10px; border-radius:12px; background:{{ $stBadge['bg'] }}; color:{{ $stBadge['color'] }};">
                    {{ $stBadge['label'] }}
                  </span>
                </td>
                <td style="padding:12px 14px; text-align:center;">
                  <a href="{{ route('admin.ppdb.show', $p->id) }}" class="btn btn-sm" style="background:#4338ca; color:#ffffff; font-weight:800; font-size:11px; padding:6px 12px; border-radius:6px; text-decoration:none; display:inline-flex; align-items:center; gap:5px;">
                    <i class="bi bi-file-earmark-person"></i> Verifikasi Berkas
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" style="text-align:center; padding:40px 16px; color:var(--text-3);">
                  <i class="bi bi-inbox" style="font-size:32px; display:block; margin-bottom:8px; opacity:0.6;"></i>
                  Tidak ada data calon siswa yang sesuai dengan filter.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- PAGINATION --}}
      @if($pendaftars->hasPages())
        <div style="padding:14px 18px; border-top:1px solid var(--border); display:flex; justify-content:center;">
          {{ $pendaftars->links() }}
        </div>
      @endif
    </div>

  </main>
</div>
</body>
</html>
