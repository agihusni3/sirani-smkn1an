@extends('dcc.akademik.layout')

@section('title', 'Hasil & Rekap Nilai Asesmen Online')
@section('breadcrumb', 'Hasil Asesmen')

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">Rekapitulasi Hasil &amp; Integritas Asesmen</h1>
    <div class="akademik-page-desc">
      {{ $asesmen->judul }} · {{ $asesmen->rombel_names }} · {{ $asesmen->distribusi?->mataPelajaran?->nama_mapel }} · KKM: <strong>{{ $asesmen->passing_grade }}</strong>
    </div>
  </div>

  <div style="display:flex; gap:10px; flex-wrap:wrap;">
    <a href="{{ route('akademik.asesmen.index') }}" class="ak-btn ak-btn-secondary">
      <i class="bi bi-arrow-left"></i>
      <span>Kembali</span>
    </a>
    <a href="{{ route('akademik.asesmen.kerjakan', $asesmen->id) }}" class="ak-btn ak-btn-secondary" target="_blank">
      <i class="bi bi-play-circle"></i>
      <span>Simulasi Ujian</span>
    </a>
    <form action="{{ route('akademik.asesmen.push_nilai', $asesmen->id) }}" method="POST" onsubmit="return confirm('Transfer semua perolehan nilai siswa ini ke Buku Nilai & Leger?')">
      @csrf
      <button type="submit" class="ak-btn ak-btn-primary">
        <i class="bi bi-cloud-arrow-up"></i>
        <span>Transfer Nilai ke Buku Nilai &amp; Leger</span>
      </button>
    </form>
  </div>
</div>

{{-- Proctor Security & Token Banner --}}
@if($asesmen->anti_cheat_mode || $asesmen->token_ujian)
  <div class="akademik-card" style="margin-bottom:20px; background:#ffffff; border:1.5px solid #cbd5e1;">
    <div class="akademik-card-body" style="padding:16px 20px; display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:16px;">
      <div style="display:flex; align-items:center; gap:14px;">
        <div style="width:44px; height:44px; border-radius:10px; background:#fef2f2; color:#dc2626; display:flex; align-items:center; justify-content:center; font-size:22px;">
          <i class="bi bi-shield-lock-fill"></i>
        </div>
        <div>
          <div style="font-weight:800; font-size:14px; color:#0f172a; display:flex; align-items:center; gap:8px;">
            <span>Sistem Pengawasan Anti-Kecurangan Aktif</span>
            <span class="ak-badge ak-badge-danger" style="font-size:10px;">Toleransi: {{ $asesmen->max_toleransi_keluar }}x Keluar Layar</span>
          </div>
          <div style="font-size:12px; color:#64748b; margin-top:2px;">
            Mode Layar Penuh: <strong>{{ $asesmen->wajib_fullscreen ? 'Wajib' : 'Opsional' }}</strong> · Acak Soal: <strong>{{ $asesmen->acak_soal ? 'Aktif' : 'Tidak' }}</strong> · Acak Opsi A-E: <strong>{{ $asesmen->acak_opsi ? 'Aktif' : 'Tidak' }}</strong>
          </div>
        </div>
      </div>

      <div style="display:flex; align-items:center; gap:12px;">
        @if($asesmen->token_ujian)
          <div style="background:#f8fafc; border:1px solid #cbd5e1; border-radius:8px; padding:6px 14px; text-align:center;">
            <div style="font-size:10px; font-weight:800; color:#64748b; text-transform:uppercase;">Token Ujian Siswa</div>
            <div style="font-family:monospace; font-size:18px; font-weight:900; color:#1e3a8a; letter-spacing:2px;" id="tokenDisplay">
              {{ $asesmen->token_ujian }}
            </div>
          </div>
          <form action="{{ route('akademik.asesmen.refresh_token', $asesmen->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengganti Token Ujian baru?')">
            @csrf
            <button type="submit" class="ak-btn ak-btn-secondary ak-btn-sm" title="Refresh Token Baru">
              <i class="bi bi-arrow-repeat"></i> Ganti Token
            </button>
          </form>
        @else
          <form action="{{ route('akademik.asesmen.refresh_token', $asesmen->id) }}" method="POST">
            @csrf
            <button type="submit" class="ak-btn ak-btn-secondary ak-btn-sm">
              <i class="bi bi-key-fill"></i> Terbitkan Token Ujian
            </button>
          </form>
        @endif
      </div>
    </div>
  </div>
