@extends('layouts.dokumen_a4')

@php
  $tglSurat = \Carbon\Carbon::parse($suratTugas->tanggal_mulai);
  $backUrl = route('situan.surat-tugas.index');
  $backLabel = 'Kembali ke Daftar SPT & SPPD';
  $anggotas = $suratTugas->anggotas;
  $isMulti = $anggotas->count() > 1;
@endphp

@section('title', 'Surat Tugas — ' . $suratTugas->nomor_surat_tugas)
@section('toolbar_title', 'Surat Perintah Tugas (SPT) Resmi')

@push('styles')
<style>
  .title-block {
    text-align: center;
    margin-bottom: 12px;
  }
  .surat-title {
    font-size: 13pt;
    font-weight: 700;
    text-transform: uppercase;
    text-decoration: underline;
    letter-spacing: 0.5px;
  }
  .surat-nomor {
    font-size: 11pt;
    margin-top: 3px;
  }

  .surat-body {
    text-align: justify;
    font-size: 10.5pt;
    line-height: 1.45;
  }
  
  .dasar-table {
    width: 100%;
    margin-bottom: 8px;
    border-collapse: collapse;
  }
  .dasar-table td {
    vertical-align: top;
    padding: 2px 0;
  }
  .dasar-label {
    width: 75px;
    font-weight: 700;
  }
  .dasar-sep {
    width: 15px;
    text-align: center;
  }

  .order-block {
    text-align: center;
    font-size: 11pt;
    font-weight: 700;
    letter-spacing: 2px;
    margin: 10px 0 8px 0;
    text-transform: uppercase;
  }

  .kepada-table {
    width: 100%;
    margin-bottom: 10px;
    border-collapse: collapse;
  }
  .kepada-table td {
    vertical-align: top;
    padding: 2px 0;
  }
  .kepada-label {
    width: 75px;
    font-weight: 700;
  }

  .person-table {
    width: 100%;
    border-collapse: collapse;
    margin: 4px 0 10px 0;
    font-size: 10pt;
  }
  .person-table th, .person-table td {
    border: 1px solid #000000;
    padding: 4px 6px;
  }
  .person-table th {
    background-color: #f1f5f9;
    text-align: center;
    font-weight: 700;
    font-size: 9.5pt;
  }

  .untuk-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 4px;
  }
  .untuk-table td {
    vertical-align: top;
    padding: 2.5px 0;
  }
  .untuk-num {
    width: 25px;
    text-align: left;
  }

  .signature-container {
    display: flex;
    justify-content: flex-end;
    margin-top: 20px;
    page-break-inside: avoid;
  }
  .signature-box {
    width: 280px;
    text-align: center;
    font-size: 10.5pt;
  }
  .sign-qr-wrap {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 4px auto 6px auto;
    width: 76px;
    height: 76px;
  }
  .sign-qr-wrap img {
    width: 76px;
    height: 76px;
    display: block;
  }
  .sign-name {
    font-weight: 700;
    text-decoration: underline;
    font-size: 10.5pt;
  }
</style>
@endpush

