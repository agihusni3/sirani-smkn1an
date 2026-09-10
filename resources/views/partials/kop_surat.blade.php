@php
  $sekolahKop = $sekolah ?? \App\Models\PengaturanSekolah::getAktif();
  $isCompact = !empty($compact);
@endphp
<div class="kop-surat-master" style="width:100%; margin-bottom:{{ $isCompact ? '10px' : '14px' }}; font-family:'Times New Roman', Times, 'Tinos', serif; color:#000;">
  <div style="display:flex; align-items:center; justify-content:space-between; gap:{{ $isCompact ? '8px' : '10px' }}; text-align:center;">
    {{-- LOGO KIRI: PROVINSI LAMPUNG (Perisai Vertikal) --}}
    <div style="width:{{ $isCompact ? '56px' : '72px' }}; min-width:{{ $isCompact ? '56px' : '72px' }}; height:{{ $isCompact ? '64px' : '80px' }}; flex-shrink:0; display:flex; align-items:center; justify-content:center;">
      <img src="{{ $sekolahKop->logo_provinsi_url }}" alt="Logo Provinsi Lampung" style="height:{{ $isCompact ? '60px' : '78px' }}; width:auto; max-width:{{ $isCompact ? '54px' : '70px' }}; object-fit:contain;" onerror="if(!this.dataset.fallback){this.dataset.fallback=1;this.src='/img/logo_prov_lampung.png';}else if(this.dataset.fallback==1){this.dataset.fallback=2;this.src='/lampung.png';}" />
    </div>

    {{-- TEKS KOP DINAS PUSAT --}}
    <div style="flex:1; text-align:center; padding:0 4px;">
      <div style="font-size:{{ $isCompact ? '9.5pt' : '11.5pt' }}; font-weight:700; letter-spacing:0.25px; text-transform:uppercase; line-height:1.2; margin-bottom:1px;">
        {{ $sekolahKop->nama_instansi_atas ?: 'PEMERINTAH PROVINSI LAMPUNG' }}
      </div>
      <div style="font-size:{{ $isCompact ? '10.5pt' : '12.5pt' }}; font-weight:700; letter-spacing:0.25px; text-transform:uppercase; line-height:1.2; margin-bottom:2px;">
        {{ $sekolahKop->nama_dinas ?: 'DINAS PENDIDIKAN DAN KEBUDAYAAN' }}
      </div>
      <div style="font-size:{{ $isCompact ? '12.5pt' : '15pt' }}; font-weight:700; letter-spacing:0.25px; text-transform:uppercase; line-height:1.2; margin-bottom:{{ $isCompact ? '2px' : '4px' }};">
        {{ $sekolahKop->nama_sekolah ?: 'SMK NEGERI 1 AIR NANINGAN' }}
      </div>
      <div style="font-size:{{ $isCompact ? '7.5pt' : '8.5pt' }}; line-height:1.3; color:#111;">
        <span>{{ $sekolahKop->alamat ?: 'Jl. Makam Baturuguk, Pekon Karang Sari' }}</span>, 
        <span>Kec. {{ $sekolahKop->kecamatan ?: 'Air Naningan' }}</span>, 
        <span>{{ $sekolahKop->kabupaten ?: 'Kab. Tanggamus' }}</span>, 
        <span>{{ $sekolahKop->provinsi ?: 'Lampung' }}</span>@if(!empty($sekolahKop->kode_pos)) <span>{{ $sekolahKop->kode_pos }}</span>@endif
      </div>
      <div style="font-size:{{ $isCompact ? '7pt' : '8pt' }}; line-height:1.3; margin-top:2px; color:#111;">
        @if(!empty($sekolahKop->npsn))<span style="white-space:nowrap;">NPSN: {{ $sekolahKop->npsn }}</span> &bull; @endif
        @if(!empty($sekolahKop->website))<span style="white-space:nowrap;">Website: {{ $sekolahKop->website }}</span> &bull; @endif
        @if(!empty($sekolahKop->email))<span style="white-space:nowrap;">Email: {{ $sekolahKop->email }}</span>@endif
      </div>
    </div>

    {{-- LOGO KANAN: SMKN 1 AIR NANINGAN --}}
    <div style="width:{{ $isCompact ? '56px' : '72px' }}; min-width:{{ $isCompact ? '56px' : '72px' }}; height:{{ $isCompact ? '64px' : '80px' }}; flex-shrink:0; display:flex; align-items:center; justify-content:center;">
      <img src="{{ $sekolahKop->logo_sekolah_url }}" alt="Logo Sekolah" style="height:{{ $isCompact ? '56px' : '72px' }}; width:auto; max-width:{{ $isCompact ? '54px' : '70px' }}; object-fit:contain;" onerror="if(!this.dataset.fallback){this.dataset.fallback=1;this.src='/img/logo.png';}else if(this.dataset.fallback==1){this.dataset.fallback=2;this.src='/logo.png';}" />
    </div>
  </div>

  {{-- GARIS GANDA KOP SURAT DINAS BAKU --}}
  <div style="border-top:{{ $isCompact ? '2px' : '2.5px' }} solid #000; border-bottom:{{ $isCompact ? '0.5px' : '0.75px' }} solid #000; height:{{ $isCompact ? '2px' : '3px' }}; margin-top:{{ $isCompact ? '6px' : '8px' }};"></div>
</div>