@endif

{{-- KPI Stats --}}
<div class="akademik-kpi-grid">
  <div class="akademik-kpi-card">
    <div>
      <div class="akademik-kpi-val">{{ $totalSiswa }}</div>
      <div class="akademik-kpi-label">Total Siswa Terdaftar</div>
    </div>
    <div class="akademik-kpi-icon icon-indigo">
      <i class="bi bi-people"></i>
    </div>
  </div>

  <div class="akademik-kpi-card">
    <div>
      <div class="akademik-kpi-val" style="color:#059669;">{{ $totalMengerjakan }}</div>
      <div class="akademik-kpi-label">Sudah Menyelesaikan</div>
    </div>
    <div class="akademik-kpi-icon icon-emerald">
      <i class="bi bi-check2-circle"></i>
    </div>
  </div>

  <div class="akademik-kpi-card">
    <div>
      <div class="akademik-kpi-val" style="color:#7c3aed;">{{ round($rataRata, 1) }}</div>
      <div class="akademik-kpi-label">Rata-rata Nilai Kelas</div>
    </div>
    <div class="akademik-kpi-icon icon-violet">
      <i class="bi bi-graph-up"></i>
    </div>
  </div>

  <div class="akademik-kpi-card">
    <div>
      <div class="akademik-kpi-val" style="color:#2563eb;">{{ $tuntas }}</div>
      <div class="akademik-kpi-label">Siswa Tuntas (&ge; {{ $asesmen->passing_grade }})</div>
    </div>
    <div class="akademik-kpi-icon icon-indigo">
      <i class="bi bi-award"></i>
    </div>
  </div>

  <div class="akademik-kpi-card">
    <div>
      <div class="akademik-kpi-val" style="color:{{ $totalCurang > 0 ? '#dc2626' : '#059669' }};">
        {{ $totalCurang }} <span style="font-size:12px; color:#64748b; font-weight:normal;">/ {{ $totalMelanggar }}</span>
      </div>
      <div class="akademik-kpi-label">Terkunci / Ada Pelanggaran</div>
    </div>
    <div class="akademik-kpi-icon" style="background:#fee2e2; color:#dc2626;">
      <i class="bi bi-shield-exclamation"></i>
    </div>
  </div>
</div>

{{-- Filter Rombel Tabs jika asesmen memiliki lebih dari 1 rombel --}}
@if($targetRombels->count() > 1)
  <div style="display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap; align-items:center;">
    <span style="font-size:12px; font-weight:800; color:#475569; margin-right:4px;">Filter Kelas:</span>
    <a href="{{ route('akademik.asesmen.hasil', $asesmen->id) }}" class="ak-btn {{ empty($selectedRombelId) ? 'ak-btn-primary' : 'ak-btn-secondary' }}" style="font-size:12px; padding:5px 14px;">
      Semua Kelas ({{ $targetRombels->count() }} Rombel)
    </a>
    @foreach($targetRombels as $tr)
      <a href="{{ route('akademik.asesmen.hasil', [$asesmen->id, 'rombel_id' => $tr->id]) }}" class="ak-btn {{ (string)$selectedRombelId === (string)$tr->id ? 'ak-btn-primary' : 'ak-btn-secondary' }}" style="font-size:12px; padding:5px 14px;">
        Kelas {{ $tr->nama_rombel }}
      </a>
    @endforeach
  </div>
@endif

