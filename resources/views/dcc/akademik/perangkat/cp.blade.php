@extends('dcc.akademik.layout')

@section('title', 'Capaian Pembelajaran (CP) — Perangkat Pembelajaran')
@section('breadcrumb')
  <a href="{{ route('akademik.perangkat.cp') }}">Perangkat Pembelajaran</a>
  <i class="bi bi-chevron-right" style="font-size: 10px;"></i>
  <span>Capaian Pembelajaran (CP)</span>
@endsection

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">
      <i class="bi bi-bookmark-star text-primary me-2"></i>
      1. Capaian Pembelajaran (CP) Resmi
    </h1>
    <div class="akademik-page-desc">
      Standar Capaian Pembelajaran Kurikulum Merdeka (SK BSKAP No. 032/H/KR/2024) per fase dan elemen kompetensi mata pelajaran.
    </div>
  </div>
</div>

@include('dcc.akademik.perangkat.partials.selector', ['showMapelSelector' => true])

@if($selectedMapel)
  {{-- Info Mata Pelajaran --}}
  <div class="akademik-card" style="margin-bottom:20px; padding:18px 22px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px;">
      <div>
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
          <span class="ak-badge ak-badge-primary" style="font-size:12px; font-weight:800;">
            Kode: {{ $selectedMapel->kode_mapel }}
          </span>
          <span class="ak-badge ak-badge-secondary" style="font-size:12px;">
            {{ $selectedMapel->jenis_label }}
          </span>
          <span class="ak-badge ak-badge-info" style="font-size:12px;">
            {{ $selectedMapel->tingkat_label }}
          </span>
        </div>
        <h2 style="font-weight:900; font-size:20px; color:var(--ak-dark); margin:0;">
          {{ $selectedMapel->nama_mapel }}
        </h2>
      </div>

      <div style="text-align:right;">
        <span class="badge" style="background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; font-size:12px; padding:6px 12px; font-weight:700;">
          <i class="bi bi-check-circle-fill me-1"></i> SK BSKAP 032/2024
        </span>
      </div>
    </div>
  </div>

  <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:24px;">
    
    {{-- Fase E (Kelas X) --}}
    <div class="akademik-card" style="border-top:4px solid #0284c7;">
      <div class="akademik-card-header" style="background:#f8fafc;">
        <h3 class="akademik-card-title" style="font-size:15px; display:flex; align-items:center; gap:8px;">
          <span class="badge bg-primary" style="font-size:11px;">Fase E</span>
          <span>Capaian Pembelajaran Umum (Kelas X SMK)</span>
        </h3>
      </div>
      <div class="akademik-card-body" style="padding:18px 20px; font-size:13px; line-height:1.65; color:#334155;">
        @if($selectedMapel->capaian_pembelajaran_fase_e)
          <div style="white-space:pre-line;">{{ $selectedMapel->capaian_pembelajaran_fase_e }}</div>
        @else
          <div style="padding:24px; text-align:center; color:#94a3b8; font-style:italic;">
            <i class="bi bi-journal-x" style="font-size:28px; display:block; margin-bottom:6px;"></i>
            Teks Capaian Pembelajaran Fase E belum dimasukkan pada master mata pelajaran.
          </div>
        @endif
      </div>
    </div>

    {{-- Fase F (Kelas XI - XII) --}}
    <div class="akademik-card" style="border-top:4px solid #7c3aed;">
      <div class="akademik-card-header" style="background:#f8fafc;">
        <h3 class="akademik-card-title" style="font-size:15px; display:flex; align-items:center; gap:8px;">
          <span class="badge bg-purple" style="background:#7c3aed; color:#fff; font-size:11px;">Fase F</span>
          <span>Capaian Pembelajaran Umum (Kelas XI &amp; XII SMK)</span>
        </h3>
      </div>
      <div class="akademik-card-body" style="padding:18px 20px; font-size:13px; line-height:1.65; color:#334155;">
        @if($selectedMapel->capaian_pembelajaran_fase_f)
          <div style="white-space:pre-line;">{{ $selectedMapel->capaian_pembelajaran_fase_f }}</div>
        @else
          <div style="padding:24px; text-align:center; color:#94a3b8; font-style:italic;">
            <i class="bi bi-journal-x" style="font-size:28px; display:block; margin-bottom:6px;"></i>
            Teks Capaian Pembelajaran Fase F belum dimasukkan pada master mata pelajaran.
          </div>
        @endif
      </div>
    </div>

  </div>

  {{-- Rincian Elemen CP --}}
  <div class="akademik-card">
    <div class="akademik-card-header" style="background:#f8fafc; display:flex; justify-content:space-between; align-items:center;">
      <h3 class="akademik-card-title" style="font-size:15px;">
        <i class="bi bi-diagram-3 text-primary me-2"></i>
        Elemen Kompetensi &amp; Deskripsi Capaian per Elemen
      </h3>
      @if($isAdminOrWaka)
        <a href="{{ route('akademik.matpel.index') }}" class="ak-btn ak-btn-secondary ak-btn-sm" style="font-size:11.5px;">
          <i class="bi bi-pencil me-1"></i> Edit CP Master
        </a>
      @endif
    </div>
    <div class="akademik-card-body" style="padding:0;">
      @php
        $elemenList = is_array($selectedMapel->elemen_cp) ? $selectedMapel->elemen_cp : (json_decode($selectedMapel->elemen_cp, true) ?? []);
      @endphp

      @if(!empty($elemenList))
        <div class="akademik-table-wrap">
          <table class="akademik-table">
            <thead>
              <tr>
                <th style="width:50px; text-align:center;">No</th>
                <th style="width:240px;">Nama Elemen CP</th>
                <th>Deskripsi Capaian Pembelajaran Elemen</th>
              </tr>
            </thead>
            <tbody>
              @foreach($elemenList as $idx => $elem)
                <tr>
                  <td style="text-align:center; font-weight:800;">{{ $loop->iteration }}</td>
                  <td style="font-weight:700; color:var(--ak-dark);">
                    <div style="font-size:13px;">{{ is_array($elem) ? ($elem['nama'] ?? $elem['elemen'] ?? '-') : $elem }}</div>
                  </td>
                  <td style="font-size:12.5px; line-height:1.55; color:#334155;">
                    {{ is_array($elem) ? ($elem['deskripsi'] ?? $elem['capaian'] ?? '-') : '-' }}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div style="padding:30px; text-align:center; color:#64748b;">
          <i class="bi bi-info-circle text-primary" style="font-size:24px; display:block; margin-bottom:6px;"></i>
          Belum ada rincian elemen CP khusus untuk mata pelajaran ini. Gunakan Capaian Pembelajaran Fase E &amp; F di atas sebagai acuan penyusunan Tujuan Pembelajaran (TP).
        </div>
      @endif
    </div>
  </div>

@else
  <div class="akademik-card" style="padding:40px 20px; text-align:center; color:#64748b;">
    <i class="bi bi-journal-text" style="font-size:36px; color:#cbd5e1; display:block; margin-bottom:12px;"></i>
    <div style="font-weight:700; font-size:15px; color:var(--ak-dark); margin-bottom:4px;">Pilih Mata Pelajaran</div>
    <div style="font-size:12.5px;">Silakan pilih mata pelajaran di atas untuk membaca dokumen Capaian Pembelajaran resmi.</div>
  </div>
@endif

@endsection
