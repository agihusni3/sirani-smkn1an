<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Bank Soal: {{ $bank->nama_bank }} — CBT SMKN 1 AN</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/dcc-cbt.css') }}?v={{ filemtime(public_path('css/dcc-cbt.css')) }}">
</head>
<body class="cbt-app-body">

  @include('dcc.cbt.partials.cbt_header')

  <main class="cbt-container" style="max-width:760px;">

    <div style="margin-bottom:20px;">
      <a href="{{ route('admin.cbt.bank.index') }}" class="cbt-btn-back">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Bank Soal
      </a>
      <h1 style="margin:10px 0 4px; font-size:20px; font-weight:900; color:#0f172a;">
        Edit Paket Bank Soal
      </h1>
      <p style="margin:0; font-size:13px; color:#64748b;">
        Kode Bank: <span class="badge bg-light text-primary border" style="font-family:monospace;">{{ $bank->kode_bank }}</span>
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
        <form action="{{ route('admin.cbt.bank.update', $bank->id) }}" method="POST">
          @csrf
          @method('PUT')

          <div style="margin-bottom:16px;">
            <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Nama Paket Bank Soal *</label>
            <input type="text" name="nama_bank" value="{{ old('nama_bank', $bank->nama_bank) }}" required class="form-control" style="font-size:13px; border-radius:8px;" />
          </div>

          <div style="margin-bottom:16px;">
            <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Mata Pelajaran *</label>
            <input type="text" name="mata_pelajaran" value="{{ old('mata_pelajaran', $bank->mata_pelajaran) }}" required class="form-control" style="font-size:13px; border-radius:8px;" />
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:16px;">
            <div>
              <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Tingkat Kelas *</label>
              <select name="tingkat" class="form-select" required style="font-size:13px; border-radius:8px;">
                <option value="10" {{ old('tingkat', $bank->tingkat) == '10' ? 'selected' : '' }}>Kelas X (Fase E)</option>
                <option value="11" {{ old('tingkat', $bank->tingkat) == '11' ? 'selected' : '' }}>Kelas XI (Fase F)</option>
                <option value="12" {{ old('tingkat', $bank->tingkat) == '12' ? 'selected' : '' }}>Kelas XII (Fase F)</option>
                <option value="semua" {{ old('tingkat', $bank->tingkat) == 'semua' ? 'selected' : '' }}>Semua Tingkat</option>
              </select>
            </div>
            <div>
              <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">KKTP Acuan *</label>
              <input type="number" name="kktp_default" value="{{ old('kktp_default', $bank->kktp_default) }}" min="0" max="100" required class="form-control" style="font-size:13px; border-radius:8px;" />
            </div>
          </div>

          <div style="margin-bottom:16px;">
            <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Jurusan (Opsional)</label>
            <select name="jurusan_id" class="form-select" style="font-size:13px; border-radius:8px;">
              <option value="">-- Semua Jurusan / Umum --</option>
              @foreach($jurusans as $jur)
                <option value="{{ $jur->id }}" {{ old('jurusan_id', $bank->jurusan_id) == $jur->id ? 'selected' : '' }}>
                  {{ $jur->nama_jurusan }} ({{ $jur->singkatan }})
                </option>
              @endforeach
            </select>
          </div>

          <div style="margin-bottom:16px;">
            <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Deskripsi / Petunjuk Pengerjaan</label>
            <textarea name="deskripsi" rows="3" class="form-control" style="font-size:13px; border-radius:8px;">{{ old('deskripsi', $bank->deskripsi) }}</textarea>
          </div>

          <div style="margin-bottom:24px;">
            <label style="display:flex; align-items:center; gap:8px; font-size:12.5px; font-weight:600; cursor:pointer;">
              <input type="checkbox" name="is_shared" value="1" {{ old('is_shared', $bank->is_shared) ? 'checked' : '' }} />
              <span>Bagikan Bank Soal ini ke guru mapel serumpun (Shared Bank)</span>
            </label>
          </div>

          <div style="display:flex; justify-content:flex-end; gap:10px;">
            <a href="{{ route('admin.cbt.bank.index') }}" class="btn btn-light" style="font-weight:700; font-size:13px; border:1px solid #cbd5e1;">Batal</a>
            <button type="submit" class="btn btn-primary" style="font-weight:700; font-size:13px; padding:8px 22px;">Simpan Perubahan</button>
          </div>

        </form>
      </div>
    </div>

  </main>

</body>
</html>
