<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $pelayanan->suratKeluar?->nomor_surat_lengkap ?? 'Surat Keterangan' }} — {{ $pelayanan->siswa?->nama }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <style>
    :root {
      --primary: #0284c7;
      --font-body: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
      --font-serif: 'Cinzel', 'Times New Roman', Times, serif;
    }
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      background-color: #f1f5f9;
      font-family: 'Times New Roman', Times, serif;
      color: #000;
      font-size: 11.5pt;
      line-height: 1.5;
      padding: 20px 0;
    }

    /* Screen Action Bar */
    .screen-toolbar {
      width: 210mm;
      margin: 0 auto 16px auto;
      background: #1e293b;
      color: #fff;
      padding: 12px 20px;
      border-radius: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 15px rgba(0,0,0,0.15);
      font-family: var(--font-body);
    }
    .toolbar-info {
      font-size: 13px;
    }
    .btn-action {
      background: #0284c7;
      color: #fff;
      border: none;
      padding: 8px 16px;
      border-radius: 6px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      text-decoration: none;
    }
    .btn-action:hover {
      background: #0369a1;
      color: #fff;
    }
    .btn-outline {
      background: transparent;
      border: 1px solid #64748b;
      color: #cbd5e1;
    }
    .btn-outline:hover {
      background: #334155;
      color: #fff;
    }

    /* A4 Print Sheet */
    .page-sheet {
      width: 210mm;
      min-height: 297mm;
      padding: 20mm 25mm 20mm 25mm;
      margin: 0 auto;
      background: #fff;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
      position: relative;
    }

    /* Kop Surat */
    .kop-container {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 16px;
      margin-bottom: 2px;
      position: relative;
    }
    .kop-logo {
      width: 78px;
      height: 78px;
      object-fit: contain;
    }
    .kop-text {
      text-align: center;
      flex-grow: 1;
    }
    .kop-instansi-1 {
      font-size: 12pt;
      font-weight: 700;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }
    .kop-instansi-2 {
      font-size: 13.5pt;
      font-weight: 700;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }
    .kop-sekolah {
      font-size: 15pt;
      font-weight: 800;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin: 1px 0;
    }
    .kop-alamat {
      font-size: 8.5pt;
      font-family: var(--font-body);
      line-height: 1.35;
      color: #1e293b;
    }

    .kop-divider {
      border: 0;
      border-top: 3px solid #000;
      border-bottom: 1px solid #000;
      height: 5px;
      margin: 8px 0 20px 0;
    }

    /* Title Surat */
    .title-block {
      text-align: center;
      margin-bottom: 20px;
    }
    .surat-title {
      font-size: 13.5pt;
      font-weight: 700;
      text-transform: uppercase;
      text-decoration: underline;
      letter-spacing: 0.5px;
    }
    .surat-nomor {
      font-size: 11pt;
      margin-top: 3px;
    }

    /* Paragraph */
    .surat-body {
      text-align: justify;
      font-size: 11.5pt;
      line-height: 1.6;
    }
    .surat-body p {
      margin-bottom: 12px;
      text-indent: 32px;
    }

    /* Data Table */
    .bio-table {
      width: 100%;
      margin: 12px 0 16px 20px;
      border-collapse: collapse;
      font-size: 11pt;
    }
    .bio-table td {
      padding: 4px 6px;
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

    /* Sign Block */
    .signature-container {
      margin-top: 30px;
      display: flex;
      justify-content: flex-end;
    }
    .signature-box {
      width: 280px;
      text-align: center;
    }
    .sign-date {
      margin-bottom: 6px;
    }
    .sign-title {
      font-weight: 700;
      margin-bottom: 10px;
    }
    .sign-qr-wrap {
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 8px auto;
      width: 90px;
      height: 90px;
    }
    .sign-qr-wrap img {
      width: 90px;
      height: 90px;
    }
    .sign-name {
      font-weight: 700;
      text-decoration: underline;
      font-size: 11.5pt;
      margin-top: 6px;
    }
    .sign-nip {
      font-size: 10pt;
    }

    /* Security Note Footer */
    .security-footer {
      position: absolute;
      bottom: 15mm;
      left: 25mm;
      right: 25mm;
      border-top: 1px dashed #94a3b8;
      padding-top: 8px;
      display: flex;
      align-items: center;
      gap: 12px;
      font-family: var(--font-body);
      font-size: 8pt;
      color: #64748b;
    }
    .security-qr-small {
      width: 40px;
      height: 40px;
      flex-shrink: 0;
    }

    /* Media Print */
    @media print {
      body {
        background: transparent;
        padding: 0;
      }
      .screen-toolbar {
        display: none !important;
      }
      .page-sheet {
        box-shadow: none;
        margin: 0;
        width: 100%;
        min-height: 100%;
        padding: 15mm 20mm;
      }
      @page {
        size: A4 portrait;
        margin: 0;
      }
    }
  </style>
</head>
<body>

  {{-- Screen Toolbar --}}
  <div class="screen-toolbar">
    <div class="toolbar-info">
      <i class="bi bi-file-earmark-check-fill text-info me-1"></i>
      <strong>Pratinjau Cetak Surat Resmi Kesiswaan</strong> &bull; No: <span style="font-family:monospace;">{{ $pelayanan->suratKeluar?->nomor_surat_lengkap }}</span>
    </div>
    <div style="display:flex; gap:10px;">
      <a href="{{ route('situan.pelayanan.index') }}" class="btn-action btn-outline">
        <i class="bi bi-arrow-left"></i> Kembali ke Loket
      </a>
      <button onclick="window.print()" class="btn-action">
        <i class="bi bi-printer-fill"></i> Cetak / Simpan PDF
      </button>
    </div>
  </div>

  {{-- Page A4 --}}
  <div class="page-sheet">
    
    {{-- 1. KOP DINAS RESMI --}}
    <div class="kop-container">
      <img src="{{ $sekolah->logo_url ?: '/img/logo.png' }}" alt="Logo Sekolah" class="kop-logo" />
      <div class="kop-text">
        <div class="kop-instansi-1">{{ $sekolah->nama_instansi_atas ?: 'PEMERINTAH PROVINSI LAMPUNG' }}</div>
        <div class="kop-instansi-2">{{ $sekolah->nama_dinas ?: 'DINAS PENDIDIKAN DAN KEBUDAYAAN' }}</div>
        <div class="kop-sekolah">{{ $sekolah->nama_sekolah ?: 'SMK NEGERI 1 AIR NANINGAN' }}</div>
        <div class="kop-alamat">
          {{ $sekolah->alamat_lengkap ?: 'Jl. Raya Air Naningan No. 01, Kec. Air Naningan, Kab. Tanggamus, Lampung 35379' }}<br>
          NPSN: {{ $sekolah->npsn ?: '69900000' }} &bull; Website: {{ $sekolah->website ?: 'smkn1airnaningan.sch.id' }} &bull; Email: {{ $sekolah->email ?: 'smkn1airnaningan@gmail.sch.id' }}
        </div>
      </div>
    </div>
    <hr class="kop-divider">

    {{-- 2. JUDUL & NOMOR SURAT --}}
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
    @endphp

    <div class="title-block">
      <div class="surat-title">{{ $title }}</div>
      <div class="surat-nomor">Nomor : {{ $pelayanan->suratKeluar?->nomor_surat_lengkap }}</div>
    </div>

    {{-- 3. ISI SURAT --}}
    <div class="surat-body">
      <p>
        Yang bertanda tangan di bawah ini, Kepala {{ $sekolah->nama_sekolah ?: 'SMK Negeri 1 Air Naningan' }}, Kabupaten Tanggamus, Provinsi Lampung, menerangkan dengan sebenarnya bahwa:
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
        <div style="margin: 0 0 10px 45px; font-weight:700;">
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

    {{-- 4. TANDA TANGAN & QR CODE VERIFIKASI RESMI --}}
    <div class="signature-container">
      <div class="signature-box">
        <div class="sign-date">
          Air Naningan, {{ $tglSurat->translatedFormat('d F Y') }}
        </div>
        <div class="sign-title">
          Kepala Sekolah,
        </div>
        <div class="sign-qr-wrap">
          @if($qrImage)
            <img src="{{ $qrImage }}" alt="QR Code Keabsahan Surat" />
          @else
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($verifyUrl ?? '#') }}" alt="QR Keabsahan" />
          @endif
        </div>
        <div class="sign-name">
          {{ $sekolah->nama_kepala_sekolah ?: 'Aprida, S.Si.' }}
        </div>
        <div class="sign-nip">
          NIP. {{ $sekolah->nip_kepala_sekolah ?: '197904172008012019' }}
        </div>
      </div>
    </div>

    {{-- 5. FOOTER KEABSAHAN ELEKTRONIK --}}
    <div class="security-footer">
      <div class="security-qr-small">
        <i class="bi bi-shield-fill-check" style="font-size:32px; color:#0284c7;"></i>
      </div>
      <div>
        <strong>DOKUMEN RESMI TATA NASKAH DINAS ELEKTRONIK (SITUAN SMKN 1 AIR NANINGAN)</strong><br>
        Keaslian dokumen ini dilindungi kode verifikasi kriptografi: <span style="font-family:monospace; color:#0f172a;">{{ $pelayanan->kode_verifikasi_qr }}</span>.<br>
        Pindai QR-Code di atas atau akses tautan <em>{{ $verifyUrl ?? route('situan.verifikasi-surat', $pelayanan->kode_verifikasi_qr) }}</em> untuk membuktikan keabsahan langsung pada pangkalan data sekolah.
      </div>
    </div>

  </div>

</body>
</html>
