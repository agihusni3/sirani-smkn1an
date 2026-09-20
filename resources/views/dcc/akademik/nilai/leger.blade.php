@extends('dcc.akademik.layout')

@section('title', 'Leger Nilai Kelas')
@section('breadcrumb', 'Leger Nilai')

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">Leger Nilai &amp; Peringkat Kelas</h1>
    <div class="akademik-page-desc">
      Rekapitulasi seluruh nilai akhir mata pelajaran per siswa di kelas <strong>{{ $selectedRombel?->nama_rombel }}</strong> (Semester {{ $semester }}).
    </div>
  </div>

  <div style="display:flex; gap:10px;">
    <button type="button" class="ak-btn ak-btn-secondary" onclick="window.print()">
      <i class="bi bi-printer"></i>
      <span>Cetak Leger</span>
    </button>
  </div>
</div>

{{-- Filter Toolbar --}}
<div class="akademik-card" style="margin-bottom:16px;">
  <div class="akademik-card-body" style="padding:14px 20px;">
    <form action="{{ route('akademik.nilai.leger') }}" method="GET" style="display:flex; flex-wrap:wrap; gap:12px; align-items:center;">
      <div style="width:200px;">
        <select name="rombel_id" class="ak-select" onchange="this.form.submit()">
          @foreach($rombels as $r)
            <option value="{{ $r->id }}" {{ $selectedRombel?->id == $r->id ? 'selected' : '' }}>{{ $r->nama_rombel }}</option>
          @endforeach
        </select>
      </div>

      <div style="width:140px;">
        <select name="semester" class="ak-select" onchange="this.form.submit()">
          <option value="1" {{ $semester == 1 ? 'selected' : '' }}>Semester 1</option>
          <option value="2" {{ $semester == 2 ? 'selected' : '' }}>Semester 2</option>
        </select>
      </div>

      <button type="submit" class="ak-btn ak-btn-secondary">
        <i class="bi bi-filter"></i>
      </button>
    </form>
  </div>
</div>

{{-- Leger Matrix Table --}}
<div class="akademik-card">
  <div class="akademik-card-body" style="padding:0;">
    @if($siswas->isEmpty() || $distribusis->isEmpty())
      <div style="padding:48px 20px; text-align:center; color:#64748b;">
        <i class="bi bi-table" style="font-size:40px; opacity:0.35; display:block; margin-bottom:10px;"></i>
        Belum ada data mata pelajaran atau siswa terdaftar di rombel ini.
      </div>
    @else
      <div class="akademik-table-wrap">
        <table class="akademik-table" style="font-size:12px;">
          <thead>
            <tr>
              <th style="width:40px;">No</th>
              <th style="min-width:180px;">Nama Siswa</th>
              @foreach($distribusis as $d)
                <th style="text-align:center; min-width:80px;" title="{{ $d->mataPelajaran?->nama_mapel }}">
                  {{ $d->mataPelajaran?->kode_mapel }}
                </th>
              @endforeach
              <th style="text-align:center; min-width:80px; background:#f1f5f9;">Rata-rata</th>
              <th style="text-align:center; min-width:90px; background:#f1f5f9;">Status</th>
            </tr>
          </thead>
          <tbody>
            @php
              // Hitung rata-rata per siswa untuk sorting
              $studentAverages = [];
              foreach ($siswas as $s) {
                $userLegers = $legers->get($s->id) ?? collect();
                $scores = $userLegers->pluck('nilai_akhir')->filter(fn($v) => $v > 0);
                $studentAverages[$s->id] = $scores->count() > 0 ? round($scores->avg(), 2) : 0;
              }
            @endphp

            @foreach($siswas as $idx => $s)
              @php
                $userLegers = $legers->get($s->id)?->keyBy('distribusi_id') ?? collect();
                $avg = $studentAverages[$s->id];
              @endphp
              <tr>
                <td>{{ $idx + 1 }}</td>
                <td>
                  <div style="font-weight:700; color:var(--ak-dark);">{{ $s->nama_lengkap }}</div>
                  <div style="font-size:10.5px; color:#64748b;">{{ $s->nisn ?? '-' }}</div>
                </td>
                @foreach($distribusis as $d)
                  @php
                    $entry = $userLegers->get($d->id);
                    $score = $entry?->nilai_akhir;
                  @endphp
                  <td style="text-align:center;">
                    @if($score !== null && $score > 0)
                      <span style="font-weight:700; color: {{ $score < 70 ? '#dc2626' : '#059669' }};">
                        {{ $score }}
                      </span>
                      <div style="font-size:10px; color:#64748b;">{{ $entry->predikat }}</div>
                    @else
                      <span style="color:#cbd5e1;">-</span>
                    @endif
                  </td>
                @endforeach
                <td style="text-align:center; background:#f8fafc; font-weight:800; color:var(--ak-primary);">
                  {{ $avg > 0 ? $avg : '-' }}
                </td>
                <td style="text-align:center; background:#f8fafc;">
                  @if($avg >= 70)
                    <span class="ak-badge ak-badge-success">Lulus</span>
                  @elseif($avg > 0)
                    <span class="ak-badge ak-badge-danger">Remedial</span>
                  @else
                    <span class="ak-badge ak-badge-secondary">Pending</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
@endsection
