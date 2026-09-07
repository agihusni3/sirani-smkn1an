<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Tanda Peserta PPDB - {{ $pendaftar->nomor_pendaftaran }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f1f5f9;
            color: #000;
        }

        .kartu-container {
            max-width: 780px;
            margin: 0 auto;
            background: #fff;
            padding: 30px 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 2px solid #0f172a;
        }

        /* Kop Surat Resmi */
        .kop {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .kop-logo {
            width: 75px;
            height: 75px;
            object-fit: contain;
        }

        .kop-text {
            text-align: center;
            flex: 1;
            padding: 0 14px;
        }

        .kop-text h2 {
            margin: 0;
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-text h1 {
            margin: 2px 0;
            font-size: 17pt;
            font-weight: 800;
            text-transform: uppercase;
        }

        .kop-text p {
            margin: 2px 0;
            font-size: 8.5pt;
            color: #333;
        }

        .judul-kartu {
            text-align: center;
            margin-bottom: 20px;
        }

        .judul-kartu h3 {
            margin: 0;
            font-size: 13pt;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .judul-kartu span {
            font-size: 10pt;
            font-weight: bold;
            color: #444;
        }

        .grid-data {
            display: flex;
            gap: 24px;
            margin-bottom: 24px;
        }

        .foto-box {
            width: 140px;
            text-align: center;
        }

        .foto-frame {
            width: 120px;
            height: 160px;
            border: 1px solid #999;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            overflow: hidden;
        }

        .foto-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .table-data {
            flex: 1;
            border-collapse: collapse;
            font-size: 10pt;
        }

        .table-data td {
            padding: 5px 8px;
            vertical-align: top;
        }

        .table-data td:first-child {
            width: 160px;
            font-weight: 600;
            color: #333;
        }

        .box-status {
            border: 2px dashed #000;
            padding: 10px 14px;
            margin-bottom: 20px;
            background: #fafafa;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 9.5pt;
        }

        .ttd-area {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            font-size: 9.5pt;
        }

        .ttd-box {
            width: 220px;
            text-align: center;
        }

        .ttd-space {
            height: 60px;
        }

        .btn-print-box {
            max-width: 780px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .kartu-container {
                box-shadow: none;
                border: 2px solid #000;
                padding: 20px 30px;
            }
            .btn-print-box {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="btn-print-box">
        <a href="{{ route('ppdb.status', ['keyword' => $pendaftar->nomor_pendaftaran]) }}" style="color: #2563eb; text-decoration: none; font-weight: bold; font-size: 14px;">
            ← Kembali ke Status PPDB
        </a>
        <button onclick="window.print()" style="padding: 9px 20px; background: #2563eb; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 14px;">
            🖨️ Cetak / Simpan PDF
        </button>
    </div>

    <div class="kartu-container">
        <!-- Kop Sekolah -->
        <div class="kop">
            <img src="{{ asset('logo_prov_lampung.png') }}" class="kop-logo" alt="Logo Lampung" onerror="this.src='{{ asset('img/logo_prov_lampung.png') }}'">
            <div class="kop-text">
                <h2>Pemerintah Provinsi Lampung</h2>
                <h2>Dinas Pendidikan dan Kebudayaan</h2>
                <h1>SMK NEGERI 1 AIR NANINGAN</h1>
                <p>{{ $sekolah->alamat ?? 'Jl. Raya Air Naningan, Kec. Air Naningan, Kab. Tanggamus, Lampung 35379' }}</p>
                <p>NPSN: {{ $sekolah->npsn ?? '70011825' }} | Email: {{ $sekolah->email ?? 'info@smkn1airnaningan.sch.id' }}</p>
            </div>
            <img src="{{ asset('logo.png') }}" class="kop-logo" alt="Logo SMKN 1" onerror="this.src='{{ asset('img/logo.png') }}'">
        </div>

        <!-- Judul -->
        <div class="judul-kartu">
            @if($pendaftar->status == 'diterima')
                <h3>SURAT KETERANGAN LULUS SELEKSI &amp; BUKTI DITERIMA PPDB</h3>
                <span>TAHUN PELAJARAN {{ $pendaftar->tahun_ajaran }}</span>
            @else
                <h3>KARTU TANDA BUKTI PENDAFTARAN &amp; JADWAL SELEKSI</h3>
                <span>TAHUN PELAJARAN {{ $pendaftar->tahun_ajaran }}</span>
            @endif
        </div>

        <!-- Status Nomor Registrasi -->
        <div class="box-status" style="{{ $pendaftar->status == 'diterima' ? 'background: #f0fdf4; border: 2px solid #16a34a;' : '' }}">
            <div>
                <strong>NOMOR PENDAFTARAN:</strong> 
                <span style="font-size: 13pt; font-family: monospace; font-weight: bold; margin-left: 6px;">{{ $pendaftar->no_pendaftaran ?? $pendaftar->nomor_pendaftaran }}</span>
            </div>
            <div>
                <strong>STATUS:</strong> 
                <span style="font-weight: bold; {{ $pendaftar->status == 'diterima' ? 'color: #15803d;' : '' }}">
                    @if($pendaftar->status == 'diterima')
                        LULUS / DITERIMA
                    @elseif($pendaftar->status == 'cadangan')
                        CADANGAN
                    @elseif($pendaftar->status == 'terverifikasi' || $pendaftar->status == 'berkas_valid')
                        BERKAS VALID / SIAP UJIAN
                    @elseif($pendaftar->status == 'ditolak')
                        TIDAK LOLOS
                    @else
                        MENUNGGU VERIFIKASI
                    @endif
                </span>
            </div>
        </div>

        @if($pendaftar->status == 'diterima')
            <!-- Panel Jurusan Diterima -->
            <div style="background: #ecfdf5; border: 1.5px solid #a7f3d0; padding: 12px 18px; margin-bottom: 20px; border-radius: 4px;">
                <div style="font-size: 9pt; color: #065f46; font-weight: bold; text-transform: uppercase;">Dinyatakan Diterima Pada:</div>
                <div style="font-size: 13pt; font-weight: bold; color: #047857; margin: 2px 0;">
                    {{ strtoupper($pendaftar->jurusanDiterima->nama_jurusan ?? ($pendaftar->jurusanPilihan1->nama_jurusan ?? '-')) }}
                </div>
                <div style="font-size: 9pt; color: #065f46; margin-top: 4px;">
                    Peringkat Kelulusan: <strong>#{{ $pendaftar->peringkat_jurusan ?: '1' }}</strong> &bull; 
                    Nilai Akhir: <strong>{{ number_format($pendaftar->nilai_akhir ?? 0, 2) }}</strong> &bull; 
                    Jalur: <strong>{{ strtoupper($pendaftar->jalur_pendaftaran) }}</strong>
                </div>
            </div>
        @elseif($pendaftar->jadwal_tes_tanggal)
            <!-- Panel Jadwal Ujian CBT & Wawancara -->
            <div style="background: #eff6ff; border: 1.5px solid #bfdbfe; padding: 10px 16px; margin-bottom: 20px; border-radius: 4px; font-size: 9.5pt;">
                <strong style="color: #1e40af; display: block; margin-bottom: 4px;">JADWAL TES SELEKSI CBT &amp; WAWANCARA:</strong>
                <table style="width: 100%; font-size: 9pt; border-collapse: collapse;">
                    <tr>
                        <td style="width: 120px; color: #475569;">Hari / Tanggal:</td>
                        <td style="font-weight: bold; color: #0f172a;">{{ \Carbon\Carbon::parse($pendaftar->jadwal_tes_tanggal)->translatedFormat('l, d F Y') }}</td>
                        <td style="width: 100px; color: #475569;">Ruang Ujian:</td>
                        <td style="font-weight: bold; color: #0f172a;">{{ $pendaftar->jadwal_tes_ruang ?: 'Lab Komputer SMKN 1' }}</td>
                    </tr>
                    <tr>
                        <td style="color: #475569;">Sesi Waktu:</td>
                        <td style="font-weight: bold; color: #0f172a;">{{ $pendaftar->jadwal_tes_sesi ?: 'Sesi 1' }}</td>
                        <td style="color: #475569;">Materi Ujian:</td>
                        <td style="font-weight: bold; color: #0f172a;">CBT Tertulis + Wawancara Kejuruan</td>
                    </tr>
                </table>
            </div>
        @endif

        <!-- Detail Siswa & Foto -->
        <div class="grid-data">
            <div class="foto-box">
                <div class="foto-frame">
                    @if($pendaftar->pas_foto)
                        <img src="{{ asset('storage/' . $pendaftar->pas_foto) }}" alt="Foto">
                    @else
                        <span style="color: #999; font-size: 9pt;">Pas Foto<br>3 x 4</span>
                    @endif
                </div>
                <p style="font-size: 8pt; margin-top: 6px; color: #555;">Tempel Pas Foto jika belum terunggah</p>
            </div>

            <table class="table-data">
                <tr>
                    <td>Nomor Induk Siswa Nasional</td>
                    <td>: <strong>{{ $pendaftar->nisn }}</strong></td>
                </tr>
                <tr>
                    <td>Nama Lengkap</td>
                    <td>: <strong>{{ strtoupper($pendaftar->nama_lengkap) }}</strong></td>
                </tr>
                <tr>
                    <td>Jenis Kelamin</td>
                    <td>: {{ $pendaftar->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                </tr>
                <tr>
                    <td>Tempat, Tanggal Lahir</td>
                    <td>: {{ $pendaftar->tempat_lahir }}, {{ \Carbon\Carbon::parse($pendaftar->tanggal_lahir)->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td>Asal Sekolah (SMP/MTs)</td>
                    <td>: {{ $pendaftar->asal_sekolah }} (Lulus: {{ $pendaftar->tahun_lulus }})</td>
                </tr>
                <tr>
                    <td>Jalur Masuk</td>
                    <td>: <strong>JALUR {{ strtoupper($pendaftar->jalur_pendaftaran) }}</strong></td>
                </tr>
                <tr>
                    <td>Pilihan Keahlian 1</td>
                    <td>: <strong>{{ $pendaftar->jurusanPilihan1->nama_jurusan ?? '-' }} ({{ $pendaftar->jurusanPilihan1->kode ?? '' }})</strong></td>
                </tr>
                <tr>
                    <td>Pilihan Keahlian 2</td>
                    <td>: {{ $pendaftar->jurusanPilihan2->nama_jurusan ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Nama Orang Tua / Wali</td>
                    <td>: {{ $pendaftar->nama_ibu ?: ($pendaftar->nama_ayah ?: '-') }}</td>
                </tr>
                <tr>
                    <td>Kontak Siswa / Orang Tua</td>
                    <td>: <strong>{{ $pendaftar->no_hp_siswa ?: ($pendaftar->no_hp_ortu ?: '-') }}</strong></td>
                </tr>
            </table>
        </div>

        <div style="font-size: 8.5pt; color: #333; line-height: 1.5; border-top: 1px dashed #999; padding-top: 10px; margin-bottom: 20px;">
            @if($pendaftar->status == 'diterima')
                <strong>Petunjuk Daftar Ulang Calon Siswa Diterima:</strong>
                <ol style="margin: 4px 0 0 16px; padding: 0;">
                    <li>Bawa Surat Keterangan Lulus ini ke Sekretariat PPDB SMKN 1 Air Naningan pada jadwal daftar ulang.</li>
                    <li>Menyerahkan fotokopi legalisir: Ijazah/SKL (2 lbr), Kartu Keluarga (2 lbr), Akta Kelahiran (2 lbr).</li>
                    <li>Menyerahkan Pas Foto berwarna terbaru 3x4 (4 lembar) dan menandatangani surat kesanggupan tata tertib sekolah.</li>
                </ol>
            @else
                <strong>Catatan untuk Peserta:</strong>
                <ol style="margin: 4px 0 0 16px; padding: 0;">
                    <li>Bawa kartu bukti pendaftaran ini saat jadwal verifikasi fisik atau pelaksanaan tes di sekolah.</li>
                    <li>Wajib hadir 30 menit sebelum sesi ujian CBT dan wawancara dimulai dengan mengenakan seragam sekolah asal.</li>
                    <li>Pantau terus pengumuman resmi berkala di website resmi: <code>{{ url('/') }}</code></li>
                </ol>
            @endif
        </div>

        <!-- Tanda Tangan -->
        <div class="ttd-area">
            <div class="ttd-box">
                Calon Peserta Didik Baru,
                <div class="ttd-space"></div>
                <strong>( {{ $pendaftar->nama_lengkap }} )</strong>
            </div>

            <div class="ttd-box">
                Air Naningan, {{ date('d F Y') }}<br>
                Panitia PPDB SMKN 1 Air Naningan,
                <div class="ttd-space"></div>
                <strong>( Tim Panitia Penerimaan )</strong>
            </div>
        </div>
    </div>

</body>
</html>
