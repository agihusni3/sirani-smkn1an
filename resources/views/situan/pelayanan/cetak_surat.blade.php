@extends('layouts.dokumen_a4')

@php
  $title = match($pelayanan->jenis_pelayanan) {
    'suket_aktif'            => 'SURAT KETERANGAN SISWA AKTIF',
    'suket_berkelakuan_baik' => 'SURAT KETERANGAN BERKELAKUAN BAIK',
    'suket_mutasi_keluar'    => 'SURAT REKOMENDASI PINDAH SEKOLAH',
    'suket_skl'              => 'SURAT KETERANGAN LULUS SEMENTARA',
    'suket_pengantar_pkl'    => 'SURAT PENGANTAR PRAKTIK KERJA LAPANGAN',
    default                  => 'SURAT KETERANGAN KESISWAAN',
  };
  $snapshot = $pelayanan->payload_snapshot ?? [];
  $tglSurat = $pelayanan->suratKeluar?->tanggal_surat ? \Carbon\Carbon::parse($pelayanan->suratKeluar->tanggal_surat) : \Carbon\Carbon::today();
  $backUrl = route('situan.pelayanan.index');
  $backLabel = 'Kembali ke Layanan Siswa';
@endphp

@section('title', ($pelayanan->suratKeluar?->nomor_surat_lengkap ?? 'Surat Keterangan') . ' — ' . ($pelayanan->siswa?->nama ?? 'Siswa'))
@section('toolbar_title', 'Surat Pelayanan Siswa & Suket Resmi')

@push('styles')
<style>
  .title-block {
    text-align: center;
    margin-bottom: 14px;
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
    font-size: 11pt;
    line-height: 1.5;
  }
  .surat-body p {
    margin-bottom: 8px;
    text-indent: 28px;
  }

  .bio-table {
    width: 100%;
    margin: 8px 0 12px 18px;
    border-collapse: collapse;
    font-size: 11pt;
  }
  .bio-table td {
    padding: 2.5px 4px;
    vertical-align: top;
  }
  .bio-label {
    width: 220px;
  }
  .bio-sep {
    width: 15px;
    text-align: center;
  }
  .bio-val {
    font-weight: 600;
  }

  .signature-container {
    display: flex;
    justify-content: flex-end;
    margin-top: 20px;
    margin-bottom: 8px;
  }
  .signature-box {
    width: 280px;
    text-align: center;
    font-size: 11pt;
  }
  .sign-date {
    margin-bottom: 3px;
  }
  .sign-title {
    font-weight: 700;
    margin-bottom: 4px;
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
    font-size: 11pt;
    margin-top: 3px;
  }
  .sign-nip {
    font-size: 10pt;
  }

  .security-footer {
    display: flex;
    align-items: center;
    gap: 12px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 8pt;
    color: #475569;
    line-height: 1.35;
  }
  .security-qr-small {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
  }
</style>
@endpush

