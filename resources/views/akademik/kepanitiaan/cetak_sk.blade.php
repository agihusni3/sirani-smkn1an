@extends('layouts.dokumen_a4')

@php
  $backUrl = route('akademik.kepanitiaan.show', $periode->id);
  $backLabel = 'Kembali ke Susunan Panitia';
  $tglSk = $periode->sk_tanggal ?? $periode->tanggal_mulai->subDays(3);
@endphp

@section('title', 'SK Kepanitiaan - ' . $periode->nama_event)
@section('toolbar_title', 'Surat Keputusan (SK) Susunan Panitia Asesmen Resmi')

@push('styles')
<style>
  .sk-title {
    text-align: center;
    margin-bottom: 12px;
  }
  .sk-main-title {
    font-size: 12.5pt;
    font-weight: 800;
    text-transform: uppercase;
    text-decoration: underline;
    letter-spacing: 0.5px;
  }
  .sk-nomor {
    font-size: 10.5pt;
    margin-top: 2px;
    font-family: monospace;
  }
  .sk-tentang {
    font-size: 11pt;
    font-weight: 700;
    text-transform: uppercase;
    margin-top: 4px;
    line-height: 1.4;
  }
  .sk-body {
    font-size: 10pt;
    line-height: 1.5;
    text-align: justify;
  }
  .sk-body p {
    text-indent: 30px;
    margin-bottom: 8px;
  }
  .sk-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    margin-bottom: 15px;
    font-size: 9.5pt;
  }
  .sk-table th, .sk-table td {
    border: 1px solid #0f172a;
    padding: 5px 7px;
  }
  .sk-table th {
    background-color: #f1f5f9;
    text-align: center;
    font-weight: 700;
    text-transform: uppercase;
  }
</style>
@endpush

@section('content')
  <div class="sk-title">
    <div class="sk-main-title">SURAT KEPUTUSAN KEPALA SMK NEGERI 1 AIR NANINGAN</div>
    <div class="sk-nomor">Nomor : {{ $periode->sk_nomor }}</div>
    <div class="sk-tentang">
      TENTANG<br>
      PENETAPAN SUSUNAN PANITIA PELAKSANA {{ strtoupper($periode->nama_event) }}<br>
      TAHUN AJARAN {{ strtoupper($periode->tahunAjaran->nama ?? date('Y')) }}
    </div>
  </div>

  <div class="sk-body">
    <table style="width:100%; border-collapse:collapse; margin-bottom:8px;">
      <tr>
        <td style="width:110px; font-weight:700; vertical-align:top;">Menimbang</td>
        <td style="width:15px; text-align:center; vertical-align:top;">:</td>
        <td style="vertical-align:top;">
          Bahwa dalam rangka memperlancar pelaksanaan asesmen pembelajaran serta menjamin mutu dan objektivitas evaluasi hasil belajar siswa, maka dipandang perlu menetapkan Panitia Pelaksana {{ $periode->nama_event }}.
        </td>
      </tr>
      <tr>
        <td style="font-weight:700; vertical-align:top;">Mengingat</td>
        <td style="text-align:center; vertical-align:top;">:</td>
        <td style="vertical-align:top;">
          1. Undang-Undang Nomor 20 Tahun 2003 tentang Sistem Pendidikan Nasional.<br>
          2. Peraturan Menteri Pendidikan, Kebudayaan, Riset, dan Teknologi tentang Standar Penilaian Pendidikan.<br>
          3. Program Kerja dan Kalender Pendidikan SMK Negeri 1 Air Naningan.
        </td>
      </tr>
    </table>

    <div style="text-align:center; font-weight:800; font-size:11pt; margin:8px 0; letter-spacing:1px;">
      MEMUTUSKAN :
    </div>

    <table style="width:100%; border-collapse:collapse; margin-bottom:8px;">
      <tr>
        <td style="width:110px; font-weight:700; vertical-align:top;">Menetapkan</td>
        <td style="width:15px; text-align:center; vertical-align:top;">:</td>
        <td style="vertical-align:top;">
          <strong>PERTAMA:</strong> Menunjuk dan mengangkat personalia yang namanya tercantum dalam lampiran keputusan ini sebagai Panitia Pelaksana {{ $periode->nama_event }}.<br>
          <strong>KEDUA:</strong> Panitia bertugas merencanakan, melaksanakan, mengawasi pengerjaan CBT, serta melaporkan hasil evaluasi asesmen kepada Kepala Sekolah.<br>
          <strong>KETIGA:</strong> Keputusan ini berlaku sejak tanggal ditetapkan sampai dengan berakhirnya seluruh rangkaian kegiatan pelaporan hasil evaluasi.
        </td>
      </tr>
    </table>

    <div style="margin-top:10px; font-weight:700; text-decoration:underline;">
      LAMPIRAN: SUSUNAN PERSONALIA PANITIA PELAKSANA
    </div>

    <table class="sk-table">
      <thead>
        <tr>
          <th style="width:30px;">No</th>
          <th>Nama &amp; NIP</th>
          <th style="width:140px;">Jabatan Kedinasan</th>
          <th style="width:140px;">Jabatan Kepanitiaan</th>
          <th>Tugas Khusus / Penempatan</th>
        </tr>
      </thead>
      <tbody>
        @forelse($panitias as $idx => $p)
        <tr>
          <td style="text-align:center; font-weight:600;">{{ $idx + 1 }}</td>
          <td>
            <div style="font-weight:700; color:#0f172a;">{{ $p->guru->nama ?? 'Guru' }}</div>
            <div style="font-size:8.5pt; color:#475569;">NIP: {{ $p->guru->nip ?? '-' }}</div>
          </td>
          <td>{{ $p->guru->jabatan ?? 'Guru Mata Pelajaran' }}</td>
          <td style="font-weight:600;">{{ $p->peran_label }}</td>
          <td style="font-size:9pt;">{{ $p->tugas_khusus ?: '-' }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center py-3 text-muted">Belum ada personil yang dimasukkan.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection

@section('ttd')
  <table style="width:100%; border-collapse:collapse; font-size:10.5pt;">
    <tr>
      <td style="width:55%;"></td>
      <td style="width:45%; text-align:center;">
        <div>Ditetapkan di : Air Naningan</div>
        <div>Pada tanggal : {{ $tglSk->translatedFormat('d F Y') }}</div>
        <div style="margin-top:4px; font-weight:700;">Kepala SMK Negeri 1 Air Naningan,</div>

        {{-- Digital QR Mode --}}
        <div class="ttd-digital-only" style="margin:8px auto; width:68px; height:68px;">
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode('VALID-SK-PANITIA-ASESMEN-' . $periode->sk_nomor) }}" alt="QR Keabsahan SK" style="width:68px; height:68px; display:block; margin:0 auto;" />
        </div>
        <div class="ttd-basah-only ttd-space" style="height:55px; display:none;"></div>

        <div style="font-weight:700; text-decoration:underline;">{{ $kepsek->nama ?? 'Kepala Sekolah' }}</div>
        <div style="font-size:9.5pt;">NIP. {{ $kepsek->nip ?? '-' }}</div>
      </td>
    </tr>
  </table>
@endsection
