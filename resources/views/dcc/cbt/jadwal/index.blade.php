<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Jadwal & Sesi Ujian CBT — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/dcc-cbt.css') }}?v={{ filemtime(public_path('css/dcc-cbt.css')) }}">
</head>
<body class="cbt-app-body">

  @include('dcc.cbt.partials.cbt_header')

  <main class="cbt-container">

    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
      <div>
        <h1 style="margin:0 0 4px; font-size:22px; font-weight:900; color:#0f172a;">
          <i class="bi bi-calendar-event text-primary me-2"></i> Jadwal & Sesi Pelaksanaan Ujian
        </h1>
        <p style="margin:0; font-size:13.5px; color:#64748b;">
          Atur jadwal, alokasi kelas rombel, token sesi, dan pengawasan proctoring real-time.
        </p>
      </div>
      <div>
        <a href="{{ route('admin.cbt.jadwal.create') }}" class="btn btn-primary" style="font-weight:800; font-size:13.5px; border-radius:8px; padding:10px 20px;">
          <i class="bi bi-plus-circle-fill me-1"></i> Jadwalkan Ujian Baru
        </a>
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

    <div class="cbt-card">
      <div class="cbt-card-body" style="padding:0;">
        <div style="overflow-x:auto;">
          <table class="cbt-table" style="margin:0;">
            <thead>
              <tr>
                <th style="width:50px; text-align:center;">No</th>
                <th>Nama Ujian & Mapel</th>
                <th>Sesi / Tipe</th>
                <th>Rombel Sasaran</th>
                <th>Waktu & Durasi</th>
                <th style="text-align:center;">Token Sesi</th>
                <th style="text-align:center;">Status</th>
                <th style="text-align:center; min-width:210px;">Aksi Pengawasan</th>
              </tr>
            </thead>
            <tbody>
              @forelse($jadwals as $idx => $j)
                <tr>
                  <td style="text-align:center; color:#64748b; font-weight:700;">{{ $jadwals->firstItem() + $idx }}</td>
                  <td>
                    <div style="font-weight:800; color:#0f172a; font-size:14px;">
                      {{ $j->nama_ujian }}
                      @if($j->is_remedial)
                        <span class="badge bg-warning text-dark" style="font-size:10.5px; margin-left:4px;">REMEDIAL</span>
                      @endif
                    </div>
                    <div style="font-size:12px; color:#64748b; margin-top:2px;">
                      <i class="bi bi-journal-bookmark me-1"></i> {{ $j->bankSoal->nama_bank ?? 'N/A' }}
                      <span style="color:#cbd5e1; margin:0 4px;">•</span>
                      {{ $j->bankSoal->mata_pelajaran ?? '-' }}
                    </div>
                    @if($j->guru)
                      <div style="font-size:11.5px; color:#0369a1; margin-top:2px;">
                        <i class="bi bi-person-badge me-1"></i> Guru: {{ $j->guru->nama_guru }}
                      </div>
                    @endif
                  </td>
                  <td>
                    <span class="badge" style="background:#e0e7ff; color:#3730a3; font-weight:700; font-size:11.5px; text-transform:uppercase;">
                      {{ $j->tipe_ujian }}
                    </span>
                    <div style="font-size:11px; color:#64748b; margin-top:3px;">
                      KKTP: <strong>{{ $j->kktp }}</strong>
                    </div>
                  </td>
                  <td>
                    <div style="display:flex; flex-wrap:wrap; gap:4px; max-width:200px;">
                      @forelse($j->rombels as $jr)
                        <span class="badge" style="background:#f1f5f9; color:#334155; border:1px solid #cbd5e1; font-size:11px; font-weight:600;">
                          {{ $jr->rombel->nama_rombel ?? 'ID: ' . $jr->rombel_id }}
                        </span>
                      @empty
                        <span style="font-size:11px; color:#94a3b8; font-style:italic;">Semua / Belum diset</span>
                      @endforelse
                    </div>
                  </td>
                  <td style="font-size:12.5px; color:#334155;">
                    <div><i class="bi bi-clock me-1 text-primary"></i> <strong>{{ $j->durasi_menit }} Menit</strong></div>
                    <div style="font-size:11.5px; color:#64748b; margin-top:2px;">
                      {{ \Carbon\Carbon::parse($j->waktu_mulai)->format('d M Y, H:i') }}
                      <br />s.d {{ \Carbon\Carbon::parse($j->waktu_selesai)->format('d M Y, H:i') }}
                    </div>
                  </td>
                  <td style="text-align:center;">
                    <div style="background:#0f172a; color:#38bdf8; font-family:monospace; font-weight:900; font-size:15px; letter-spacing:2px; padding:4px 10px; border-radius:6px; display:inline-block;">
                      {{ $j->token_ujian }}
                    </div>
                    <form action="{{ route('admin.cbt.jadwal.refresh_token', $j->id) }}" method="POST" style="display:inline; margin-left:4px;">
                      @csrf
                      <button type="submit" title="Generate Token Baru" style="background:none; border:none; color:#64748b; cursor:pointer; font-size:13px;" onclick="return confirm('Regenerasi token ujian?')">
                        <i class="bi bi-arrow-repeat"></i>
                      </button>
                    </form>
                  </td>
                  <td style="text-align:center;">
                    @if($j->status === 'aktif')
                      <span class="cbt-status-badge cbt-status-badge-active">
                        <span class="cbt-pulse-dot"></span> AKTIF
                      </span>
                    @elseif($j->status === 'draft')
                      <span class="cbt-status-badge" style="background:#f1f5f9; color:#475569;">DRAFT</span>
                    @else
                      <span class="cbt-status-badge" style="background:#fee2e2; color:#991b1b;">SELESAI</span>
                    @endif
                  </td>
                  <td style="text-align:center;">
                    <div style="display:flex; justify-content:center; gap:6px; flex-wrap:wrap;">
                      <a href="{{ route('admin.cbt.proctor.index', $j->id) }}" class="btn btn-sm" style="background:#0072bc; color:#ffffff; font-weight:700; font-size:11.5px; border-radius:6px; padding:5px 10px;" title="Buka Proctoring Live">
                        <i class="bi bi-display me-1"></i> Proctor
                      </a>
                      <a href="{{ route('admin.cbt.rekap.nilai', $j->id) }}" class="btn btn-sm btn-outline-secondary" style="font-weight:700; font-size:11.5px; border-radius:6px; padding:5px 10px;" title="Rekap Nilai & Ketuntasan">
                        <i class="bi bi-clipboard-data me-1"></i> Rekap
                      </a>
                      <form action="{{ route('admin.cbt.jadwal.toggle_status', $j->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-light" style="font-size:11.5px; border:1px solid #cbd5e1;" title="Ubah Status Aktif/Selesai">
                          <i class="bi bi-power {{ $j->status === 'aktif' ? 'text-success' : 'text-danger' }}"></i>
                        </button>
                      </form>
                      <form action="{{ route('admin.cbt.jadwal.destroy', $j->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus sesi ujian ini beserta lembar nilai terkait?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" style="font-size:11.5px; border-radius:6px;" title="Hapus Jadwal">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" style="text-align:center; padding:48px 20px; color:#94a3b8;">
                    <i class="bi bi-calendar-x" style="font-size:40px; display:block; margin-bottom:10px; color:#cbd5e1;"></i>
                    Belum ada sesi ujian yang dijadwalkan. Klik tombol di atas untuk menjadwalkan ujian.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($jadwals->hasPages())
          <div style="padding:16px 20px; border-top:1px solid #e2e8f0;">
            {{ $jadwals->links() }}
          </div>
        @endif

      </div>
    </div>

  </main>

</body>
</html>
