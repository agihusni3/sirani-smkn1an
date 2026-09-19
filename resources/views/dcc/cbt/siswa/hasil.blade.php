<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hasil Ujian: {{ $jadwal->nama_ujian }} — {{ $siswa->nama_siswa }}</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/dcc-cbt.css') }}?v={{ filemtime(public_path('css/dcc-cbt.css')) }}">
</head>
<body style="background:#f4f6f9; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; font-family:'Plus Jakarta Sans', sans-serif;">

  <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; width:100%; max-width:540px; box-shadow:0 10px 30px rgba(0,0,0,0.08); overflow:hidden;">
    
    <div style="background: linear-gradient(135deg, #0a4d80 0%, #0072bc 100%); color:#ffffff; padding:28px 24px; text-align:center;">
      <div style="width:60px; height:60px; border-radius:50%; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; margin:0 auto 12px; font-size:32px;">
        <i class="bi bi-check2-circle"></i>
      </div>
      <h2 style="margin:0 0 4px; font-size:20px; font-weight:900;">Ujian Berhasil Diselesaikan!</h2>
      <p style="margin:0; font-size:13px; opacity:0.9;">
        Jawaban Anda telah tersimpan secara aman di server sekolah.
      </p>
    </div>

    <div style="padding:26px 24px;">

      <!-- Identitas Singkat -->
      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px 18px; margin-bottom:20px; font-size:13px; line-height:1.6;">
        <div style="display:flex; justify-content:space-between;">
          <span style="color:#64748b;">Nama Siswa:</span>
          <span style="font-weight:800; color:#0f172a;">{{ $siswa->nama_siswa }}</span>
        </div>
        <div style="display:flex; justify-content:space-between;">
          <span style="color:#64748b;">NISN:</span>
          <span style="font-family:monospace; font-weight:700;">{{ $siswa->nisn }}</span>
        </div>
        <div style="display:flex; justify-content:space-between;">
          <span style="color:#64748b;">Sesi Ujian:</span>
          <span style="font-weight:700;">{{ $jadwal->nama_ujian }}</span>
        </div>
        <div style="display:flex; justify-content:space-between;">
          <span style="color:#64748b;">Waktu Selesai:</span>
          <span>{{ \Carbon\Carbon::parse($peserta->waktu_selesai)->format('d M Y, H:i') }} WIB</span>
        </div>
      </div>

      <!-- Skor Nilai & Ketuntasan Belajar KKTP -->
      @if($jadwal->tampilkan_nilai || $peserta->nilai_akhir !== null)
        <div style="text-align:center; padding:16px; border:2px dashed {{ $peserta->is_tuntas ? '#86efac' : '#fca5a5' }}; background:{{ $peserta->is_tuntas ? '#f0fdf4' : '#fef2f2' }}; border-radius:12px; margin-bottom:20px;">
          <div style="font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px;">
            Nilai Perolehan Akhir
          </div>
          <div style="font-size:48px; font-weight:900; line-height:1; color:{{ $peserta->is_tuntas ? '#16a34a' : '#dc2626' }}; margin-bottom:8px;">
            {{ $peserta->nilai_akhir }}
          </div>
          <div>
            @if($peserta->is_tuntas)
              <span class="badge" style="background:#16a34a; font-size:12.5px; padding:6px 14px; font-weight:800; letter-spacing:0.5px;">
                <i class="bi bi-check-circle-fill me-1"></i> TUNTAS (KKTP: {{ $jadwal->kktp }})
              </span>
              <p style="font-size:12px; color:#166534; margin:8px 0 0;">
                Selamat! Anda telah mencapai kriteria ketuntasan tujuan pembelajaran.
              </p>
            @else
              <span class="badge" style="background:#dc2626; font-size:12.5px; padding:6px 14px; font-weight:800; letter-spacing:0.5px;">
                <i class="bi bi-exclamation-circle-fill me-1"></i> PERLU REMEDIAL (KKTP: {{ $jadwal->kktp }})
              </span>
              <p style="font-size:12px; color:#991b1b; margin:8px 0 0;">
                Nilai Anda belum mencapai target KKTP. Silakan persiapkan diri untuk sesi bimbingan remedial dari guru pengampu.
              </p>
            @endif
          </div>
        </div>
      @else
        <div style="text-align:center; padding:20px; background:#f8fafc; border-radius:12px; margin-bottom:20px;">
          <i class="bi bi-shield-lock text-primary" style="font-size:32px;"></i>
          <p style="margin:10px 0 0; font-size:13px; color:#64748b;">
            Nilai ujian akan diumumkan secara resmi oleh Guru Mata Pelajaran setelah seluruh rangkaian koreksi selesai.
          </p>
        </div>
      @endif

      @if($peserta->jumlah_pelanggaran > 0)
        <div style="background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; padding:10px 14px; border-radius:8px; font-size:12px; margin-bottom:18px;">
          <i class="bi bi-exclamation-triangle-fill me-1"></i>
          Catatan Integritas: Tercatat <strong>{{ $peserta->jumlah_pelanggaran }} kali</strong> beralih tab/layar selama ujian berlangsung.
        </div>
      @endif

      <div style="display:flex; justify-content:center; gap:10px;">
        <a href="{{ route('cbt.siswa.dashboard') }}" class="btn btn-primary" style="background:#0072bc; border-color:#0072bc; font-weight:800; font-size:13.5px; padding:10px 24px; border-radius:10px;">
          <i class="bi bi-house-door me-1"></i> Kembali ke Beranda CBT
        </a>
      </div>

    </div>

  </div>

</body>
</html>
