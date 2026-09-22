@extends('dcc.akademik.layout')

@section('title', 'Bank Soal Asesmen')
@section('breadcrumb', 'Bank Soal')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css">
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/contrib/auto-render.min.js"></script>
<style>
  .bank-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 18px;
    margin-bottom: 12px;
    transition: all 0.15s ease;
  }
  .bank-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03);
  }
</style>
@endpush

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">Bank Soal Asesmen</h1>
    <div class="akademik-page-desc">
      Kumpulan butir pertanyaan yang siap dipanggil dan digunakan kembali dalam penyusunan paket ujian atau asesmen.
    </div>
  </div>

  <div style="display:flex; gap:8px;">
    <a href="{{ route('akademik.asesmen.index') }}" class="ak-btn ak-btn-secondary">
      <i class="bi bi-arrow-left"></i>
      <span>Kembali ke Paket Asesmen</span>
    </a>
  </div>
</div>

{{-- Filter Card --}}
<div class="akademik-card" style="margin-bottom:20px;">
  <div class="akademik-card-body" style="padding:14px 20px;">
    <form action="{{ route('akademik.bank_soal.index') }}" method="GET" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
      
      <div style="width:240px;">
        <select name="mapel_id" class="ak-select" onchange="this.form.submit()">
          <option value="">Semua Mata Pelajaran</option>
          @foreach($allMapels as $m)
            <option value="{{ $m->id }}" {{ request('mapel_id') == $m->id ? 'selected' : '' }}>
              {{ $m->nama_mapel }}
            </option>
          @endforeach
        </select>
      </div>

      <div style="flex:1; min-width:260px;">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari teks butir soal atau opsi jawaban..." class="ak-input">
      </div>

      <div>
        <button type="submit" class="ak-btn ak-btn-secondary">
          <i class="bi bi-search"></i>
          <span>Cari</span>
        </button>
        @if(request()->hasAny(['mapel_id', 'q']))
          <a href="{{ route('akademik.bank_soal.index') }}" class="ak-btn ak-btn-secondary" title="Reset Filter">
            <i class="bi bi-arrow-counterclockwise"></i>
          </a>
        @endif
      </div>
    </form>
  </div>
</div>

{{-- Content List --}}
@if($bankSoals->isEmpty())
  <div class="akademik-card" style="text-align:center; padding:60px 20px;">
    <div style="width:64px; height:64px; border-radius:50%; background:#f1f5f9; color:#94a3b8; display:flex; align-items:center; justify-content:center; margin:0 auto 14px; font-size:28px;">
      <i class="bi bi-archive"></i>
    </div>
    <h3 style="font-size:16px; font-weight:800; color:#1e293b; margin-bottom:6px;">Belum Ada Soal di Bank Soal</h3>
    <p style="font-size:13px; color:#64748b; max-width:420px; margin:0 auto 16px; line-height:1.5;">
      Setiap butir pertanyaan yang Anda tambahkan pada paket asesmen akan secara otomatis disimpan di sini agar dapat dipanggil kembali sewaktu-waktu.
    </p>
    <a href="{{ route('akademik.asesmen.index') }}" class="ak-btn ak-btn-primary">
      <i class="bi bi-journal-check me-1"></i> Buka Paket Asesmen
    </a>
  </div>
@else
  <div id="bankSoalList">
    @foreach($bankSoals as $idx => $bs)
      <div class="bank-card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; flex-wrap:wrap; gap:8px;">
          <div style="display:flex; align-items:center; gap:8px;">
            <span class="ak-badge ak-badge-primary" style="font-size:11px;">
              {{ $bs->mataPelajaran?->nama_mapel ?? 'Mata Pelajaran' }}
            </span>
            <span class="ak-badge ak-badge-secondary" style="font-size:11px;">
              {{ strtoupper(str_replace('_', ' ', $bs->tipe)) }} · Bobot: {{ $bs->bobot }}
            </span>
            @if($bs->guru)
              <span style="font-size:11.5px; color:#64748b;">
                Oleh: <strong>{{ $bs->guru->nama_guru }}</strong>
              </span>
            @endif
          </div>

          <form action="{{ route('akademik.bank_soal.destroy', $bs->id) }}" method="POST" onsubmit="return confirm('Hapus butir pertanyaan ini dari Bank Soal?')" style="margin:0;">
            @csrf
            @method('DELETE')
            <button type="submit" class="ak-btn ak-btn-secondary ak-btn-sm text-danger" title="Hapus dari Bank Soal" style="padding:4px 9px; font-size:11.5px;">
              <i class="bi bi-trash me-1"></i> Hapus
            </button>
          </form>
        </div>

        {{-- Teks Pertanyaan --}}
        <div class="render-math-target" style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:10px; line-height:1.6;">
          {!! $bs->pertanyaan !!}
        </div>

        @if($bs->gambar_url)
          <div style="margin-bottom:10px; max-width:240px;">
            <img src="{{ $bs->gambar_url }}" alt="Gambar Soal" style="width:100%; border-radius:8px; border:1px solid #cbd5e1;">
          </div>
        @endif

        {{-- Opsi Jawaban --}}
        <div style="display:flex; flex-direction:column; gap:4px; font-size:12.5px; margin-bottom:8px;">
          @foreach(['A' => $bs->opsi_a, 'B' => $bs->opsi_b, 'C' => $bs->opsi_c, 'D' => $bs->opsi_d, 'E' => $bs->opsi_e] as $abjad => $teksOpsi)
            @if(!empty($teksOpsi))
              @php $isKunci = ($bs->kunci_jawaban == $abjad); @endphp
              <div class="render-math-target" style="display:flex; align-items:center; gap:8px; padding:4px 10px; border-radius:6px; {{ $isKunci ? 'font-weight:800; color:#065f46; background:#ecfdf5; border:1px solid #a7f3d0;' : 'color:#475569;' }}">
                <strong style="width:16px;">{{ $abjad }}.</strong>
                <span style="flex:1;">{{ $teksOpsi }}</span>
                @if($isKunci)
                  <i class="bi bi-check-circle-fill text-success" title="Kunci Jawaban"></i>
                @endif
              </div>
            @endif
          @endforeach
        </div>

        @if($bs->pembahasan)
          <div style="margin-top:8px; padding:6px 12px; background:#f8fafc; border-radius:6px; font-size:12px; color:#64748b;">
            <strong style="color:#334155;">Pembahasan:</strong> {{ $bs->pembahasan }}
          </div>
        @endif
      </div>
    @endforeach

    <div style="margin-top:20px;">
      {{ $bankSoals->links() }}
    </div>
  </div>
@endif
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('bankSoalList');
    if (container && typeof renderMathInElement === 'function') {
      renderMathInElement(container, {
        delimiters: [
          {left: '$$', right: '$$', display: true},
          {left: '$', right: '$', display: false},
          {left: '\\(', right: '\\)', display: false},
          {left: '\\[', right: '\\]', display: true}
        ],
        throwOnError: false
      });
    }
  });
</script>
@endpush
