<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bank Soal — CBT SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/dcc-cbt.css') }}?v={{ filemtime(public_path('css/dcc-cbt.css')) }}">
</head>
<body class="cbt-app-body">

  @include('dcc.cbt.partials.cbt_header')

  <main class="cbt-container">

    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
      <div>
        <h1 style="margin:0 0 4px; font-size:22px; font-weight:900; color:#0f172a;">
          <i class="bi bi-collection-fill text-primary me-2"></i> Bank Soal Digital
        </h1>
        <p style="margin:0; font-size:13.5px; color:#64748b;">
          Kelola paket soal asesmen, tagging indikator TP Kurikulum Merdeka, dan impor CSV instan.
        </p>
      </div>
      <div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahBank" style="font-weight:800; font-size:13.5px; border-radius:8px; padding:10px 20px;">
          <i class="bi bi-plus-circle-fill me-1"></i> Buat Paket Bank Soal
        </button>
      </div>
    </div>

    @if(session('success'))
      <div style="background:#dcfce7; border:1px solid #bbf7d0; color:#166534; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:13.5px; font-weight:600;">
        <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div style="background:#fee2e2; border:1px solid #fecaca; color:#991b1b; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:13.5px; font-weight:600;">
        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
      </div>
    @endif

    <!-- Search & Filter Bar -->
    <div class="cbt-card" style="margin-bottom:20px;">
      <div class="cbt-card-body" style="padding:14px 18px;">
        <form method="GET" action="{{ route('admin.cbt.bank.index') }}" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
          <div style="flex-grow:1; min-width:220px;">
            <input type="text" name="mapel" value="{{ request('mapel') }}" placeholder="Cari berdasarkan nama bank soal atau mapel..." class="form-control" style="font-size:13px; border-radius:8px;" />
          </div>
          <div style="width:160px;">
            <select name="tingkat" class="form-select" style="font-size:13px; border-radius:8px;">
              <option value="">Semua Tingkat</option>
              <option value="10" {{ request('tingkat') == '10' ? 'selected' : '' }}>Kelas X (Fase E)</option>
              <option value="11" {{ request('tingkat') == '11' ? 'selected' : '' }}>Kelas XI (Fase F)</option>
              <option value="12" {{ request('tingkat') == '12' ? 'selected' : '' }}>Kelas XII (Fase F)</option>
            </select>
          </div>
          <div>
            <button type="submit" class="btn btn-primary" style="font-weight:700; font-size:13px; border-radius:8px;">
              <i class="bi bi-filter me-1"></i> Filter
            </button>
            @if(request()->hasAny(['mapel', 'tingkat']))
              <a href="{{ route('admin.cbt.bank.index') }}" class="btn btn-outline-secondary" style="font-size:13px; border-radius:8px;">Reset</a>
            @endif
          </div>
        </form>
      </div>
    </div>

    <!-- Grid Kartu Bank Soal -->
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(290px, 1fr)); gap:18px;">
      @forelse($banks as $b)
        <div class="cbt-course-card">
          <div class="cbt-course-stripe"></div>
          <div class="cbt-course-content">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
              <span class="cbt-course-code">{{ $b->kode_bank }}</span>
              @if($b->is_shared)
                <span class="badge bg-info text-dark" style="font-size:10px;">SHARED</span>
              @endif
            </div>

            <h3 class="cbt-course-title">
              <a href="{{ route('admin.cbt.soal.index', $b->id) }}" style="color:inherit; text-decoration:none;">
                {{ $b->nama_bank }}
              </a>
            </h3>

            <div style="font-size:12.5px; color:#64748b; margin-bottom:10px;">
              <i class="bi bi-journal-text me-1"></i> {{ $b->mata_pelajaran }}
              <span style="color:#cbd5e1; margin:0 4px;">•</span>
              Kelas {{ strtoupper($b->tingkat) }}
            </div>

            <div style="display:flex; gap:8px; margin-bottom:12px; flex-wrap:wrap;">
              <span class="badge bg-light text-dark border" style="font-weight:700;">
                <i class="bi bi-list-ol text-primary me-1"></i> {{ $b->soals_count }} Butir
              </span>
              <span class="badge bg-light text-dark border" style="font-weight:700;">
                KKTP: {{ $b->kktp_default }}
              </span>
              @if($b->jurusan)
                <span class="badge bg-light text-secondary border">{{ $b->jurusan->singkatan ?? 'Umum' }}</span>
              @endif
            </div>

            <div style="font-size:11.5px; color:#94a3b8;">
              <i class="bi bi-person-circle me-1"></i> {{ $b->guru->nama_guru ?? 'Guru Mapel' }}
            </div>
          </div>

          <div class="cbt-course-footer">
            <a href="{{ route('admin.cbt.soal.index', $b->id) }}" class="btn btn-sm btn-primary" style="font-size:12px; font-weight:700; border-radius:6px; padding:5px 12px;">
              <i class="bi bi-pencil-square me-1"></i> Kelola Butir ({{ $b->soals_count }})
            </a>
            <div style="display:flex; gap:6px;">
              <a href="{{ route('admin.cbt.bank.edit', $b->id) }}" class="btn btn-sm btn-outline-secondary" style="font-size:12px; padding:5px 8px; border-radius:6px;" title="Edit Pengaturan Bank">
                <i class="bi bi-gear"></i>
              </a>
              <form action="{{ route('admin.cbt.bank.destroy', $b->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus paket bank soal ini beserta seluruh isinya?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" style="font-size:12px; padding:5px 8px; border-radius:6px;" title="Hapus Bank Soal">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </div>
          </div>
        </div>
      @empty
        <div style="grid-column: 1 / -1; background:#ffffff; border:1px dashed #cbd5e1; border-radius:12px; padding:48px 20px; text-align:center; color:#94a3b8;">
          <i class="bi bi-folder-x" style="font-size:44px; display:block; margin-bottom:10px; color:#cbd5e1;"></i>
          Tidak ada paket Bank Soal yang sesuai kriteria pencarian.
        </div>
      @endforelse
    </div>

    @if($banks->hasPages())
      <div style="margin-top:24px;">
        {{ $banks->links() }}
      </div>
    @endif

  </main>

  <!-- Modal Tambah Bank Soal -->
  <div class="modal fade" id="modalTambahBank" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="border-radius:12px; border:none; box-shadow:0 10px 25px rgba(0,0,0,0.15);">
        <form action="{{ route('admin.cbt.bank.store') }}" method="POST">
          @csrf
          <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:16px 20px;">
            <h5 class="modal-title" style="font-weight:800; font-size:16px; color:#0f172a;">
              <i class="bi bi-plus-circle-fill text-primary me-2"></i> Buat Paket Bank Soal Baru
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" style="padding:20px;">
            <div style="margin-bottom:14px;">
              <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:4px;">Nama Paket Soal *</label>
              <input type="text" name="nama_bank" placeholder="Contoh: STS Ganjil - Dasar Pemrograman X" required class="form-control" style="font-size:13px; border-radius:8px;" />
            </div>
            <div style="margin-bottom:14px;">
              <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:4px;">Mata Pelajaran *</label>
              <input type="text" name="mata_pelajaran" placeholder="Contoh: Matematika / Informatika" required class="form-control" style="font-size:13px; border-radius:8px;" />
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
              <div>
                <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:4px;">Tingkat Kelas *</label>
                <select name="tingkat" class="form-select" required style="font-size:13px; border-radius:8px;">
                  <option value="10">Kelas X (Fase E)</option>
                  <option value="11">Kelas XI (Fase F)</option>
                  <option value="12">Kelas XII (Fase F)</option>
                  <option value="semua">Semua Tingkat</option>
                </select>
              </div>
              <div>
                <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:4px;">KKTP Acuan *</label>
                <input type="number" name="kktp_default" value="75" min="0" max="100" required class="form-control" style="font-size:13px; border-radius:8px;" />
              </div>
            </div>
            <div style="margin-bottom:14px;">
              <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:4px;">Jurusan (Opsional)</label>
              <select name="jurusan_id" class="form-select" style="font-size:13px; border-radius:8px;">
                <option value="">-- Semua Jurusan / Umum --</option>
                @foreach($jurusans as $jur)
                  <option value="{{ $jur->id }}">{{ $jur->nama_jurusan }} ({{ $jur->singkatan }})</option>
                @endforeach
              </select>
            </div>

            @if($isSupervisor)
              <div style="margin-bottom:14px;">
                <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:4px;">Tetapkan Guru Pemilik *</label>
                <select name="guru_id" class="form-select" style="font-size:13px; border-radius:8px;">
                  @foreach($gurus as $g)
                    <option value="{{ $g->id }}">{{ $g->nama_guru }}</option>
                  @endforeach
                </select>
              </div>
            @endif

            <div style="margin-bottom:14px;">
              <label style="display:flex; align-items:center; gap:8px; font-size:12.5px; font-weight:600; cursor:pointer;">
                <input type="checkbox" name="is_shared" value="1" />
                <span>Bagikan Bank Soal ini ke guru mapel serumpun (Shared Bank)</span>
              </label>
            </div>
          </div>
          <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:12px 20px;">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="font-size:13px; font-weight:700;">Batal</button>
            <button type="submit" class="btn btn-primary" style="font-size:13px; font-weight:700; padding:8px 20px;">Simpan Bank Soal</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