{{-- Table Hasil Siswa --}}
<div class="akademik-card">
  <div class="akademik-card-header" style="display:flex; justify-content:space-between; align-items:center;">
    <h3 class="akademik-card-title">
      <i class="bi bi-person-lines-fill text-primary"></i>
      <span>Daftar Lembar Jawaban &amp; Audit Integritas ({{ $siswas->count() }} Siswa)</span>
    </h3>
  </div>
  <div class="akademik-card-body" style="padding:0;">
    <div class="akademik-table-wrap">
      <table class="akademik-table">
        <thead>
          <tr>
            <th style="width:40px;">No</th>
            <th>NISN</th>
            <th>Nama Lengkap Siswa</th>
            @if($targetRombels->count() > 1)
              <th>Kelas</th>
            @endif
            <th>Waktu &amp; Durasi</th>
            <th style="text-align:center;">Integritas &amp; Pelanggaran</th>
            <th style="text-align:center;">Skor Nilai</th>
            <th style="text-align:center;">Status Kelulusan</th>
            <th style="text-align:center; width:120px;">Aksi Pengawas</th>
          </tr>
        </thead>
        <tbody>
          @foreach($siswas as $idx => $s)
            @php
              $h = $hasils->get($s->id);
              $violations = $h?->jumlah_pelanggaran ?? 0;
              $statusKejujuran = $h?->status_kejujuran ?? 'jujur';
            @endphp
            <tr>
              <td>{{ $idx + 1 }}</td>
              <td><code>{{ $s->nisn ?? '-' }}</code></td>
              <td>
                <div style="font-weight:700; color:var(--ak-dark);">{{ $s->nama }}</div>
                @if($h && $h->catatan_pengawas)
                  <div style="font-size:10.5px; color:#7c3aed; margin-top:2px;">
                    <i class="bi bi-info-circle me-1"></i>{{ $h->catatan_pengawas }}
                  </div>
                @endif
              </td>
              @if($targetRombels->count() > 1)
                <td>
                  <span class="ak-badge ak-badge-secondary">{{ $s->rombels->first()?->nama_rombel ?? '-' }}</span>
                </td>
              @endif
              <td style="font-size:12px; color:#64748b;">
                @if($h && $h->mulai_pada)
                  <div>{{ \Carbon\Carbon::parse($h->mulai_pada)->isoFormat('D MMM, HH:mm') }}</div>
                  @if($h->durasi_detik)
                    <div style="font-size:11px; color:#475569;">
                      <i class="bi bi-stopwatch me-1"></i>{{ floor($h->durasi_detik / 60) }}m {{ $h->durasi_detik % 60 }}s
                    </div>
                  @endif
                @else
                  <span style="color:#cbd5e1;">Belum Mulai</span>
                @endif
              </td>

              {{-- Status Integritas --}}
              <td style="text-align:center;">
                @if(!$h || !$h->mulai_pada)
                  <span class="ak-badge ak-badge-secondary">-</span>
                @elseif($violations == 0)
                  <span class="ak-badge ak-badge-success" title="Tidak ada pelanggaran tercatat">
                    <i class="bi bi-shield-check me-1"></i> 100% Bersih
                  </span>
                @elseif($violations < $asesmen->max_toleransi_keluar)
                  <button type="button" class="ak-badge ak-badge-warning" style="border:none; cursor:pointer;" onclick="tampilkanLogModal('{{ $s->nama_lengkap }}', {{ json_encode($h->log_pelanggaran ?? []) }})">
                    <i class="bi bi-exclamation-triangle me-1"></i> {{ $violations }}x Pelanggaran
                  </button>
                @else
                  <button type="button" class="ak-badge ak-badge-danger" style="border:none; cursor:pointer;" onclick="tampilkanLogModal('{{ $s->nama_lengkap }}', {{ json_encode($h->log_pelanggaran ?? []) }})">
                    <i class="bi bi-shield-x me-1"></i> Terkunci ({{ $violations }}x)
                  </button>
                @endif
              </td>

              {{-- Skor Nilai --}}
              <td style="text-align:center;">
                @if($h && $h->is_selesai)
                  <span style="font-size:17px; font-weight:900; color: {{ $h->nilai >= $asesmen->passing_grade ? '#059669' : '#dc2626' }};">
                    {{ $h->nilai }}
                  </span>
                @elseif($h && $h->mulai_pada)
                  <span class="ak-badge ak-badge-warning">Sedang Mengerjakan</span>
                @else
                  <span style="color:#cbd5e1; font-size:12px;">-</span>
                @endif
              </td>

              {{-- Status Kelulusan --}}
              <td style="text-align:center;">
                @if($h && $h->is_selesai)
                  @if($h->nilai >= $asesmen->passing_grade)
                    <span class="ak-badge ak-badge-success">Tuntas</span>
                  @else
                    <span class="ak-badge ak-badge-danger">Remedial</span>
                  @endif
                @else
                  <span class="ak-badge ak-badge-secondary">Belum Selesai</span>
                @endif
              </td>

              {{-- Aksi Pengawas --}}
              <td style="text-align:center;">
                @if($h && ($h->mulai_pada || $violations > 0))
                  <form action="{{ route('akademik.asesmen.reset_siswa', ['id' => $asesmen->id, 'siswaId' => $s->id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin me-reset sesi ujian & pelanggaran untuk {{ $s->nama_lengkap }}?')">
                    @csrf
                    <button type="submit" class="ak-btn ak-btn-secondary ak-btn-sm" title="Reset sesi pengerjaan & integritas siswa">
                      <i class="bi bi-arrow-counterclockwise"></i> Reset Sesi
                    </button>
                  </form>
                @else
                  <span style="color:#cbd5e1; font-size:11px;">-</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- Modal Log Pelanggaran --}}
