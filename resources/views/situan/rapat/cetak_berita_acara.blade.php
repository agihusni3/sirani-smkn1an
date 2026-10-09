@extends('layouts.dokumen_a4')

@php
  $backUrl = route('situan.rapat.show', $rapat->id);
  $backLabel = 'Kembali ke Rapat';
  $tgl = $rapat->tanggal_rapat;
  $tahunAngka = $tgl->year;
  $bulanText = $tgl->translatedFormat('F');
  $hariText = $tgl->translatedFormat('l');
  $tglAngka = $tgl->day;
@endphp

@section('title', 'Berita Acara - ' . $rapat->judul_rapat)
@section('toolbar_title', 'Berita Acara Rapat Pleno Kedinasan')

@push('styles')
<style>
  .ba-body {
    font-size: 11pt;
    line-height: 1.6;
    text-align: justify;
  }
  .ba-body p {
    text-indent: 36px;
    margin-bottom: 12px;
  }
  .ba-poin-box {
    margin: 12px 0 16px 20px;
    font-size: 11pt;
  }
</style>
@endpush

@section('content')
  <div style="text-align:center; margin-bottom:18px;">
    <h3 style="margin:0; font-size:13pt; font-weight:800; text-transform:uppercase; text-decoration:underline; letter-spacing:1px;">
      BERITA ACARA
    </h3>
    <div style="font-size:10.5pt; font-weight:700; margin-top:3px; color:#1e293b;">
      PELAKSANAAN {{ strtoupper($rapat->judul_rapat) }}
    </div>
    <div style="font-size:10pt; font-family:monospace; margin-top:2px;">
      Nomor: 421.5/BA-{{ str_pad($rapat->id, 3, '0', STR_PAD_LEFT) }}/V.01/DP.2/{{ $tahunAngka }}
    </div>
  </div>

  <div class="ba-body">
    <p>
      Pada hari ini <strong>{{ $hariText }}</strong>, tanggal <strong>{{ $tglAngka }}</strong> bulan <strong>{{ $bulanText }}</strong> tahun <strong>{{ $tahunAngka }}</strong>, bertempat di <strong>{{ $rapat->tempat }}</strong> SMK Negeri 1 Air Naningan, telah diselenggarakan rapat dinas resmi dengan agenda pokok mengenai:
    </p>

    <div style="text-align:center; font-weight:700; font-size:11.5pt; margin:8px 0 14px 0; padding:6px; background:#f8fafc; border:1px solid #cbd5e1; border-radius:6px;">
      "{{ $rapat->judul_rapat }}"
    </div>

    <p>
      Rapat tersebut dipimpin langsung oleh <strong>{{ $rapat->pimpinan_display }}</strong> selaku {{ $rapat->pimpinan_jabatan ?? 'Pimpinan Rapat' }} serta dihadiri oleh Dewan Guru dan Tenaga Kependidikan SMK Negeri 1 Air Naningan sebanyak <strong>{{ $rapat->jumlah_hadir ?? count($pesertas) }}</strong> orang <em>(daftar hadir terlampir sebagai bagian yang tidak terpisahkan dari berita acara ini)</em>.
    </p>

    <p>
      Setelah mendengar pemaparan, pengarahan pimpinan, serta mempertimbangkan saran, masukan, dan hasil musyawarah mufakat seluruh peserta rapat, maka dengan ini <strong>DITETAPKAN DAN DISEPAKATI</strong> hal-hal sebagai berikut:
    </p>

    <div class="ba-poin-box">
      @if($rapat->hasil_keputusan)
        <div style="white-space: pre-line; line-height:1.6; font-weight:500;">{{ $rapat->hasil_keputusan }}</div>
      @else
        <ol style="margin-left: 20px; padding-left: 0;">
          <li>Menerima dan menyetujui seluruh rangkaian materi pembahasan rapat dinas secara bulat dan mufakat.</li>
          <li>Menetapkan keputusan rapat sebagai pedoman resmi operasional kedinasan di SMK Negeri 1 Air Naningan.</li>
          <li>Menginstruksikan kepada seluruh unsur terkait untuk menindaklanjuti hasil keputusan sesuai dengan tanggung jawab masing-masing.</li>
        </ol>
      @endif
    </div>

    <p>
      Demikian Berita Acara ini dibuat dan ditandatangani oleh pimpinan rapat serta perwakilan peserta rapat pada hari dan tanggal sebagaimana tersebut di atas, dengan penuh tanggung jawab untuk dipergunakan sebagaimana mestinya.
    </p>
  </div>
@endsection

@section('ttd')
  <table style="width:100%; border-collapse:collapse; font-size:10.5pt; margin-top:20px;">
    <tr>
      <td style="width:50%; text-align:center;">
        <div>Notulis / Saksi,</div>
        <div style="font-weight:600;">Guru / Staf Pelaksana</div>

        <div class="ttd-digital-only" style="margin:6px auto; width:64px; height:64px;">
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode('VALID-BA-SAKSI-' . $rapat->id) }}" alt="QR Keabsahan" style="width:64px; height:64px; display:block; margin:0 auto;" />
        </div>
        <div class="ttd-basah-only ttd-space" style="height:55px; display:none;"></div>

        <div style="font-weight:700; text-decoration:underline;">{{ $rapat->notulis_display }}</div>
        <div style="font-size:9.5pt;">NIP. {{ $rapat->notulis->nip ?? '-' }}</div>
      </td>
      <td style="width:50%; text-align:center;">
        <div>Air Naningan, {{ $tgl->translatedFormat('d F Y') }}</div>
        <div style="font-weight:600;">{{ $rapat->pimpinan_jabatan ?? 'Pimpinan Rapat' }},</div>

        <div class="ttd-digital-only" style="margin:6px auto; width:64px; height:64px;">
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode('VALID-BA-PIMPINAN-' . $rapat->id) }}" alt="QR Keabsahan" style="width:64px; height:64px; display:block; margin:0 auto;" />
        </div>
        <div class="ttd-basah-only ttd-space" style="height:55px; display:none;"></div>

        <div style="font-weight:700; text-decoration:underline;">{{ $rapat->pimpinan_display }}</div>
        <div style="font-size:9.5pt;">NIP. {{ $rapat->pimpinan->nip ?? '-' }}</div>
      </td>
    </tr>
  </table>
@endsection
