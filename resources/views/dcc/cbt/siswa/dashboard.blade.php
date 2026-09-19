<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Peserta Ujian — {{ $siswa->nama_siswa }}</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/dcc-cbt.css') }}?v={{ filemtime(public_path('css/dcc-cbt.css')) }}">
</head>
<body class="cbt-app-body">

  <header class="cbt-navbar-top">
    <div class="cbt-brand-section">
      <img src="{{ asset('images/logo.png') }}" alt="Logo" class="cbt-brand-logo" onerror="this.src='{{ asset('apple-touch-icon.png') }}'" />
      <div class="cbt-brand-text">
        <h1>PORTAL CBT SISWA</h1>
        <span>SMK NEGERI 1 AIR NANINGAN</span>
      </div>
    </div>

    <div class="cbt-user-profile">
      <div style="text-align:right;">
        <div style="font-weight:800; font-size:13px; color:#ffffff;">{{ $siswa->nama_siswa }}</div>
        <div style="font-size:11px; color:#93c5fd;">NISN: {{ $siswa->nisn }} | {{ $siswa->rombel->nama_rombel ?? '-' }}</div>
      </div>
      <a href="{{ route('cbt.siswa.logout') }}" class="btn btn-sm btn-outline-light" style="font-size:11.5px; border-radius:6px;" onclick="return confirm('Keluar dari akun CBT?')">
        <i class="bi bi-box-arrow-right"></i> Keluar
      </a>
    </div>
  </header>

  <main class="cbt-container" style="max-width:960px;">

    <!-- Card Profil Siswa Singkat -->
    <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:20px 24px; margin-bottom:24px; display:flex; align-items:center; gap:18px; flex-wrap:wrap; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
      <div style="width:52px; height:52px; border-radius:50%; background:#e0f2fe; color:#0369a1; font-size:22px; font-weight:900; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        {{ strtoupper(substr($siswa->nama_siswa, 0, 1)) }}
      </div>
      <div style="flex-grow:1;">
        <h2 style="margin:0 0 4px; font-size:18px; font-weight:800; color:#0f172a;">{{ $siswa->nama_siswa }}</h2>
        <div style="font-size:12.5px; color:#64748b;">
          NISN: <strong>{{ $siswa->nisn }}</strong>
          <span style="color:#cbd5e1; margin:0 6px;">•</span>
          Kelas: <strong>{{ $siswa->rombel->nama_rombel ?? '-' }}</strong>
          <span style="color:#cbd5e1; margin:0 6px;">•</span>
          Jurusan: <strong>{{ $siswa->rombel->jurusan->nama_jurusan ?? '-' }}</strong>
        </div>
      </div>
      <div>
        <span class="badge" style="background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; padding:6px 12px; font-size:12px; font-weight:700;">
          <i class="bi bi-check-circle-fill me-1"></i> Terverifikasi Ujian
        </span>
      </div>
    </div>

    @if(session('success'))
      <div style="background:#dcfce7; border:1px solid #bbf7d0; color:#166534; padding:12px 16px; border-radius:8px; margin-bottom:18px; font-size:13.5px; font-weight:600;">
        <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div style="background:#fee2e2; border:1px solid #fecaca; color:#991b1b; padding:12px 16px; border-radius:8px; margin-bottom:18px; font-size:13.5px; font-weight:600;">
        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
      </div>
    @endif

    <!-- Section: Ujian Aktif Hari Ini -->
    <div style="margin-bottom:32px;">
      <h3 style="margin:0 0 14px; font-size:17px; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:8px;">
        <i class="bi bi-broadcast text-primary"></i> Sesi Ujian Aktif Saat Ini
      </h3>

      @if($jadwalAktif->isEmpty())
        <div style="background:#ffffff; border:1px dashed #cbd5e1; border-radius:12px; padding:36px 20px; text-align:center; color:#94a3b8;">
          <i class="bi bi-calendar-check" style="font-size:36px; display:block; margin-bottom:8px; color:#cbd5e1;"></i>
          Tidak ada sesi ujian aktif yang dijadwalkan untuk kelas Anda saat ini.
        </div>
      @else
        <div style="display:grid; grid-template-columns:1fr; gap:14px;">
          @foreach($jadwalAktif as $ja)
            <div class="cbt-card">
              <div class="cbt-card-body" style="padding:22px;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px; margin-bottom:14px;">
                  <div>
                    <span class="badge" style="background:#e0f2fe; color:#0369a1; font-weight:700; font-size:11px; text-transform:uppercase; margin-bottom:6px;">
                      {{ strtoupper($ja->tipe_ujian) }}
                      @if($ja->is_remedial)
                        • REMEDIAL
                      @endif
                    </span>
                    <h4 style="margin:0 0 4px; font-size:17px; font-weight:900; color:#0f172a;">
                      {{ $ja->nama_ujian }}
                    </h4>
                    <div style="font-size:13px; color:#64748b;">
                      Mata Pelajaran: <strong>{{ $ja->bankSoal->mata_pelajaran ?? '-' }}</strong>
                      <span style="color:#cbd5e1; margin:0 6px;">•</span>
                      Jumlah: <strong>{{ $ja->bankSoal->soals->count() }} Butir Soal</strong>
                      <span style="color:#cbd5e1; margin:0 6px;">•</span>
                      Durasi: <strong>{{ $ja->durasi_menit }} Menit</strong>
                    </div>
                  </div>

                  <div style="text-align:right;">
                    <div style="font-size:11px; color:#64748b;">Batas Waktu Selesai:</div>
                    <div style="font-size:13px; font-weight:700; color:#0f172a;">
                      {{ \Carbon\Carbon::parse($ja->waktu_selesai)->format('H:i') }} WIB
                    </div>
                  </div>
                </div>

                <!-- Form Masukkan Token -->
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:16px;">
                  <form action="{{ route('cbt.siswa.verifikasi_token', $ja->id) }}" method="POST" style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                    @csrf
                    <div style="flex-grow:1; min-width:200px;">
                      <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:4px;">
                        Masukkan Token 6-Karakter dari Pengawas:
                      </label>
                      <input type="text" name="token_ujian" required placeholder="Contoh: AB12CD" maxlength="6" class="form-control" style="font-family:monospace; font-weight:900; font-size:16px; letter-spacing:3px; text-transform:uppercase; border-radius:8px; background:#ffffff;" />
                    </div>
                    <div style="align-self:flex-end;">
                      <button type="submit" class="btn btn-primary" style="background:#0072bc; border-color:#0072bc; font-weight:800; font-size:13.5px; padding:10px 22px; border-radius:8px;">
                        Mulai Ujian <i class="bi bi-arrow-right ms-1"></i>
                      </button>
                    </div>
                  </form>
                </div>

              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>

    <!-- Section: Riwayat Ujian Siswa -->
    <div>
      <h3 style="margin:0 0 14px; font-size:16px; font-weight:800; color:#0f172a;">
        <i class="bi bi-clock-history text-secondary me-1"></i> Riwayat Ujian yang Telah Diselesaikan
      </h3>

      <div class="cbt-card">
        <div class="cbt-card-body" style="padding:0;">
          <div style="overflow-x:auto;">
            <table class="cbt-table" style="margin:0;">
              <thead>
                <tr>
                  <th>Nama Ujian</th>
                  <th>Mata Pelajaran</th>
                  <th>Waktu Selesai</th>
                  <th style="text-align:center;">Nilai</th>
                  <th style="text-align:center;">Status</th>
                  <th style="text-align:center;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($riwayatUjian as $r)
                  <tr>
                    <td style="font-weight:700; color:#0f172a;">{{ $r->jadwal->nama_ujian ?? '-' }}</td>
                    <td>{{ $r->jadwal->bankSoal->mata_pelajaran ?? '-' }}</td>
                    <td style="font-size:12px; color:#64748b;">
                      {{ \Carbon\Carbon::parse($r->waktu_selesai)->format('d M Y, H:i') }}
                    </td>
                    <td style="text-align:center; font-weight:900; font-size:15px; color:{{ $r->is_tuntas ? '#16a34a' : '#dc2626' }};">
                      @if($r->jadwal->tampilkan_nilai || $r->is_tuntas !== null)
                        {{ $r->nilai_akhir }}
                      @else
                        <span style="font-size:12px; color:#94a3b8; font-weight:normal;">Tersimpan</span>
                      @endif
                    </td>
                    <td style="text-align:center;">
                      @if($r->is_tuntas)
                        <span class="cbt-status-badge cbt-status-badge-tuntas">TUNTAS</span>
                      @else
                        <span class="cbt-status-badge cbt-status-badge-remidi">REMIDIAL</span>
                      @endif
                    </td>
                    <td style="text-align:center;">
                      <a href="{{ route('cbt.siswa.hasil', $r->jadwal_ujian_id ?? ($r->jadwal->id ?? 1)) }}" class="btn btn-sm btn-outline-primary" style="font-size:11.5px; border-radius:6px;">
                        Lihat Rincian
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" style="text-align:center; padding:32px; color:#94a3b8;">
                      Belum ada riwayat ujian yang diselesaikan.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </main>

</body>
</html>
