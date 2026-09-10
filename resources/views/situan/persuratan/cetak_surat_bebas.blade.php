@extends('layouts.dokumen_a4')

@section('title', 'Surat Dinas — ' . $surat->nomor_surat_lengkap)

@section('content')
  {{-- BARIS KEPALA SURAT DINAS (NOMOR & TUJUAN) --}}
  <table style="width:100%; border-collapse:collapse; margin-top:10px; margin-bottom:18px; font-size:11.5pt;">
    <tr>
      <td style="width:58%; vertical-align:top; line-height:1.6;">
        <table style="width:100%; border-collapse:collapse;">
          <tr>
            <td style="width:85px; vertical-align:top;">Nomor</td>
            <td style="width:12px; vertical-align:top;">:</td>
            <td style="vertical-align:top;"><strong>{{ $surat->nomor_surat_lengkap }}</strong></td>
          </tr>
          <tr>
            <td style="vertical-align:top;">Sifat</td>
            <td style="vertical-align:top;">:</td>
            <td style="vertical-align:top;">{{ $surat->sifat_surat ?: 'Biasa' }}</td>
          </tr>
          <tr>
            <td style="vertical-align:top;">Lampiran</td>
            <td style="vertical-align:top;">:</td>
            <td style="vertical-align:top;">{{ $surat->lampiran ?: '-' }}</td>
          </tr>
          <tr>
            <td style="vertical-align:top;">Perihal</td>
            <td style="vertical-align:top;">:</td>
            <td style="vertical-align:top;"><strong>{{ $surat->perihal }}</strong></td>
          </tr>
        </table>
      </td>
      <td style="width:42%; vertical-align:top; line-height:1.5; padding-left:20px;">
        <div>{{ $sekolah->kecamatan ?? 'Air Naningan' }}, {{ $surat->tanggal_surat->translatedFormat('d F Y') }}</div>
        <div style="margin-top:12px;">
          Kepada Yth.<br />
          <strong>{{ $surat->tujuan_surat }}</strong><br />
          di —<br />
          &nbsp;&nbsp;&nbsp;&nbsp;Tempat
        </div>
      </td>
    </tr>
  </table>

  {{-- ISI SURAT DINAS --}}
  <div style="font-size:11.5pt; line-height:1.6; text-align:justify; margin-bottom:25px;">
    @if($surat->isi_surat)
      {!! nl2br(e($surat->isi_surat)) !!}
    @else
      <p style="text-indent: 40px; margin-bottom:12px;">
        Dengan hormat, sehubungan dengan urusan kedinasan dan pelaksanaan program kerja di SMK Negeri 1 Air Naningan, bersama ini kami sampaikan mengenai <strong>{{ $surat->perihal }}</strong> kepada pihak terkait sebagaimana tertera pada tujuan surat.
      </p>
      <p style="text-indent: 40px; margin-bottom:12px;">
        Demikian surat ini kami sampaikan untuk diketahui dan dipergunakan sebagaimana mestinya. Atas perhatian dan kerja sama yang baik, kami ucapkan terima kasih.
      </p>
    @endif
  </div>
@endsection

@section('ttd')
  <div class="ttd-box">
    <div>{{ $surat->jabatan_penandatangan ?: 'Kepala Sekolah' }},</div>
    <div class="ttd-space"></div>
    <div class="ttd-nama">{{ $surat->penandatangan ?: ($sekolah->nama_kepala_sekolah ?? 'Kepala Sekolah') }}</div>
    <div class="ttd-nip">
      @if($surat->nip_penandatangan || $sekolah->nip_kepala_sekolah)
        NIP. {{ $surat->nip_penandatangan ?: $sekolah->nip_kepala_sekolah }}
      @else
        NIP. -
      @endif
    </div>
  </div>
@endsection

@section('footer')
  @if($surat->tembusan)
    <div style="font-size:9.5pt; line-height:1.4; margin-top:12px; border-top:1px dashed #CBD5E1; padding-top:6px; margin-bottom:8px;">
      <strong>Tembusan Yth:</strong><br />
      {!! nl2br(e($surat->tembusan)) !!}
    </div>
  @endif
  <div style="display:flex; justify-content:space-between; align-items:center; font-size:8pt; color:#64748b; border-top:1px solid #e2e8f0; padding-top:4px; font-family:'Plus Jakarta Sans', sans-serif;">
    <span>Dokumen Resmi Tata Usaha · <strong>SITUAN (Sistem Informasi Tata Usaha &amp; Administrasi Terpadu) SMKN 1 Air Naningan</strong></span>
    <span>No. Reg: {{ $surat->nomor_surat_lengkap }}</span>
  </div>
@endsection
