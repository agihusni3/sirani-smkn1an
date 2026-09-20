@extends('dcc.akademik.layout')

@section('title', 'Detail Jurnal KBM')
@section('breadcrumb', 'Detail Jurnal')

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">Sesi KBM Pertemuan Ke-{{ $jurnal->pertemuan_ke }}</h1>
    <div class="akademik-page-desc">
      {{ $jurnal->distribusi?->mataPelajaran?->nama_mapel }} · {{ $jurnal->distribusi?->rombel?->nama_rombel }} · {{ \Carbon\Carbon::parse($jurnal->tanggal)->isoFormat('dddd, D MMMM Y') }}
    </div>
  </div>

  <a href="{{ route('akademik.jurnal.index') }}" class="ak-btn ak-btn-secondary">
    <i class="bi bi-arrow-left"></i>
    <span>Kembali ke Daftar</span>
  </a>
</div>

{{-- Detail Materi Card --}}
<div class="akademik-card" style="margin-bottom:20px;">
  <div class="akademik-card-header">
    <h3 class="akademik-card-title">
      <i class="bi bi-file-earmark-text text-primary"></i>
      <span>Rincian Pengajaran Sesi Ini</span>
    </h3>
    <span class="ak-badge ak-badge-primary">Guru: {{ $jurnal->distribusi?->guru?->nama }}</span>
  </div>
  <div class="akademik-card-body">
    <div style="margin-bottom:14px;">
      <div style="font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase;">Materi Pokok yang Diajarkan</div>
      <div style="font-size:15px; font-weight:600; color:var(--ak-dark); margin-top:4px;">{{ $jurnal->materi_ajar }}</div>
    </div>

    @if($jurnal->metode_pembelajaran)
      <div style="margin-bottom:14px;">
        <div style="font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase;">Metode Pembelajaran</div>
        <div style="font-size:13px; color:var(--ak-dark); margin-top:2px;">{{ $jurnal->metode_pembelajaran }}</div>
      </div>
    @endif

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-top:14px; padding-top:14px; border-top:1px solid #f1f5f9;">
      <div>
        <div style="font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase;">Catatan Guru</div>
        <div style="font-size:13px; color:var(--ak-dark); margin-top:2px;">{{ $jurnal->catatan_guru ?: 'Tidak ada catatan khusus.' }}</div>
      </div>
      <div>
        <div style="font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase;">Refleksi Pembelajaran</div>
        <div style="font-size:13px; color:var(--ak-dark); margin-top:2px;">{{ $jurnal->refleksi ?: 'Tidak ada catatan refleksi.' }}</div>
      </div>
    </div>
  </div>
</div>

{{-- Presensi Kehadiran Siswa --}}
<div class="akademik-card">
  <div class="akademik-card-header">
    <h3 class="akademik-card-title">
      <i class="bi bi-people text-primary"></i>
      <span>Presensi Kehadiran Siswa ({{ $rekap['total'] }} Siswa)</span>
    </h3>
    <div style="display:flex; gap:8px;">
      <span class="ak-badge ak-badge-success">{{ $rekap['hadir'] }} Hadir</span>
      <span class="ak-badge ak-badge-primary">{{ $rekap['izin'] }} Izin</span>
      <span class="ak-badge ak-badge-warning">{{ $rekap['sakit'] }} Sakit</span>
      <span class="ak-badge ak-badge-danger">{{ $rekap['alfa'] }} Alfa</span>
    </div>
  </div>
  <div class="akademik-card-body" style="padding:0;">
    <div class="akademik-table-wrap">
      <table class="akademik-table">
        <thead>
          <tr>
            <th style="width:50px;">No</th>
            <th>NISN</th>
            <th>Nama Lengkap Siswa</th>
            <th style="width:140px; text-align:center;">Status</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
          @foreach($jurnal->kehadirans as $idx => $k)
            <tr>
              <td>{{ $idx + 1 }}</td>
              <td><code>{{ $k->siswa?->nisn ?? '-' }}</code></td>
              <td style="font-weight:700; color:var(--ak-dark);">{{ $k->siswa?->nama_lengkap ?? '-' }}</td>
              <td style="text-align:center;">
                @if($k->status == 'hadir')
                  <span class="ak-badge ak-badge-success"><i class="bi bi-check-lg me-1"></i> Hadir</span>
                @elseif($k->status == 'izin')
                  <span class="ak-badge ak-badge-primary"><i class="bi bi-info-circle me-1"></i> Izin</span>
                @elseif($k->status == 'sakit')
                  <span class="ak-badge ak-badge-warning"><i class="bi bi-thermometer me-1"></i> Sakit</span>
                @else
                  <span class="ak-badge ak-badge-danger"><i class="bi bi-x-circle me-1"></i> Alfa</span>
                @endif
              </td>
              <td style="font-size:12px; color:#64748b;">{{ $k->keterangan ?? '-' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
