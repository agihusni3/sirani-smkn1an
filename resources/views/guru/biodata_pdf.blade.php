<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Biodata GTK - {{ $guru->nama_lengkap_gelar ?: $guru->nama }} - {{ $sekolah->nama_sekolah ?? 'SMKN 1 Air Naningan' }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <style>
    @page {
      size: A4 portrait;
      margin: 12mm 15mm 15mm 15mm;
    }
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      background-color: #334155;
      font-family: 'Times New Roman', Times, serif;
      color: #000000;
      font-size: 10pt;
      line-height: 1.35;
      -webkit-font-smoothing: antialiased;
      margin: 0;
      padding: 20px 0 40px;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      overflow-x: auto;
    }
    .print-actions-bar {
      width: 210mm;
      max-width: 95vw;
      background: #1E293B;
      border: 1px solid #334155;
      border-radius: 12px;
      padding: 12px 18px;
      margin-bottom: 16px;
      box-shadow: 0 10px 25px -5px rgba(0,0,0,0.4);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
      box-sizing: border-box;
      font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .print-title-info {
      color: #FFFFFF;
      font-size: 13.5px;
      font-weight: 800;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .print-btn-group {
      display: flex;
      gap: 8px;
    }
    .btn-action {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 12px;
      font-weight: 700;
      padding: 7px 14px;
      border-radius: 6px;
      border: none;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.2s ease;
    }
    .btn-action-primary {
      background: #2563EB;
      color: #FFFFFF;
    }
    .btn-action-primary:hover {
      background: #1D4ED8;
    }
    .btn-action-secondary {
      background: #475569;
      color: #FFFFFF;
    }
    .btn-action-secondary:hover {
      background: #64748B;
    }
    .page-sheet {
      width: 210mm;
      min-height: 297mm;
      background: #FFFFFF;
      padding: 15mm 16mm 16mm 16mm;
      box-shadow: 0 15px 35px rgba(0,0,0,0.3);
      position: relative;
      box-sizing: border-box;
      margin-bottom: 20px;
    }
    
    /* KOP DINAS RESMI */
    .kop-surat {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 3px double #000000;
      padding-bottom: 8px;
      margin-bottom: 12px;
    }
    .kop-logo {
      width: 70px;
      height: 70px;
      object-fit: contain;
    }
    .kop-teks {
      flex: 1;
      text-align: center;
      padding: 0 10px;
    }
    .kop-instansi-atas {
      font-size: 12pt;
      font-weight: bold;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }
    .kop-dinas {
      font-size: 13pt;
      font-weight: bold;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }
    .kop-sekolah {
      font-size: 15pt;
      font-weight: bold;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      margin-top: 1px;
    }
    .kop-alamat {
      font-size: 8.5pt;
      font-style: italic;
      margin-top: 3px;
      line-height: 1.25;
    }

    /* JUDUL DOKUMEN */
    .doc-header {
      text-align: center;
      margin-bottom: 12px;
    }
    .doc-title {
      font-size: 12.5pt;
      font-weight: bold;
      text-decoration: underline;
      letter-spacing: 0.8px;
      text-transform: uppercase;
    }
    .doc-sub {
      font-size: 9pt;
      font-family: 'Plus Jakarta Sans', sans-serif;
      color: #334155;
      margin-top: 3px;
      font-weight: 600;
    }

    /* SECTION HEADER */
    .section-title {
      font-size: 10pt;
      font-weight: bold;
      background: #F1F5F9;
      border-left: 4px solid #0F172A;
      padding: 4px 8px;
      margin: 10px 0 6px;
      font-family: 'Plus Jakarta Sans', sans-serif;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }

    /* TABEL DATA */
    .table-data {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 6px;
    }
    .table-data td {
      padding: 3.5px 4px;
      vertical-align: top;
      font-size: 9.5pt;
    }
    .td-label {
      width: 28%;
      color: #000;
    }
    .td-colon {
      width: 2%;
      text-align: center;
    }
    .td-value {
      width: 70%;
      font-weight: bold;
    }

    /* FOTO & IDENTITAS CONTAINER */
    .identitas-container {
      display: flex;
      gap: 14px;
      align-items: flex-start;
    }
    .identitas-table {
      flex: 1;
    }
    .identitas-photo-box {
      width: 110px;
      text-align: center;
      padding: 5px;
      background: #F8FAFC;
      border: 1px solid #CBD5E1;
      border-radius: 4px;
    }
    .identitas-photo {
      width: 100px;
      height: 130px;
      object-fit: cover;
      border-radius: 2px;
      border: 1px solid #94A3B8;
      display: block;
      margin: 0 auto;
    }
    .photo-caption {
      font-size: 7.5pt;
      font-family: 'Plus Jakarta Sans', sans-serif;
      color: #475569;
      margin-top: 4px;
      font-weight: 700;
      text-transform: uppercase;
    }

    /* TABEL SERTIFIKAT */
    .table-sertifikat {
      width: 100%;
      border-collapse: collapse;
      margin-top: 4px;
    }
    .table-sertifikat th, .table-sertifikat td {
      border: 1px solid #000000;
      padding: 4px 6px;
      font-size: 8.5pt;
    }
    .table-sertifikat th {
      background: #E2E8F0;
      font-weight: bold;
      text-align: center;
      font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* TANDA TANGAN */
    .ttd-section {
      margin-top: 18px;
      display: flex;
      justify-content: space-between;
      page-break-inside: avoid;
    }
    .ttd-box {
      width: 45%;
      text-align: center;
      font-size: 9.5pt;
    }
    .ttd-space {
      height: 55px;
    }
    .ttd-name {
      font-weight: bold;
      text-decoration: underline;
    }
    .ttd-nip {
      font-size: 8.5pt;
      margin-top: 2px;
    }

    .doc-footer {
      margin-top: 15px;
      padding-top: 6px;
      border-top: 1px dashed #94A3B8;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 7.5pt;
      font-family: 'Plus Jakarta Sans', sans-serif;
      color: #64748B;
    }

    @media print {
      body {
        background: transparent;
        padding: 0;
      }
      .print-actions-bar {
        display: none !important;
      }
      .page-sheet {
        width: 100%;
        min-height: auto;
        padding: 0;
        box-shadow: none;
        margin: 0;
      }
    }
  </style>
</head>
<body>

  <!-- BAR AKSI CETAK -->
  <div class="print-actions-bar">
    <div class="print-title-info">
      <i class="bi bi-person-badge-fill" style="font-size:18px; color:#38BDF8;"></i>
      <span>Lembar Biodata PTK Resmi: {{ $guru->nama_lengkap_gelar ?: $guru->nama }}</span>
    </div>
    <div class="print-btn-group">
      <a href="{{ route('guru.index') }}" class="btn-action btn-action-secondary">
        <i class="bi bi-arrow-left"></i> Kembali ke Data Guru
      </a>
      <button onclick="window.print()" class="btn-action btn-action-primary">
        <i class="bi bi-printer-fill"></i> Cetak / Simpan PDF
      </button>
    </div>
  </div>

  <!-- LEMBAR DOKUMEN A4 -->
  <div class="page-sheet">

    <!-- KOP RESMI -->
    <div class="kop-surat">
      <img src="{{ $sekolah && $sekolah->logo_sekolah ? asset('storage/' . $sekolah->logo_sekolah) : asset('images/logo_provinsi_lampung.png') }}" 
           alt="Logo Instansi" class="kop-logo" onerror="this.src='{{ asset('images/logo_smk.png') }}';">
      <div class="kop-teks">
        <div class="kop-instansi-atas">{{ $sekolah->nama_instansi_atas ?? 'PEMERINTAH PROVINSI LAMPUNG' }}</div>
        <div class="kop-dinas">{{ $sekolah->nama_dinas ?? 'DINAS PENDIDIKAN DAN KEBUDAYAAN' }}</div>
        <div class="kop-sekolah">{{ $sekolah->nama_sekolah ?? 'SMK NEGERI 1 AIR NANINGAN' }}</div>
        <div class="kop-alamat">
          {{ $sekolah->alamat_lengkap ?? 'Jl. Raya Air Naningan, Kec. Air Naningan, Kab. Tanggamus, Lampung 35379' }}<br>
          Website: {{ $sekolah->website ?? 'smkn1airnaningan.sch.id' }} | Email: {{ $sekolah->email ?? 'smkn1airnaningan@gmail.com' }}
        </div>
      </div>
      <img src="{{ asset('images/logo_smk.png') }}" alt="Logo SMK" class="kop-logo" onerror="this.style.opacity='0';">
    </div>

    <!-- JUDUL DOKUMEN -->
    <div class="doc-header">
      <div class="doc-title">BIODATA PENDIDIK DAN TENAGA KEPENDIDIKAN (GTK)</div>
      <div class="doc-sub">Format Resmi Pangkalan Data Sekolah &amp; Layanan Terpadu SIM-GTK</div>
    </div>

    <!-- BAGIAN A: IDENTITAS PRIBADI -->
    <div class="section-title">A. IDENTITAS PRIBADI</div>
    <div class="identitas-container">
      <div class="identitas-table">
        <table class="table-data">
          <tr>
            <td class="td-label">1. Nama Lengkap</td>
            <td class="td-colon">:</td>
            <td class="td-value">{{ $guru->nama_lengkap_gelar ?: $guru->nama }}</td>
          </tr>
          <tr>
            <td class="td-label">2. Nomor Induk Kependudukan (NIK)</td>
            <td class="td-colon">:</td>
            <td class="td-value">{{ $guru->nik ?: '-' }}</td>
          </tr>
          <tr>
            <td class="td-label">3. Tempat, Tanggal Lahir</td>
            <td class="td-colon">:</td>
            <td class="td-value">
              {{ $guru->tempat_lahir ?: '-' }}, {{ $guru->tanggal_lahir ? \Carbon\Carbon::parse($guru->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
            </td>
          </tr>
          <tr>
            <td class="td-label">4. Jenis Kelamin</td>
            <td class="td-colon">:</td>
            <td class="td-value">{{ $guru->jenis_kelamin === 'L' ? 'Laki-laki' : ($guru->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</td>
          </tr>
          <tr>
            <td class="td-label">5. Agama</td>
            <td class="td-colon">:</td>
            <td class="td-value">{{ $guru->agama ?: 'Islam' }}</td>
          </tr>
          <tr>
            <td class="td-label">6. Alamat Tempat Tinggal</td>
            <td class="td-colon">:</td>
            <td class="td-value">{{ $guru->alamat ?: 'Kecamatan Air Naningan, Kabupaten Tanggamus' }}</td>
          </tr>
          <tr>
            <td class="td-label">7. Nomor Telepon / WhatsApp</td>
            <td class="td-colon">:</td>
            <td class="td-value">{{ $guru->no_hp ?: '-' }}</td>
          </tr>
          <tr>
            <td class="td-label">8. Status Keaktifan</td>
            <td class="td-colon">:</td>
            <td class="td-value" style="color:{{ $guru->status === 'aktif' ? '#059669' : '#dc2626' }};">
              {{ strtoupper($guru->status ?: 'AKTIF') }}
            </td>
          </tr>
        </table>
      </div>

      <div class="identitas-photo-box">
        <img src="{{ $guru->foto_url }}" alt="Foto GTK" class="identitas-photo" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($guru->nama) }}&size=200';">
        <div class="photo-caption">{{ $guru->label_kepegawaian }}</div>
      </div>
    </div>

    <!-- BAGIAN B: KEPEGAWAIAN & LEGALITAS -->
    <div class="section-title">B. KEPEGAWAIAN &amp; LEGALITAS</div>
    <table class="table-data">
      <tr>
        <td class="td-label">1. Nomor Induk Pegawai (NIP / NI PPPK)</td>
        <td class="td-colon">:</td>
        <td class="td-value">{{ $guru->nip ?: 'Belum Ada / Non-ASN' }}</td>
      </tr>
      <tr>
        <td class="td-label">2. NUPTK (Pendidik)</td>
        <td class="td-colon">:</td>
        <td class="td-value">{{ $guru->nuptk ?: 'Belum Memiliki NUPTK' }}</td>
      </tr>
      <tr>
        <td class="td-label">3. Status Kepegawaian</td>
        <td class="td-colon">:</td>
        <td class="td-value">{{ $guru->label_kepegawaian }}</td>
      </tr>
      <tr>
        <td class="td-label">4. Pangkat / Golongan Ruang</td>
        <td class="td-colon">:</td>
        <td class="td-value">{{ $guru->golongan_pangkat ?: '-' }}</td>
      </tr>
      <tr>
        <td class="td-label">5. Nomor SK Pengangkatan</td>
        <td class="td-colon">:</td>
        <td class="td-value">{{ $guru->nomor_sk_pengangkatan ?: '-' }}</td>
      </tr>
      <tr>
        <td class="td-label">6. Terhitung Mulai Tanggal (TMT) Kerja</td>
        <td class="td-colon">:</td>
        <td class="td-value">{{ $guru->tmt_kerja ? \Carbon\Carbon::parse($guru->tmt_kerja)->translatedFormat('d F Y') : '-' }}</td>
      </tr>
      <tr>
        <td class="td-label">7. Lembaga Pengangkat</td>
        <td class="td-colon">:</td>
        <td class="td-value">{{ $guru->lembaga_pengangkat ?: 'Pemerintah Provinsi Lampung / Dinas Pendidikan' }}</td>
      </tr>
    </table>

    <!-- BAGIAN C: KUALIFIKASI PENDIDIKAN & SERTIFIKASI -->
    <div class="section-title">C. KUALIFIKASI AKADEMIK &amp; SERTIFIKASI</div>
    <table class="table-data">
      <tr>
        <td class="td-label">1. Pendidikan Terakhir</td>
        <td class="td-colon">:</td>
        <td class="td-value">{{ $guru->pendidikan_terakhir ?: 'S1' }}</td>
      </tr>
      <tr>
        <td class="td-label">2. Program Studi / Jurusan Kuliah</td>
        <td class="td-colon">:</td>
        <td class="td-value">{{ $guru->jurusan_kuliah ?: '-' }}</td>
      </tr>
      <tr>
        <td class="td-label">3. Nama Perguruan Tinggi / Kampus</td>
        <td class="td-colon">:</td>
        <td class="td-value">{{ $guru->kampus ?: '-' }}</td>
      </tr>
      <tr>
        <td class="td-label">4. Tahun Kelulusan</td>
        <td class="td-colon">:</td>
        <td class="td-value">{{ $guru->tahun_lulus ?: '-' }}</td>
      </tr>
      <tr>
        <td class="td-label">5. Status Sertifikasi Pendidik</td>
        <td class="td-colon">:</td>
        <td class="td-value">
          @if($guru->status_sertifikasi === 'sudah')
            <span style="color:#059669;">SUDAH BERSERTIFIKASI PENDIDIK</span>
          @else
            <span>BELUM SERTIFIKASI</span>
          @endif
        </td>
      </tr>
      <tr>
        <td class="td-label">6. Nomor Sertifikat Pendidik / Serdik</td>
        <td class="td-colon">:</td>
        <td class="td-value">{{ $guru->nomor_serdik ?: '-' }}</td>
      </tr>
    </table>

    <!-- BAGIAN D: PENUGASAN, MAPEL & BEBAN KERJA -->
    <div class="section-title">D. PENUGASAN, MAPEL &amp; BEBAN KERJA (JJM)</div>
    <table class="table-data">
      <tr>
        <td class="td-label">1. Jabatan Utama / Jenis PTK</td>
        <td class="td-colon">:</td>
        <td class="td-value">{{ $guru->jabatan }} {{ $guru->jenis_ptk && $guru->jenis_ptk !== $guru->jabatan ? '(' . $guru->jenis_ptk . ')' : '' }}</td>
      </tr>
      <tr>
        <td class="td-label">2. Mata Pelajaran yang Diampu</td>
        <td class="td-colon">:</td>
        <td class="td-value">
          @if(!empty($guru->list_mapel))
            {{ implode(', ', $guru->list_mapel) }}
          @elseif($guru->mapel_diampu)
            {{ $guru->mapel_diampu }}
          @else
            -
          @endif
        </td>
      </tr>
      <tr>
        <td class="td-label">3. Total Beban Jam Mengajar (JJM)</td>
        <td class="td-colon">:</td>
        <td class="td-value">{{ $guru->jjm ? $guru->jjm . ' Jam Pelajaran (JP) / Minggu' : '-' }}</td>
      </tr>
      <tr>
        <td class="td-label">4. Tugas Tambahan di Sekolah</td>
        <td class="td-colon">:</td>
        <td class="td-value">
          @if(!empty($guru->list_tugas_tambahan))
            {{ implode(', ', $guru->list_tugas_tambahan) }}
          @else
            Tidak Ada Tugas Tambahan
          @endif
        </td>
      </tr>
      <tr>
        <td class="td-label">5. SK Beban Kerja Mengajar &amp; JJM</td>
        <td class="td-colon">:</td>
        <td class="td-value">{{ $guru->sk_tugas_tambahan ?: '-' }}</td>
      </tr>
      <tr>
        <td class="td-label">6. Hari Aktif Mengajar Mingguan</td>
        <td class="td-colon">:</td>
        <td class="td-value">
          @if(!empty($guru->hari_mengajar))
            {{ implode(', ', array_map('ucfirst', (array)$guru->hari_mengajar)) }}
          @else
            Senin s.d. Jumat (Jadwal Penuh)
          @endif
        </td>
      </tr>
    </table>

    <!-- BAGIAN E: PORTOFOLIO SERTIFIKAT PELATIHAN -->
    <div class="section-title">E. RIWAYAT PORTOFOLIO SERTIFIKAT PELATIHAN ({{ $guru->sertifikats->count() }})</div>
    @if($guru->sertifikats->count() > 0)
      <table class="table-sertifikat">
        <thead>
          <tr>
            <th style="width:5%;">No</th>
            <th style="width:38%;">Nama Pelatihan / Diklat / Bimtek</th>
            <th style="width:27%;">Lembaga Penyelenggara</th>
            <th style="width:10%;">Tahun</th>
            <th style="width:20%;">Nomor Sertifikat</th>
          </tr>
        </thead>
        <tbody>
          @foreach($guru->sertifikats as $idx => $s)
            <tr>
              <td style="text-align:center;">{{ $idx + 1 }}</td>
              <td><strong>{{ $s->nama_pelatihan }}</strong></td>
              <td>{{ $s->penyelenggara }}</td>
              <td style="text-align:center;">{{ $s->tahun }}</td>
              <td>{{ $s->nomor_sertifikat ?: '-' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @else
      <div style="font-size:9pt; font-style:italic; color:#64748B; padding:6px 0;">
        Belum ada riwayat sertifikat pelatihan yang diunggah ke dalam portofolio GTK.
      </div>
    @endif

    <!-- TANDA TANGAN & PENGESAHAN -->
    <div class="ttd-section">
      <div class="ttd-box">
        <div>Pegawai / Pendidik yang Bersangkutan,</div>
        <div class="ttd-space"></div>
        <div class="ttd-name">{{ $guru->nama_lengkap_gelar ?: $guru->nama }}</div>
        <div class="ttd-nip">{{ $guru->nip ? 'NIP. ' . $guru->nip : ($guru->nuptk ? 'NUPTK. ' . $guru->nuptk : 'NIK. ' . ($guru->nik ?: '-')) }}</div>
      </div>

      <div class="ttd-box">
        <div>Air Naningan, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
        <div>Mengetahui,</div>
        <div style="font-weight:bold;">Kepala Sekolah</div>
        <div class="ttd-space"></div>
        <div class="ttd-name">{{ $sekolah->nama_kepala_sekolah ?? 'Aprida, S.Si.' }}</div>
        <div class="ttd-nip">{{ $sekolah->nip_kepala_sekolah ? 'NIP. ' . $sekolah->nip_kepala_sekolah : '-' }}</div>
      </div>
    </div>

    <!-- FOOTER INFORMASI -->
    <div class="doc-footer">
      <div>Dicetak melalui Sistem Informasi Presensi &amp; Layanan Akademik (SIRANI) SMKN 1 Air Naningan</div>
      <div>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }} WIB</div>
    </div>

  </div>

</body>
</html>
