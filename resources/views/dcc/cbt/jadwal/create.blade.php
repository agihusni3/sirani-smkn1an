<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Jadwalkan Sesi Ujian Baru — CBT SMKN 1 AN</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/dcc-cbt.css') }}?v={{ filemtime(public_path('css/dcc-cbt.css')) }}">
</head>
<body class="cbt-app-body">

  @include('dcc.cbt.partials.cbt_header')

  <main class="cbt-container" style="max-width:880px;">

    <div style="margin-bottom:20px;">
      <a href="{{ route('admin.cbt.jadwal.index') }}" style="display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:700; color:#0072bc; text-decoration:none; margin-bottom:8px;">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Jadwal
      </a>
      <h1 style="margin:6px 0 4px; font-size:22px; font-weight:900; color:#0f172a;">
        Jadwalkan Sesi Ujian Baru
      </h1>
      <p style="margin:0; font-size:13.5px; color:#64748b;">
        Tentukan paket bank soal, kelas rombel peserta, durasi, dan token pengawasan.
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
      <div class="cbt-card-body" style="padding:26px;">
        <form action="{{ route('admin.cbt.jadwal.store') }}" method="POST">
          @csrf

          <div style="display:grid; grid-template-columns:2fr 1fr; gap:16px; margin-bottom:18px;">
            <div>
              <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Pilih Paket Bank Soal *</label>
              <select name="bank_soal_id" class="form-select" required style="font-size:13px; border-radius:8px;">
                <option value="">-- Pilih Paket Bank Soal --</option>
                @foreach($banks as $b)
                  <option value="{{ $b->id }}" {{ old('bank_soal_id') == $b->id ? 'selected' : '' }}>
                    {{ $b->kode_bank }} — {{ $b->nama_bank }} ({{ $b->mata_pelajaran }} | {{ $b->soals_count }} butir)
                  </option>
                @endforeach
              </select>
            </div>
            <div>
              <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Tipe Asesmen *</label>
              <select name="tipe_ujian" class="form-select" required style="font-size:13px; border-radius:8px;">
                <option value="uh" {{ old('tipe_ujian') == 'uh' ? 'selected' : '' }}>Ulangan Harian (UH)</option>
                <option value="pts" {{ old('tipe_ujian') == 'pts' ? 'selected' : '' }}>Penilaian Tengah Semester (PTS/STS)</option>
                <option value="pas" {{ old('tipe_ujian') == 'pas' ? 'selected' : '' }}>Penilaian Akhir Semester (PAS/SAS)</option>
                <option value="us" {{ old('tipe_ujian') == 'us' ? 'selected' : '' }}>Ujian Sekolah (US)</option>
                <option value="asesmen" {{ old('tipe_ujian') == 'asesmen' ? 'selected' : '' }}>Asesmen Diagnostik / Formatif</option>
              </select>
            </div>
          </div>

          <div style="margin-bottom:18px;">
            <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Nama / Judul Sesi Ujian *</label>
            <input type="text" name="nama_ujian" value="{{ old('nama_ujian') }}" placeholder="Contoh: STS Ganjil 2026 - Matematika Kelas X" required class="form-control" style="font-size:13.5px; border-radius:8px;" />
          </div>

          @if($isSupervisor)
            <div style="margin-bottom:18px;">
              <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Guru Penanggung Jawab *</label>
              <select name="guru_id" class="form-select" style="font-size:13px; border-radius:8px;">
                @foreach($gurus as $g)
                  <option value="{{ $g->id }}" {{ old('guru_id') == $g->id ? 'selected' : '' }}>{{ $g->nama_guru }}</option>
                @endforeach
              </select>
            </div>
          @endif

          <div style="display:grid; grid-template-columns:1fr 1fr 120px 120px; gap:14px; margin-bottom:18px;">
            <div>
              <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:4px;">Waktu Mulai Akses *</label>
              <input type="datetime-local" name="waktu_mulai" value="{{ old('waktu_mulai', date('Y-m-d\TH:i')) }}" required class="form-control" style="font-size:13px; border-radius:8px;" />
            </div>
            <div>
              <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:4px;">Waktu Selesai Akses *</label>
              <input type="datetime-local" name="waktu_selesai" value="{{ old('waktu_selesai', date('Y-m-d\TH:i', strtotime('+2 hours'))) }}" required class="form-control" style="font-size:13px; border-radius:8px;" />
            </div>
            <div>
              <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:4px;">Durasi (Mnt) *</label>
              <input type="number" name="durasi_menit" value="{{ old('durasi_menit', 60) }}" min="5" required class="form-control" style="font-size:13px; border-radius:8px;" />
            </div>
            <div>
              <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:4px;">Target KKTP *</label>
              <input type="number" name="kktp" value="{{ old('kktp', 75) }}" min="0" max="100" required class="form-control" style="font-size:13px; border-radius:8px;" />
            </div>
          </div>

          <div style="margin-bottom:18px;">
            <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Token Masuk Sesi Ujian *</label>
            <div style="display:flex; align-items:center; gap:12px;">
              <input type="text" name="token_ujian" value="{{ old('token_ujian', $token) }}" maxlength="6" required class="form-control" style="font-family:monospace; font-size:18px; font-weight:900; letter-spacing:4px; text-transform:uppercase; max-width:200px; text-align:center; background:#0f172a; color:#38bdf8; border-radius:8px;" />
              <span style="font-size:12px; color:#64748b;">Token 6-karakter acak untuk dibagikan ke siswa saat ujian dimulai.</span>
            </div>
          </div>

          <div style="margin-bottom:20px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:16px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
              <label style="font-size:13px; font-weight:800; color:#0f172a; margin:0;">Pilih Kelas / Rombel Sasaran Peserta *</label>
              <div style="font-size:12px;">
                <button type="button" class="btn btn-sm btn-link p-0" onclick="document.querySelectorAll('.chk-rombel').forEach(c => c.checked = true)">Pilih Semua</button>
                <span style="color:#cbd5e1; margin:0 4px;">•</span>
                <button type="button" class="btn btn-sm btn-link p-0 text-secondary" onclick="document.querySelectorAll('.chk-rombel').forEach(c => c.checked = false)">Kosongkan</button>
              </div>
            </div>
            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(180px, 1fr)); gap:8px; max-height:220px; overflow-y:auto; padding-right:6px;">
              @foreach($rombels as $r)
                <label style="display:flex; align-items:center; gap:8px; padding:6px 10px; background:#ffffff; border:1px solid #cbd5e1; border-radius:6px; font-size:12.5px; cursor:pointer;">
                  <input type="checkbox" name="rombels[]" value="{{ $r->id }}" class="chk-rombel" {{ (is_array(old('rombels')) && in_array($r->id, old('rombels'))) ? 'checked' : '' }} />
                  <span style="font-weight:600; color:#1e293b;">{{ $r->nama_rombel }}</span>
                </label>
              @endforeach
            </div>
          </div>

          <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:16px; margin-bottom:24px;">
            <h4 style="margin:0 0 10px; font-size:13px; font-weight:800; color:#166534;">Konfigurasi Integritas & Keamanan Ujian</h4>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:12px;">
              <label style="display:flex; align-items:center; gap:8px; font-size:12.5px; font-weight:600; cursor:pointer;">
                <input type="checkbox" name="acak_soal" value="1" {{ old('acak_soal', true) ? 'checked' : '' }} />
                <span>Acak Nomor Soal Tiap Siswa</span>
              </label>
              <label style="display:flex; align-items:center; gap:8px; font-size:12.5px; font-weight:600; cursor:pointer;">
                <input type="checkbox" name="acak_opsi" value="1" {{ old('acak_opsi', true) ? 'checked' : '' }} />
                <span>Acak Opsi Pilihan Ganda (A-E)</span>
              </label>
              <label style="display:flex; align-items:center; gap:8px; font-size:12.5px; font-weight:600; cursor:pointer;">
                <input type="checkbox" name="tampilkan_nilai" value="1" {{ old('tampilkan_nilai') ? 'checked' : '' }} />
                <span>Tampilkan Nilai ke Siswa saat Selesai</span>
              </label>
            </div>
          </div>

          <div style="display:flex; justify-content:flex-end; gap:10px;">
            <a href="{{ route('admin.cbt.jadwal.index') }}" class="btn btn-light" style="font-weight:700; font-size:13px; border:1px solid #cbd5e1;">Batal</a>
            <button type="submit" class="btn btn-primary" style="font-weight:700; font-size:13.5px; padding:9px 26px; border-radius:8px;">
              <i class="bi bi-calendar-check me-1"></i> Terbitkan Jadwal Ujian
            </button>
          </div>

        </form>
      </div>
    </div>

  </main>

</body>
</html>
