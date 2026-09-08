@php
  $sekolahKop = $sekolah ?? \App\Models\PengaturanSekolah::getAktif();
@endphp
<div class="kop-surat-master" style="width:100%; margin-bottom:12px; font-family:'Times New Roman', Times, serif; color:#000;">
  <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; text-align:center;">
    {{-- LOGO KIRI: PROVINSI LAMPUNG --}}
    <div style="width:75px; height:75px; flex-shrink:0; display:flex; align-items:center; justify-content:center;">
      <img src="{{ $sekolahKop->logo_provinsi_url }}" alt="Logo Provinsi Lampung" style="max-width:75px; max-height:75px; object-fit:contain;" />
    </div>

    {{-- TEKS KOP DINAS PUSAT --}}
    <div style="flex:1; text-align:center; padding:0 4px;">
      <div style="font-size:11pt; font-weight:700; letter-spacing:0.5px; text-transform:uppercase; line-height:1.25;">
        {{ $sekolahKop->nama_instansi_atas ?: 'PEMERINTAH PROVINSI LAMPUNG' }}
      </div>
      <div style="font-size:12pt; font-weight:800; letter-spacing:0.5px; text-transform:uppercase; line-height:1.25;">
        {{ $sekolahKop->nama_dinas ?: 'DINAS PENDIDIKAN DAN KEBUDAYAAN' }}
      </div>
      <div style="font-size:14pt; font-weight:900; letter-spacing:0.5px; text-transform:uppercase; line-height:1.25; margin:1px 0;">
        {{ $sekolahKop->nama_sekolah ?: 'SMK NEGERI 1 AIR NANINGAN' }}
      </div>
      <div style="font-size:9pt; line-height:1.35; margin-top:2px;">
        {{ $sekolahKop->alamat_lengkap ?: ($sekolahKop->alamat . ', Kec. ' . $sekolahKop->kecamatan . ', ' . $sekolahKop->kabupaten . ', ' . $sekolahKop->provinsi . ' ' . $sekolahKop->kode_pos) }}<br>
        @if($sekolahKop->npsn) NPSN: {{ $sekolahKop->npsn }} &bull; @endif
        @if($sekolahKop->website) Website: {{ $sekolahKop->website }} &bull; @endif
        @if($sekolahKop->email) Email: {{ $sekolahKop->email }} @endif
      </div>
    </div>

    {{-- LOGO KANAN: SMKN 1 AIR NANINGAN --}}
    <div style="width:75px; height:75px; flex-shrink:0; display:flex; align-items:center; justify-content:center;">
      <img src="{{ $sekolahKop->logo_sekolah_url }}" alt="Logo Sekolah" style="max-width:75px; max-height:75px; object-fit:contain;" />
    </div>
  </div>

  {{-- GARIS GANDA KOP SURAT DINAS BAKU --}}
  <div style="border-bottom:3px solid #000; border-top:1px solid #000; height:3px; margin-top:8px;"></div>
</div>
