@extends('dcc.akademik.layout')

@section('title', 'Hasil & Rekap Nilai Asesmen Online')
@section('breadcrumb', 'Hasil Asesmen')

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">Rekapitulasi Hasil Asesmen Online</h1>
    <div class="akademik-page-desc">
      {{ $asesmen->judul }} · {{ $asesmen->distribusi?->rombel?->nama_rombel }} · {{ $asesmen->distribusi?->mataPelajaran?->nama_mapel }} · Standar KKM: <strong>{{ $asesmen->passing_grade }}</strong>
    </div>
  </div>

  <div style="display:flex; gap:10px;">
    <a href="{{ route('akademik.asesmen.index') }}" class="ak-btn ak-btn-secondary">
      <i class="bi bi-arrow-left"></i>
      <span>Kembali</span>
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
</div>

{{-- Table Hasil Siswa --}}
<div class="akademik-card">
  <div class="akademik-card-header">
    <h3 class="akademik-card-title">
      <i class="bi bi-person-lines-fill text-primary"></i>
      <span>Daftar Nilai Siswa ({{ $siswas->count() }} Siswa)</span>
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
            <th>Waktu Pengerjaan</th>
            <th>Durasi</th>
            <th style="text-align:center;">Skor Nilai</th>
            <th style="text-align:center;">Status Kelulusan</th>
          </tr>
        </thead>
        <tbody>
          @foreach($siswas as $idx => $s)
            @php
              $h = $hasils->get($s->id);
            @endphp
            <tr>
              <td>{{ $idx + 1 }}</td>
              <td><code>{{ $s->nisn ?? '-' }}</code></td>
              <td style="font-weight:700; color:var(--ak-dark);">{{ $s->nama_lengkap }}</td>
              <td style="font-size:12px; color:#64748b;">
                @if($h && $h->mulai_pada)
                  {{ \Carbon\Carbon::parse($h->mulai_pada)->isoFormat('D MMM Y, HH:mm') }} WIB
                @else
                  -
                @endif
              </td>
              <td style="font-size:12px;">
                @if($h && $h->durasi_detik)
                  {{ floor($h->durasi_detik / 60) }}m {{ $h->durasi_detik % 60 }}s
                @else
                  -
                @endif
              </td>
              <td style="text-align:center;">
                @if($h && $h->is_selesai)
                  <span style="font-size:16px; font-weight:800; color: {{ $h->nilai >= $asesmen->passing_grade ? '#059669' : '#dc2626' }};">
                    {{ $h->nilai }}
                  </span>
                @else
                  <span style="color:#cbd5e1; font-size:12px;">Belum Ujian</span>
                @endif
              </td>
              <td style="text-align:center;">
                @if($h && $h->is_selesai)
                  @if($h->nilai >= $asesmen->passing_grade)
                    <span class="ak-badge ak-badge-success">Tuntas (Lulus)</span>
                  @else
                    <span class="ak-badge ak-badge-danger">Remedial</span>
                  @endif
                @else
                  <span class="ak-badge ak-badge-secondary">Belum Mengerjakan</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
