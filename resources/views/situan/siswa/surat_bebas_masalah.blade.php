@extends('layouts.dokumen_a4')

@php
  $backUrl = route('siswa.index');
  $backLabel = 'Kembali ke Data Siswa';
@endphp

@section('title', 'Surat Keterangan Bebas Masalah — ' . $siswa->nama)
@section('toolbar_title', 'Surat Keterangan Bebas Masalah & Resume Presensi — ' . $siswa->nama)

@push('styles')
<style>
  .surat-title-block {
    text-align: center;
    margin-bottom: 12px;
  }
  .surat-main-title {
    font-size: 13pt;
    font-weight: 800;
    text-transform: uppercase;
    text-decoration: underline;
    letter-spacing: 0.5px;
  }
  .surat-nomor {
    font-size: 10.5pt;
    font-weight: 700;
    margin-top: 2px;
    font-family: 'JetBrains Mono', monospace;
  }

  .surat-paragraph {
    text-align: justify;
    margin-bottom: 8px;
    line-height: 1.35;
    font-size: 10.5pt;
  }

  .bio-table {
    width: 100%;
    margin: 6px 0 10px;
    border-collapse: collapse;
  }
  .bio-table td {
    padding: 2px 4px;
    vertical-align: top;
    font-size: 10.5pt;
  }
  .bio-label {
    width: 190px;
    font-weight: 600;
  }
  .bio-sep {
    width: 15px;
    text-align: center;
  }
  .bio-val {
    font-weight: 700;
  }

  .rekap-box {
    border: 1.5px solid #000000;
    border-radius: 4px;
    padding: 8px 10px;
    margin: 8px 0 12px;
    background: #FAFAFA;
  }
  .rekap-box-title {
    font-size: 10pt;
    font-weight: 800;
    text-transform: uppercase;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .grid-stats {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 6px;
    text-align: center;
  }
  .stat-item {
    background: #FFFFFF;
    border: 1px solid #CCCCCC;
    padding: 4px 2px;
    border-radius: 4px;
  }
  .stat-item-val {
    font-size: 12pt;
    font-weight: 800;
    font-family: 'JetBrains Mono', monospace;
  }
  .stat-item-lbl {
    font-size: 7.5pt;
    font-weight: 700;
    text-transform: uppercase;
    color: #333333;
  }

  .statement-box {
    border: 2px solid #16A34A;
    background: #F0FDF4;
    padding: 8px 12px;
    border-radius: 6px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .statement-box.has-kasus {
    border-color: #DC2626;
    background: #FEF2F2;
  }
  .statement-text {
    font-size: 10pt;
    font-weight: 700;
    line-height: 1.3;
  }

  .sig-section {
    margin-top: 10px;
  }
  .sig-date-row {
    text-align: right;
    font-size: 10.5pt;
    margin-bottom: 6px;
  }
  .sig-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px 20px;
    text-align: center;
    font-size: 10pt;
  }
  .sig-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    min-height: 75px;
  }
  .sig-name {
    font-weight: 800;
    text-decoration: underline;
  }
  .sig-nip {
    font-size: 9pt;
  }
</style>
@endpush

