@extends('layouts.dokumen_a4')

@php
  $backUrl = route('situan.rapat.show', $rapat->id);
  $backLabel = 'Kembali ke Rapat';
  $tglSurat = $rapat->tanggal_rapat->subDays(2);
@endphp

@section('title', 'Surat Undangan - ' . $rapat->judul_rapat)
@section('toolbar_title', 'Surat Undangan Rapat Kedinasan Resmi')

@section('content')
  {{-- Baris Nomor & Tujuan Surat --}}
  <table style="width:100%; border-collapse:collapse; margin-top:8px; margin-bottom:16px; font-size:11pt; line-height:1.5;">
    <tr>
      <td style="width:58%; vertical-align:top;">
        <table style="width:100%; border-collapse:collapse;">
          <tr>
            <td style="width:85px; vertical-align:top;">Nomor</td>
            <td style="width:12px; vertical-align:top;">:</td>
            <td style="vertical-align:top;"><strong>{{ $rapat->nomor_surat }}</strong></td>
          </tr>
          <tr>
            <td style="vertical-align:top;">Sifat</td>
            <td style="vertical-align:top;">:</td>
            <td style="vertical-align:top;">Penting / Dinas</td>
          </tr>
          <tr>
            <td style="vertical-align:top;">Lampiran</td>
            <td style="vertical-align:top;">:</td>
            <td style="vertical-align:top;">-</td>
          </tr>
          <tr>
            <td style="vertical-align:top;">Perihal</td>
            <td style="vertical-align:top;">:</td>
            <td style="vertical-align:top;"><strong>Undangan {{ $rapat->judul_rapat }}</strong></td>
          </tr>
        </table>
      </td>
      <td style="width:42%; vertical-align:top; padding-left:15px;">
        <div>{{ $sekolah->kecamatan ?? 'Air Naningan' }}, {{ $tglSurat->translatedFormat('d F Y') }}</div>
        <div style="margin-top:10px;">
          Kepada Yth.<br />
          <strong>
            @if($rapat->peserta_tipe == 'guru')
              Bapak/Ibu Dewan Guru
            @elseif($rapat->peserta_tipe == 'tendik')
              Bapak/Ibu Tenaga Kependidikan (TU)
            @else
              Bapak/Ibu Pendidik & Tenaga Kependidikan
            @endif
          </strong><br />
          SMK Negeri 1 Air Naningan<br />
          di —<br />
          &nbsp;&nbsp;&nbsp;&nbsp;Tempat
        </div>
      </td>
    </tr>
  </table>

  {{-- Isi Undangan --}}
  <div style="font-size:11pt; line-height:1.6; text-align:justify; margin-bottom:16px;">
    <p style="text-indent: 36px; margin-bottom:10px;">
      Dengan hormat, sehubungan dengan agenda kerja kedinasan di lingkungan SMK Negeri 1 Air Naningan, mengharap kehadiran Bapak/Ibu pada rapat dinas yang akan diselenggarakan pada:
    </p>

    <table style="width:100%; border-collapse:collapse; margin:10px 0 12px 28px; font-size:11pt; line-height:1.6;">
      <tr>
        <td style="width:120px; font-weight:600;">Hari / Tanggal</td>
        <td style="width:15px; text-align:center;">:</td>
        <td><strong>{{ $rapat->tanggal_rapat->translatedFormat('l, d F Y') }}</strong></td>
      </tr>
      <tr>
        <td style="font-weight:600;">Waktu</td>
        <td style="text-align:center;">:</td>
        <td>Pukul {{ $rapat->jam_mulai }} WIB @if($rapat->jam_selesai) s.d. {{ $rapat->jam_selesai }} WIB @else s.d. Selesai @endif</td>
      </tr>
      <tr>
        <td style="font-weight:600;">Tempat</td>
        <td style="text-align:center;">:</td>
        <td>{{ $rapat->tempat }}</td>
      </tr>
      <tr>
        <td style="font-weight:600; vertical-align:top;">Agenda Rapat</td>
        <td style="text-align:center; vertical-align:top;">:</td>
        <td style="vertical-align:top;">
          <strong>{{ $rapat->judul_rapat }}</strong>
          @if($rapat->agenda)
            <div style="margin-top:4px; font-size:10.5pt; color:#334155;">
              {!! nl2br(e($rapat->agenda)) !!}
            </div>
          @endif
        </td>
      </tr>
      <tr>
        <td style="font-weight:600;">Pimpinan Rapat</td>
        <td style="text-align:center;">:</td>
        <td>{{ $rapat->pimpinan_display }} ({{ $rapat->pimpinan_jabatan ?? 'Pimpinan Rapat' }})</td>
      </tr>
    </table>

    <p style="text-indent: 36px; margin-bottom:10px;">
      Mengingat pentingnya agenda pembahasan ini demi kelancaran manajemen dan mutu pendidikan sekolah, kami mengharapkan kehadiran Bapak/Ibu tepat pada waktunya dan tidak mewakilkan tanpa izin pimpinan.
    </p>
    <p style="text-indent: 36px; margin-bottom:0;">
      Demikian surat undangan ini kami sampaikan, atas perhatian dan kerja sama yang baik diucapkan terima kasih.
    </p>
  </div>
@endsection

@section('ttd')
  <table style="width:100%; border-collapse:collapse; font-size:11pt;">
    <tr>
      <td style="width:55%;"></td>
      <td style="width:45%; text-align:center;">
        <div>Air Naningan, {{ $tglSurat->translatedFormat('d F Y') }}</div>
        <div style="margin-top:2px; font-weight:600;">Kepala SMK Negeri 1 Air Naningan,</div>

        {{-- Digital QR Mode --}}
        <div class="ttd-digital-only" style="margin:8px auto; width:72px; height:72px;">
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode('VALID-UNDANGAN-SMKN1AN-' . $rapat->nomor_surat) }}" alt="QR Keabsahan" style="width:72px; height:72px; display:block; margin:0 auto;" />
        </div>

        {{-- Basah Space --}}
        <div class="ttd-basah-only ttd-space" style="height:60px; display:none;"></div>

        <div style="font-weight:bold; text-decoration:underline;">{{ $kepsek->nama ?? 'Kepala Sekolah' }}</div>
        <div style="font-size:10pt;">NIP. {{ $kepsek->nip ?? '-' }}</div>
      </td>
    </tr>
  </table>
@endsection
