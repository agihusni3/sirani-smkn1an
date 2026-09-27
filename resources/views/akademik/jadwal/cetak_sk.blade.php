<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SK PEMBAGIAN TUGAS MENGAJAR - SMKN 1 AIR NANINGAN - T.A {{ $ta?->tahun_ajaran ?? '2025/2026' }}</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    @page {
      size: portrait;
      margin: 15mm 15mm;
    }
    * { box-sizing: border-box; font-family: "Times New Roman", Times, serif; }
    body {
      margin: 0;
      padding: 20px;
      font-size: 12pt;
      line-height: 1.35;
      color: #000;
      background: #fff;
    }
    .no-print {
      margin-bottom: 20px;
      padding: 12px 18px;
      background: #f8fafc;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-family: Arial, sans-serif;
    }
    @media print {
      .no-print { display: none !important; }
      body { padding: 0; }
      .page-break { page-break-before: always; }
    }
    .btn-action {
      background: #2563eb;
      color: #fff;
      padding: 8px 16px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 700;
      font-size: 13px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      cursor: pointer;
      border: none;
      font-family: Arial, sans-serif;
    }
    .btn-secondary { background: #64748b; }

    /* Kop Surat Resmi */
    .kop-surat {
      display: flex;
      align-items: center;
      border-bottom: 3px double #000;
      padding-bottom: 8px;
      margin-bottom: 16px;
    }
    .kop-logo {
      width: 75px;
      height: 90px;
      object-fit: contain;
      margin-right: 15px;
    }
    .kop-teks {
      flex: 1;
      text-align: center;
    }
    .kop-instansi { font-size: 13pt; font-weight: 700; letter-spacing: 1px; }
    .kop-dinas { font-size: 14pt; font-weight: 800; letter-spacing: 1px; }
    .kop-sekolah { font-size: 16pt; font-weight: 900; letter-spacing: 1.5px; }
    .kop-alamat { font-size: 9.5pt; font-style: italic; margin-top: 3px; font-family: Arial, sans-serif; }

    .judul-sk {
      text-align: center;
      margin-top: 14px;
      margin-bottom: 18px;
    }
    .judul-sk h3 {
      font-size: 12pt;
      font-weight: 800;
      margin: 0;
      text-transform: uppercase;
      text-decoration: underline;
    }
    .judul-sk .nomor-sk {
      font-size: 11pt;
      margin-top: 3px;
    }
    .judul-sk .tentang {
      font-size: 11pt;
      font-weight: 800;
      margin-top: 8px;
      text-transform: uppercase;
    }

    .tabel-sk {
      width: 100%;
      border-collapse: collapse;
      font-size: 9.5pt;
      margin-top: 12px;
      margin-bottom: 14px;
    }
    .tabel-sk th, .tabel-sk td {
      border: 1px solid #000;
      padding: 4px 6px;
      vertical-align: middle;
    }
    .tabel-sk th {
      background: #f1f5f9;
      font-weight: 800;
      text-align: center;
      font-size: 9.5pt;
    }
    @media print {
      .tabel-sk th { background: #e2e8f0 !important; -webkit-print-color-adjust: exact; }
    }

    .tanda-tangan {
      width: 100%;
      margin-top: 24px;
      display: flex;
      justify-content: flex-end;
    }
    .ttd-box {
      width: 250px;
      text-align: left;
      font-size: 11pt;
    }
  </style>
</head>
<body>

  {{-- Tombol Navigasi Cetak --}}
  <div class="no-print">
    <div style="font-weight:700; color:#0f172a; font-size:14px;">
      <i class="bi bi-file-earmark-text-fill text-primary me-2"></i> SK Pembagian Tugas Mengajar &amp; Tugas Tambahan Guru
    </div>
    <div style="display:flex; gap:8px;">
      <a href="{{ route('akademik.jadwal.index', ['tab' => 'distribusi', 'semester' => $semester]) }}" class="btn-action btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali ke Sistem
      </a>
      <button onclick="window.print()" class="btn-action">
        <i class="bi bi-printer-fill"></i> Cetak / Simpan PDF
      </button>
    </div>
  </div>

  {{-- Kop Surat Resmi --}}
  <div class="kop-surat">
    @if(!empty($sekolah->logo_provinsi))
      <img src="{{ asset('storage/' . $sekolah->logo_provinsi) }}" class="kop-logo" alt="Logo Provinsi">
    @else
      <div style="width:75px; text-align:center;"><i class="bi bi-mortarboard-fill" style="font-size:45px;"></i></div>
    @endif
    <div class="kop-teks">
      <div class="kop-instansi">{{ $sekolah->nama_instansi_atas ?? 'PEMERINTAH PROVINSI LAMPUNG' }}</div>
      <div class="kop-dinas">{{ $sekolah->nama_dinas ?? 'DINAS PENDIDIKAN DAN KEBUDAYAAN' }}</div>
      <div class="kop-sekolah">{{ $sekolah->nama_sekolah ?? 'SMK NEGERI 1 AIR NANINGAN' }}</div>
      <div class="kop-alamat">
        {{ $sekolah->alamat ?? 'Jl. Makam Baturuguk, Pekon Karang Sari' }}, Kec. {{ $sekolah->kecamatan ?? 'Air Naningan' }}, Kab. {{ $sekolah->kabupaten ?? 'Tanggamus' }} - Lampung
        @if(!empty($sekolah->website)) · Web: {{ $sekolah->website }} @endif
        @if(!empty($sekolah->email)) · Email: {{ $sekolah->email }} @endif
      </div>
    </div>
  </div>

  {{-- Judul SK --}}
  <div class="judul-sk">
    <h3>KEPUTUSAN KEPALA SEKOLAH MENENGAH KEJURUAN NEGERI 1 AIR NANINGAN</h3>
    <div class="nomor-sk">Nomor : 800 / &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; / V.01 / DP.4 / {{ date('Y') }}</div>
    <div class="tentang">
      TENTANG<br>
      PEMBAGIAN TUGAS GURU DALAM PROSES BELAJAR MENGAJAR DAN TUGAS TAMBAHAN<br>
      TAHUN PELAJARAN {{ $ta?->tahun_ajaran ?? '2025/2026' }} SEMESTER {{ $semester == 1 ? 'GANJIL' : 'GENAP' }}
    </div>
  </div>

  {{-- Isi Pembukaan SK Ringkas --}}
  <div style="font-size:10.5pt; text-align:justify; margin-bottom:12px;">
    <b>Menimbang :</b> Bahwa dalam rangka memperlancar jalannya proses belajar mengajar di SMK Negeri 1 Air Naningan pada Tahun Pelajaran {{ $ta?->tahun_ajaran ?? '2025/2026' }}, perlu menetapkan pembagian tugas guru dan tugas tambahan lainnya dalam suatu Surat Keputusan Kepala Sekolah.<br>
    <b>Mengingat :</b> 1. Undang-Undang Nomor 20 Tahun 2003 tentang Sistem Pendidikan Nasional; 2. Undang-Undang Nomor 14 Tahun 2005 tentang Guru dan Dosen; 3. Permendikbud Nomor 15 Tahun 2018 tentang Pemenuhan Beban Kerja Guru, Kepala Sekolah, dan Pengawas Sekolah; 4. Kepmendikbudristek No. 262/M/2022 tentang Pedoman Penerapan Kurikulum SMK.
  </div>

  <div style="text-align:center; font-weight:800; font-size:11pt; margin:10px 0;">MEMUTUSKAN</div>

  <div style="font-size:10.5pt; text-align:justify; margin-bottom:14px;">
    <b>Menetapkan :</b><br>
    <b>Pertama :</b> Pembagian Tugas Mengajar Guru Tahun Pelajaran {{ $ta?->tahun_ajaran ?? '2025/2026' }} Semester {{ $semester == 1 ? 'Ganjil' : 'Genap' }} sebagaimana tercantum dalam Lampiran I Keputusan ini.<br>
    <b>Kedua :</b> Menugaskan guru untuk melaksanakan tugas tambahan (Wali Kelas, Kepala Lab/Bengkel, Pembina OSIS/Ekstrakurikuler) sebagaimana tercantum dalam Lampiran II Keputusan ini.<br>
    <b>Ketiga :</b> Masing-masing guru wajib melaporkan pelaksanaan tugasnya secara tertulis dan berkala kepada Kepala Sekolah melalui Wakil Kepala Sekolah Bidang Kurikulum.<br>
    <b>Keempat :</b> Keputusan ini berlaku sejak tanggal ditetapkan.
  </div>

  {{-- Tanda Tangan Halaman Muka SK --}}
  <div class="tanda-tangan">
    <div class="ttd-box">
      Ditetapkan di : Air Naningan<br>
      Pada tanggal : {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br><br>
      <b>Kepala SMK Negeri 1 Air Naningan,</b>
      <br><br><br><br>
      <b><u>{{ $sekolah->nama_kepala_sekolah ?? 'Aprida, S.Si.' }}</u></b><br>
      NIP. {{ $sekolah->nip_kepala_sekolah ?? '197904172008012019' }}
    </div>
  </div>

  {{-- Halaman Baru: Lampiran I --}}
  <div class="page-break"></div>

  <div style="font-size:10pt; font-weight:700; margin-bottom:4px;">
    LAMPIRAN I : KEPUTUSAN KEPALA SMKN 1 AIR NANINGAN
  </div>
  <div style="font-size:9.5pt; margin-bottom:8px;">
    NOMOR : 800 / &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; / V.01 / DP.4 / {{ date('Y') }}<br>
    TENTANG : REKAPITULASI PEMBAGIAN BEBAN MENGAJAR (JTM) &amp; TUGAS TAMBAHAN GURU TAHUN PELAJARAN {{ $ta?->tahun_ajaran ?? '2025/2026' }}
  </div>

  {{-- Tabel Lampiran I --}}
  <table class="tabel-sk">
    <thead>
      <tr>
        <th style="width:25px;">No</th>
        <th>Nama Guru / NIP / Pangkat</th>
        <th style="width:35px;">Kode</th>
        <th>Mata Pelajaran yang Diampu</th>
        <th>Kelas / Rombel</th>
        <th style="width:35px;">JTM</th>
        <th>Tugas Tambahan</th>
        <th style="width:35px;">Eqv</th>
        <th style="width:45px;">Total JP</th>
        <th style="width:70px;">Status</th>
      </tr>
    </thead>
    <tbody>
      @php $no = 1; @endphp
      @foreach($dataGuruSk as $g)
        @php
          $rowspan = max(1, count($g->rincian_mapel));
        @endphp
        @if(count($g->rincian_mapel) > 0)
          @foreach($g->rincian_mapel as $idx => $m)
            <tr>
              @if($idx === 0)
                <td rowspan="{{ $rowspan }}" style="text-align:center;">{{ $no++ }}</td>
                <td rowspan="{{ $rowspan }}">
                  <b>{{ $g->guru->nama }}</b>
                  @if($g->guru->nip)<div style="font-size:8.5pt; color:#334155;">NIP: {{ $g->guru->nip }}</div>@endif
                  @if($g->guru->golongan_pangkat)<div style="font-size:8pt; color:#475569;">Gol: {{ $g->guru->golongan_pangkat }}</div>@endif
                </td>
                <td rowspan="{{ $rowspan }}" style="text-align:center; font-weight:800;">
                  {{ $g->guru->kode_nomor ?? '-' }}
                </td>
              @endif

              <td>{{ $m['nama_mapel'] }}</td>
              <td>{{ $m['kelas_list'] }}</td>
              <td style="text-align:center; font-weight:700;">{{ $m['total_jam'] }}</td>

              @if($idx === 0)
                <td rowspan="{{ $rowspan }}">
                  @if(empty($g->tugas_tambahan))
                    <span style="color:#94a3b8; font-style:italic;">-</span>
                  @else
                    @foreach($g->tugas_tambahan as $tt)
                      <div>• {{ $tt['nama'] }} ({{ $tt['jp'] }} JP)</div>
                    @endforeach
                  @endif
                </td>
                <td rowspan="{{ $rowspan }}" style="text-align:center; font-weight:700;">
                  {{ $g->ekuivalen_tugas > 0 ? $g->ekuivalen_tugas : '-' }}
                </td>
                <td rowspan="{{ $rowspan }}" style="text-align:center; font-weight:900; font-size:10.5pt; background:#f8fafc;">
                  {{ $g->total_ekuivalen }}
                </td>
                <td rowspan="{{ $rowspan }}" style="text-align:center; font-size:8.5pt; font-weight:700;">
                  @if($g->total_ekuivalen >= 24 && $g->total_ekuivalen <= 40)
                    <span style="color:#16a34a;">MEMENUHI ({{ $g->total_ekuivalen }} JP)</span>
                  @elseif($g->total_ekuivalen > 40)
                    <span style="color:#dc2626;">OVERLOAD ({{ $g->total_ekuivalen }} JP)</span>
                  @else
                    <span style="color:#ea580c;">KURANG ({{ $g->total_ekuivalen }} JP)</span>
                  @endif
                </td>
              @endif
            </tr>
          @endforeach
        @else
          {{-- Guru tanpa mapel (cth Kepala Sekolah) --}}
          <tr>
            <td style="text-align:center;">{{ $no++ }}</td>
            <td>
              <b>{{ $g->guru->nama }}</b>
              @if($g->guru->nip)<div style="font-size:8.5pt; color:#334155;">NIP: {{ $g->guru->nip }}</div>@endif
            </td>
            <td style="text-align:center; font-weight:800;">{{ $g->guru->kode_nomor ?? '-' }}</td>
            <td style="color:#94a3b8; font-style:italic;">-</td>
            <td style="color:#94a3b8; font-style:italic;">-</td>
            <td style="text-align:center;">0</td>
            <td>
              @foreach($g->tugas_tambahan as $tt)
                <div>• {{ $tt['nama'] }} ({{ $tt['jp'] }} JP)</div>
              @endforeach
            </td>
            <td style="text-align:center; font-weight:700;">{{ $g->ekuivalen_tugas }}</td>
            <td style="text-align:center; font-weight:900; font-size:10.5pt; background:#f8fafc;">{{ $g->total_ekuivalen }}</td>
            <td style="text-align:center; font-size:8.5pt; font-weight:700; color:#16a34a;">MEMENUHI</td>
          </tr>
        @endif
      @endforeach
    </tbody>
  </table>

  {{-- Tanda Tangan Lampiran --}}
  <div class="tanda-tangan">
    <div class="ttd-box">
      Air Naningan, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
      <b>Kepala SMK Negeri 1 Air Naningan,</b>
      <br><br><br><br>
      <b><u>{{ $sekolah->nama_kepala_sekolah ?? 'Aprida, S.Si.' }}</u></b><br>
      NIP. {{ $sekolah->nip_kepala_sekolah ?? '197904172008012019' }}
    </div>
  </div>

  {{-- ============================================================ --}}
  {{-- HALAMAN BARU: LAMPIRAN II (PEMBAGIAN TUGAS TAMBAHAN & WALI KELAS) --}}
  {{-- ============================================================ --}}
  <div class="page-break"></div>

  <div class="lampiran-header">
    <b>LAMPIRAN II : KEPUTUSAN KEPALA SMKN 1 AIR NANINGAN</b><br>
    NOMOR : {{ $sekolah->nomor_sk_pembagian_tugas ?? '800/012/SMK.01/2026' }}<br>
    TANGGAL : {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
    TENTANG : PEMBAGIAN TUGAS TAMBAHAN GURU (WAKIL KEPALA SEKOLAH, KEPALA PROGRAM KEAHLIAN, KEPALA BENGKEL/LAB, KEPALA PERPUSTAKAAN, PEMBINA OSIS/EKSTRAKURIKULER, KOORDINATOR P5, &amp; WALI KELAS) SEMESTER {{ $semester == 1 ? 'GANJIL' : 'GENAP' }} TAHUN PELAJARAN {{ $ta?->tahun_ajaran ?? '2025/2026' }}
  </div>

  <table class="sk-table" style="margin-top:14px;">
    <thead>
      <tr>
        <th style="width:30px;">NO</th>
        <th style="width:190px;">NAMA GURU / NIP</th>
        <th style="width:80px;">GOL/PANGKAT</th>
        <th style="width:160px;">JABATAN / PENUGASAN UTAMA</th>
        <th>TUGAS TAMBAHAN YANG DITUGASKAN</th>
        <th style="width:110px;">KATEGORI</th>
        <th style="width:65px;">EKUIVALENSI (JP)</th>
      </tr>
    </thead>
    <tbody>
      @php $noTt = 1; @endphp
      @foreach($dataGuruSk as $g)
        @if(!empty($g->tugas_tambahan))
          @php $cnt = count($g->tugas_tambahan); @endphp
          @foreach($g->tugas_tambahan as $tIdx => $tt)
            <tr>
              @if($tIdx === 0)
                <td rowspan="{{ $cnt }}" style="text-align:center;">{{ $noTt++ }}</td>
                <td rowspan="{{ $cnt }}">
                  <b>{{ $g->guru->nama }}</b>
                  @if($g->guru->nip)<div style="font-size:8.5pt; color:#334155;">NIP: {{ $g->guru->nip }}</div>@endif
                </td>
                <td rowspan="{{ $cnt }}" style="text-align:center;">
                  {{ $g->guru->golongan_pangkat ?? 'GTT' }}
                </td>
                <td rowspan="{{ $cnt }}">
                  {{ $g->guru->jabatan ?? 'Guru Mata Pelajaran' }}
                </td>
              @endif
              <td>
                <b>{{ $tt['nama'] }}</b>
              </td>
              <td style="text-align:center; font-size:8.5pt;">
                {{ $tt['kategori'] ?? 'Tugas Tambahan' }}
              </td>
              <td style="text-align:center; font-weight:800; font-size:10pt;">
                +{{ $tt['jp'] }} JP
              </td>
            </tr>
          @endforeach
        @endif
      @endforeach
    </tbody>
  </table>

  {{-- Tanda Tangan Lampiran II --}}
  <div class="tanda-tangan">
    <div class="ttd-box">
      Air Naningan, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
      <b>Kepala SMK Negeri 1 Air Naningan,</b>
      <br><br><br><br>
      <b><u>{{ $sekolah->nama_kepala_sekolah ?? 'Aprida, S.Si.' }}</u></b><br>
      NIP. {{ $sekolah->nip_kepala_sekolah ?? '197904172008012019' }}
    </div>
  </div>

</body>
</html>