@section('content')

  {{-- JUDUL & NOMOR SURAT --}}
  <div class="title-block">
    <div class="surat-title">SURAT PERINTAH TUGAS</div>
    <div class="surat-nomor">Nomor : {{ $suratTugas->nomor_surat_tugas }}</div>
  </div>

  <div class="surat-body">

    {{-- DASAR PENUGASAN --}}
    <table class="dasar-table">
      <tr>
        <td class="dasar-label">Dasar</td>
        <td class="dasar-sep">:</td>
        <td>{{ $suratTugas->dasar_penugasan ?: 'Program Kerja dan Agenda Kedinasan SMK Negeri 1 Air Naningan Tahun Ajaran ' . date('Y') . '/' . (date('Y')+1) }}</td>
      </tr>
    </table>

    {{-- MEMERINTAHKAN --}}
    <div class="order-block">MEMERINTAHKAN :</div>

    {{-- KEPADA --}}
    <table class="kepada-table">
      <tr>
        <td class="kepada-label">Kepada</td>
        <td class="dasar-sep">:</td>
        <td>
          @if(!$isMulti && $anggotas->count() === 1)
            {{-- Format Single Personil --}}
            @php $p = $anggotas->first(); @endphp
            <table style="width:100%; border-collapse:collapse;">
              <tr>
                <td style="width:170px;">Nama</td>
                <td style="width:15px; text-align:center;">:</td>
                <td style="font-weight:700;">{{ $p->nama }}</td>
              </tr>
              <tr>
                <td>NIP / NUPTK</td>
                <td style="text-align:center;">:</td>
                <td>{{ $p->nip ?: '-' }}</td>
              </tr>
              <tr>
                <td>Pangkat / Golongan</td>
                <td style="text-align:center;">:</td>
                <td>{{ $p->pangkat_golongan ?: '-' }}</td>
              </tr>
              <tr>
                <td>Jabatan</td>
                <td style="text-align:center;">:</td>
                <td>{{ $p->jabatan ?: 'Guru' }}</td>
              </tr>
            </table>
          @else
            {{-- Format Multi-Personil (Tabel Tim) --}}
            <span>Daftar Pegawai / Guru yang namanya tercantum dalam tabel berikut:</span>
            <table class="person-table" style="margin-top:6px;">
              <thead>
                <tr>
                  <th style="width:28px;">No</th>
                  <th>Nama Lengkap &amp; NIP</th>
                  <th style="width:130px;">Pangkat / Golongan</th>
                  <th style="width:140px;">Jabatan</th>
                  <th style="width:110px;">Kedudukan</th>
                </tr>
              </thead>
              <tbody>
                @foreach($anggotas as $idx => $item)
                  <tr>
                    <td style="text-align:center; font-weight:700;">{{ $idx + 1 }}</td>
                    <td>
                      <div style="font-weight:700;">{{ $item->nama }}</div>
                      <div style="font-size:9pt; color:#333;">NIP: {{ $item->nip ?: '-' }}</div>
                    </td>
                    <td>{{ $item->pangkat_golongan ?: '-' }}</td>
                    <td>{{ $item->jabatan ?: 'Guru' }}</td>
                    <td style="text-align:center; font-weight:600;">{{ $item->peran }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </td>
      </tr>
    </table>

    {{-- UNTUK --}}
    <table class="kepada-table" style="margin-top:4px;">
      <tr>
        <td class="kepada-label">Untuk</td>
        <td class="dasar-sep">:</td>
        <td>
          <table class="untuk-table">
            <tr>
              <td class="untuk-num">1.</td>
              <td>Melaksanakan tugas kedinasan dalam rangka: <strong>{{ $suratTugas->maksud_tugas }}</strong>.</td>
            </tr>
            <tr>
              <td class="untuk-num">2.</td>
              <td>
                Tempat / Tujuan: <strong>{{ $suratTugas->tempat_tujuan }}</strong>
                @if($suratTugas->lokasi_spesifik)
                  <span>({{ $suratTugas->lokasi_spesifik }})</span>
                @endif.
              </td>
            </tr>
            <tr>
              <td class="untuk-num">3.</td>
              <td>
                Waktu Pelaksanaan: 
                @if($suratTugas->tanggal_mulai->eq($suratTugas->tanggal_selesai))
                  <strong>{{ $suratTugas->tanggal_mulai->translatedFormat('d F Y') }}</strong> ({{ $suratTugas->lama_hari }} hari).
                @else
                  <strong>{{ $suratTugas->tanggal_mulai->translatedFormat('d F Y') }} s.d. {{ $suratTugas->tanggal_selesai->translatedFormat('d F Y') }}</strong> ({{ $suratTugas->lama_hari }} hari).
                @endif
              </td>
            </tr>
            @if($suratTugas->alat_transportasi)
              <tr>
                <td class="untuk-num">4.</td>
                <td>Menggunakan sarana transportasi: {{ $suratTugas->alat_transportasi }}.</td>
              </tr>
            @endif
            <tr>
              <td class="untuk-num">{{ $suratTugas->alat_transportasi ? '5.' : '4.' }}</td>
              <td>Biaya yang timbul dibebankan pada: <strong>{{ $suratTugas->sumber_anggaran ?: 'BOS Reguler SMK Negeri 1 Air Naningan' }}</strong>.</td>
            </tr>
            <tr>
              <td class="untuk-num">{{ $suratTugas->alat_transportasi ? '6.' : '5.' }}</td>
              <td>Melaksanakan tugas ini dengan penuh tanggung jawab dan menyampaikan laporan hasil pelaksanaan tugas dinas kepada Kepala Sekolah.</td>
            </tr>
          </table>
        </td>
      </tr>
    </table>

  </div>

  {{-- AREA TANDA TANGAN KEPALA SEKOLAH --}}
  <div class="signature-container">
    <div class="signature-box">
      <div>Ditetapkan di : Air Naningan</div>
      <div>Pada tanggal : {{ $tglSurat->translatedFormat('d F Y') }}</div>
      <div style="font-weight:700; margin-top:4px; margin-bottom:4px;">
        Kepala SMK Negeri 1 Air Naningan,
      </div>

      {{-- MODE 1: DIGITAL TTE (QR CODE) --}}
      <div class="ttd-digital-only">
        <div class="sign-qr-wrap">
          @if($qrImage)
            <img src="{{ $qrImage }}" alt="QR Code Keabsahan Surat Tugas">
          @else
            <div style="width:76px; height:76px; border:1px dashed #999; display:flex; align-items:center; justify-content:center; font-size:9px;">QR TTE</div>
          @endif
        </div>
      </div>

      {{-- MODE 2: BASAH / MANUAL --}}
      <div class="ttd-basah-only" style="height:65px;"></div>

      <div class="sign-name">{{ $namaKepsek }}</div>
      <div style="font-size:9.5pt;">{{ $pangkatKepsek }}</div>
      <div style="font-size:9.5pt;">NIP. {{ $nipKepsek }}</div>
    </div>
  </div>

@endsection

@section('footer')
  <div style="display:flex; justify-content:space-between; align-items:center; font-size:8pt; color:#475569;">
    <div>
      <i class="bi bi-shield-check me-1"></i> Keabsahan dokumen ini dapat diverifikasi publik melalui scan QR Code atau tautan:
      <span style="font-family:monospace;">{{ $verifyUrl }}</span>
    </div>
    <div>SITUAN SMKN 1 AN</div>
  </div>
@endsection
