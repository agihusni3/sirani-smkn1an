@extends('layouts.dokumen_a4')

@php
  $backUrl = route('panitia-asesmen.administrasi');
  $backLabel = 'Kembali ke Administrasi Ujian';
@endphp

@section('title', 'Daftar Hadir Ujian - ' . ($periode->nama_event ?? 'CBT'))
@section('toolbar_title', 'Daftar Hadir Peserta Asesmen Ruang Resmi (A4)')

@push('styles')
<style>
  .table-presensi {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    font-size: 9.5pt;
  }
  .table-presensi th, .table-presensi td {
    border: 1px solid #0f172a;
    padding: 5px 8px;
  }
  .table-presensi th {
    background-color: #f1f5f9;
    text-align: center;
    font-weight: 700;
    text-transform: uppercase;
  }
  .ttd-col-1, .ttd-col-2 {
    width: 95px;
    vertical-align: top;
    padding-left: 6px;
    font-size: 9pt;
  }
</style>
@endpush

@section('content')
  <div style="text-align:center; margin-bottom:12px;">
    <h3 style="margin:0; font-size:12pt; font-weight:800; text-transform:uppercase; text-decoration:underline; letter-spacing:0.5px;">
      DAFTAR HADIR PESERTA ASESMEN BERBASIS KOMPUTER (CBT)
    </h3>
    <div style="font-size:10pt; font-weight:700; margin-top:2px; color:#1e293b;">
      {{ strtoupper($periode->nama_event ?? 'ASESMEN SEKOLAH') }}
    </div>
  </div>

  <table style="width:100%; border-collapse:collapse; margin-bottom:8px; font-size:9.5pt; line-height:1.4;">
    <tr>
      <td style="width:110px; font-weight:600;">Mata Pelajaran</td>
      <td style="width:12px; text-align:center;">:</td>
      <td style="width:40%;"><strong>{{ $mapelNama }}</strong></td>
      <td style="width:80px; font-weight:600;">Ruang Ujian</td>
      <td style="width:12px; text-align:center;">:</td>
      <td><strong>{{ $ruang }}</strong></td>
    </tr>
    <tr>
      <td style="font-weight:600;">Hari / Tanggal</td>
      <td style="text-align:center;">:</td>
      <td>{{ date('l, d F Y') }}</td>
      <td style="font-weight:600;">Sesi Ujian</td>
      <td style="text-align:center;">:</td>
      <td>{{ $sesi }}</td>
    </tr>
    @if($rombel)
    <tr>
      <td style="font-weight:600;">Kelas / Rombel</td>
      <td style="text-align:center;">:</td>
      <td colspan="4">{{ $rombel->nama_rombel }}</td>
    </tr>
    @endif
  </table>

  <table class="table-presensi">
    <thead>
      <tr>
        <th style="width:35px;">No</th>
        <th style="width:120px;">No. Peserta / NISN</th>
        <th>Nama Lengkap Peserta</th>
        <th colspan="2" style="width:190px;">Tanda Tangan</th>
        <th style="width:75px;">Ket.</th>
      </tr>
    </thead>
    <tbody>
      @forelse($siswas as $index => $s)
        @php
          $no = $index + 1;
          $isOdd = ($no % 2 !== 0);
        @endphp
        <tr>
          <td style="text-align:center; font-weight:600;">{{ $no }}</td>
          <td style="font-family:monospace; text-align:center;">{{ $s->nisn ?: ('PES-' . str_pad($s->id, 4, '0', STR_PAD_LEFT)) }}</td>
          <td style="font-weight:700; text-transform:uppercase;">{{ $s->nama }}</td>
          <td class="ttd-col-1">
            @if($isOdd)
              <span>{{ $no }}. .....................</span>
            @endif
          </td>
          <td class="ttd-col-2">
            @if(!$isOdd)
              <span>{{ $no }}. .....................</span>
            @endif
          </td>
          <td style="text-align:center; font-size:8.5pt;"></td>
        </tr>
      @empty
        @for($i = 1; $i <= 15; $i++)
        <tr>
          <td style="text-align:center;">{{ $i }}</td>
          <td></td>
          <td></td>
          <td class="ttd-col-1">@if($i % 2 !== 0) {{ $i }}. ......... @endif</td>
          <td class="ttd-col-2">@if($i % 2 === 0) {{ $i }}. ......... @endif</td>
          <td></td>
        </tr>
        @endfor
      @endforelse
    </tbody>
  </table>
@endsection

@section('ttd')
  <table style="width:100%; border-collapse:collapse; font-size:10pt; margin-top:15px;">
    <tr>
      <td style="width:50%; text-align:center;">
        <div>Mengetahui / Mengawasi,</div>
        <div style="font-weight:700;">Pengawas Ruang Ujian,</div>
        <div style="height:55px;"></div>
        <div style="font-weight:700; text-decoration:underline;">( ........................................................... )</div>
        <div style="font-size:9pt;">NIP. .....................................................</div>
      </td>
      <td style="width:50%; text-align:center;">
        <div>Air Naningan, {{ date('d F Y') }}</div>
        <div style="font-weight:700;">Proktor Ruang / Lab,</div>
        <div style="height:55px;"></div>
        <div style="font-weight:700; text-decoration:underline;">( ........................................................... )</div>
        <div style="font-size:9pt;">NIP. .....................................................</div>
      </td>
    </tr>
  </table>
@endsection
