@extends('layouts.dokumen_a4')

@php
  $backUrl = route('situan.radar-kgb.index');
  $backLabel = 'Kembali ke Radar KGB';
@endphp

@section('title', 'Surat Pengantar KGB — ' . $guru->nama)
@section('toolbar_title', 'Surat Pengantar Usul KGB — ' . $guru->nama)

@push('styles')
<style>
  .header-surat {
    display: flex;
    justify-content: space-between;
    margin-bottom: 14px;
    font-size: 11pt;
    line-height: 1.4;
  }
  .meta-table td {
    padding: 1.5px 4px;
    vertical-align: top;
  }
  .tujuan-surat {
    width: 48%;
    text-align: left;
  }

  .surat-body {
    text-align: justify;
    font-size: 11pt;
    line-height: 1.45;
  }
  .surat-body p {
    margin-bottom: 8px;
    text-indent: 28px;
  }

  .bio-table {
    width: 100%;
    margin: 6px 0 10px 16px;
    border-collapse: collapse;
    font-size: 10.5pt;
  }
  .bio-table td {
    padding: 2px 4px;
    vertical-align: top;
  }
  .bio-label {
    width: 210px;
  }
  .bio-sep {
    width: 15px;
    text-align: center;
  }
  .bio-val {
    font-weight: 600;
  }

  .lampiran-box {
    margin: 4px 0 12px 34px;
    font-size: 10.5pt;
  }
  .lampiran-box ol {
    padding-left: 16px;
    margin: 0;
  }
  .lampiran-box li {
    margin-bottom: 2px;
  }

  .signature-container {
    display: flex;
    justify-content: flex-end;
    margin-top: 16px;
  }
  .signature-box {
    width: 260px;
    text-align: center;
    font-size: 11pt;
  }
  .sign-space {
    height: 55px;
  }
  .sign-name {
    font-weight: 700;
    text-decoration: underline;
  }
</style>
@endpush

@section('content')
  {{-- HEADER SURAT KELUAR DINAS --}}
  <div class="header-surat">
    <div>
      <table class="meta-table">
        <tr>
          <td>Nomor</td>
          <td>:</td>
          <td><strong>{{ $generator['nomor_surat_lengkap'] }}</strong></td>
        </tr>
        <tr>
          <td>Lampiran</td>
          <td>:</td>
          <td>1 (Satu) Berkas Usulan</td>
        </tr>
        <tr>
          <td>Perihal</td>
          <td>:</td>
          <td><strong>Usul Kenaikan Gaji Berkala (KGB)</strong></td>
        </tr>
      </table>
    </div>

    <div class="tujuan-surat">
      Air Naningan, {{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}<br><br>
      Kepada Yth.<br>
      <strong>Kepala Dinas Pendidikan dan Kebudayaan Provinsi Lampung</strong><br>
      c.q. Kepala Cabang Dinas Wilayah II<br>
      di -<br>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>Tempat</u>
    </div>
  </div>

  {{-- ISI SURAT PENGANTAR --}}
  <div class="surat-body">
    <p>
      Dengan hormat, bersama ini kami sampaikan usulan Kenaikan Gaji Berkala (KGB) bagi Pegawai Negeri Sipil / Pegawai Pemerintah dengan Perjanjian Kerja (PPPK) di lingkungan {{ $sekolah->nama_sekolah ?: 'SMK Negeri 1 Air Naningan' }} atas nama:
    </p>

    <table class="bio-table">
      <tr>
        <td class="bio-label">Nama Pegawai</td>
        <td class="bio-sep">:</td>
        <td class="bio-val" style="text-transform:uppercase;">{{ $guru->nama }}</td>
      </tr>
      <tr>
        <td class="bio-label">NIP / NIK</td>
        <td class="bio-sep">:</td>
        <td class="bio-val">{{ $guru->nip ?: ($guru->nik ?: '-') }}</td>
      </tr>
      <tr>
        <td class="bio-label">NUPTK</td>
        <td class="bio-sep">:</td>
        <td class="bio-val">{{ $guru->nuptk ?: '-' }}</td>
      </tr>
      <tr>
        <td class="bio-label">Pangkat / Golongan Ruang</td>
        <td class="bio-sep">:</td>
        <td class="bio-val">{{ $guru->golongan_ruang ?: 'Penata Muda, III/a' }}</td>
      </tr>
      <tr>
        <td class="bio-label">Jabatan / Tugas</td>
        <td class="bio-sep">:</td>
        <td class="bio-val">{{ $guru->jenis_ptk ?: 'Guru Mata Pelajaran' }}</td>
      </tr>
      <tr>
        <td class="bio-label">TMT KGB Terakhir</td>
        <td class="bio-sep">:</td>
        <td class="bio-val">
          {{ $guru->tmt_kgb_terakhir ? \Carbon\Carbon::parse($guru->tmt_kgb_terakhir)->translatedFormat('d F Y') : '-' }}
        </td>
      </tr>
      <tr>
        <td class="bio-label">TMT KGB Baru (Diusulkan)</td>
        <td class="bio-sep">:</td>
        <td class="bio-val">
          {{ $guru->tmt_kgb_terakhir ? \Carbon\Carbon::parse($guru->tmt_kgb_terakhir)->addYears(2)->translatedFormat('d F Y') : \Carbon\Carbon::today()->translatedFormat('d F Y') }}
        </td>
      </tr>
      <tr>
        <td class="bio-label">Unit Kerja</td>
        <td class="bio-sep">:</td>
        <td class="bio-val">{{ $sekolah->nama_sekolah ?: 'SMK Negeri 1 Air Naningan' }}</td>
      </tr>
    </table>

    <p>
      Sebagai bahan pertimbangan dan kelengkapan berkas pengusulan, bersama ini kami lampirkan:
    </p>

    <div class="lampiran-box">
      <ol>
        <li>Fotokopi Surat Keputusan (SK) Kenaikan Pangkat Terakhir yang telah dilegalisir;</li>
        <li>Fotokopi Surat Keputusan (SK) Kenaikan Gaji Berkala (KGB) Terakhir;</li>
        <li>Fotokopi Penilaian Prestasi Kerja Pegawai / Sasaran Kinerja Pegawai (SKP) tahun terakhir;</li>
        <li>Surat Pernyataan Melaksanakan Tugas (SPMT) dari Kepala Sekolah;</li>
        <li>Daftar Riwayat Kepangkatan dan Gaji.</li>
      </ol>
    </div>

    <p>
      Demikian permohonan ini kami sampaikan, atas perhatian dan perkenan Bapak Kepala Dinas, kami ucapkan terima kasih.
    </p>
  </div>
@endsection

@section('ttd')
  {{-- TANDA TANGAN KEPALA SEKOLAH --}}
  <div class="signature-container">
    <div class="signature-box">
      <div>Kepala Sekolah,</div>
      <div class="sign-space"></div>
      <div class="sign-name">{{ $sekolah->nama_kepala_sekolah ?: 'Aprida, S.Si.' }}</div>
      <div>NIP. {{ $sekolah->nip_kepala_sekolah ?: '197904172008012019' }}</div>
    </div>
  </div>
@endsection

@section('footer')
  <div style="display:flex; justify-content:space-between; align-items:center; font-size:8pt; color:#64748b; font-family:'Plus Jakarta Sans', sans-serif;">
    <span>Dokumen Resmi Kepegawaian &amp; Tata Usaha · <strong>SITUAN (Sistem Informasi Tata Usaha &amp; Administrasi Terpadu) SMKN 1 Air Naningan</strong></span>
    <span>Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</span>
  </div>
@endsection

