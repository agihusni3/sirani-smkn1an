@extends('dcc.akademik.layout')

@section('title', 'Penilaian Dimensi P5BK')
@section('breadcrumb', 'Penilaian P5BK')

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">{{ $proyek->nama_proyek }}</h1>
    <div class="akademik-page-desc">
      Tema: <strong>{{ $proyek->tema }}</strong> · Kelas: <strong>{{ $proyek->rombel?->nama_rombel }}</strong> · Semester {{ $proyek->semester }}
    </div>
  </div>

  <a href="{{ route('akademik.p5bk.index') }}" class="ak-btn ak-btn-secondary">
    <i class="bi bi-arrow-left"></i>
    <span>Kembali</span>
  </a>
</div>

{{-- Skala Penilaian Card --}}
<div class="akademik-card" style="margin-bottom:16px;">
  <div class="akademik-card-body" style="padding:12px 20px; font-size:12px; display:flex; flex-wrap:wrap; gap:16px; align-items:center;">
    <span style="font-weight:700; color:var(--ak-dark);">Keterangan Skala Nilai:</span>
    <span class="ak-badge ak-badge-danger">1: Belum Berkembang (BB)</span>
    <span class="ak-badge ak-badge-warning">2: Mulai Berkembang (MB)</span>
    <span class="ak-badge ak-badge-primary">3: Berkembang Sesuai Harapan (BSH)</span>
    <span class="ak-badge ak-badge-success">4: Sangat Berkembang (SB)</span>
  </div>
</div>

<form action="{{ route('akademik.p5bk.nilai.store', $proyek->id) }}" method="POST">
  @csrf

  <div class="akademik-card">
    <div class="akademik-card-header">
      <h3 class="akademik-card-title">
        <i class="bi bi-award text-primary"></i>
        <span>Lembar Penilaian 6 Dimensi Profil Pelajar Pancasila</span>
      </h3>
    </div>
    <div class="akademik-card-body" style="padding:0;">
      @if($siswas->isEmpty())
        <div style="padding:40px; text-align:center; color:#64748b;">
          Belum ada data siswa di rombel ini.
        </div>
      @else
        <div class="akademik-table-wrap">
          <table class="akademik-table" style="font-size:12px;">
            <thead>
              <tr>
                <th style="width:35px;">No</th>
                <th style="min-width:160px;">Nama Siswa</th>
                <th style="text-align:center;">Beriman &amp; Taqwa</th>
                <th style="text-align:center;">Kebhinekaan</th>
                <th style="text-align:center;">Gotong Royong</th>
                <th style="text-align:center;">Mandiri</th>
                <th style="text-align:center;">Bernalar Kritis</th>
                <th style="text-align:center;">Kreatif</th>
                <th style="text-align:center;">Budaya Kerja</th>
                <th style="min-width:200px;">Catatan Proses</th>
              </tr>
            </thead>
            <tbody>
              @foreach($siswas as $idx => $s)
                @php
                  $n = $existingNilai->get($s->id);
                @endphp
                <tr>
                  <td>{{ $idx + 1 }}</td>
                  <td>
                    <div style="font-weight:700; color:var(--ak-dark);">{{ $s->nama_lengkap }}</div>
                    <div style="font-size:10.5px; color:#64748b;">{{ $s->nisn ?? '-' }}</div>
                  </td>
                  <td style="text-align:center;">
                    <select name="dimensi[{{ $s->id }}][beriman]" class="ak-select" style="padding:4px; font-size:12px; width:65px;">
                      @for($v=1; $v<=4; $v++)
                        <option value="{{ $v }}" {{ ($n?->beriman_bertaqwa ?? 3) == $v ? 'selected' : '' }}>{{ $v }}</option>
                      @endfor
                    </select>
                  </td>
                  <td style="text-align:center;">
                    <select name="dimensi[{{ $s->id }}][kebhinekaan]" class="ak-select" style="padding:4px; font-size:12px; width:65px;">
                      @for($v=1; $v<=4; $v++)
                        <option value="{{ $v }}" {{ ($n?->berkebhinekaan_global ?? 3) == $v ? 'selected' : '' }}>{{ $v }}</option>
                      @endfor
                    </select>
                  </td>
                  <td style="text-align:center;">
                    <select name="dimensi[{{ $s->id }}][gotong_royong]" class="ak-select" style="padding:4px; font-size:12px; width:65px;">
                      @for($v=1; $v<=4; $v++)
                        <option value="{{ $v }}" {{ ($n?->bergotong_royong ?? 3) == $v ? 'selected' : '' }}>{{ $v }}</option>
                      @endfor
                    </select>
                  </td>
                  <td style="text-align:center;">
                    <select name="dimensi[{{ $s->id }}][mandiri]" class="ak-select" style="padding:4px; font-size:12px; width:65px;">
                      @for($v=1; $v<=4; $v++)
                        <option value="{{ $v }}" {{ ($n?->mandiri ?? 3) == $v ? 'selected' : '' }}>{{ $v }}</option>
                      @endfor
                    </select>
                  </td>
                  <td style="text-align:center;">
                    <select name="dimensi[{{ $s->id }}][nalar_kritis]" class="ak-select" style="padding:4px; font-size:12px; width:65px;">
                      @for($v=1; $v<=4; $v++)
                        <option value="{{ $v }}" {{ ($n?->bernalar_kritis ?? 3) == $v ? 'selected' : '' }}>{{ $v }}</option>
                      @endfor
                    </select>
                  </td>
                  <td style="text-align:center;">
                    <select name="dimensi[{{ $s->id }}][kreatif]" class="ak-select" style="padding:4px; font-size:12px; width:65px;">
                      @for($v=1; $v<=4; $v++)
                        <option value="{{ $v }}" {{ ($n?->kreatif ?? 3) == $v ? 'selected' : '' }}>{{ $v }}</option>
                      @endfor
                    </select>
                  </td>
                  <td style="text-align:center;">
                    <select name="dimensi[{{ $s->id }}][budaya_kerja]" class="ak-select" style="padding:4px; font-size:12px; width:65px;">
                      @for($v=1; $v<=4; $v++)
                        <option value="{{ $v }}" {{ ($n?->nilai_budaya_kerja ?? 3) == $v ? 'selected' : '' }}>{{ $v }}</option>
                      @endfor
                    </select>
                  </td>
                  <td>
                    <input type="text" name="catatan[{{ $s->id }}]" value="{{ $n?->catatan }}" class="ak-input" style="font-size:12px; padding:4px 8px;" placeholder="Catatan keaktifan...">
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div style="padding:16px 20px; border-top:1px solid var(--ak-slate-200); display:flex; justify-content:flex-end; gap:10px;">
          <a href="{{ route('akademik.p5bk.index') }}" class="ak-btn ak-btn-secondary">Batal</a>
          <button type="submit" class="ak-btn ak-btn-primary">
            <i class="bi bi-check2-circle"></i>
            <span>Simpan Penilaian P5BK</span>
          </button>
        </div>
      @endif
    </div>
  </div>
</form>
@endsection
