@extends('layouts.dokumen_a4')

@php
  $backUrl = route('panitia-asesmen.administrasi');
  $backLabel = 'Kembali ke Administrasi Ujian';
@endphp

@section('title', 'Berita Acara Ujian - ' . ($periode->nama_event ?? 'CBT'))
@section('toolbar_title', 'Berita Acara Pelaksanaan Ujian Ruang Resmi (A4)')

@push('styles')
<style>
  .ba-body {
    font-size: 10.5pt;
    line-height: 1.6;
    text-align: justify;
  }
  .ba-body p {
    text-indent: 32px;
    margin-bottom: 10px;
  }
  .ba-table {
    width: 100%;
    border-collapse: collapse;
    margin: 8px 0 12px 20px;
    font-size: 10.5pt;
  }
  .ba-table td {
    vertical-align: top;
    padding: 2.5px 0;
  }
</style>
@endpush

@section('content')
  <div style="text-align:center; margin-bottom:14px;">
    <h3 style="margin:0; font-size:12.5pt; font-weight:800; text-transform:uppercase; text-decoration:underline; letter-spacing:0.5px;">
      BERITA ACARA PELAKSANAAN
    </h3>
    <div style="font-size:11pt; font-weight:700; margin-top:2px; color:#1e293b;">
      ASESMEN BERBASIS KOMPUTER (CBT) {{ strtoupper($periode->nama_event ?? 'SEKOLAH') }}
    </div>
  </div>

  <div class="ba-body">
    <p>
      Pada hari ini <strong>{{ date('l') }}</strong>, tanggal <strong>{{ date('d') }}</strong> bulan <strong>{{ date('F') }}</strong> tahun <strong>{{ date('Y') }}</strong>, di SMK Negeri 1 Air Naningan, telah diselenggarakan Asesmen Berbasis Komputer (CBT) dengan rincian pelaksanaan sebagai berikut:
    </p>

    <table class="ba-table">
      <tr>
        <td style="width:160px; font-weight:600;">Mata Pelajaran</td>
        <td style="width:15px; text-align:center;">:</td>
        <td><strong>{{ $mapelNama }}</strong></td>
      </tr>
      <tr>
        <td style="font-weight:600;">Ruang Ujian</td>
        <td style="text-align:center;">:</td>
        <td>{{ $ruang }}</td>
      </tr>
      <tr>
        <td style="font-weight:600;">Sesi Ujian</td>
        <td style="text-align:center;">:</td>
        <td>{{ $sesi }}</td>
      </tr>
      <tr>
        <td style="font-weight:600;">Waktu Pengerjaan</td>
        <td style="text-align:center;">:</td>
        <td>Pukul ............. s/d ............. WIB</td>
      </tr>
      <tr>
        <td style="font-weight:600;">Jumlah Peserta Seharusnya</td>
        <td style="text-align:center;">:</td>
        <td>............. orang</td>
      </tr>
      <tr>
        <td style="font-weight:600;">Jumlah Peserta Hadir</td>
        <td style="text-align:center;">:</td>
        <td>............. orang</td>
      </tr>
      <tr>
        <td style="font-weight:600;">Jumlah Tidak Hadir</td>
        <td style="text-align:center;">:</td>
        <td>............. orang</td>
      </tr>
    </table>

    <div style="margin-top:10px; font-weight:600;">
      Peserta yang Tidak Hadir (Nama / No. Peserta / Alasan):
    </div>
    <div style="border:1px solid #cbd5e1; border-radius:4px; padding:10px; min-height:45px; margin-top:4px; font-size:9.5pt; color:#475569;">
      1. ..........................................................................................................................................................<br>
      2. ..........................................................................................................................................................
    </div>

    <div style="margin-top:10px; font-weight:600;">
      Catatan / Kejadian Khusus Selama Ujian Berlangsung:
    </div>
    <div style="border:1px solid #cbd5e1; border-radius:4px; padding:10px; min-height:45px; margin-top:4px; font-size:9.5pt; color:#475569;">
      Pelaksanaan ujian berlangsung dengan tertib, aman, dan lancar. Server CBT dan koneksi jaringan berfungsi normal tanpa kendala yang berarti.
    </div>

    <p style="margin-top:12px;">
      Demikian Berita Acara ini dibuat dengan sebenarnya dan ditandatangani oleh Pengawas dan Proktor ruang ujian untuk dipergunakan sebagaimana mestinya.
    </p>
  </div>
@endsection

@section('ttd')
  <table style="width:100%; border-collapse:collapse; font-size:10pt; margin-top:15px;">
    <tr>
      <td style="width:50%; text-align:center;">
        <div>Mengetahui,</div>
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
