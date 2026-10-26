@php
  $sekolahKop = $sekolah ?? \App\Models\PengaturanSekolah::getAktif();
  $isCompact = !empty($compact);
  $logoProvHeight = $isCompact ? '58px' : '70px';
  $logoSekolahHeight = $isCompact ? '58px' : '68px';
  $colLogoWidth = $isCompact ? '65px' : '76px';
@endphp
<div class="kop-surat-master" style="width:100%; margin-bottom:{{ $isCompact ? '8px' : '12px' }}; font-family:'Times New Roman', Times, 'Tinos', serif; color:#000;">
  <table style="width:100%; border-collapse:collapse; border:none; margin:0; padding:0; background:transparent;">
    <tr>
      {{-- LOGO KIRI: PROVINSI LAMPUNG --}}
      <td style="width:{{ $colLogoWidth }}; text-align:center; vertical-align:middle; border:none !important; padding:0;">
        <img src="{{ $sekolahKop->logo_provinsi_url }}" alt="Logo Provinsi Lampung" 
             style="height:{{ $logoProvHeight }}; width:auto; max-width:{{ $colLogoWidth }}; object-fit:contain; display:block; margin:0 auto;" 
             onerror="if(!this.dataset.fallback){this.dataset.fallback=1;this.src='/img/logo_prov_lampung.png';}else if(this.dataset.fallback==1){this.dataset.fallback=2;this.src='/img/lampung.png';}" />
      </td>

      {{-- TEKS KOP DINAS PUSAT --}}
      <td style="text-align:center; vertical-align:middle; border:none !important; padding:0 10px;">
        <div style="font-size:{{ $isCompact ? '10pt' : '11.5pt' }}; font-weight:700; letter-spacing:0.4px; text-transform:uppercase; line-height:1.2; margin-bottom:1px; color:#000;">
          {{ $sekolahKop->nama_instansi_atas ?: 'PEMERINTAH PROVINSI LAMPUNG' }}
        </div>
        <div style="font-size:{{ $isCompact ? '11pt' : '12.5pt' }}; font-weight:700; letter-spacing:0.4px; text-transform:uppercase; line-height:1.2; margin-bottom:2px; color:#000;">
          {{ $sekolahKop->nama_dinas ?: 'DINAS PENDIDIKAN DAN KEBUDAYAAN' }}
        </div>
        <div style="font-size:{{ $isCompact ? '13pt' : '15.5pt' }}; font-weight:900; letter-spacing:0.5px; text-transform:uppercase; line-height:1.2; margin-bottom:{{ $isCompact ? '2px' : '4px' }}; color:#000;">
          {{ $sekolahKop->nama_sekolah ?: 'SMK NEGERI 1 AIR NANINGAN' }}
        </div>
        <div style="font-size:{{ $isCompact ? '7.5pt' : '8.5pt' }}; line-height:1.25; color:#111;">
          <span>{{ $sekolahKop->alamat ?: 'Jl. Makam Baturuguk, Pekon Karang Sari' }}</span>, 
          <span>Kec. {{ $sekolahKop->kecamatan ?: 'Air Naningan' }}</span>, 
          <span>{{ $sekolahKop->kabupaten ?: 'Kab. Tanggamus' }}</span>, 
          <span>{{ $sekolahKop->provinsi ?: 'Lampung' }} {{ $sekolahKop->kode_pos ?: '35379' }}</span>
        </div>
        <div style="font-size:{{ $isCompact ? '7pt' : '8pt' }}; line-height:1.25; margin-top:1px; color:#111;">
          @if(!empty($sekolahKop->npsn))<span style="white-space:nowrap;">NPSN: {{ $sekolahKop->npsn }}</span> &bull; @endif
          @if(!empty($sekolahKop->telepon))<span style="white-space:nowrap;">Telepon: {{ $sekolahKop->telepon }}</span> &bull; @endif
          @if(!empty($sekolahKop->website))<span style="white-space:nowrap;">Website: {{ $sekolahKop->website }}</span> &bull; @endif
          @if(!empty($sekolahKop->email))<span style="white-space:nowrap;">Email: {{ $sekolahKop->email }}</span>@endif
        </div>
      </td>

      {{-- LOGO KANAN: SMKN 1 AIR NANINGAN --}}
      <td style="width:{{ $colLogoWidth }}; text-align:center; vertical-align:middle; border:none !important; padding:0;">
        <img src="{{ $sekolahKop->logo_sekolah_url }}" alt="Logo Sekolah" 
             style="height:{{ $logoSekolahHeight }}; width:auto; max-width:{{ $colLogoWidth }}; object-fit:contain; display:block; margin:0 auto;" 
             onerror="if(!this.dataset.fallback){this.dataset.fallback=1;this.src='/img/logo.png';}else if(this.dataset.fallback==1){this.dataset.fallback=2;this.src='/logo.png';}" />
      </td>
    </tr>
  </table>

  {{-- GARIS GANDA KOP SURAT DINAS BAKU --}}
  <div style="border-top:{{ $isCompact ? '2px' : '2.5px' }} solid #000; border-bottom:{{ $isCompact ? '0.5px' : '0.8px' }} solid #000; height:{{ $isCompact ? '2px' : '2.5px' }}; margin-top:{{ $isCompact ? '5px' : '7px' }};"></div>
</div>
