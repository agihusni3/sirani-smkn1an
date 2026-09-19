<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Butir Soal: {{ $bank->nama_bank }} — CBT SMKN 1 AN</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/dcc-cbt.css') }}?v={{ filemtime(public_path('css/dcc-cbt.css')) }}">
</head>
<body class="cbt-app-body">

  @include('dcc.cbt.partials.cbt_header')

  <main class="cbt-container">

    <div style="margin-bottom:20px;">
      <a href="{{ route('admin.cbt.bank.index') }}" style="display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:700; color:#0072bc; text-decoration:none; margin-bottom:8px;">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Bank Soal
      </a>
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
          <h1 style="margin:0 0 4px; font-size:22px; font-weight:900; color:#0f172a;">
            {{ $bank->nama_bank }}
          </h1>
          <p style="margin:0; font-size:13px; color:#64748b;">
            Mata Pelajaran: <strong>{{ $bank->mata_pelajaran }}</strong> | Tingkat: <strong>Kelas {{ strtoupper($bank->tingkat) }}</strong> | Total: <strong>{{ $soals->count() }} Butir Soal</strong>
          </p>
        </div>
        <div style="display:flex; gap:10px;">
          <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalImportCsv" style="font-weight:700; font-size:13px; border-radius:8px; padding:8px 16px;">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Impor Soal CSV
          </button>
          <a href="{{ route('admin.cbt.soal.create', $bank->id) }}" class="btn btn-primary" style="font-weight:800; font-size:13px; border-radius:8px; padding:8px 18px;">
            <i class="bi bi-plus-circle-fill me-1"></i> Tambah Soal
          </a>
        </div>
      </div>
    </div>

    @if(session('success'))
      <div style="background:#dcfce7; border:1px solid #bbf7d0; color:#166534; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:13.5px; font-weight:600;">
        <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
      </div>
    @endif

    <div class="cbt-card">
      <div class="cbt-card-body" style="padding:0;">
        @forelse($soals as $s)
          <div style="padding:20px 24px; border-bottom:1px solid #e2e8f0; display:flex; gap:16px; align-items:flex-start;">
            <div style="width:36px; height:36px; border-radius:8px; background:#0072bc; color:#ffffff; font-weight:800; font-size:15px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
              {{ $s->nomor_urut }}
            </div>

            <div style="flex-grow:1;">
              <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                <span class="badge" style="background:#e0f2fe; color:#0369a1; font-weight:700; font-size:11px; text-transform:uppercase;">
                  {{ strtoupper($s->jenis_soal) }}
                </span>
                <span class="badge bg-light text-dark border" style="font-size:11px;">Bobot: {{ $s->bobot_nilai }}</span>
                @if($s->kode_tp)
                  <span class="badge bg-light text-secondary border" style="font-size:11px;">
                    <i class="bi bi-tag me-1"></i> TP: {{ $s->kode_tp }}
                  </span>
                @endif
              </div>

              @if($s->media_gambar)
                <div style="margin-bottom:12px;">
                  <img src="{{ asset($s->media_gambar) }}" alt="Lampiran Soal" style="max-width:320px; max-height:200px; border-radius:8px; border:1px solid #cbd5e1; object-fit:contain;" />
                </div>
              @endif

              <div style="font-size:14.5px; color:#1e293b; line-height:1.6; margin-bottom:14px;">
                {!! nl2br(e($s->pertanyaan)) !!}
              </div>

              @if($s->jenis_soal === 'pg')
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:8px; margin-bottom:12px;">
                  @foreach(['A', 'B', 'C', 'D', 'E'] as $huruf)
                    @php 
                      $opsiKey = 'opsi_' . strtolower($huruf); 
                      $isKunci = (strtoupper(trim($s->kunci_jawaban)) === $huruf);
                    @endphp
                    @if(!empty($s->$opsiKey))
                      <div style="display:flex; align-items:center; gap:8px; padding:6px 12px; border-radius:6px; border:1px solid {{ $isKunci ? '#22c55e' : '#e2e8f0' }}; background: {{ $isKunci ? '#f0fdf4' : '#f8fafc' }}; font-size:13px;">
                        <span style="font-weight:800; font-size:12px; color:{{ $isKunci ? '#166534' : '#64748b' }};">{{ $huruf }}.</span>
                        <span style="color:{{ $isKunci ? '#166534' : '#334155' }}; font-weight:{{ $isKunci ? '700' : 'normal' }};">{{ $s->$opsiKey }}</span>
                        @if($isKunci)
                          <i class="bi bi-check-circle-fill text-success ms-auto"></i>
                        @endif
                      </div>
                    @endif
                  @endforeach
                </div>
                <div style="font-size:12.5px; color:#166534; font-weight:700;">
                  <i class="bi bi-key-fill me-1"></i> Kunci Benar: Opsi {{ strtoupper($s->kunci_jawaban) }}
                </div>
              @elseif($s->jenis_soal === 'esai')
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px 14px; font-size:12.5px; color:#475569;">
                  <strong>Panduan Kunci / Rubrik Esai:</strong> {{ $s->kunci_jawaban ?: 'Belum diset' }}
                </div>
              @endif
            </div>

            <div style="display:flex; gap:6px; flex-shrink:0;">
              <a href="{{ route('admin.cbt.soal.edit', [$bank->id, $s->id]) }}" class="btn btn-sm btn-outline-secondary" style="font-size:12px; padding:4px 8px; border-radius:6px;" title="Edit Soal">
                <i class="bi bi-pencil"></i>
              </a>
              <form action="{{ route('admin.cbt.soal.destroy', [$bank->id, $s->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus butir soal #{{ $s->nomor_urut }}?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" style="font-size:12px; padding:4px 8px; border-radius:6px;" title="Hapus Soal">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </div>
          </div>
        @empty
          <div style="text-align:center; padding:56px 20px; color:#94a3b8;">
            <i class="bi bi-question-circle" style="font-size:44px; display:block; margin-bottom:12px; color:#cbd5e1;"></i>
            Belum ada butir soal di dalam paket bank ini.
            <div style="margin-top:14px; display:flex; justify-content:center; gap:10px;">
              <a href="{{ route('admin.cbt.soal.create', $bank->id) }}" class="btn btn-sm btn-primary" style="font-weight:700; border-radius:8px;">
                <i class="bi bi-plus-circle me-1"></i> Tambah Soal Manual
              </a>
              <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalImportCsv" style="font-weight:700; border-radius:8px;">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Impor dari CSV
              </button>
            </div>
          </div>
        @endforelse
      </div>
    </div>

  </main>

  <!-- Modal Impor CSV -->
  <div class="modal fade" id="modalImportCsv" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="border-radius:12px; border:none;">
        <form action="{{ route('admin.cbt.soal.import_csv', $bank->id) }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:16px 20px;">
            <h5 class="modal-title" style="font-weight:800; font-size:16px; color:#0f172a;">
              <i class="bi bi-file-earmark-spreadsheet text-success me-2"></i> Impor Soal dari File CSV
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" style="padding:20px;">
            <div style="margin-bottom:16px;">
              <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Pilih File CSV (*.csv) *</label>
              <input type="file" name="file_csv" accept=".csv,text/csv" required class="form-control" style="font-size:13px; border-radius:8px;" />
            </div>

            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; font-size:12px; color:#475569; line-height:1.5;">
              <strong style="color:#0f172a;">Susunan Kolom CSV:</strong><br />
              <code>Pertanyaan, Opsi A, Opsi B, Opsi C, Opsi D, Opsi E, Kunci (A-E), Bobot (1.0), Kode TP</code>
              <div style="margin-top:6px; color:#64748b;">
                Baris pertama otomatis dianggap header. Kunci jawaban diisi huruf <strong>A, B, C, D, atau E</strong>.
              </div>
            </div>
          </div>
          <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:12px 20px;">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="font-size:13px; font-weight:700;">Batal</button>
            <button type="submit" class="btn btn-success" style="font-size:13px; font-weight:700; padding:8px 20px;">Mulai Impor Soal</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