@section('content')
  {{-- 2. JUDUL & NOMOR SURAT --}}
  <div class="title-block">
    <div class="surat-title">{{ $title }}</div>
    <div class="surat-nomor">Nomor : {{ $pelayanan->suratKeluar?->nomor_surat_lengkap }}</div>
  </div>

  {{-- 3. ISI SURAT --}}
  <div class="surat-body">
    <p>
      Yang bertanda tangan di bawah ini, Kepala {{ $sekolah->nama_sekolah ?: 'SMK NEGERI 1 AIR NANINGAN' }}, Kabupaten Tanggamus, Provinsi Lampung, menerangkan dengan sebenarnya bahwa:
    </p>

    <table class="bio-table">
      <tr>
        <td class="bio-label">Nama Lengkap Siswa</td>
        <td class="bio-sep">:</td>
        <td class="bio-val" style="text-transform:uppercase;">{{ $snapshot['nama'] ?? ($pelayanan->siswa?->nama ?? '-') }}</td>
      </tr>
      <tr>
        <td class="bio-label">Nomor Induk Siswa Nasional (NISN)</td>
        <td class="bio-sep">:</td>
        <td class="bio-val">{{ $snapshot['nisn'] ?? ($pelayanan->siswa?->nisn ?? '-') }}</td>
      </tr>
      <tr>
        <td class="bio-label">Nomor Induk Siswa (NIS)</td>
        <td class="bio-sep">:</td>
        <td class="bio-val">{{ $snapshot['nis'] ?? ($pelayanan->siswa?->nis ?? '-') }}</td>
      </tr>
      <tr>
        <td class="bio-label">Tempat, Tanggal Lahir</td>
        <td class="bio-sep">:</td>
        <td class="bio-val">
          {{ $snapshot['tempat_lahir'] ?? ($pelayanan->siswa?->tempat_lahir ?? '-') }}, 
          {{ $snapshot['tanggal_lahir'] ?? ($pelayanan->siswa?->tanggal_lahir ? \Carbon\Carbon::parse($pelayanan->siswa->tanggal_lahir)->translatedFormat('d F Y') : '-') }}
        </td>
      </tr>
      <tr>
        <td class="bio-label">Jenis Kelamin</td>
        <td class="bio-sep">:</td>
        <td class="bio-val">{{ $snapshot['jenis_kelamin'] ?? ($pelayanan->siswa?->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan') }}</td>
      </tr>
      <tr>
        <td class="bio-label">Kelas / Program Keahlian</td>
        <td class="bio-sep">:</td>
        <td class="bio-val">{{ $snapshot['rombel'] ?? '-' }}</td>
      </tr>
      <tr>
        <td class="bio-label">Nama Orang Tua / Wali</td>
        <td class="bio-sep">:</td>
        <td class="bio-val">{{ $snapshot['nama_ortu'] ?? ($pelayanan->siswa?->nama_ortu ?: ($pelayanan->siswa?->nama_ayah ?: '-')) }}</td>
      </tr>
      <tr>
        <td class="bio-label">Alamat Domisili</td>
        <td class="bio-sep">:</td>
        <td class="bio-val">{{ $snapshot['alamat'] ?? ($pelayanan->siswa?->alamat ?: '-') }}</td>
      </tr>
    </table>

    {{-- Narrative Content by Jenis --}}
    @if($pelayanan->jenis_pelayanan === 'suket_aktif')
      <p>
        Adalah benar nama tersebut di atas terdaftar secara resmi sebagai peserta didik aktif pada <strong>{{ $sekolah->nama_sekolah ?: 'SMK Negeri 1 Air Naningan' }}</strong> Tahun Pelajaran {{ $pelayanan->suratKeluar?->tahun_agenda }}/{{ ($pelayanan->suratKeluar?->tahun_agenda ?? 2026) + 1 }}, dan tercatat aktif mengikuti seluruh kegiatan proses belajar mengajar.
      </p>
      <p>
        Surat keterangan ini diterbitkan atas permohonan yang bersangkutan untuk dipergunakan sebagai kelengkapan administrasi: <strong>{{ $pelayanan->keperluan }}</strong>.
      </p>
    @elseif($pelayanan->jenis_pelayanan === 'suket_berkelakuan_baik')
      <p>
        Adalah benar nama tersebut di atas merupakan peserta didik <strong>{{ $sekolah->nama_sekolah ?: 'SMK Negeri 1 Air Naningan' }}</strong> yang selama menempuh pendidikan menunjukkan budi pekerti yang baik, bertingkah laku sopan santun, mematuhi tata tertib sekolah, serta tidak pernah terlibat perbuatan tindak pidana, penyalahgunaan narkoba, ataupun pelanggaran disiplin berat.
      </p>
      <p>
        Surat keterangan ini dibuat dengan sesungguhnya untuk keperluan: <strong>{{ $pelayanan->keperluan }}</strong>.
      </p>
    @elseif($pelayanan->jenis_pelayanan === 'suket_mutasi_keluar')
      <p>
        Sesuai dengan surat permohonan pindah sekolah dari orang tua/wali peserta didik yang bersangkutan, Kepala Sekolah menyatakan <strong>TIDAK BERKEBERATAN</strong> atas kepindahan peserta didik tersebut ke:
      </p>
      <div style="margin: 2px 0 6px 36px; font-weight:700; line-height:1.4;">
        Sekolah Tujuan : {{ $snapshot['sekolah_tujuan'] ?? '-' }}<br>
        Alasan Pindah &nbsp;: {{ $snapshot['alasan_mutasi'] ?? ($pelayanan->keperluan ?: 'Mengikuti domisili orang tua') }}
      </div>
      <p>
        Surat rekomendasi ini diterbitkan untuk dipergunakan dalam pengurusan administrasi perpindahan data pada sistem DAPODIK Kemendikdasmen RI.
      </p>
    @elseif($pelayanan->jenis_pelayanan === 'suket_skl')
      <p>
        Menerangkan bahwa peserta didik tersebut di atas telah mengikuti seluruh rangkaian Ujian Sekolah dan Uji Kompetensi Keahlian (UKK) serta dinyatakan <strong>LULUS</strong> dari satuan pendidikan {{ $sekolah->nama_sekolah ?: 'SMK Negeri 1 Air Naningan' }}.
      </p>
      <p>
        Surat Keterangan Lulus ini berlaku sebagai bukti kelulusan sementara sebelum Ijazah Asli diterbitkan secara definitif oleh Dinas Pendidikan dan Kebudayaan Provinsi Lampung.
      </p>
    @else
      <p>
        Menerangkan bahwa peserta didik tersebut di atas adalah siswa aktif {{ $sekolah->nama_sekolah ?: 'SMK Negeri 1 Air Naningan' }} dan surat ini diberikan guna keperluan: <strong>{{ $pelayanan->keperluan }}</strong>.
      </p>
    @endif

    <p>
      Demikian surat keterangan ini kami buat dengan sebenarnya dan agar dapat dipergunakan sebagaimana mestinya oleh pihak-pihak yang berkepentingan.
    </p>
  </div>
@endsection

@section('ttd')
  {{-- TANDA TANGAN & QR CODE VERIFIKASI RESMI --}}
  <div class="signature-container">
    <div class="signature-box">
      <div class="sign-date">
        Air Naningan, {{ $tglSurat->translatedFormat('d F Y') }}
      </div>
      <div class="sign-title">
        Kepala Sekolah,
      </div>
      {{-- MODE DIGITAL: QR Code Keabsahan & TTE --}}
      <div class="sign-qr-wrap ttd-digital-only">
        @if($qrImage)
          <img src="{{ $qrImage }}" alt="QR Code Keabsahan Surat" />
        @else
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($verifyUrl ?? '#') }}" alt="QR Keabsahan" />
        @endif
      </div>

      {{-- MODE BASAH: Ruang Tanda Tangan & Cap Fisik --}}
      <div class="ttd-basah-only" style="height: 58px; display: none;"></div>

      <div class="sign-name">
        {{ $namaKepsek }}
      </div>
      <div class="sign-nip">
        NIP. {{ $nipKepsek }}
      </div>
    </div>
  </div>
@endsection

@section('footer')
  {{-- FOOTER KEABSAHAN ELEKTRONIK (MODE DIGITAL) --}}
  <div class="security-footer ttd-digital-only">
    <div class="security-qr-small">
      <i class="bi bi-shield-fill-check" style="font-size:26px; color:#0284c7;"></i>
    </div>
    <div>
      <strong>DOKUMEN RESMI TATA NASKAH DINAS ELEKTRONIK (SITUAN SMKN 1 AIR NANINGAN)</strong><br>
      Keaslian dokumen ini dilindungi kode verifikasi kriptografi: <span style="font-family:monospace; color:#0f172a; font-weight:700;">{{ $pelayanan->kode_verifikasi_qr }}</span>.<br>
      Pindai QR-Code di atas atau akses tautan <em>{{ $verifyUrl ?? route('situan.verifikasi-surat', $pelayanan->kode_verifikasi_qr) }}</em> untuk membuktikan keabsahan langsung pada pangkalan data sekolah.
    </div>
  </div>

  {{-- FOOTER PENGESAHAN FISIK (MODE BASAH) --}}
  <div class="ttd-basah-only" style="display:none; font-family:'Plus Jakarta Sans', sans-serif; font-size:8pt; color:#64748b;">
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <span>Dokumen Resmi Tata Usaha · <strong>SITUAN (Sistem Informasi Tata Usaha &amp; Administrasi Terpadu) SMKN 1 Air Naningan</strong></span>
      <span>No. Reg: {{ $pelayanan->suratKeluar?->nomor_surat_lengkap ?? '-' }} · Verifikasi TTD Fisik</span>
    </div>
  </div>
@endsection
