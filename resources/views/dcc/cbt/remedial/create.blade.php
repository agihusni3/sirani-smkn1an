<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Buat Sesi Remedial 1-Klik: {{ $parent->nama_ujian }} — CBT SMKN 1 AN</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/dcc-cbt.css') }}?v={{ filemtime(public_path('css/dcc-cbt.css')) }}">
</head>
<body class="cbt-app-body">

  @include('dcc.cbt.partials.cbt_header')

  <main class="cbt-container" style="max-width:880px;">

    <div style="margin-bottom:20px;">
      <a href="{{ route('admin.cbt.rekap.nilai', $parent->id) }}" style="display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:700; color:#0072bc; text-decoration:none; margin-bottom:8px;">
        <i class="bi bi-arrow-left"></i> Kembali ke Rekap Nilai Ujian
      </a>
      <h1 style="margin:6px 0 4px; font-size:22px; font-weight:900; color:#0f172a;">
        <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Buat Sesi Remedial Otomatis 1-Klik
      </h1>
      <p style="margin:0; font-size:13.5px; color:#64748b;">
        Ujian Induk: <strong>{{ $parent->nama_ujian }}</strong> (KKTP: <strong>{{ $parent->kktp }}</strong>)
      </p>
    </div>

    @if($pesertaRemidi->isEmpty())
      <div style="background:#dcfce7; border:1px solid #bbf7d0; color:#166534; padding:20px; border-radius:10px; text-align:center;">
        <i class="bi bi-check-circle-fill" style="font-size:32px; display:block; margin-bottom:8px;"></i>
        <strong style="font-size:16px;">Luar Biasa! Seluruh Siswa Telah Tuntas.</strong>
        <p style="margin:6px 0 0; font-size:13.5px;">Tidak ditemukan siswa dengan nilai di bawah batas KKTP ({{ $parent->kktp }}).</p>
      </div>
    @else

      <div class="cbt-card">
        <div class="cbt-card-body" style="padding:26px;">
          <form action="{{ route('admin.cbt.remedial.store', $parent->id) }}" method="POST">
            @csrf

            <div style="background:#fffbeb; border:1px solid #fef3c7; border-radius:10px; padding:16px; margin-bottom:20px; display:flex; gap:14px; align-items:center;">
              <i class="bi bi-info-circle-fill text-warning" style="font-size:24px; flex-shrink:0;"></i>
              <div style="font-size:13px; color:#92400e; line-height:1.5;">
                Sistem secara otomatis mendeteksi <strong>{{ $pesertaRemidi->count() }} siswa</strong> yang belum mencapai KKTP pada ujian ini. Anda dapat menentukan kebijakan penskoran remedial di bawah ini.
              </div>
            </div>

            <div style="margin-bottom:18px;">
              <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Judul Sesi Remedial *</label>
              <input type="text" name="nama_ujian" value="{{ old('nama_ujian', 'Remedial: ' . $parent->nama_ujian) }}" required class="form-control" style="font-size:13.5px; border-radius:8px;" />
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:18px;">
              <div>
                <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Paket Bank Soal Remedial *</label>
                <select name="bank_soal_id" class="form-select" required style="font-size:13px; border-radius:8px;">
                  @foreach($banks as $b)
                    <option value="{{ $b->id }}" {{ $b->id == $parent->bank_soal_id ? 'selected' : '' }}>
                      {{ $b->kode_bank }} — {{ $b->nama_bank }} ({{ $b->mata_pelajaran }})
                    </option>
                  @endforeach
                </select>
                <span style="display:block; font-size:11px; color:#94a3b8; margin-top:3px;">Secara default menggunakan bank soal yang sama (diacak), atau Anda dapat memilih paket soal remedial khusus.</span>
              </div>

              <div>
                <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Kebijakan Nilai Remedial *</label>
                <select name="remedial_policy" class="form-select" required style="font-size:13px; border-radius:8px;">
                  <option value="cap_kktp" selected>1. Mentok KKTP (Maksimal bernilai {{ $parent->kktp }} - Standar Kurikulum)</option>
                  <option value="nilai_tertinggi">2. Nilai Tertinggi (Ambil nilai terbaik Ujian 1 vs Remedial)</option>
                  <option value="rata_rata">3. Rata-Rata (Rata-rata nilai Ujian 1 dan Nilai Remedial)</option>
                  <option value="murni">4. Nilai Murni (Sesuai perolehan asli nilai remedial)</option>
                </select>
              </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr 120px 140px; gap:14px; margin-bottom:18px;">
              <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:4px;">Waktu Mulai *</label>
                <input type="datetime-local" name="waktu_mulai" value="{{ old('waktu_mulai', date('Y-m-d\TH:i')) }}" required class="form-control" style="font-size:13px; border-radius:8px;" />
              </div>
              <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:4px;">Waktu Selesai *</label>
                <input type="datetime-local" name="waktu_selesai" value="{{ old('waktu_selesai', date('Y-m-d\TH:i', strtotime('+3 hours'))) }}" required class="form-control" style="font-size:13px; border-radius:8px;" />
              </div>
              <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:4px;">Durasi (Mnt) *</label>
                <input type="number" name="durasi_menit" value="{{ old('durasi_menit', $parent->durasi_menit) }}" min="5" required class="form-control" style="font-size:13px; border-radius:8px;" />
              </div>
              <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:4px;">Token Remidi *</label>
                <input type="text" name="token_ujian" value="{{ $tokenBaru }}" maxlength="6" required class="form-control" style="font-family:monospace; font-weight:800; text-align:center; background:#0f172a; color:#38bdf8; font-size:15px; letter-spacing:2px; border-radius:8px;" />
              </div>
            </div>

            <!-- Peserta Sasaran Remedial (Checkbox List) -->
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:16px; margin-bottom:24px;">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <label style="font-size:13px; font-weight:800; color:#0f172a; margin:0;">
                  Daftar Siswa Belum Tuntas ({{ $pesertaRemidi->count() }} Siswa)
                </label>
                <div style="font-size:12px;">
                  <button type="button" class="btn btn-sm btn-link p-0" onclick="document.querySelectorAll('.chk-siswa-remidi').forEach(c => c.checked = true)">Pilih Semua</button>
                  <span style="color:#cbd5e1; margin:0 4px;">•</span>
                  <button type="button" class="btn btn-sm btn-link p-0 text-secondary" onclick="document.querySelectorAll('.chk-siswa-remidi').forEach(c => c.checked = false)">Kosongkan</button>
                </div>
              </div>

              <div style="max-height:260px; overflow-y:auto; padding-right:6px;">
                <table class="table table-sm table-bordered" style="font-size:12.5px; margin:0; background:#ffffff;">
                  <thead class="table-light">
                    <tr>
                      <th style="width:40px; text-align:center;">Ikut</th>
                      <th>NISN</th>
                      <th>Nama Siswa</th>
                      <th>Rombel</th>
                      <th style="text-align:center;">Nilai Sebelumnya</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($pesertaRemidi as $p)
                      <tr>
                        <td style="text-align:center;">
                          <input type="checkbox" name="siswa_ids[]" value="{{ $p->siswa_id }}" checked class="chk-siswa-remidi" />
                        </td>
                        <td style="font-family:monospace;">{{ $p->siswa->nisn }}</td>
                        <td style="font-weight:700; color:#0f172a;">{{ $p->siswa->nama_siswa }}</td>
                        <td>{{ $p->siswa->rombel->nama_rombel ?? '-' }}</td>
                        <td style="text-align:center; font-weight:800; color:#dc2626;">
                          {{ $p->nilai_akhir !== null ? $p->nilai_akhir : 'Belum Ada' }}
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
              <a href="{{ route('admin.cbt.rekap.nilai', $parent->id) }}" class="btn btn-light" style="font-weight:700; font-size:13px; border:1px solid #cbd5e1;">Batal</a>
              <button type="submit" class="btn btn-warning" style="font-weight:800; font-size:13.5px; padding:9px 24px; border-radius:8px; color:#78350f;">
                <i class="bi bi-lightning-charge-fill me-1"></i> Terbitkan Sesi Remedial Sekarang
              </button>
            </div>

          </form>
        </div>
      </div>

    @endif

  </main>

</body>
</html>
