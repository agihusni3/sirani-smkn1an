@php
  $isCompact = !empty($compact);
@endphp
<table style="width:100%; border-collapse:collapse; margin-bottom:0; border:none !important;">
  <tr>
    <td style="width:{{ $isCompact ? '54px' : '65px' }}; text-align:center; vertical-align:middle; border:none !important; padding:0;">
      @if(!empty($logoProvBase64))
        <img src="{{ $logoProvBase64 }}" style="width:{{ $isCompact ? '48px' : '58px' }}; height:auto; max-height:{{ $isCompact ? '60px' : '72px' }};" alt="Logo Provinsi">
      @endif
    </td>
    <td style="text-align:center; vertical-align:middle; border:none !important; padding:0 8px;">
      <div style="font-family:'Times New Roman',Times,serif; font-size:{{ $isCompact ? '9.5pt' : '10.5pt' }}; font-weight:bold; text-transform:uppercase; line-height:1.15;">
        {{ $sekolah->nama_instansi_atas ?: 'PEMERINTAH PROVINSI LAMPUNG' }}
      </div>
      <div style="font-family:'Times New Roman',Times,serif; font-size:{{ $isCompact ? '10.5pt' : '11.5pt' }}; font-weight:bold; text-transform:uppercase; line-height:1.15; margin-top:1px;">
        {{ $sekolah->nama_dinas ?: 'DINAS PENDIDIKAN DAN KEBUDAYAAN' }}
      </div>
      <div style="font-family:'Times New Roman',Times,serif; font-size:{{ $isCompact ? '12.5pt' : '14.5pt' }}; font-weight:900; text-transform:uppercase; line-height:1.15; margin-top:2px;">
        {{ $sekolah->nama_sekolah ?: 'SMK NEGERI 1 AIR NANINGAN' }}
      </div>
      <div style="font-family:'Times New Roman',Times,serif; font-size:{{ $isCompact ? '7pt' : '7.5pt' }}; line-height:1.2; margin-top:2px; font-style:italic;">
        {{ $sekolah->alamat ?: 'Jl. Makam Baturuguk, Pekon Karang Sari' }}, Kec. {{ $sekolah->kecamatan ?: 'Air Naningan' }}, Kab. {{ $sekolah->kabupaten ?: 'Tanggamus' }}, Prov. {{ $sekolah->provinsi ?: 'Lampung' }} {{ $sekolah->kode_pos ?: '35379' }}
      </div>
      <div style="font-family:'Times New Roman',Times,serif; font-size:{{ $isCompact ? '6.5pt' : '7pt' }}; line-height:1.2; margin-top:1px;">
        @if(!empty($sekolah->npsn))NPSN: {{ $sekolah->npsn }} &bull; @endif
        @if(!empty($sekolah->website))Website: {{ $sekolah->website }} &bull; @endif
        @if(!empty($sekolah->email))Email: {{ $sekolah->email }}@endif
      </div>
    </td>
    <td style="width:{{ $isCompact ? '54px' : '65px' }}; text-align:center; vertical-align:middle; border:none !important; padding:0;">
      @if(!empty($logoSekolahBase64))
        <img src="{{ $logoSekolahBase64 }}" style="width:{{ $isCompact ? '48px' : '58px' }}; height:auto; max-height:{{ $isCompact ? '60px' : '72px' }};" alt="Logo Sekolah">
      @endif
    </td>
  </tr>
</table>
<div style="border-top:{{ $isCompact ? '2px' : '2.5px' }} solid #000; border-bottom:{{ $isCompact ? '0.6px' : '0.8px' }} solid #000; height:{{ $isCompact ? '2px' : '3px' }}; margin:{{ $isCompact ? '3px 0 10px 0' : '5px 0 14px 0' }};"></div>