<div class="modal" id="modalLogPelanggaran" tabindex="-1" style="background:rgba(15,23,42,0.6);">
  <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
    <div class="modal-content" style="border-radius:14px; border:none; box-shadow:0 20px 25px -5px rgba(0,0,0,0.2);">
      <div class="modal-header" style="background:#fef2f2; border-bottom:1px solid #fecaca; border-radius:14px 14px 0 0;">
        <h5 class="modal-title" style="font-weight:800; font-size:15px; color:#991b1b;">
          <i class="bi bi-shield-exclamation me-1"></i> Audit Log Pelanggaran Integritas
        </h5>
        <button type="button" class="btn-close" onclick="tutupLogModal()"></button>
      </div>
      <div class="modal-body" style="padding:20px;">
        <div style="font-size:13px; color:#475569; margin-bottom:14px;">
          Siswa: <strong id="modalSiswaNama" style="color:#0f172a;">-</strong>
        </div>
        <div id="modalLogList" style="display:flex; flex-direction:column; gap:8px; max-height:280px; overflow-y:auto;">
        </div>
      </div>
      <div class="modal-footer" style="background:#f8fafc; border-top:1px solid #e2e8f0; border-radius:0 0 14px 14px;">
        <button type="button" class="ak-btn ak-btn-secondary" onclick="tutupLogModal()">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script>
  function tampilkanLogModal(nama, logs) {
    document.getElementById('modalSiswaNama').innerText = nama;
    const listEl = document.getElementById('modalLogList');
    listEl.innerHTML = '';

    if (!logs || logs.length === 0) {
      listEl.innerHTML = '<div style="padding:20px; text-align:center; color:#64748b; font-size:13px;">Tidak ada riwayat pelanggaran tercatat.</div>';
    } else {
      logs.forEach((item, idx) => {
        const row = document.createElement('div');
        row.style.background = '#fff1f2';
        row.style.border = '1px solid #fecdd3';
        row.style.borderRadius = '8px';
        row.style.padding = '10px 12px';
        row.style.fontSize = '12px';
        row.innerHTML = `
          <div style="display:flex; justify-content:space-between; font-weight:700; color:#9f1239; margin-bottom:2px;">
            <span>#${idx + 1} Tipe: ${item.tipe || 'Keluar Layar'}</span>
            <span style="font-family:monospace;">${item.waktu || '-'} WIB</span>
          </div>
          <div style="color:#475569;">${item.keterangan || 'Terdeteksi keluar dari jendela ujian'}</div>
        `;
        listEl.appendChild(row);
      });
    }

    const modal = document.getElementById('modalLogPelanggaran');
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
  }

  function tutupLogModal() {
    document.getElementById('modalLogPelanggaran').style.display = 'none';
  }
</script>
@endsection
