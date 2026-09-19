<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DCC Asesmen & Belajar Tuntas — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/dcc-cbt.css') }}?v={{ filemtime(public_path('css/dcc-cbt.css')) }}">
</head>
<body class="cbt-app-body">

  @include('dcc.cbt.partials.cbt_header')

  <main class="cbt-container">

    <!-- Hero / Welcome Banner -->
    <div style="background: linear-gradient(135deg, #0072bc, #0a4d80); color:#ffffff; border-radius:14px; padding:24px 28px; margin-bottom:24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; box-shadow: 0 4px 14px rgba(0, 114, 188, 0.18);">
      <div>
        <span style="background:rgba(255,255,255,0.2); padding:4px 10px; border-radius:20px; font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">
          <i class="bi bi-shield-check me-1"></i> Filosofi: Tuntas, Terukur, Terpantau
        </span>
        <h2 style="margin:8px 0 4px; font-size:22px; font-weight:800; letter-spacing:-0.3px;">
          Selamat Datang di Portal Asesmen Terpadu
        </h2>
        <p style="margin:0; font-size:13.5px; opacity:0.9; max-width:640px; line-height:1.5;">
          Kelola bank soal berbasis Capaian Pembelajaran (CP/TP), jadwalkan sesi ujian anti-curang, pantau siswa secara langsung, dan terbitkan remedial 1-klik untuk memastikan seluruh murid tuntas.
        </p>
      </div>
      <div style="display:flex; gap:10px;">
        <a href="{{ route('admin.cbt.jadwal.create') }}" class="btn btn-light" style="font-weight:800; color:#0072bc; font-size:13px; border-radius:8px; padding:10px 18px;">
          <i class="bi bi-plus-circle-fill me-1"></i> Jadwalkan Ujian
        </a>
        <a href="{{ route('admin.cbt.bank.index') }}" class="btn btn-outline-light" style="font-weight:700; font-size:13px; border-radius:8px; padding:10px 18px;">
          <i class="bi bi-journal-plus me-1"></i> Bank Soal
        </a>
      </div>
    </div>

    <!-- Quick Metrics (Cards) -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin-bottom:28px;">
      <div class="cbt-card">
        <div class="cbt-card-body" style="display:flex; align-items:center; gap:16px; padding:18px 20px;">
          <div style="width:48px; height:48px; border-radius:10px; background:#e0f2fe; color:#0369a1; display:flex; align-items:center; justify-content:center; font-size:22px;">
            <i class="bi bi-collection-fill"></i>
          </div>
          <div>
            <div style="font-size:24px; font-weight:900; color:#0f172a; line-height:1;">{{ $totalBank }}</div>
            <div style="font-size:12.5px; color:#64748b; font-weight:600; margin-top:3px;">Paket Bank Soal</div>
          </div>
        </div>
      </div>

      <div class="cbt-card">
        <div class="cbt-card-body" style="display:flex; align-items:center; gap:16px; padding:18px 20px;">
          <div style="width:48px; height:48px; border-radius:10px; background:#dcfce7; color:#15803d; display:flex; align-items:center; justify-content:center; font-size:22px;">
            <i class="bi bi-calendar-event-fill"></i>
          </div>
          <div>
            <div style="font-size:24px; font-weight:900; color:#0f172a; line-height:1;">{{ $totalJadwal }}</div>
            <div style="font-size:12.5px; color:#64748b; font-weight:600; margin-top:3px;">Total Sesi Dijadwalkan</div>
          </div>
        </div>
      </div>

      <div class="cbt-card">
        <div class="cbt-card-body" style="display:flex; align-items:center; gap:16px; padding:18px 20px;">
          <div style="width:48px; height:48px; border-radius:10px; background:#fef3c7; color:#b45309; display:flex; align-items:center; justify-content:center; font-size:22px;">
            <i class="bi bi-broadcast"></i>
          </div>
          <div>
            <div style="font-size:24px; font-weight:900; color:#0f172a; line-height:1;">{{ $jadwalAktif }}</div>
            <div style="font-size:12.5px; color:#64748b; font-weight:600; margin-top:3px;">Sesi Ujian Aktif</div>
          </div>
        </div>
      </div>

      <div class="cbt-card">
        <div class="cbt-card-body" style="display:flex; align-items:center; gap:16px; padding:18px 20px;">
          <div style="width:48px; height:48px; border-radius:10px; background:#f1f5f9; color:#475569; display:flex; align-items:center; justify-content:center; font-size:22px;">
            <i class="bi bi-people-fill"></i>
          </div>
          <div>
            <div style="font-size:24px; font-weight:900; color:#0f172a; line-height:1;">{{ $totalPeserta }}</div>
            <div style="font-size:12.5px; color:#64748b; font-weight:600; margin-top:3px;">Peserta Terdata</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Section: Kartu Mata Pelajaran / Bank Soal (Grid Identik Kejar.id) -->
    <div style="margin-bottom:28px;">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
        <h3 style="margin:0; font-size:17px; font-weight:800; color:#0f172a;">
          <i class="bi bi-grid text-primary me-2"></i> Mata Pelajaran & Bank Soal Aktif
        </h3>
        <a href="{{ route('admin.cbt.bank.index') }}" style="font-size:13px; font-weight:700; color:#0072bc; text-decoration:none;">
          Lihat Semua Bank Soal <i class="bi bi-arrow-right"></i>
        </a>
      </div>

      <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:18px;">
        @forelse($bankCards as $b)
          <div class="cbt-course-card">
            <div class="cbt-course-stripe"></div>
            <div class="cbt-course-content">
              <div class="cbt-course-code">{{ $b->kode_bank }}</div>
              <h4 class="cbt-course-title">{{ $b->nama_bank }}</h4>
              <div style="font-size:12.5px; color:#64748b; margin-bottom:8px;">
                <i class="bi bi-book me-1"></i> {{ $b->mata_pelajaran }}
                <span style="color:#cbd5e1; margin:0 4px;">•</span>
                Kelas {{ strtoupper($b->tingkat) }}
              </div>
              <div style="font-size:12px; color:#475569; display:flex; align-items:center; gap:6px;">
                <span class="badge bg-light text-dark border" style="font-weight:700;">
                  <i class="bi bi-list-check text-primary me-1"></i> {{ $b->soals_count }} Butir Soal
                </span>
                <span class="badge bg-light text-dark border" style="font-weight:700;">
                  KKTP: {{ $b->kktp_default }}
                </span>
              </div>
            </div>
            <div class="cbt-course-footer">
              <span style="font-size:11.5px; color:#94a3b8;">
                <i class="bi bi-person me-1"></i> {{ $b->guru->nama_guru ?? 'Guru Mapel' }}
              </span>
              <a href="{{ route('admin.cbt.soal.index', $b->id) }}" class="btn btn-sm btn-primary" style="font-size:12px; font-weight:700; padding:4px 12px; border-radius:6px;">
                Buka Soal
              </a>
            </div>
          </div>
        @empty
          <div style="grid-column: 1 / -1; background:#ffffff; border:1px dashed #cbd5e1; border-radius:12px; padding:40px 20px; text-align:center; color:#94a3b8;">
            <i class="bi bi-folder-plus" style="font-size:40px; color:#94a3b8; display:block; margin-bottom:10px;"></i>
            Belum ada paket Bank Soal yang dibuat. Silakan buat bank soal pertama Anda.
            <div style="margin-top:14px;">
              <a href="{{ route('admin.cbt.bank.index') }}" class="btn btn-sm btn-primary" style="font-weight:700; border-radius:8px;">
                + Tambah Bank Soal Baru
              </a>
            </div>
          </div>
        @endforelse
      </div>
    </div>

    <!-- Section: Sesi Ujian Berjalan & Live Proctoring -->
    <div>
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
        <h3 style="margin:0; font-size:17px; font-weight:800; color:#0f172a;">
          <i class="bi bi-activity text-success me-2"></i> Sesi Ujian Terbaru & Pengawasan Langsung
        </h3>
        <a href="{{ route('admin.cbt.jadwal.index') }}" style="font-size:13px; font-weight:700; color:#0072bc; text-decoration:none;">
          Kelola Jadwal <i class="bi bi-arrow-right"></i>
        </a>
      </div>

      <div class="cbt-card">
        <div class="cbt-card-body" style="padding:0;">
          <div style="overflow-x:auto;">
            <table class="cbt-table" style="margin:0;">
              <thead>
                <tr>
                  <th>Nama Sesi Ujian</th>
                  <th>Mata Pelajaran</th>
                  <th>Rombel Sasaran</th>
                  <th>Waktu Pelaksanaan</th>
                  <th style="text-align:center;">Token Sesi</th>
                  <th style="text-align:center;">Status</th>
                  <th style="text-align:center;">Aksi Pengawasan</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentJadwals as $rj)
                  <tr>
                    <td>
                      <div style="font-weight:800; color:#0f172a; font-size:13.5px;">{{ $rj->nama_ujian }}</div>
                      @if($rj->is_remedial)
                        <span class="badge bg-warning text-dark" style="font-size:10px;">REMEDIAL</span>
                      @endif
                    </td>
                    <td>
                      <span style="font-size:12.5px; font-weight:600; color:#334155;">{{ $rj->bankSoal->mata_pelajaran ?? '-' }}</span>
                    </td>
                    <td>
                      <div style="display:flex; flex-wrap:wrap; gap:4px;">
                        @forelse($rj->rombels as $r)
                          <span class="badge" style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; font-size:11px;">
                            {{ $r->rombel->nama_rombel ?? 'Rombel' }}
                          </span>
                        @empty
                          <span style="color:#94a3b8; font-size:11px;">-</span>
                        @endforelse
                      </div>
                    </td>
                    <td style="font-size:12px; color:#64748b;">
                      <div><i class="bi bi-clock me-1 text-primary"></i> {{ $rj->durasi_menit }} Menit</div>
                      <div>{{ \Carbon\Carbon::parse($rj->waktu_mulai)->format('d M Y, H:i') }}</div>
                    </td>
                    <td style="text-align:center;">
                      <span style="background:#0f172a; color:#38bdf8; font-family:monospace; font-weight:800; font-size:14px; letter-spacing:2px; padding:4px 8px; border-radius:6px;">
                        {{ $rj->token_ujian }}
                      </span>
                    </td>
                    <td style="text-align:center;">
                      @if($rj->status === 'aktif')
                        <span class="cbt-status-badge cbt-status-badge-active">
                          <span class="cbt-pulse-dot"></span> AKTIF
                        </span>
                      @elseif($rj->status === 'draft')
                        <span class="cbt-status-badge" style="background:#f1f5f9; color:#475569;">DRAFT</span>
                      @else
                        <span class="cbt-status-badge" style="background:#fee2e2; color:#991b1b;">SELESAI</span>
                      @endif
                    </td>
                    <td style="text-align:center;">
                      <a href="{{ route('admin.cbt.proctor.index', $rj->id) }}" class="btn btn-sm" style="background:#0072bc; color:#ffffff; font-weight:700; font-size:12px; padding:4px 10px; border-radius:6px;">
                        <i class="bi bi-display me-1"></i> Proctor Live
                      </a>
                      <a href="{{ route('admin.cbt.rekap.nilai', $rj->id) }}" class="btn btn-sm btn-outline-secondary" style="font-weight:700; font-size:12px; padding:4px 10px; border-radius:6px;">
                        <i class="bi bi-clipboard-data"></i> Rekap
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" style="text-align:center; padding:32px; color:#94a3b8;">
                      Belum ada sesi ujian yang dijadwalkan.
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
