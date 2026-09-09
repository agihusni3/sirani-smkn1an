<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INSTRUMEN &amp; RUBRIK WAWANCARA PPDB — {{ $pendaftar ? $pendaftar->nama_lengkap : 'SMKN 1 AIR NANINGAN' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm 10mm 12mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 20px 0;
            background: #e2e8f0;
            color: #000000;
            line-height: 1.25;
            font-size: 11.5pt;
        }

        .page-a4 {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 10mm 14mm;
            box-shadow: 0 4px 18px rgba(0,0,0,0.15);
            position: relative;
        }

        .screen-toolbar {
            width: 210mm;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #1e293b;
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
            background: #ffffff;
            padding: 8px 16px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
        }

        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #2563eb;
            color: #ffffff;
            font-weight: 800;
            font-size: 13px;
            padding: 9px 20px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(37,99,235,0.25);
        }

        .kop-table {
            width: 100%;
            border-bottom: 3px double #000000;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }

        .kop-logo {
            width: 70px;
            text-align: center;
            vertical-align: middle;
        }

        .kop-logo img {
            width: 65px;
            height: auto;
        }

        .kop-text {
            text-align: center;
            vertical-align: middle;
        }

        .kop-prov {
            font-size: 11pt;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin: 0;
            text-transform: uppercase;
        }

        .kop-dinas {
            font-size: 12pt;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
        }

        .kop-sekolah {
            font-size: 14pt;
            font-weight: 900;
            letter-spacing: 0.5px;
            margin: 1px 0;
            text-transform: uppercase;
        }

        .kop-alamat {
            font-size: 8.5pt;
            font-style: italic;
            margin: 0;
            color: #1e293b;
        }

        .doc-title {
            text-align: center;
            margin-bottom: 10px;
        }

        .doc-title h2 {
            font-size: 12.5pt;
            font-weight: 800;
            margin: 0 0 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: underline;
        }

        .doc-title p {
            font-size: 9.5pt;
            margin: 0;
            font-weight: 700;
        }

        .table-identitas {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 10px;
        }

        .table-identitas td {
            padding: 2.5px 4px;
            vertical-align: top;
        }

        .table-rubrik {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 10px;
        }

        .table-rubrik th,
        .table-rubrik td {
            border: 1px solid #000000;
            padding: 4.5px 6px;
            vertical-align: top;
        }

        .table-rubrik th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: 800;
            text-transform: uppercase;
        }

        .score-box {
            font-family: monospace;
            font-weight: 800;
            font-size: 11pt;
            text-align: center;
        }

        .checklist-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 10px;
        }

        .checklist-table th,
        .checklist-table td {
            border: 1px solid #000000;
            padding: 4px 6px;
        }

        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-top: 14px;
        }

        .ttd-table td {
            vertical-align: top;
            text-align: center;
            width: 50%;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .screen-toolbar {
                display: none !important;
            }
            .page-a4 {
                box-shadow: none;
                padding: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="screen-toolbar">
        <a href="{{ route('admin.ppdb.seleksi', ['tab' => 'wawancara']) }}" class="btn-back">
            &larr; Kembali ke Tab Wawancara
        </a>
        <div style="display: flex; gap: 10px;">
            <button type="button" onclick="window.print()" class="btn-print">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align:middle;">
                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                    <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
                </svg>
                Cetak Lembar Instrumen (A4)
            </button>
        </div>
    </div>

    <div class="page-a4">
        {{-- KOP RESMI SEKOLAH --}}
        <table class="kop-table">
            <tr>
                <td class="kop-logo">
                    <img src="/img/logo.png" alt="Logo Sekolah" onerror="this.style.display='none'">
                </td>
                <td class="kop-text">
                    <div class="kop-prov">Pemerintah Provinsi Lampung</div>
                    <div class="kop-dinas">Dinas Pendidikan dan Kebudayaan</div>
                    <div class="kop-sekolah">SMK Negeri 1 Air Naningan</div>
                    <div class="kop-alamat">
                        Jalan Raya Air Naningan, Kec. Air Naningan, Kab. Tanggamus, Lampung 35379<br>
                        Laman: smkn1airnaningan.sch.id &bull; Pos-el: smkn1airnaningan@gmail.com
                    </div>
                </td>
            </tr>
        </table>

        {{-- JUDUL DOKUMEN --}}
        <div class="doc-title">
            <h2>Instrumen &amp; Rubrik Wawancara Minat Kejuruan PPDB 2026</h2>
            <p>Standar Penilaian Terstruktur Calon Siswa Baru SMK Negeri 1 Air Naningan</p>
        </div>

        {{-- IDENTITAS CALON SISWA --}}
        <table class="table-identitas">
            <tr>
                <td style="width: 17%; font-weight: bold;">No. Pendaftaran</td>
                <td style="width: 33%;">: <strong>{{ $pendaftar->no_pendaftaran ?? '____________________' }}</strong></td>
                <td style="width: 18%; font-weight: bold;">Pilihan Jurusan 1</td>
                <td style="width: 32%;">: <strong>{{ $pendaftar->jurusan1->nama_jurusan ?? ($pendaftar->jurusanPilihan1->nama_jurusan ?? 'Semua Peminatan') }}</strong></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Nama Lengkap</td>
                <td>: <strong>{{ $pendaftar->nama_lengkap ?? '__________________________________' }}</strong></td>
                <td style="font-weight: bold;">Pilihan Jurusan 2</td>
                <td>: {{ $pendaftar->jurusan2->nama_jurusan ?? ($pendaftar->jurusanPilihan2->nama_jurusan ?? '-') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Asal SMP / MTs</td>
                <td>: {{ $pendaftar->asal_sekolah ?? '__________________________________' }}</td>
                <td style="font-weight: bold;">Waktu / Ruang</td>
                <td>: {{ $pendaftar ? $pendaftar->jadwal_sesi_resmi : '08.00 - 10.00 WIB' }} / {{ $pendaftar ? $pendaftar->jadwal_ruang_resmi : 'Lab SMKN 1 AN' }}</td>
            </tr>
        </table>

        {{-- TABEL RUBRIK 4 KRITERIA --}}
        <table class="table-rubrik">
            <thead>
                <tr>
                    <th style="width: 26%;">Kriteria &amp; Bobot</th>
                    <th style="width: 36%;">Panduan Pertanyaan Pemandu (Pewawancara)</th>
                    <th style="width: 26%;">Pedoman Rubrik Penilaian</th>
                    <th style="width: 12%;">Skor (0-100)</th>
                </tr>
            </thead>
            <tbody>
                {{-- 1. MOTIVASI --}}
                <tr>
                    <td>
                        <strong>1. Motivasi &amp; Minat Belajar</strong><br>
                        <span style="font-size: 7.5pt; color: #334155;">Bobot: <strong>25%</strong></span>
                        <div style="font-size: 7.5pt; color: #475569; margin-top: 3px;">
                            Menilai kemauan murni, kesungguhan belajar di SMK, serta kejelasan orientasi masa depan.
                        </div>
                    </td>
                    <td>
                        &bull; <em>"Mengapa memilih SMKN 1 Air Naningan? Apakah ini murni pilihan dan kemauan sendiri?"</em><br>
                        &bull; <em>"Apa cita-cita atau rencana setelah lulus nanti? Ingin langsung bekerja di industri, wirausaha mandiri, atau kuliah?"</em><br>
                        &bull; <em>"Sejauh mana Anda mengetahui kompetensi dari jurusan yang dipilih?"</em>
                    </td>
                    <td style="font-size: 7.5pt;">
                        <strong>85-100</strong>: Kemauan sendiri, sangat antusias, tujuan karir matang &amp; realistis.<br>
                        <strong>70-84</strong>: Minat wajar, namun wawasan karir masih umum.<br>
                        <strong>50-69</strong>: Ikut-ikutan teman, pasif.<br>
                        <strong>&lt;50</strong>: Terpaksa karena dorongan orang lain, tidak berminat.
                    </td>
                    <td class="score-box">
                        {{ $pendaftar && $pendaftar->nilai_wawancara_motivasi !== null ? number_format($pendaftar->nilai_wawancara_motivasi, 0) : '' }}
                    </td>
                </tr>

                {{-- 2. KARAKTER & DISIPLIN --}}
                <tr>
                    <td>
                        <strong>2. Karakter, Sikap &amp; Disiplin</strong><br>
                        <span style="font-size: 7.5pt; color: #334155;">Bobot: <strong>25%</strong></span>
                        <div style="font-size: 7.5pt; color: #475569; margin-top: 3px;">
                            Menilai tata krama, integritas, kejujuran, dan kesiapan mematuhi tata tertib sekolah.
                        </div>
                    </td>
                    <td>
                        &bull; <em>"Siapkah mematuhi tata tertib sekolah: jam masuk 07.00 WIB, seragam rapi, rambut pendek (putra), larangan merokok/vape di lingkungan sekolah?"</em><br>
                        &bull; <em>"Bagaimana respon Anda jika ditegur atau dibimbing guru atas suatu kesalahan?"</em><br>
                        &bull; <em>"Bagaimana riwayat absensi dan pergaulan Anda selama di SMP/MTs?"</em>
                    </td>
                    <td style="font-size: 7.5pt;">
                        <strong>85-100</strong>: Tutur kata santun, gestur sopan, kontak mata baik, jujur, komitmen disiplin tinggi.<br>
                        <strong>70-84</strong>: Sikap wajar, terbuka untuk dibina.<br>
                        <strong>50-69</strong>: Kurang fokus, pernah ada catatan indispliner ringan.<br>
                        <strong>&lt;50</strong>: Bersikap defensif/acuh, menolak aturan sekolah.
                    </td>
                    <td class="score-box">
                        {{ $pendaftar && $pendaftar->nilai_wawancara_karakter !== null ? number_format($pendaftar->nilai_wawancara_karakter, 0) : '' }}
                    </td>
                </tr>

                {{-- 3. KESIAPAN KEJURUAN & UJI KHUSUS --}}
                <tr>
                    <td>
                        <strong>3. Kesiapan Kejuruan &amp; Fisik</strong><br>
                        <span style="font-size: 7.5pt; color: #334155;">Bobot: <strong>30%</strong></span>
                        <div style="font-size: 7.5pt; color: #475569; margin-top: 3px;">
                            Diselaraskan dengan Jurusan Pilihan 1:
                            <strong>{{ $pendaftar->jurusan1->kode_jurusan ?? 'RPL / APHP / TSM' }}</strong>.
                        </div>
                    </td>
                    <td>
                        @php
                            $kode = strtoupper($pendaftar->jurusan1->kode_jurusan ?? '');
                        @endphp
                        @if(str_contains($kode, 'RPL'))
                            <strong>[RPL]</strong>: <em>"Siapkah duduk berjam-jam berkonsentrasi memecahkan logika kode pemrograman? Pernahkah memakai PC/laptop atau siap memanfaatkan fasilitas lab sekolah secara disiplin?"</em>
                        @elseif(str_contains($kode, 'APHP'))
                            <strong>[APHP]</strong>: <em>"Siapkah berkegiatan di dapur/lab pengolahan pangan yang hangat &amp; mencuci peralatan? Tertarikkah mengolah hasil tani lokal (kopi, pisang, rempah) menjadi produk bernilai jual?"</em>
                        @elseif(str_contains($kode, 'TSM'))
                            <strong>[TSM]</strong>: <em>"Siapkah menghadapi oli, kotoran bengkel, dan disiplin tinggi dalam SOP K3 bengkel motor? Minatkah mendalami mesin injeksi dan kelistrikan motor?"</em>
                        @else
                            &bull; <strong>RPL</strong>: Logika coding, kesiapan di depan monitor, pemikiran analitis.<br>
                            &bull; <strong>APHP</strong>: Kebersihan diri, minat agro-kuliner, kesiapan kerja lab pangan.<br>
                            &bull; <strong>TSM</strong>: Minat mekanik bengkel, tidak takut kotor oli, ketahanan fisik.
                        @endif
                    </td>
                    <td style="font-size: 7.5pt;">
                        <strong>85-100</strong>: Bebas buta warna, antusias tinggi pada bidang teknis kejuruan, kesiapan fisik prima.<br>
                        <strong>70-84</strong>: Bebas buta warna, siap belajar dari dasar.<br>
                        <strong>&lt;70</strong>: Buta warna total (berisiko pada instalasi kabel/desain), atau enggan menjalani praktik kejuruan.
                    </td>
                    <td class="score-box">
                        {{ $pendaftar && $pendaftar->nilai_wawancara_kejuruan !== null ? number_format($pendaftar->nilai_wawancara_kejuruan, 0) : '' }}
                    </td>
                </tr>

                {{-- 4. DUKUNGAN ORANG TUA --}}
                <tr>
                    <td>
                        <strong>4. Dukungan Orang Tua / Wali</strong><br>
                        <span style="font-size: 7.5pt; color: #334155;">Bobot: <strong>20%</strong></span>
                        <div style="font-size: 7.5pt; color: #475569; margin-top: 3px;">
                            Menilai komitmen keluarga dalam mendukung pembelajaran dan magang PKL industri.
                        </div>
                    </td>
                    <td>
                        &bull; <em>"Apakah orang tua/wali merestui jurusan ini dan bersedia hadir jika diundang sekolah?"</em><br>
                        &bull; <em>"Apakah orang tua siap mendukung pembiayaan perlengkapan praktik dan pelaksanaan PKL industri selama 6 bulan di luar sekolah?"</em>
                    </td>
                    <td style="font-size: 7.5pt;">
                        <strong>85-100</strong>: Orang tua mendukung penuh moral &amp; material, menyetujui program PKL industri.<br>
                        <strong>70-84</strong>: Orang tua mendukung wajar &amp; siap bekerja sama.<br>
                        <strong>&lt;70</strong>: Orang tua menolak jurusan / lepas tangan.
                    </td>
                    <td class="score-box">
                        {{ $pendaftar && $pendaftar->nilai_wawancara_ortu !== null ? number_format($pendaftar->nilai_wawancara_ortu, 0) : '' }}
                    </td>
                </tr>

                {{-- TOTAL TERBOBOT --}}
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td colspan="3" style="text-align: right; font-size: 9pt; padding: 6px;">
                        TOTAL SKOR WAWANCARA TERBOBOT (25% M + 25% K + 30% J + 20% O):
                    </td>
                    <td class="score-box" style="font-size: 13pt; color: #1e40af; background: #eff6ff;">
                        {{ $pendaftar && $pendaftar->nilai_wawancara_total !== null ? number_format($pendaftar->nilai_wawancara_total, 1) : '' }}
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- PEMERIKSAAN FISIK & KESEHATAN AWAL --}}
        <table class="checklist-table">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th colspan="4" style="text-align: left; font-size: 8.5pt;">Pemeriksaan Fisik &amp; Kesiapan Kejuruan:</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 25%;">
                        [ &nbsp; ] Bebas Buta Warna (Ishihara)
                    </td>
                    <td style="width: 25%;">
                        [ &nbsp; ] Tidak Bertato
                    </td>
                    <td style="width: 25%;">
                        [ &nbsp; ] Tidak Bertindik (Putra)
                    </td>
                    <td style="width: 25%;">
                        [ &nbsp; ] Bebas Riwayat Penyakit Berat
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="font-size: 8pt; color: #1e293b;">
                        <strong>Catatan Khusus Penguji / Observasi:</strong><br>
                        <div style="min-height: 28px; margin-top: 2px;">
                            {{ $pendaftar->catatan_wawancara ?? '(Tuliskan catatan khusus terkait fisik, potensi minat, komitmen siswa/wali bila ada)' }}
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- TANDA TANGAN PEWAWANCARA & KETUA PANITIA --}}
        <table class="ttd-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    <strong>Ketua Panitia PPDB 2026</strong>
                    <div style="height: 48px;"></div>
                    <strong><u>Hendra Pratama, S.Pd.</u></strong><br>
                    NIP. 19850412 201101 1 008
                </td>
                <td>
                    Air Naningan, {{ $pendaftar && $pendaftar->diwawancara_pada ? $pendaftar->diwawancara_pada->translatedFormat('d F Y') : \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    <strong>Guru Pewawancara / Penguji</strong>
                    <div style="height: 48px;"></div>
                    <strong><u>{{ $pendaftar->pewawancara->name ?? '( ____________________________ )' }}</u></strong><br>
                    NIP. {{ $pendaftar->pewawancara->nip ?? '..................................................' }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
