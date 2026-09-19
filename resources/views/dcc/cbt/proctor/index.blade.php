<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="refresh" content="20" /> <!-- Auto refresh every 20s for live proctoring -->
  <title>Live Proctoring: {{ $jadwal->nama_ujian }} — CBT SMKN 1 AN</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/dcc-cbt.css') }}?v={{ filemtime(public_path('css/dcc-cbt.css')) }}">
</head>
<body class="cbt-app-body">

  @include('dcc.cbt.partials.cbt_header')

  <main class="cbt-container">

    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
      <div>
        <a href="{{ route('admin.cbt.jadwal.index') }}" style="display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:700; color:#0072bc; text-decoration:none; margin-bottom:8px;">
          <i class="bi bi-arrow-left"></i> Kembali ke Jadwal Ujian
        </a>
        <h1 style="margin:6px 0 4px; font-size:22px; font-weight:900; color:#0f172a; display:flex; align-items:center; gap:8px;">
          <span class="cbt-pulse-dot" style="width:12px; height:12px;"></span>
          Pengawasan Langsung (*Live Proctoring*): {{ $jadwal->nama_ujian }}
        </h1>
        <p style="margin:0; font-size:13px; color:#64748b;">
          Paket: <strong>{{ $jadwal->bankSoal->nama_bank ?? '-' }}</strong> | Durasi: <strong>{{ $jadwal->durasi_menit }} Menit</strong> | Auto-refresh setiap 20 detik.
        </p>
      </div>

      <div style="display:flex; align-items:center; gap:12px;">
        <div style="text-align:right;">
          <div style="font-size:11px; color:#64748b; font-weight:700;">TOKEN SESI</div>
          <div style="background:#0f172a; color:#38bdf8; font-family:monospace; font-weight:900; font-size:20px; letter-spacing:4px; padding:4px 14px; border-radius:8px;">
            {{ $jadwal->token_ujian }}
          </div>
        </div>
        <form action="{{ route('admin.cbt.jadwal.refresh_token', $jadwal->id) }}" method="POST" onsubmit="return confirm('Regenerasi token ujian sekarang?')">
          @csrf
          <button type="submit" class="btn btn-outline-secondary btn-sm" title="Refresh Token">
            <i class="bi bi-arrow-repeat"></i>
          </button>
        </form>
      </div>
    </div>

    @if(session('success'))
      <div style="background:#dcfce7; border:1px solid #bbf7d0; color:#166534; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:13.5px; font-weight:600;">
        <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
      </div>
    @endif

    <!-- Status KPI Grid -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:14px; margin-bottom:24px;">
      <div class="cbt-card">
        <div class="cbt-card-body" style="padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
          <div>
            <div style="font-size:24px; font-weight:900; color:#0f172a;">{{ $totalSiswa }}</div>
            <div style="font-size:12px; color:#64748b; font-weight:600;">Total Terdaftar</div>
          </div>
          <i class="bi bi-people text-primary" style="font-size:26px;"></i>
        </div>
      </div>

      <div class="cbt-card">
        <div class="cbt-card-body" style="padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
          <div>
            <div style="font-size:24px; font-weight:900; color:#0284c7;">{{ $sedangMengerjakan }}</div>
            <div style="font-size:12px; color:#64748b; font-weight:600;">Sedang Mengerjakan</div>
          </div>
          <i class="bi bi-pencil-square text-info" style="font-size:26px;"></i>
        </div>
      </div>

      <div class="cbt-card">
        <div class="cbt-card-body" style="padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
          <div>
            <div style="font-size:24px; font-weight:900; color:#16a34a;">{{ $selesai }}</div>
            <div style="font-size:12px; color:#64748b; font-weight:600;">Sudah Selesai</div>
          </div>
          <i class="bi bi-check2-circle text-success" style="font-size:26px;"></i>
        </div>
      </div>

      <div class="cbt-card">
        <div class="cbt-card-body" style="padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
          <div>
            <div style="font-size:24px; font-weight:900; color:#94a3b8;">{{ $belum }}</div>
            <div style="font-size:12px; color:#64748b; font-weight:600;">Belum Masuk</div>
          </div>
          <i class="bi bi-hourglass text-muted" style="font-size:26px;"></i>
        </div>
      </div>
    </div>

    <!-- Live Proctoring Grid: Kartu Peserta Ujian -->
    <div style="margin-bottom:14px; display:flex; justify-content:space-between; align-items:center;">
      <h3 style="margin:0; font-size:16px; font-weight:800; color:#0f172a;">
        Denah Peserta Ujian Live ({{ $pesertas->count() }} Siswa)
      </h3>
      <div style="font-size:12px; color:#64748b;">
        <span class="badge bg-success" style="font-size:10px;">● Online</span>
        <span class="badge bg-warning text-dark" style="font-size:10px; margin-left:4px;">● Ada Peringatan Curang</span>
        <span class="badge bg-secondary" style="font-size:10px; margin-left:4px;">● Selesai</span>
      </div>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:14px;">
      @forelse($pesertas as $p)
        @php
          $isMengerjakan = ($p->status === 'mengerjakan');
          $isSelesai = ($p->status === 'selesai');
          $totalSoal = $jadwal->bankSoal->soals->count() ?: 1;
          $terjawabCount = $p->jawabans->whereNotNull('jawaban_siswa')->count();
          $persenJawab = round(($terjawabCount / $totalSoal) * 100);
        @endphp

        <div class="cbt-proctor-card {{ $isMengerjakan ? 'cbt-proctor-online' : '' }} {{ $p->jumlah_pelanggaran > 0 ? 'cbt-proctor-warning' : '' }}">
          <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
            <div>
              <div style="font-weight:800; font-size:13.5px; color:#0f172a;">{{ $p->siswa->nama_siswa }}</div>
              <div style="font-size:11px; color:#64748b;">NISN: {{ $p->siswa->nisn }} | {{ $p->siswa->rombel->nama_rombel ?? '-' }}</div>
            </div>
            <div>
              @if($isMengerjakan)
                <span class="badge bg-success" style="font-size:10.5px;">MENGERJAKAN</span>
              @elseif($isSelesai)
                <span class="badge bg-secondary" style="font-size:10.5px;">SELESAI</span>
              @else
                <span class="badge bg-light text-dark border" style="font-size:10.5px;">BELUM</span>
              @endif
            </div>
          </div>

          <!-- Progress Bar Soal Terjawab -->
          <div style="margin:10px 0;">
            <div style="display:flex; justify-content:space-between; font-size:11px; color:#64748b; margin-bottom:4px;">
              <span>Terjawab: <strong>{{ $terjawabCount }} / {{ $totalSoal }}</strong></span>
              <span><strong>{{ $persenJawab }}%</strong></span>
            </div>
            <div class="progress" style="height:6px; background:#e2e8f0; border-radius:4px;">
              <div class="progress-bar {{ $isSelesai ? 'bg-secondary' : 'bg-primary' }}" role="progressbar" style="width: {{ $persenJawab }}%;"></div>
            </div>
          </div>

          <!-- Warning Pelanggaran Tab Switch -->
          @if($p->jumlah_pelanggaran > 0)
            <div style="background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; padding:4px 8px; border-radius:6px; font-size:11px; font-weight:700; margin-bottom:8px;">
              <i class="bi bi-exclamation-triangle-fill me-1"></i> Pindah Tab: {{ $p->jumlah_pelanggaran }}x
            </div>
          @endif

          <!-- Action Buttons -->
          <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #f1f5f9; padding-top:8px; margin-top:8px;">
            <span style="font-size:10.5px; color:#94a3b8;">
              IP: {{ $p->ip_address ? Str::limit($p->ip_address, 15) : '-' }}
            </span>
            <div style="display:flex; gap:6px;">
              <form action="{{ route('admin.cbt.proctor.reset_login', $p->id) }}" method="POST" onsubmit="return confirm('Reset login {{ $p->siswa->nama_siswa }}? Siswa dapat login kembali.')">
                @csrf
                <button type="submit" class="btn btn-sm btn-light border" style="font-size:11px; padding:2px 6px;" title="Reset Login">
                  <i class="bi bi-arrow-repeat text-warning"></i> Reset
                </button>
              </form>
              @if($isMengerjakan)
                <form action="{{ route('admin.cbt.proctor.force_finish', $p->id) }}" method="POST" onsubmit="return confirm('Paksa selesai ujian {{ $p->siswa->nama_siswa }} sekarang?')">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-danger" style="font-size:11px; padding:2px 6px;" title="Paksa Selesai">
                    <i class="bi bi-stop-circle"></i> Selesai
                  </button>
                </form>
              @endif
            </div>
          </div>
        </div>
      @empty
        <div style="grid-column:1 / -1; padding:40px 20px; text-align:center; color:#94a3b8; background:#ffffff; border-radius:10px;">
          Belum ada peserta yang terdaftar pada sesi ujian ini.
        </div>
      @endforelse
    </div>

  </main>

</body>
</html>
