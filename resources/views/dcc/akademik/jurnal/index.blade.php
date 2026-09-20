@extends('dcc.akademik.layout')

@section('title', 'Jurnal KBM Harian')
@section('breadcrumb', 'Jurnal KBM')

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">Jurnal Kegiatan Belajar Mengajar (KBM)</h1>
    <div class="akademik-page-desc">
      Pencatatan materi ajar harian, refleksi pengajaran guru, dan absensi kehadiran siswa per sesi tatap muka.
    </div>
  </div>

  <a href="{{ route('akademik.jurnal.create') }}" class="ak-btn ak-btn-primary">
    <i class="bi bi-pencil-square"></i>
    <span>Isi Jurnal KBM Baru</span>
  </a>
</div>

{{-- Filter Toolbar --}}
<div class="akademik-card" style="margin-bottom:16px;">
  <div class="akademik-card-body" style="padding:14px 20px;">
    <form action="{{ route('akademik.jurnal.index') }}" method="GET" style="display:flex; flex-wrap:wrap; gap:12px; align-items:center;">
      <div style="width:170px;">
        <input type="date" name="tanggal" class="ak-input" value="{{ request('tanggal') }}" onchange="this.form.submit()">
      </div>

      @if(auth()->user()?->isAdmin() || auth()->user()?->isWakaKurikulum())
        <div style="flex:1; min-width:200px;">
          <select name="guru_id" class="ak-select" onchange="this.form.submit()">
            <option value="">Semua Guru Pengajar</option>
            @foreach($gurus as $g)
              <option value="{{ $g->id }}" {{ request('guru_id') == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
            @endforeach
          </select>
        </div>
      @endif

      <div style="width:180px;">
        <select name="rombel_id" class="ak-select" onchange="this.form.submit()">
          <option value="">Semua Rombel</option>
          @foreach($rombels as $r)
            <option value="{{ $r->id }}" {{ request('rombel_id') == $r->id ? 'selected' : '' }}>{{ $r->nama_rombel }}</option>
          @endforeach
        </select>
      </div>

      <button type="submit" class="ak-btn ak-btn-secondary">
        <i class="bi bi-filter"></i>
      </button>

      @if(request()->anyFilled(['tanggal', 'guru_id', 'rombel_id']))
        <a href="{{ route('akademik.jurnal.index') }}" class="ak-btn ak-btn-secondary" title="Reset">
          <i class="bi bi-x-circle"></i>
        </a>
      @endif
    </form>
  </div>
</div>

{{-- Data Table --}}
<div class="akademik-card">
  <div class="akademik-card-body" style="padding:0;">
    @if($jurnals->isEmpty())
      <div style="padding:48px 20px; text-align:center; color:var(--ak-slate-600);">
        <i class="bi bi-journal-x" style="font-size:40px; opacity:0.35; display:block; margin-bottom:10px;"></i>
        Belum ada catatan jurnal KBM sesuai filter.
      </div>
    @else
      <div class="akademik-table-wrap">
        <table class="akademik-table">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Pertemuan</th>
              <th>Guru Pengajar</th>
              <th>Mata Pelajaran &amp; Rombel</th>
              <th>Materi Pokok yang Diajarkan</th>
              <th>Kehadiran Siswa</th>
              <th style="width:90px; text-align:center;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($jurnals as $j)
              <tr>
                <td>
                  <div style="font-weight:700;">{{ \Carbon\Carbon::parse($j->tanggal)->isoFormat('D MMM Y') }}</div>
                  <div style="font-size:11px; color:#64748b;">{{ \Carbon\Carbon::parse($j->tanggal)->isoFormat('dddd') }}</div>
                </td>
                <td>
                  <span class="ak-badge ak-badge-primary">Ke-{{ $j->pertemuan_ke }}</span>
                </td>
                <td>
                  <div style="font-weight:700; color:var(--ak-dark);">{{ $j->distribusi?->guru?->nama ?? '-' }}</div>
                </td>
                <td>
                  <div style="font-weight:700; color:var(--ak-primary);">{{ $j->distribusi?->mataPelajaran?->nama_mapel ?? '-' }}</div>
                  <span class="ak-badge ak-badge-secondary" style="margin-top:2px;">{{ $j->distribusi?->rombel?->nama_rombel ?? '-' }}</span>
                </td>
                <td style="max-width:280px;">
                  <div style="font-weight:600; font-size:13px; color:var(--ak-dark);">{{ Str::limit($j->materi_ajar, 80) }}</div>
                  @if($j->metode_pembelajaran)
                    <div style="font-size:11px; color:#64748b;">Metode: {{ $j->metode_pembelajaran }}</div>
                  @endif
                </td>
                <td>
                  @php
                    $hadir = $j->kehadirans->where('status', 'hadir')->count();
                    $tidakHadir = $j->kehadirans->whereIn('status', ['izin', 'sakit', 'alfa'])->count();
                  @endphp
                  <span class="ak-badge ak-badge-success">{{ $hadir }} Hadir</span>
                  @if($tidakHadir > 0)
                    <span class="ak-badge ak-badge-danger">{{ $tidakHadir }} Absen</span>
                  @endif
                </td>
                <td style="text-align:center;">
                  <div style="display:flex; justify-content:center; gap:6px;">
                    <a href="{{ route('akademik.jurnal.show', $j->id) }}" class="ak-btn ak-btn-secondary ak-btn-sm" title="Lihat Presensi">
                      <i class="bi bi-eye"></i>
                    </a>
                    @if(auth()->user()?->isAdmin() || auth()->user()?->guru_id == $j->distribusi?->guru_id)
                      <form action="{{ route('akademik.jurnal.destroy', $j->id) }}" method="POST" onsubmit="return confirm('Hapus jurnal KBM ini?')" style="margin:0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="ak-btn ak-btn-secondary ak-btn-sm text-danger" title="Hapus">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    @endif
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div style="padding:16px 20px;">
        {{ $jurnals->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
