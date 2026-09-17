@extends('layouts.dokumen_a4')

@php
  $tglSurat = \Carbon\Carbon::parse($sppd->tanggal_berangkat);
  $backUrl = route('situan.surat-tugas.index');
  $backLabel = 'Kembali ke Daftar SPT & SPPD';
  $otherAnggotas = $suratTugas->anggotas->where('id', '!=', $sppd->surat_tugas_anggota_id);
@endphp

@section('title', 'SPPD & Visum — ' . $sppd->nomor_sppd)
@section('toolbar_title', 'SPPD Lembar I & II (Visum Lengkap)')

@push('styles')
<style>
  .sppd-page {
    page-break-after: always;
  }
  @media print {
    .sppd-page-break {
      page-break-before: always !important;
      margin-top: 0 !important;
      padding-top: 15mm !important;
    }
  }

  .meta-right {
    margin-left: auto;
    width: 260px;
    font-size: 9pt;
    margin-bottom: 8px;
  }
  .meta-right table {
    width: 100%;
    border-collapse: collapse;
  }
  .meta-right td {
    padding: 1px 0;
  }

  .title-sppd {
    text-align: center;
    font-size: 12pt;
    font-weight: 700;
    text-decoration: underline;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .nomor-sppd {
    text-align: center;
    font-size: 10.5pt;
    margin-top: 2px;
    margin-bottom: 12px;
  }

  /* TABEL LEMBAR I */
  .table-sppd {
    width: 100%;
    border-collapse: collapse;
    font-size: 10pt;
    margin-bottom: 12px;
  }
  .table-sppd th, .table-sppd td {
    border: 1px solid #000;
    padding: 3.5px 6px;
    vertical-align: top;
  }
  .col-no {
    width: 28px;
    text-align: center;
    font-weight: 700;
  }
  .col-label {
    width: 240px;
  }

  /* TABEL LEMBAR II (VISUM) */
  .table-visum {
    width: 100%;
    border-collapse: collapse;
    font-size: 9.5pt;
    margin-top: 10px;
  }
  .table-visum td {
    border: 1px solid #000;
    padding: 6px 8px;
    vertical-align: top;
  }
  .visum-col {
    width: 50%;
  }

  .signature-block {
    display: flex;
    justify-content: flex-end;
    margin-top: 14px;
    page-break-inside: avoid;
  }
  .sign-box {
    width: 270px;
    text-align: center;
    font-size: 10pt;
  }
  .sign-name {
    font-weight: 700;
    text-decoration: underline;
  }
  .sign-qr-wrap {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 3px auto 4px auto;
    width: 70px;
    height: 70px;
  }
  .sign-qr-wrap img {
    width: 70px;
    height: 70px;
  }
</style>
@endpush

@section('content')

  {{-- ══════════════════════════════════════════════════════════════
       LEMBAR I : SURAT PERINTAH PERJALANAN DINAS (SPPD)
       ══════════════════════════════════════════════════════════════ --}}
  <div class="sppd-page">

    {{-- KOP KECIL DI SUDUT KANAN ATAS --}}
    <div class="meta-right">
      <table>
        <tr>
          <td style="width:75px;">Lembar Ke</td>
          <td style="width:12px;">:</td>
          <td>1 (Satu)</td>
        </tr>
        <tr>
          <td>Kode No</td>
          <td>:</td>
          <td>094 (Perjalanan Dinas)</td>
        </tr>
        <tr>
          <td>Nomor SPPD</td>
          <td>:</td>
          <td style="font-weight:700;">{{ $sppd->nomor_sppd }}</td>
        </tr>
      </table>
    </div>

    <div class="title-sppd">SURAT PERINTAH PERJALANAN DINAS</div>
    <div class="nomor-sppd">( S P P D )</div>

    <table class="table-sppd">
      <tr>
        <td class="col-no">1.</td>
        <td class="col-label">Pejabat Pembuat Komitmen / Berwenang</td>
        <td colspan="2"><strong>Kepala SMK Negeri 1 Air Naningan</strong></td>
      </tr>
      <tr>
        <td class="col-no">2.</td>
        <td class="col-label">Nama Pegawai yang diperintahkan</td>
        <td colspan="2"><strong>{{ $sppd->nama_pelaksana }}</strong></td>
      </tr>
      <tr>
        <td class="col-no">3.</td>
        <td class="col-label">
          a. Pangkat dan Golongan Ruang<br>
          b. Jabatan / Instansi<br>
          c. Tingkat Biaya Perjalanan Dinas
        </td>
        <td colspan="2">
          a. {{ $sppd->pangkat_golongan ?: '-' }}<br>
          b. {{ $sppd->jabatan ?: 'Guru' }} / SMK Negeri 1 Air Naningan<br>
          c. {{ $sppd->tingkat_biaya ?: 'Tingkat C' }}
        </td>
      </tr>
      <tr>
        <td class="col-no">4.</td>
        <td class="col-label">Maksud Perjalanan Dinas</td>
        <td colspan="2">{{ $sppd->maksud_perjalanan }}</td>
      </tr>
      <tr>
        <td class="col-no">5.</td>
        <td class="col-label">Alat Angkutan yang dipergunakan</td>
        <td colspan="2">{{ $sppd->alat_angkut ?: 'Kendaraan Dinas / Pribadi' }}</td>
      </tr>
      <tr>
        <td class="col-no">6.</td>
        <td class="col-label">
          a. Tempat Berangkat<br>
          b. Tempat Tujuan
        </td>
        <td colspan="2">
          a. {{ $sppd->tempat_berangkat }}<br>
          b. {{ $sppd->tempat_tujuan }}
        </td>
      </tr>
      <tr>
        <td class="col-no">7.</td>
        <td class="col-label">
          a. Lamanya Perjalanan Dinas<br>
          b. Tanggal Berangkat<br>
          c. Tanggal Harus Kembali
        </td>
        <td colspan="2">
          a. {{ $sppd->lama_perjalanan }} Hari<br>
          b. {{ $sppd->tanggal_berangkat->translatedFormat('d F Y') }}<br>
          c. {{ $sppd->tanggal_harus_kembali->translatedFormat('d F Y') }}
        </td>
      </tr>
      <tr>
        <td class="col-no">8.</td>
        <td class="col-label">Pengikut / Rombongan</td>
        <td style="width:180px; font-weight:700; text-align:center;">Nama</td>
        <td style="text-align:center; font-weight:700;">Keterangan</td>
      </tr>
      @if($otherAnggotas->count() > 0)
        @foreach($otherAnggotas as $oa)
          <tr>
            <td></td>
            <td></td>
            <td>{{ $oa->nama }}</td>
            <td>{{ $oa->peran }} (NIP. {{ $oa->nip ?: '-' }})</td>
          </tr>
        @endforeach
      @else
        <tr>
          <td></td>
          <td></td>
          <td style="text-align:center; color:#555;">-</td>
          <td style="text-align:center; color:#555;">-</td>
        </tr>
      @endif
      <tr>
        <td class="col-no">9.</td>
        <td class="col-label">
          Pembebanan Anggaran<br>
          a. Instansi<br>
          b. Mata Anggaran / Kode Rekening
        </td>
        <td colspan="2">
          <br>
          a. {{ $sppd->instansi_pembeban_anggaran }}<br>
          b. {{ $sppd->mata_anggaran ?: 'BOS Reguler SMK Negeri 1 Air Naningan' }}
        </td>
      </tr>
      <tr>
        <td class="col-no">10.</td>
        <td class="col-label">Keterangan Lain-lain</td>
        <td colspan="2">{{ $sppd->keterangan_lain ?: 'Dasar SPT No: ' . $suratTugas->nomor_surat_tugas }}</td>
      </tr>
    </table>

    {{-- TANDA TANGAN PPK / KEPSEK LEMBAR I --}}
    <div class="signature-block">
      <div class="sign-box">
        <div>Dikeluarkan di : Air Naningan</div>
        <div>Pada tanggal : {{ $tglSurat->translatedFormat('d F Y') }}</div>
        <div style="font-weight:700; margin-top:3px; margin-bottom:3px;">
          Kepala SMK Negeri 1 Air Naningan,
        </div>

        {{-- MODE TTE --}}
        <div class="ttd-digital-only">
          <div class="sign-qr-wrap">
            @if($qrImage)
              <img src="{{ $qrImage }}" alt="QR Code Keabsahan SPPD">
            @else
              <div style="width:70px; height:70px; border:1px dashed #999; display:flex; align-items:center; justify-content:center; font-size:9px;">QR TTE</div>
            @endif
          </div>
        </div>

        {{-- MODE BASAH --}}
        <div class="ttd-basah-only" style="height:60px;"></div>

        <div class="sign-name">{{ $namaKepsek }}</div>
        <div style="font-size:9pt;">{{ $pangkatKepsek }}</div>
        <div style="font-size:9pt;">NIP. {{ $nipKepsek }}</div>
      </div>
    </div>

  </div>


  {{-- ══════════════════════════════════════════════════════════════
       LEMBAR II : LEMBAR VISUM PERJALANAN DINAS (STEMPEL & PARAF)
       ══════════════════════════════════════════════════════════════ --}}
  <div class="sppd-page-break" style="margin-top:25px; border-top:2px dashed #94a3b8; padding-top:20px;">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
      <div style="font-size:9.5pt; font-weight:700; text-transform:uppercase;">
        LEMBAR VISUM SPPD (LEMBAR II)
      </div>
      <div style="font-size:9pt; font-family:monospace;">
        No. SPPD : {{ $sppd->nomor_sppd }}
      </div>
    </div>

    <table class="table-visum">
      {{-- BARIS 1: Keberangkatan & Kedatangan Pertama --}}
      <tr>
        <td class="visum-col">
          <div style="font-weight:700; margin-bottom:3px;">I. Berangkat dari : Air Naningan</div>
          <div style="margin-bottom:2px;">(Tempat Kedudukan Semula)</div>
          <div>Pada tanggal : {{ $sppd->tanggal_berangkat->translatedFormat('d F Y') }}</div>
          <div>Ke : {{ $sppd->tempat_tujuan }}</div>
          
          <div style="text-align:center; margin-top:8px;">
            <div style="font-weight:700;">Kepala SMK Negeri 1 Air Naningan</div>
            
            <div class="ttd-digital-only" style="justify-content:center; margin:3px 0;">
              @if($qrImage)
                <img src="{{ $qrImage }}" style="width:55px; height:55px;" alt="QR Code">
              @endif
            </div>
            <div class="ttd-basah-only" style="height:45px;"></div>

            <div style="font-weight:700; text-decoration:underline;">{{ $namaKepsek }}</div>
            <div style="font-size:8.5pt;">NIP. {{ $nipKepsek }}</div>
          </div>
        </td>

        <td class="visum-col">
          <div style="font-weight:700; margin-bottom:3px;">II. Tiba di : {{ $sppd->tempat_tujuan }}</div>
          <div>Pada tanggal : ..............................................</div>
          <div style="margin-top:6px; margin-bottom:4px;">Kepala / Pejabat Instansi yang dituju :</div>
          <div style="height:55px; border-bottom:1px dotted #ccc; display:flex; align-items:center; justify-content:center; color:#999; font-size:8pt;">
            (Tanda Tangan, Cap Stempel &amp; Nama Terang)
          </div>
          <div style="margin-top:4px;">NIP. ............................................................</div>
        </td>
      </tr>

      {{-- BARIS 2: Kepulangan dari Tempat Tujuan --}}
      <tr>
        <td class="visum-col">
          <div style="font-weight:700; margin-bottom:3px;">III. Berangkat dari : {{ $sppd->tempat_tujuan }}</div>
          <div>Ke : Air Naningan, Tanggamus</div>
          <div>Pada tanggal : ..............................................</div>
          <div style="margin-top:6px; margin-bottom:4px;">Kepala / Pejabat Instansi yang dituju :</div>
          <div style="height:55px; border-bottom:1px dotted #ccc; display:flex; align-items:center; justify-content:center; color:#999; font-size:8pt;">
            (Tanda Tangan, Cap Stempel &amp; Nama Terang)
          </div>
          <div style="margin-top:4px;">NIP. ............................................................</div>
        </td>

        <td class="visum-col">
          <div style="font-weight:700; margin-bottom:3px;">IV. Tiba kembali di : Air Naningan</div>
          <div>(Tempat Kedudukan Semula)</div>
          <div>Pada tanggal : {{ $sppd->tanggal_harus_kembali->translatedFormat('d F Y') }}</div>
          <div style="margin-top:4px; font-size:9pt;">Telah diperiksa dengan keterangan bahwa perjalanan tersebut di atas benar-benar dilakukan atas perintahnya dan semata-mata untuk kepentingan jabatan.</div>
          <div style="text-align:center; margin-top:6px;">
            <div style="font-weight:700;">Kepala SMK Negeri 1 Air Naningan</div>
            <div style="height:45px;"></div>
            <div style="font-weight:700; text-decoration:underline;">{{ $namaKepsek }}</div>
            <div style="font-size:8.5pt;">NIP. {{ $nipKepsek }}</div>
          </div>
        </td>
      </tr>

      {{-- BARIS 3: Catatan Lain-Lain & Perhatian --}}
      <tr>
        <td colspan="2" style="font-size:8.5pt; color:#334155;">
          <div style="font-weight:700; text-transform:uppercase; margin-bottom:2px;">V. Catatan Lain-Lain / Perhatian :</div>
          <div>Pejabat yang berwenang menerbitkan SPPD, pegawai yang melakukan perjalanan dinas, para pejabat yang mengesahkan tanggal berangkat/tiba serta bendaharawan bertanggung jawab berdasarkan peraturan-peraturan Keuangan Negara apabila Negara mendapat rugi akibat kesalahan, kealpaan dan kealpaannya (Undang-Undang Perbendaharaan Negara).</div>
        </td>
      </tr>
    </table>

  </div>

@endsection

@section('footer')
  <div style="display:flex; justify-content:space-between; align-items:center; font-size:8pt; color:#475569;">
    <div>
      <i class="bi bi-shield-check me-1"></i> SPPD Resmi Terdaftar &bull; Hash: <span style="font-family:monospace;">{{ $suratTugas->kode_verifikasi_qr }}</span>
    </div>
    <div>Lembar I &amp; II Visum &bull; SITUAN SMKN 1 AN</div>
  </div>
@endsection
