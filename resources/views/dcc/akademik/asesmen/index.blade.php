@extends('dcc.akademik.layout')

@section('title', 'Asesmen Penilaian Berbasis Online')
@section('breadcrumb', 'Asesmen Online')

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">Asesmen Penilaian Berbasis Online</h1>
    <div class="akademik-page-desc">
      Evaluasi pembelajaran berbasis komputer (CBT): Kuis, Ulangan Harian, Penilaian Tengah Semester (PTS), dan Penilaian Akhir Semester (PAS).
    </div>
  </div>

  <a href="{{ route('akademik.asesmen.create') }}" class="ak-btn ak-btn-primary">
    <i class="bi bi-plus-circle"></i>
    <span>Buat Asesmen Online Baru</span>
  </a>
</div>

{{-- Filter Toolbar --}}
<div class="akademik-card" style="margin-bottom:16px;">
  <div class="akademik-card-body" style="padding:14px 20px;">
    <form action="{{ route('akademik.asesmen.index') }}" method="GET" style="display:flex; flex-wrap:wrap; gap:12px; align-items:center;">
      <div style="flex:1; min-width:200px;">
        <select name="distribusi_id" class="ak-select" onchange="this.form.submit()">
          <option value="">Semua Mata Pelajaran &amp; Rombel</option>
          @foreach($distribusis as $d)
            <option value="{{ $d->id }}" {{ request('distribusi_id') == $d->id ? 'selected' : '' }}>
              {{ $d->rombel?->nama_rombel }} — {{ $d->mataPelajaran?->nama_mapel }}
            </option>
          @endforeach
        </select>
      </div>

      <div style="width:180px;">
        <select name="jenis" class="ak-select" onchange="this.form.submit()">
          <option value="">Semua Jenis Asesmen</option>
          <option value="kuis" {{ request('jenis') == 'kuis' ? 'selected' : '' }}>Kuis Harian</option>
          <option value="ulangan_harian" {{ request('jenis') == 'ulangan_harian' ? 'selected' : '' }}>Ulangan Harian (UH)</option>
          <option value="pts" {{ request('jenis') == 'pts' ? 'selected' : '' }}>PTS (Tengah Semester)</option>
          <option value="pas" {{ request('jenis') == 'pas' ? 'selected' : '' }}>PAS (Akhir Semester)</option>
          <option value="tugas" {{ request('jenis') == 'tugas' ? 'selected' : '' }}>Tugas Daring</option>
        </select>
      </div>

      <button type="submit" class="ak-btn ak-btn-secondary">
        <i class="bi bi-filter"></i>
      </button>

      @if(request()->anyFilled(['distribusi_id', 'jenis']))
        <a href="{{ route('akademik.asesmen.index') }}" class="ak-btn ak-btn-secondary" title="Reset">
          <i class="bi bi-x-circle"></i>
        </a>
      @endif
    </form>
  </div>
</div>

{{-- Data Table --}}
<div class="akademik-card">
  <div class="akademik-card-body" style="padding:0;">
    @if($asesmens->isEmpty())
      <div style="padding:48px 20px; text-align:center; color:#64748b;">
        <i class="bi bi-laptop" style="font-size:40px; opacity:0.35; display:block; margin-bottom:10px;"></i>
        Belum ada asesmen online yang dibuat. Klik tombol "Buat Asesmen Online Baru" di atas.
      </div>
    @else
      <div class="akademik-table-wrap">
        <table class="akademik-table">
          <thead>
            <tr>
              <th style="width:40px;">No</th>
              <th>Judul Asesmen</th>
              <th>Mata Pelajaran &amp; Rombel</th>
              <th>Jenis &amp; Durasi</th>
              <th>Butir Soal</th>
              <th>Peserta Selesai</th>
              <th>Status Ujian</th>
              <th style="width:190px; text-align:center;">Aksi &amp; Ujian</th>
            </tr>
          </thead>
          <tbody>
            @foreach($asesmens as $idx => $a)
              <tr>
                <td>{{ $asesmens->firstItem() + $idx }}</td>
                <td>
                  <div style="font-weight:700; color:var(--ak-dark); font-size:14px;">{{ $a->judul }}</div>
                  <div style="font-size:11px; color:#64748b;">KKM / Standar Lulus: <strong>{{ $a->passing_grade }}</strong></div>
                </td>
                <td>
                  <div style="font-weight:700; color:var(--ak-primary);">{{ $a->distribusi?->mataPelajaran?->nama_mapel ?? '-' }}</div>
                  <span class="ak-badge ak-badge-secondary">{{ $a->distribusi?->rombel?->nama_rombel ?? '-' }}</span>
                </td>
                <td>
                  <span class="ak-badge ak-badge-primary" style="text-transform:uppercase;">
                    {{ str_replace('_', ' ', $a->jenis) }}
                  </span>
                  <div style="font-size:11px; color:#64748b; margin-top:2px;">
                    <i class="bi bi-clock me-1"></i>{{ $a->durasi_menit }} Menit
                  </div>
                </td>
                <td>
                  <a href="{{ route('akademik.asesmen.soal', $a->id) }}" class="ak-badge {{ $a->soals_count > 0 ? 'ak-badge-success' : 'ak-badge-warning' }}" style="text-decoration:none;" title="Kelola Soal">
                    <i class="bi bi-question-circle me-1"></i>{{ $a->soals_count }} Soal
                  </a>
                </td>
                <td>
                  <a href="{{ route('akademik.asesmen.hasil', $a->id) }}" class="ak-badge ak-badge-primary" style="text-decoration:none;" title="Lihat Rekap Nilai">
                    <i class="bi bi-people me-1"></i>{{ $a->hasils_count }} Siswa
                  </a>
                </td>
                <td>
                  <form action="{{ route('akademik.asesmen.toggle', $a->id) }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" class="ak-btn ak-btn-sm {{ $a->is_active ? 'ak-btn-primary' : 'ak-btn-secondary' }}" title="Klik untuk mengubah status aktif">
                      <i class="bi {{ $a->is_active ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                      <span>{{ $a->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </button>
                  </form>
                </td>
                <td style="text-align:center;">
                  <div style="display:flex; justify-content:center; gap:6px;">
                    <a href="{{ route('akademik.asesmen.soal', $a->id) }}" class="ak-btn ak-btn-secondary ak-btn-sm" title="Kelola Butir Soal">
                      <i class="bi bi-card-checklist"></i>
                    </a>
                    <a href="{{ route('akademik.asesmen.hasil', $a->id) }}" class="ak-btn ak-btn-secondary ak-btn-sm" title="Pantau Hasil Siswa">
                      <i class="bi bi-bar-chart"></i>
                    </a>
                    <a href="{{ route('akademik.asesmen.kerjakan', $a->id) }}" class="ak-btn ak-btn-primary ak-btn-sm" title="Simulasi Ujian">
                      <i class="bi bi-play-circle"></i>
                    </a>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div style="padding:16px 20px;">
        {{ $asesmens->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
