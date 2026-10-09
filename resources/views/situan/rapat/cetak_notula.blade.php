@extends('layouts.dokumen_a4')

@php
  $backUrl = route('situan.rapat.show', $rapat->id);
  $backLabel = 'Kembali ke Rapat';
@endphp

@section('title', 'Notula Rapat - ' . $rapat->judul_rapat)
@section('toolbar_title', 'Notula Rapat Kedinasan Resmi')

@push('styles')
<style>
  .notula-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 12px;
    font-size: 10.5pt;
  }
  .notula-table td {
    vertical-align: top;
    padding: 3px 0;
  }
  .notula-label {
    width: 155px;
    font-weight: 600;
  }
  .notula-sep {
    width: 15px;
    text-align: center;
  }
  .section-title {
    font-size: 11pt;
    font-weight: 700;
    text-transform: uppercase;
    margin-top: 14px;
    margin-bottom: 6px;
    border-bottom: 1.5px solid #0f172a;
    padding-bottom: 2px;
  }
  .section-content {
    font-size: 10.5pt;
    line-height: 1.6;
    text-align: justify;
    margin-bottom: 10px;
  }
</style>
@endpush

@section('content')
  <div style="text-align:center; margin-bottom:14px;">
    <h3 style="margin:0; font-size:13pt; font-weight:800; text-transform:uppercase; text-decoration:underline; letter-spacing:0.5px;">
      NOTULA RAPAT DINAS
    </h3>
    <div style="font-size:10.5pt; font-weight:700; margin-top:3px; color:#1e293b;">
      {{ $rapat->judul_rapat }}
    </div>
    <div style="font-size:9.5pt; color:#475569; margin-top:1px;">
      Nomor Agenda: {{ $rapat->nomor_surat ?? '-' }}
    </div>
  </div>

  {{-- Tabel Identitas Penyelenggaraan --}}
  <table class="notula-table">
    <tr>
      <td class="notula-label">Hari / Tanggal</td>
      <td class="notula-sep">:</td>
      <td><strong>{{ $rapat->tanggal_rapat->translatedFormat('l, d F Y') }}</strong></td>
    </tr>
    <tr>
      <td class="notula-label">Waktu Rapat</td>
      <td class="notula-sep">:</td>
      <td>Pukul {{ $rapat->jam_mulai }} WIB @if($rapat->jam_selesai) s.d. {{ $rapat->jam_selesai }} WIB @else s.d. Selesai @endif</td>
    </tr>
    <tr>
      <td class="notula-label">Tempat Rapat</td>
      <td class="notula-sep">:</td>
      <td>{{ $rapat->tempat }}</td>
    </tr>
    <tr>
      <td class="notula-label">Pimpinan Rapat</td>
      <td class="notula-sep">:</td>
      <td>{{ $rapat->pimpinan_display }} ({{ $rapat->pimpinan_jabatan ?? 'Pimpinan' }})</td>
    </tr>
    <tr>
      <td class="notula-label">Notulis Rapat</td>
      <td class="notula-sep">:</td>
      <td>{{ $rapat->notulis_display }}</td>
    </tr>
    <tr>
      <td class="notula-label">Kehadiran Peserta</td>
      <td class="notula-sep">:</td>
      <td>
        Hadir: <strong>{{ $rapat->jumlah_hadir ?? '-' }}</strong> orang &nbsp;|&nbsp; 
        Tidak Hadir: <strong>{{ $rapat->jumlah_tidak_hadir ?? 0 }}</strong> orang &nbsp;
        <em>(Daftar hadir terlampir)</em>
      </td>
    </tr>
    <tr>
      <td class="notula-label">Agenda / Acara</td>
      <td class="notula-sep">:</td>
      <td>{{ $rapat->agenda ?: $rapat->judul_rapat }}</td>
    </tr>
  </table>

  {{-- I. Jalannya Acara & Risalah Rapat --}}
  <div class="section-title">I. Jalannya Rapat & Risalah Pembahasan</div>
  <div class="section-content">
    @if($rapat->jalannya_acara)
      {!! nl2br(e($rapat->jalannya_acara)) !!}
    @else
      <ol style="margin-left: 20px; padding-left: 0; margin-top: 4px; margin-bottom: 4px;">
        <li>Pembukaan oleh pembawa acara / notulis pada pukul {{ $rapat->jam_mulai }} WIB dengan membaca basmalah / doa bersama.</li>
        <li>Pengarahan dan pemaparan materi inti rapat oleh Pimpinan Rapat ({{ $rapat->pimpinan_display }}).</li>
        <li>Sesi tanggapan, diskusi, dan saran masukan dari seluruh peserta forum rapat dinas.</li>
        <li>Perumusan kesepakatan bersama dan penutupan rapat.</li>
      </ol>
    @endif
  </div>

  {{-- II. Hasil Keputusan Rapat --}}
  <div class="section-title">II. Hasil Keputusan & Kesimpulan Rapat</div>
  <div class="section-content">
    @if($rapat->hasil_keputusan)
      {!! nl2br(e($rapat->hasil_keputusan)) !!}
    @else
      <div style="font-style: italic; color:#64748b; padding: 6px 0;">
        (Hasil keputusan dan kesimpulan rapat belum diinputkan pada sistem SITUAN).
      </div>
    @endif
  </div>

  {{-- III. Tindak Lanjut & Catatan --}}
  @if($rapat->tindak_lanjut || $rapat->catatan_khusus)
  <div class="section-title">III. Rencana Tindak Lanjut & Catatan</div>
  <div class="section-content">
    @if($rapat->tindak_lanjut)
      <div><strong>Tindak Lanjut:</strong> {!! nl2br(e($rapat->tindak_lanjut)) !!}</div>
    @endif
    @if($rapat->catatan_khusus)
      <div style="margin-top:4px;"><strong>Catatan Khusus:</strong> {!! nl2br(e($rapat->catatan_khusus)) !!}</div>
    @endif
  </div>
  @endif

  <div style="font-size:10pt; font-style:italic; margin-top:8px; color:#475569;">
    Demikian Notula Rapat Dinas ini dibuat dengan sebenarnya untuk diketahui dan dipergunakan sebagaimana mestinya.
  </div>
@endsection

@section('ttd')
  <table style="width:100%; border-collapse:collapse; font-size:10.5pt; margin-top:10px;">
    <tr>
      <td style="width:50%; text-align:center;">
        <div>Mengetahui,</div>
        <div style="font-weight:600;">{{ $rapat->pimpinan_jabatan ?? 'Pimpinan Rapat' }}</div>

        {{-- Digital QR Mode --}}
        <div class="ttd-digital-only" style="margin:6px auto; width:64px; height:64px;">
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode('VALID-NOTULA-PIMPINAN-' . $rapat->id) }}" alt="QR Keabsahan" style="width:64px; height:64px; display:block; margin:0 auto;" />
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
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode('VALID-NOTULA-NOTULIS-' . $rapat->id) }}" alt="QR Keabsahan" style="width:64px; height:64px; display:block; margin:0 auto;" />
        </div>
        <div class="ttd-basah-only ttd-space" style="height:55px; display:none;"></div>

        <div style="font-weight:700; text-decoration:underline;">{{ $rapat->notulis_display }}</div>
        <div style="font-size:9.5pt;">NIP. {{ $rapat->notulis->nip ?? '-' }}</div>
      </td>
    </tr>
  </table>
@endsection
