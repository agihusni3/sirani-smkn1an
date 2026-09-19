<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tambah Butir Soal: {{ $bank->nama_bank }} — CBT SMKN 1 AN</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/dcc-cbt.css') }}?v={{ filemtime(public_path('css/dcc-cbt.css')) }}">
</head>
<body class="cbt-app-body">

  @include('dcc.cbt.partials.cbt_header')

  <main class="cbt-container" style="max-width:840px;">

    <div style="margin-bottom:20px;">
      <a href="{{ route('admin.cbt.soal.index', $bank->id) }}" style="display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:700; color:#0072bc; text-decoration:none; margin-bottom:8px;">
        <i class="bi bi-arrow-left"></i> Kembali ke Butir Soal
      </a>
      <h1 style="margin:6px 0 4px; font-size:20px; font-weight:900; color:#0f172a;">
        Tambah Butir Soal Baru
      </h1>
      <p style="margin:0; font-size:13px; color:#64748b;">
        Paket: <strong>{{ $bank->nama_bank }}</strong> ({{ $bank->mata_pelajaran }})
      </p>
    </div>

    @if(isset($errors) && $errors->any())
      <div style="background:#fee2e2; border:1px solid #fecaca; color:#b91c1c; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:13px;">
        <ul style="margin:0; padding-left:18px;">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="cbt-card">
      <div class="cbt-card-body" style="padding:24px;">
        <form action="{{ route('admin.cbt.soal.store', $bank->id) }}" method="POST" enctype="multipart/form-data">
          @csrf

          <div style="display:grid; grid-template-columns:100px 150px 120px 1fr; gap:14px; margin-bottom:18px;">
            <div>
              <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:4px;">No. Urut *</label>
              <input type="number" name="nomor_urut" value="{{ old('nomor_urut', $nextNomor) }}" required class="form-control" style="font-size:13px; border-radius:8px;" />
            </div>
            <div>
              <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:4px;">Bentuk Soal *</label>
              <select name="jenis_soal" id="jenis_soal" onchange="toggleBentukSoal(this.value)" class="form-select" style="font-size:13px; border-radius:8px;" required>
                <option value="pg">Pilihan Ganda</option>
                <option value="esai">Uraian / Esai</option>
              </select>
            </div>
            <div>
              <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:4px;">Bobot Nilai *</label>
              <input type="number" step="0.1" name="bobot_nilai" value="1.0" required class="form-control" style="font-size:13px; border-radius:8px;" />
            </div>
            <div>
              <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:4px;">Kode TP (Kurikulum Merdeka)</label>
              <input type="text" name="kode_tp" value="{{ old('kode_tp') }}" placeholder="Contoh: TP 1.2" class="form-control" style="font-size:13px; border-radius:8px;" />
            </div>
          </div>

          <div style="margin-bottom:18px;">
            <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Teks Pertanyaan *</label>
            <textarea name="pertanyaan" rows="5" required placeholder="Tuliskan pertanyaan soal di sini..." class="form-control" style="font-size:13.5px; line-height:1.6; border-radius:8px;">{{ old('pertanyaan') }}</textarea>
          </div>

          <div style="margin-bottom:18px;">
            <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Lampiran Gambar Soal (Opsional)</label>
            <input type="file" name="media_gambar" accept="image/*" class="form-control" style="font-size:13px; border-radius:8px;" />
            <span style="display:block; font-size:11px; color:#94a3b8; margin-top:3px;">Mendukung format JPG, PNG, WebP maksimal 3 MB.</span>
          </div>

          {{-- Section Pilihan Ganda --}}
          <div id="section-pg" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:18px; margin-bottom:18px;">
            <h4 style="margin:0 0 12px; font-size:13.5px; font-weight:800; color:#0f172a;">Opsi Pilihan Jawaban:</h4>

            @foreach(['A', 'B', 'C', 'D', 'E'] as $huruf)
              <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                <span style="font-weight:900; font-size:12px; width:28px; height:28px; border-radius:50%; background:#e0f2fe; color:#0369a1; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                  {{ $huruf }}
                </span>
                <input type="text" name="opsi_{{ strtolower($huruf) }}" value="{{ old('opsi_' . strtolower($huruf)) }}" placeholder="Teks opsi {{ $huruf }} {{ $huruf === 'E' ? '(Opsional)' : '*' }}" class="form-control" style="font-size:13px; border-radius:8px;" />
              </div>
            @endforeach

            <div style="margin-top:14px; padding-top:14px; border-top:1px solid #e2e8f0;">
              <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:6px;">Kunci Jawaban Benar *</label>
              <div style="display:flex; gap:16px;">
                @foreach(['A', 'B', 'C', 'D', 'E'] as $huruf)
                  <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-weight:700; font-size:13px;">
                    <input type="radio" name="kunci_jawaban" value="{{ $huruf }}" {{ $huruf === 'A' ? 'checked' : '' }} />
                    <span>Opsi {{ $huruf }}</span>
                  </label>
                @endforeach
              </div>
            </div>
          </div>

          {{-- Section Esai --}}
          <div id="section-esai" style="display:none; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:18px; margin-bottom:18px;">
            <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:6px;">Panduan Kunci Jawaban / Rubrik Esai *</label>
            <textarea name="kunci_jawaban_esai" rows="3" placeholder="Pedoman koreksi atau kata kunci jawaban esai..." class="form-control" style="font-size:13px; border-radius:8px;"></textarea>
          </div>

          <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:24px;">
            <a href="{{ route('admin.cbt.soal.index', $bank->id) }}" class="btn btn-light" style="font-weight:700; font-size:13px; border:1px solid #cbd5e1;">Batal</a>
            <button type="submit" class="btn btn-primary" style="font-weight:700; font-size:13px; padding:8px 24px;">
              <i class="bi bi-save"></i> Simpan Butir Soal
            </button>
          </div>

        </form>
      </div>
    </div>

  </main>

  <script>
    function toggleBentukSoal(val) {
      const secPg = document.getElementById('section-pg');
      const secEsai = document.getElementById('section-esai');
      if (val === 'esai') {
        secPg.style.display = 'none';
        secEsai.style.display = 'block';
      } else {
        secPg.style.display = 'block';
        secEsai.style.display = 'none';
      }
    }
  </script>
</body>
</html>
