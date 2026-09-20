@extends('dcc.akademik.layout')

@section('title', 'Buku Nilai & Asesmen')
@section('breadcrumb', 'Input Nilai')

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">Buku Nilai Formatif &amp; Sumatif</h1>
    <div class="akademik-page-desc">
      Pilih kelas dan mata pelajaran yang diampu untuk memasukkan nilai asesmen siswa.
    </div>
  </div>

  <a href="{{ route('akademik.nilai.leger') }}" class="ak-btn ak-btn-secondary">
    <i class="bi bi-table"></i>
    <span>Lihat Leger Nilai Kelas</span>
  </a>
</div>

<div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap:18px;">
  @forelse($distribusis as $d)
    <div class="akademik-card" style="margin-bottom:0; display:flex; flex-direction:column; justify-content:space-between;">
      <div>
        <div class="akademik-card-header" style="background:#ffffff; border-bottom:1px solid #f1f5f9;">
          <span class="ak-badge ak-badge-secondary" style="font-size:12px; font-weight:800;">{{ $d->rombel?->nama_rombel }}</span>
          <span class="ak-badge ak-badge-primary">Semester {{ $d->semester }}</span>
        </div>
        <div class="akademik-card-body">
          <h4 style="font-size:16px; font-weight:800; color:var(--ak-dark); margin:0 0 6px 0;">
            {{ $d->mataPelajaran?->nama_mapel }}
          </h4>
          <div style="font-size:12px; color:#64748b; margin-bottom:12px;">
            Kode: <code>{{ $d->mataPelajaran?->kode_mapel }}</code> · Fase {{ $d->mataPelajaran?->fase }} ({{ $d->total_jam_per_minggu }} JP/Mgg)
          </div>
          <div style="font-size:12px; color:var(--ak-dark); display:flex; align-items:center; gap:6px;">
            <i class="bi bi-person-badge text-primary"></i>
            <span>{{ $d->guru?->nama }}</span>
          </div>
        </div>
      </div>

      <div style="padding:14px 22px; background:#f8fafc; border-top:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center;">
        <span style="font-size:12px; font-weight:600; color:#64748b;">
          {{ $d->rombel?->siswas()->whereIn('status', ['aktif', 'pkl'])->count() }} Siswa
        </span>
        <a href="{{ route('akademik.nilai.input', $d->id) }}" class="ak-btn ak-btn-primary ak-btn-sm">
          <i class="bi bi-pencil-square"></i>
          <span>Buka Buku Nilai</span>
        </a>
      </div>
    </div>
  @empty
    <div style="grid-column: 1 / -1; padding:50px; text-align:center; background:#ffffff; border-radius:14px; border:1px solid #e2e8f0; color:#64748b;">
      <i class="bi bi-journal-x" style="font-size:40px; opacity:0.35; display:block; margin-bottom:10px;"></i>
      Belum ada jadwal mengajar yang ditugaskan kepada Anda.
    </div>
  @endforelse
</div>
@endsection