@section('content')
  {{-- 2. JUDUL SURAT RESMI --}}
  <div class="surat-title-block">
    <div class="surat-main-title">SURAT KETERANGAN BEBAS KASUS DISIPLIN &amp; RESUME PRESENSI</div>
    <div class="surat-nomor">Nomor: {{ $nomorSurat }}</div>
  </div>

  {{-- 3. PEMBUKA SURAT --}}
  <p class="surat-paragraph">
    Yang bertanda tangan di bawah ini, Tim Bimbingan Konseling dan Kesiswaan <strong>{{ $sekolah->nama_sekolah ?: 'SMK Negeri 1 Air Naningan' }}</strong>, menerangkan dengan sebenarnya bahwa peserta didik:
  </p>

  {{-- 4. BIODATA PESERTA DIDIK --}}
  <table class="bio-table">
    <tr>
      <td class="bio-label">Nama Lengkap Siswa</td>
      <td class="bio-sep">:</td>
      <td class="bio-val" style="font-size:11pt; text-transform:uppercase;">{{ $siswa->nama }}</td>
    </tr>
    <tr>
      <td class="bio-label">Nomor Induk Siswa Nasional (NISN)</td>
      <td class="bio-sep">:</td>
      <td class="bio-val">{{ $siswa->nisn ?: '-' }}</td>
    </tr>
    <tr>
      <td class="bio-label">Nomor Induk Siswa (NIS)</td>
      <td class="bio-sep">:</td>
      <td class="bio-val">{{ $siswa->nis ?: '-' }}</td>
    </tr>
    <tr>
      <td class="bio-label">Rombel / Tingkat Terakhir</td>
      <td class="bio-sep">:</td>
      <td class="bio-val">{{ $rombelAktif ? $rombelAktif->nama_rombel : 'Alumni / Tingkat XII' }}{{ !empty($rombelAktif?->jurusan?->nama_jurusan) ? ' (' . $rombelAktif->jurusan->nama_jurusan . ')' : '' }}</td>
    </tr>
    <tr>
      <td class="bio-label">Tahun Ajaran Kelulusan</td>
      <td class="bio-sep">:</td>
      <td class="bio-val">{{ $sekolah->tahun_ajaran_aktif ?? '2026/2027' }}</td>
    </tr>
  </table>

  {{-- 5. RESUME PRESENSI 3 TAHUN --}}
  <div class="rekap-box">
    <div class="rekap-box-title">
      <i class="bi bi-calendar-check-fill" style="color:#0284c7;"></i>
      <span>Resume Rekapitulasi Presensi &amp; Disiplin Selama Pendidikan (3 Tahun)</span>
    </div>
    <div class="grid-stats">
      <div class="stat-item">
        <div class="stat-item-val" style="color:#16A34A;">{{ $stats['hadir'] ?? 0 }}</div>
        <div class="stat-item-lbl">Hadir Tepat</div>
      </div>
      <div class="stat-item">
        <div class="stat-item-val" style="color:#CA8A04;">{{ $stats['terlambat'] ?? 0 }}</div>
        <div class="stat-item-lbl">Terlambat</div>
      </div>
      <div class="stat-item">
        <div class="stat-item-val" style="color:#2563EB;">{{ $stats['izin'] ?? 0 }}</div>
        <div class="stat-item-lbl">Izin Sah</div>
      </div>
      <div class="stat-item">
        <div class="stat-item-val" style="color:#9333EA;">{{ $stats['sakit'] ?? 0 }}</div>
        <div class="stat-item-lbl">Sakit (S)</div>
      </div>
      <div class="stat-item">
        <div class="stat-item-val" style="color:#DC2626;">{{ $stats['alpha'] ?? 0 }}</div>
        <div class="stat-item-lbl">Alpha (A)</div>
      </div>
      <div class="stat-item">
        <div class="stat-item-val" style="color:#B91C1C;">{{ $stats['bolos'] ?? 0 }}</div>
        <div class="stat-item-lbl">Bolos (B)</div>
      </div>
    </div>
  </div>

  {{-- 6. KETERANGAN BEBAS KASUS DISIPLIN --}}
  @if($isBebasMasalah)
    <div class="statement-box">
      <div style="font-size:24px; color:#16A34A;"><i class="bi bi-patch-check-fill"></i></div>
      <div class="statement-text" style="color:#14532D;">
        BERSIH &amp; BEBAS TANGGUNGAN KASUS DISIPLIN.<br />
        <span style="font-weight:400; font-size:9pt; color:#166534;">
          Siswa yang bersangkutan selama menempuh pendidikan di SMK Negeri 1 Air Naningan menunjukkan sikap, integritas, dan perilaku yang BAIK, serta TIDAK MEMILIKI TANGGUNGAN SANKSI PEMBINAAN KESISWAAN.
        </span>
      </div>
    </div>
  @else
    <div class="statement-box has-kasus">
      <div style="font-size:24px; color:#DC2626;"><i class="bi bi-exclamation-octagon-fill"></i></div>
      <div class="statement-text" style="color:#7F1D1D;">
        MASIH MEMILIKI {{ $kasusAktif }} TANGGUNGAN KASUS PEMBINAAN AKTIF.<br />
        <span style="font-weight:400; font-size:9pt; color:#991B1B;">
          Siswa wajib menyelesaikan proses pembinaan dengan Guru BK / Wali Kelas sebelum surat bebas tanggungan disahkan.
        </span>
      </div>
    </div>
  @endif

  <p class="surat-paragraph">
    Demikian Surat Keterangan Bebas Masalah &amp; Resume Presensi ini diterbitkan dengan sebenarnya untuk dipergunakan sebagaimana mestinya sebagai syarat pengambilan ijazah, dokumen kelulusan, ataupun lampiran kelengkapan melamar pekerjaan di Dunia Usaha / Dunia Industri (DU/DI).
  </p>
@endsection

@section('ttd')
  {{-- 7. PENGESAHAN 4 PIHAK RESMI --}}
  <div class="sig-section">
    <div class="sig-date-row">
      Air Naningan, {{ $tanggalSurat }}
    </div>

    <div class="sig-grid">
      {{-- Kolom 1: Wali Kelas --}}
      <div class="sig-box">
        <div>Wali Kelas,</div>
        <div>
          <div class="sig-name">{{ $waliKelas->nama ?? '................................................' }}</div>
          <div class="sig-nip">NIP. {{ $waliKelas->nip ?? '................................' }}</div>
        </div>
      </div>

      {{-- Kolom 2: Guru BK / Konselor --}}
      <div class="sig-box">
        <div>Guru Bimbingan Konseling (BK),</div>
        <div>
          <div class="sig-name">................................................</div>
          <div class="sig-nip">NIP. ................................</div>
        </div>
      </div>

      {{-- Kolom 3: Waka Kesiswaan --}}
      <div class="sig-box">
        <div>Waka Bidang Kesiswaan,</div>
        <div>
          <div class="sig-name">................................................</div>
          <div class="sig-nip">NIP. ................................</div>
        </div>
      </div>

      {{-- Kolom 4: Mengetahui Kepala Sekolah --}}
      <div class="sig-box">
        <div>Mengetahui,<br />Kepala SMKN 1 Air Naningan</div>
        <div>
          <div class="sig-name">{{ $sekolah->nama_kepala_sekolah ?: 'Aprida, S.Si.' }}</div>
          <div class="sig-nip">NIP. {{ $sekolah->nip_kepala_sekolah ?: '197904172008012019' }}</div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('footer')
  <div style="display:flex; justify-content:space-between; font-size:8pt; color:#64748b;">
    <div>Dokumen Resmi Tata Usaha &middot; SITUAN (Sistem Informasi Tata Usaha &amp; Administrasi Terpadu) SMKN 1 Air Naningan</div>
    <div>Tanggal Terbit: {{ date('d/m/Y H:i') }} WIB | ID: {{ $nomorSurat }}</div>
  </div>
@endsection
