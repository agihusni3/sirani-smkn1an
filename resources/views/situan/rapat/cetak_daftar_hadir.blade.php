@extends('layouts.dokumen_a4')

@php
  $backUrl = route('situan.rapat.show', $rapat->id);
  $backLabel = 'Kembali ke Rapat';
@endphp

@section('title', 'Daftar Hadir - ' . $rapat->judul_rapat)
@section('toolbar_title', 'Daftar Hadir Peserta Rapat Resmi')

@push('styles')
<style>
  .table-presensi {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    font-size: 10pt;
  }
  .table-presensi th, .table-presensi td {
    border: 1px solid #1e293b;
    padding: 6px 8px;
  }
  .table-presensi th {
    background-color: #f1f5f9;
    text-align: center;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 9.5pt;
  }
  .ttd-col-1 {
    width: 95px;
    vertical-align: top;
    padding-left: 6px;
    font-size: 9.5pt;
  }
  .ttd-col-2 {
    width: 95px;
    vertical-align: top;
    padding-left: 6px;
    font-size: 9.5pt;
  }
</style>
@endpush

@section('content')
  <div style="text-align:center; margin-bottom:12px;">
    <h3 style="margin:0; font-size:12pt; font-weight:800; text-transform:uppercase; text-decoration:underline; letter-spacing:0.5px;">
      DAFTAR HADIR PESERTA RAPAT
    </h3>
    <div style="font-size:10.5pt; font-weight:600; margin-top:3px; color:#1e293b;">
      {{ $rapat->judul_rapat }}
    </div>
  </div>

  {{-- Meta Rapat Singkat --}}
  <table style="width:100%; border-collapse:collapse; margin-bottom:10px; font-size:10pt; line-height:1.4;">
    <tr>
      <td style="width:90px; font-weight:600;">Hari / Tanggal</td>
      <td style="width:12px; text-align:center;">:</td>
      <td style="width:40%;">{{ $rapat->tanggal_rapat->translatedFormat('l, d F Y') }}</td>
      <td style="width:60px; font-weight:600;">Waktu</td>
      <td style="width:12px; text-align:center;">:</td>
      <td>{{ $rapat->jam_mulai }} WIB @if($rapat->jam_selesai) s.d. {{ $rapat->jam_selesai }} WIB @else s.d. Selesai @endif</td>
    </tr>
    <tr>
      <td style="font-weight:600;">Tempat</td>
      <td style="text-align:center;">:</td>
      <td>{{ $rapat->tempat }}</td>
      <td style="font-weight:600;">Pimpinan</td>
      <td style="text-align:center;">:</td>
      <td>{{ $rapat->pimpinan_display }}</td>
    </tr>
  </table>

  {{-- Tabel Presensi Peserta --}}
  <table class="table-presensi">
    <thead>
      <tr>
        <th style="width:35px;">No</th>
        <th>Nama Lengkap & NIP</th>
        <th style="width:150px;">Jabatan / Tugas</th>
        <th colspan="2" style="width:190px;">Tanda Tangan</th>
        <th style="width:80px;">Ket.</th>
      </tr>
    </thead>
    <tbody>
      @forelse($pesertas as $index => $peserta)
        @php
          $no = $index + 1;
          $isOdd = ($no % 2 !== 0);
        @endphp
        <tr>
          <td style="text-align:center; font-weight:600;">{{ $no }}</td>
          <td>
            <div style="font-weight:700; color:#0f172a;">{{ $peserta->nama }}</div>
            <div style="font-size:8.5pt; color:#475569;">NIP: {{ $peserta->nip ?? '-' }}</div>
          </td>
          <td style="font-size:9pt;">{{ $peserta->jabatan ?? 'Guru / Staf' }}</td>
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
          <td style="text-align:center; font-size:9pt;"></td>
        </tr>
      @empty
        {{-- Baris Kosong Default 15 Baris Jika Peserta Kosong --}}
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
  <table style="width:100%; border-collapse:collapse; font-size:10.5pt; margin-top:15px;">
    <tr>
      <td style="width:50%; text-align:center;">
        <div>Mengetahui / Memimpin,</div>
        <div style="font-weight:600;">{{ $rapat->pimpinan_jabatan ?? 'Pimpinan Rapat' }}</div>

        {{-- Digital QR Mode --}}
        <div class="ttd-digital-only" style="margin:6px auto; width:64px; height:64px;">
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode('VALID-PRESENSI-PIMPINAN-' . $rapat->id) }}" alt="QR Keabsahan" style="width:64px; height:64px; display:block; margin:0 auto;" />
        </div>
        <div class="ttd-basah-only ttd-space" style="height:55px; display:none;"></div>

        <div style="font-weight:700; text-decoration:underline;">{{ $rapat->pimpinan_display }}</div>
        <div style="font-size:9.5pt;">NIP. {{ $rapat->pimpinan->nip ?? '-' }}</div>
      </td>
      <td style="width:50%; text-align:center;">
        <div>Air Naningan, {{ $rapat->tanggal_rapat->translatedFormat('d F Y') }}</div>
        <div style="font-weight:600;">Notulis Rapat,</div>

        {{-- Digital QR Mode --}}
        <div class="ttd-digital-only" style="margin:6px auto; width:64px; height:64px;">
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode('VALID-PRESENSI-NOTULIS-' . $rapat->id) }}" alt="QR Keabsahan" style="width:64px; height:64px; display:block; margin:0 auto;" />
        </div>
        <div class="ttd-basah-only ttd-space" style="height:55px; display:none;"></div>

        <div style="font-weight:700; text-decoration:underline;">{{ $rapat->notulis_display }}</div>
        <div style="font-size:9.5pt;">NIP. {{ $rapat->notulis->nip ?? '-' }}</div>
      </td>
    </tr>
  </table>
@endsection
