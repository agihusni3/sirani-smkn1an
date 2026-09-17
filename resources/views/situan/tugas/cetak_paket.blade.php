@extends('layouts.dokumen_a4')

@php
  $tglSurat = \Carbon\Carbon::parse($suratTugas->tanggal_mulai);
  $backUrl = route('situan.surat-tugas.index');
  $backLabel = 'Kembali ke Daftar SPT & SPPD';
  $anggotas = $suratTugas->anggotas;
  $isMulti = $anggotas->count() > 1;
  $sppds = $suratTugas->sppds;
@endphp

@section('title', 'Paket Cetak 3-in-1 — ' . $suratTugas->nomor_surat_tugas)
@section('toolbar_title', 'Paket Lengkap 3-in-1: Surat Tugas + SPPD + Visum')

@push('styles')
<style>
  .paket-page {
    page-break-after: always;
  }
  @media print {
    .paket-page {
      page-break-after: always !important;
    }
  }

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
  
  .dasar-table, .kepada-table {
    width: 100%;
    margin-bottom: 8px;
    border-collapse: collapse;
  }
  .dasar-table td, .kepada-table td {
    vertical-align: top;
    padding: 2px 0;
  }
  .dasar-label, .kepada-label {
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

  .person-table {
    width: 100%;
    border-collapse: collapse;
    margin: 4px 0 10px 0;
    font-size: 10pt;
  }
  .person-table th, .person-table td {
    border: 1px solid #000;
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

  /* SPPD STYLING */
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
  .table-sppd {
    width: 100%;
    border-collapse: collapse;
    font-size: 10pt;
    margin-bottom: 12px;
  }
  .table-sppd td {
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
</style>
@endpush

@section('content')

  {{-- ══════════════════════════════════════════════════════════════
       BAGIAN 1: SURAT PERINTAH TUGAS (SPT)
       ══════════════════════════════════════════════════════════════ --}}
  <div class="paket-page">

    <div class="title-block">
      <div class="surat-title">SURAT PERINTAH TUGAS</div>
      <div class="surat-nomor">Nomor : {{ $suratTugas->nomor_surat_tugas }}</div>
    </div>

    <div class="surat-body">
      <table class="dasar-table">
        <tr>
          <td class="dasar-label">Dasar</td>
          <td class="dasar-sep">:</td>
          <td>{{ $suratTugas->dasar_penugasan ?: 'Program Kerja dan Agenda Kedinasan SMK Negeri 1 Air Naningan Tahun Ajaran ' . date('Y') . '/' . (date('Y')+1) }}</td>
        </tr>
      </table>

      <div class="order-block">MEMERINTAHKAN :</div>

      <table class="kepada-table">
        <tr>
          <td class="kepada-label">Kepada</td>
          <td class="dasar-sep">:</td>
          <td>
            @if(!$isMulti && $anggotas->count() === 1)
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

    {{-- TTD KEPSEK SPT --}}
    <div class="signature-container">
      <div class="signature-box">
        <div>Ditetapkan di : Air Naningan</div>
        <div>Pada tanggal : {{ $tglSurat->translatedFormat('d F Y') }}</div>
        <div style="font-weight:700; margin-top:4px; margin-bottom:4px;">
          Kepala SMK Negeri 1 Air Naningan,
        </div>

        <div class="ttd-digital-only">
          <div class="sign-qr-wrap">
            @if($qrImage)
              <img src="{{ $qrImage }}" alt="QR Code Keabsahan Surat Tugas">
            @endif
          </div>
        </div>

        <div class="ttd-basah-only" style="height:65px;"></div>

        <div class="sign-name">{{ $namaKepsek }}</div>
        <div style="font-size:9.5pt;">{{ $pangkatKepsek }}</div>
        <div style="font-size:9.5pt;">NIP. {{ $nipKepsek }}</div>
      </div>
    </div>

  </div>


  {{-- ══════════════════════════════════════════════════════════════
       BAGIAN 2 & 3: SPPD LEMBAR I & LEMBAR II VISUM (UNTUK SETIAP SPPD)
       ══════════════════════════════════════════════════════════════ --}}
  @foreach($sppds as $sppdIndex => $sppd)
    
    {{-- LEMBAR I SPPD --}}
    <div class="paket-page" style="page-break-before:always; padding-top:10px;">
      
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
        @php
          $otherAngs = $suratTugas->anggotas->where('id', '!=', $sppd->surat_tugas_anggota_id);
        @endphp
        @if($otherAngs->count() > 0)
          @foreach($otherAngs as $oa)
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

      <div class="signature-container">
        <div class="signature-box">
          <div>Dikeluarkan di : Air Naningan</div>
          <div>Pada tanggal : {{ $tglSurat->translatedFormat('d F Y') }}</div>
          <div style="font-weight:700; margin-top:3px; margin-bottom:3px;">
            Kepala SMK Negeri 1 Air Naningan,
          </div>

          <div class="ttd-digital-only">
            <div class="sign-qr-wrap">
              @if($qrImage)
                <img src="{{ $qrImage }}" alt="QR Code Keabsahan SPPD">
              @endif
            </div>
          </div>

          <div class="ttd-basah-only" style="height:60px;"></div>

          <div class="sign-name">{{ $namaKepsek }}</div>
          <div style="font-size:9pt;">{{ $pangkatKepsek }}</div>
          <div style="font-size:9pt;">NIP. {{ $nipKepsek }}</div>
        </div>
      </div>

    </div>

    {{-- LEMBAR II VISUM SPPD --}}
    <div class="paket-page" style="page-break-before:always; padding-top:10px;">
      
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
        <div style="font-size:9.5pt; font-weight:700; text-transform:uppercase;">
          LEMBAR VISUM SPPD (LEMBAR II) &bull; a.n. {{ $sppd->nama_pelaksana }}
        </div>
        <div style="font-size:9pt; font-family:monospace;">
          No. SPPD : {{ $sppd->nomor_sppd }}
        </div>
      </div>

      <table class="table-visum">
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

        <tr>
          <td colspan="2" style="font-size:8.5pt; color:#334155;">
            <div style="font-weight:700; text-transform:uppercase; margin-bottom:2px;">V. Catatan Lain-Lain / Perhatian :</div>
            <div>Pejabat yang berwenang menerbitkan SPPD, pegawai yang melakukan perjalanan dinas, para pejabat yang mengesahkan tanggal berangkat/tiba serta bendaharawan bertanggung jawab berdasarkan peraturan-peraturan Keuangan Negara apabila Negara mendapat rugi akibat kesalahan, kealpaan dan kealpaannya (Undang-Undang Perbendaharaan Negara).</div>
          </td>
        </tr>
      </table>

    </div>

  @endforeach

@endsection

@section('footer')
  <div style="display:flex; justify-content:space-between; align-items:center; font-size:8pt; color:#475569;">
    <div>
      <i class="bi bi-shield-check me-1"></i> Paket Dokumen Resmi 3-in-1 (SPT &amp; SPPD) &bull; Hash: <span style="font-family:monospace;">{{ $suratTugas->kode_verifikasi_qr }}</span>
    </div>
    <div>SITUAN SMKN 1 AN</div>
  </div>
@endsection
