<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rekap Nilai & Ketuntasan KKTP: {{ $jadwal->nama_ujian }} — CBT SMKN 1 AN</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/dcc-cbt.css') }}?v={{ filemtime(public_path('css/dcc-cbt.css')) }}">
</head>
<body class="cbt-app-body">

  @include('dcc.cbt.partials.cbt_header')

  <main class="cbt-container">

    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
      <div>
        <a href="{{ route('admin.cbt.jadwal.index') }}" style="display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:700; color:#0072bc; text-decoration:none; margin-bottom:8px;">
          <i class="bi bi-arrow-left"></i> Kembali ke Daftar Jadwal
        </a>
        <h1 style="margin:6px 0 4px; font-size:22px; font-weight:900; color:#0f172a;">
          <i class="bi bi-clipboard-data-fill text-primary me-2"></i> Rekap Nilai & Analisis Ketuntasan Belajar
        </h1>
        <p style="margin:0; font-size:13.5px; color:#64748b;">
          Ujian: <strong>{{ $jadwal->nama_ujian }}</strong> | Mapel: <strong>{{ $jadwal->bankSoal->mata_pelajaran ?? '-' }}</strong> | Batas KKTP: <strong>{{ $jadwal->kktp }}</strong>
        </p>
      </div>

      <div style="display:flex; gap:10px;">
        @if($belumTuntas > 0)
          <a href="{{ route('admin.cbt.remedial.create', $jadwal->id) }}" class="btn btn-warning" style="font-weight:800; font-size:13px; border-radius:8px; padding:10px 18px; color:#78350f;">
            <i class="bi bi-lightning-charge-fill me-1"></i> Buat Remedial ({{ $belumTuntas }} Siswa)
          </a>
        @endif
        <button type="button" class="btn btn-outline-secondary" onclick="window.print()" style="font-weight:700; font-size:13px; border-radius:8px; padding:10px 16px;">
          <i class="bi bi-printer me-1"></i> Cetak / PDF
        </button>
      </div>
    </div>

    <!-- KPI Metrics Rekapitulasi -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:14px; margin-bottom:24px;">
      <div class="cbt-card">
        <div class="cbt-card-body" style="padding:16px 20px;">
          <div style="font-size:24px; font-weight:900; color:#0f172a;">{{ $totalPeserta }}</div>
          <div style="font-size:12px; color:#64748b; font-weight:600;">Total Peserta</div>
        </div>
      </div>

      <div class="cbt-card">
        <div class="cbt-card-body" style="padding:16px 20px;">
          <div style="font-size:24px; font-weight:900; color:#0284c7;">{{ $rataRata }}</div>
          <div style="font-size:12px; color:#64748b; font-weight:600;">Rata-Rata Nilai</div>
        </div>
      </div>

      <div class="cbt-card">
        <div class="cbt-card-body" style="padding:16px 20px;">
          <div style="font-size:24px; font-weight:900; color:#16a34a;">{{ $tuntas }}</div>
          <div style="font-size:12px; color:#16a34a; font-weight:700;">Tuntas (&ge; {{ $jadwal->kktp }})</div>
        </div>
      </div>

      <div class="cbt-card">
        <div class="cbt-card-body" style="padding:16px 20px;">
          <div style="font-size:24px; font-weight:900; color:#dc2626;">{{ $belumTuntas }}</div>
          <div style="font-size:12px; color:#dc2626; font-weight:700;">Belum Tuntas (&lt; {{ $jadwal->kktp }})</div>
        </div>
      </div>

      <div class="cbt-card">
        <div class="cbt-card-body" style="padding:16px 20px;">
          <div style="font-size:24px; font-weight:900; color:#475569;">{{ $nilaiTertinggi }} / {{ $nilaiTerendah }}</div>
          <div style="font-size:12px; color:#64748b; font-weight:600;">Tertinggi / Terendah</div>
        </div>
      </div>
    </div>

    <!-- Tabel Rekapitulasi Nilai Siswa -->
    <div class="cbt-card">
      <div class="cbt-card-body" style="padding:0;">
        <div style="overflow-x:auto;">
          <table class="cbt-table" style="margin:0;">
            <thead>
              <tr>
                <th style="width:40px; text-align:center;">No</th>
                <th>NISN</th>
                <th>Nama Peserta Didik</th>
                <th>Rombel</th>
                <th style="text-align:center;">Waktu Selesai</th>
                <th style="text-align:center;">Skor PG</th>
                <th style="text-align:center;">Skor Esai</th>
                <th style="text-align:center;">Nilai Akhir</th>
                <th style="text-align:center;">Status Ketuntasan</th>
              </tr>
            </thead>
            <tbody>
              @forelse($pesertas as $idx => $p)
                @php
                  $isSelesai = ($p->status === 'selesai');
                  $nilai = $p->nilai_akhir;
                  $tuntas = $p->is_tuntas;
                @endphp
                <tr>
                  <td style="text-align:center; color:#64748b; font-weight:700;">{{ $idx + 1 }}</td>
                  <td style="font-family:monospace; font-size:12.5px;">{{ $p->siswa->nisn }}</td>
                  <td>
                    <div style="font-weight:800; color:#0f172a; font-size:13.5px;">{{ $p->siswa->nama_siswa }}</div>
                    @if($p->jumlah_pelanggaran > 0)
                      <span class="badge bg-danger" style="font-size:10px;">Pelanggaran Tab: {{ $p->jumlah_pelanggaran }}x</span>
                    @endif
                  </td>
                  <td>
                    <span class="badge" style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; font-size:11.5px;">
                      {{ $p->siswa->rombel->nama_rombel ?? '-' }}
                    </span>
                  </td>
                  <td style="text-align:center; font-size:12px; color:#64748b;">
                    {{ $p->waktu_selesai ? \Carbon\Carbon::parse($p->waktu_selesai)->format('H:i:s') : '-' }}
                  </td>
                  <td style="text-align:center; font-size:13px; font-weight:700;">
                    {{ $p->nilai_pg !== null ? $p->nilai_pg : '-' }}
                  </td>
                  <td style="text-align:center; font-size:13px; font-weight:700;">
                    {{ $p->nilai_esai !== null ? $p->nilai_esai : '-' }}
                  </td>
                  <td style="text-align:center;">
                    @if($nilai !== null)
                      <span style="font-size:16px; font-weight:900; color:{{ $tuntas ? '#16a34a' : '#dc2626' }};">
                        {{ $nilai }}
                      </span>
                    @else
                      <span style="color:#94a3b8; font-size:12px;">Belum selesai</span>
                    @endif
                  </td>
                  <td style="text-align:center;">
                    @if($nilai !== null)
                      @if($tuntas)
                        <span class="cbt-status-badge cbt-status-badge-tuntas">
                          <i class="bi bi-check-circle-fill"></i> TUNTAS
                        </span>
                      @else
                        <span class="cbt-status-badge cbt-status-badge-remidi">
                          <i class="bi bi-exclamation-circle-fill"></i> REMIDIAL
                        </span>
                      @endif
                    @else
                      <span class="badge bg-light text-dark border">DALAM PROSES</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" style="text-align:center; padding:40px 20px; color:#94a3b8;">
                    Belum ada data nilai peserta untuk sesi ujian ini.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </main>

</body>
</html>
