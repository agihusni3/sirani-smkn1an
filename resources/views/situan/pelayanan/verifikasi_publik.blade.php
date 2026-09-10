<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verifikasi Keabsahan Dokumen — SMKN 1 Air Naningan</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: #f8fafc;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .verify-card {
      background: #ffffff;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
      border: 1px solid #e2e8f0;
      overflow: hidden;
    }
    .badge-valid {
      background: linear-gradient(135deg, #059669 0%, #10b981 100%);
      color: #fff;
    }
    .badge-invalid {
      background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
      color: #fff;
    }
    .info-label {
      font-size: 12px;
      text-transform: uppercase;
      font-weight: 700;
      color: #64748b;
      letter-spacing: 0.5px;
    }
    .info-value {
      font-size: 14.5px;
      font-weight: 600;
      color: #0f172a;
    }
    .mono-hash {
      font-family: 'JetBrains Mono', monospace;
      font-size: 12px;
      background: #f1f5f9;
      padding: 4px 8px;
      border-radius: 6px;
      color: #334155;
      word-break: break-all;
    }
  </style>
</head>
<body>

  {{-- Header --}}
  <nav class="navbar navbar-expand-lg bg-white border-bottom py-3">
    <div class="container justify-content-center justify-content-md-start">
      <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
        <img src="{{ $sekolah->logo_url ?: '/img/logo.png' }}" alt="Logo" width="36" height="36">
        <div>
          <div class="fw-bold text-dark" style="font-size:15px; line-height:1.2;">{{ $sekolah->nama_sekolah ?: 'SMK Negeri 1 Air Naningan' }}</div>
          <div class="text-muted" style="font-size:11px;">Pusat Validasi Surat &amp; Tata Naskah Dinas Elektronik</div>
        </div>
      </a>
    </div>
  </nav>

  {{-- Main Container --}}
  <main class="container my-5">
    <div class="row justify-content-center">
      <div class="col-lg-8 col-xl-7">

        @if($pelayanan)
          {{-- VALID DOCUMENT STATE --}}
          <div class="verify-card">
            <div class="p-4 p-md-5 text-center badge-valid">
              <div class="rounded-circle bg-white d-inline-flex p-3 mb-3 shadow-sm text-success">
                <i class="bi bi-shield-fill-check fs-1"></i>
              </div>
              <h2 class="h4 fw-bold text-white mb-1">DOKUMEN RESMI TERVERIFIKASI</h2>
              <p class="text-white-50 mb-0 small">Surat Keterangan ini terdaftar sah secara elektronik dalam database SITUAN SMK Negeri 1 Air Naningan.</p>
            </div>

            <div class="p-4 p-md-5">
              @php
                $snapshot = $pelayanan->payload_snapshot ?? [];
                $namaLayanan = match($pelayanan->jenis_pelayanan) {
                  'suket_aktif'            => 'Surat Keterangan Siswa Aktif',
                  'suket_berkelakuan_baik' => 'Surat Keterangan Berkelakuan Baik',
                  'suket_mutasi_keluar'    => 'Surat Rekomendasi Pindah Sekolah',
                  'suket_skl'              => 'Surat Keterangan Lulus Sementara (SKL)',
                  'suket_pengantar_pkl'    => 'Surat Pengantar PKL',
                  default                  => 'Surat Keterangan Kesiswaan',
                };
              @endphp

              <div class="mb-4 pb-3 border-bottom">
                <div class="info-label">Jenis Dokumen</div>
                <div class="h5 fw-bold text-primary mb-0">{{ $namaLayanan }}</div>
              </div>

              <div class="row g-3 mb-4">
                <div class="col-sm-6">
                  <div class="info-label">Nomor Surat Resmi</div>
                  <div class="info-value font-monospace text-primary">{{ $pelayanan->suratKeluar?->nomor_surat_lengkap ?? '-' }}</div>
                </div>
                <div class="col-sm-6">
                  <div class="info-label">Tanggal Diterbitkan</div>
                  <div class="info-value">
                    <i class="bi bi-calendar-check me-1 text-secondary"></i>
                    {{ $pelayanan->suratKeluar?->tanggal_surat ? \Carbon\Carbon::parse($pelayanan->suratKeluar->tanggal_surat)->translatedFormat('d F Y') : '-' }}
                  </div>
                </div>
              </div>

              <div class="p-3 bg-light rounded-3 border mb-4">
                <div class="fw-bold small text-dark mb-2 text-uppercase" style="letter-spacing:0.5px;">
                  <i class="bi bi-person-circle text-secondary me-1"></i> Identitas Peserta Didik
                </div>
                <div class="row g-2">
                  <div class="col-sm-6">
                    <div class="info-label">Nama Lengkap</div>
                    <div class="info-value text-uppercase">{{ $snapshot['nama'] ?? ($pelayanan->siswa?->nama ?? '-') }}</div>
                  </div>
                  <div class="col-sm-6">
                    <div class="info-label">NISN / NIS</div>
                    <div class="info-value">{{ $snapshot['nisn'] ?? '-' }} / {{ $snapshot['nis'] ?? '-' }}</div>
                  </div>
                  <div class="col-sm-6">
                    <div class="info-label">Kelas / Rombel</div>
                    <div class="info-value">{{ $snapshot['rombel'] ?? '-' }}</div>
                  </div>
                  <div class="col-sm-6">
                    <div class="info-label">Nama Orang Tua / Wali</div>
                    <div class="info-value">{{ $snapshot['nama_ortu'] ?? '-' }}</div>
                  </div>
                </div>
              </div>

              <div class="mb-4">
                <div class="info-label">Tujuan / Keperluan Surat</div>
                <div class="info-value text-dark">{{ $pelayanan->keperluan }}</div>
              </div>

              @if($pelayanan->jenis_pelayanan === 'suket_mutasi_keluar' && !empty($snapshot['sekolah_tujuan']))
                <div class="p-3 bg-warning-subtle rounded-3 border border-warning-subtle mb-4">
                  <div class="info-label text-warning-emphasis">Sekolah Tujuan Pindah:</div>
                  <div class="fw-bold text-dark">{{ $snapshot['sekolah_tujuan'] }}</div>
                  @if(!empty($snapshot['alasan_mutasi']))
                    <div class="small text-muted mt-1">Alasan: {{ $snapshot['alasan_mutasi'] }}</div>
                  @endif
                </div>
              @endif

              <div class="row g-3 mb-4">
                <div class="col-sm-6">
                  <div class="info-label">Penandatangan</div>
                  <div class="info-value">{{ $sekolah->nama_kepala_sekolah ?: 'Aprida, S.Si. (Kepala Sekolah)' }}</div>
                </div>
                <div class="col-sm-6">
                  <div class="info-label">Status Keabsahan</div>
                  <div class="info-value text-success"><i class="bi bi-patch-check-fill me-1"></i> Asli &amp; Berlaku</div>
                </div>
              </div>

              <div class="pt-3 border-top">
                <div class="info-label mb-1">Kode Enkripsi Verifikasi Dokumen</div>
                <div class="mono-hash">{{ $pelayanan->kode_verifikasi_qr }}</div>
                <div class="text-muted mt-2" style="font-size:11.5px;">
                  <i class="bi bi-info-circle me-1"></i> Dokumen ini diterbitkan oleh Subbagian Tata Usaha SMKN 1 Air Naningan dan dilindungi sistem anti-pemalsuan berbasis basis data tersentral.
                </div>
              </div>
            </div>
          </div>
        @elseif($suratKeluar)
          {{-- VALID SURAT KELUAR / SURAT DINAS STATE --}}
          <div class="verify-card">
            <div class="p-4 p-md-5 text-center badge-valid">
              <div class="rounded-circle bg-white d-inline-flex p-3 mb-3 shadow-sm text-success">
                <i class="bi bi-shield-fill-check fs-1"></i>
              </div>
              <h2 class="h4 fw-bold text-white mb-1">DOKUMEN RESMI TERVERIFIKASI</h2>
              <p class="text-white-50 mb-0 small">Naskah dinas ini terdaftar sah secara elektronik dalam Buku Agenda Surat Keluar SITUAN SMK Negeri 1 Air Naningan.</p>
            </div>

            <div class="p-4 p-md-5">
              @php
                $namaJenis = match($suratKeluar->kategori_surat) {
                  'Pengantar KGB'        => 'Surat Pengantar Kenaikan Gaji Berkala (KGB)',
                  'SK Penetapan Sanksi'  => 'Surat Keputusan (SK) Kepala Sekolah - Pembinaan Kedisiplinan',
                  'Panggilan Orang Tua'  => 'Surat Panggilan Orang Tua / Wali Siswa',
                  'Berita Acara BK'      => 'Berita Acara Musyawarah & Pembinaan BK',
                  'Suket Bebas Masalah'  => 'Surat Keterangan Bebas Masalah Kesiswaan',
                  default                => $suratKeluar->perihal ?: 'Surat Dinas Resmi',
                };
              @endphp

              <div class="mb-4 pb-3 border-bottom">
                <div class="info-label">Kategori Dokumen</div>
                <div class="h5 fw-bold text-primary mb-0">{{ $namaJenis }}</div>
              </div>

              <div class="row g-3 mb-4">
                <div class="col-sm-6">
                  <div class="info-label">Nomor Surat Resmi</div>
                  <div class="info-value font-monospace text-primary">{{ $suratKeluar->nomor_surat_lengkap }}</div>
                </div>
                <div class="col-sm-6">
                  <div class="info-label">Tanggal Diterbitkan</div>
                  <div class="info-value">
                    <i class="bi bi-calendar-check me-1 text-secondary"></i>
                    {{ $suratKeluar->tanggal_surat ? \Carbon\Carbon::parse($suratKeluar->tanggal_surat)->translatedFormat('d F Y') : '-' }}
                  </div>
                </div>
              </div>

              <div class="p-3 bg-light rounded-3 border mb-4">
                <div class="fw-bold small text-dark mb-2 text-uppercase" style="letter-spacing:0.5px;">
                  <i class="bi bi-envelope-paper text-secondary me-1"></i> Detail Perihal &amp; Tujuan Surat
                </div>
                <div class="mb-2">
                  <div class="info-label">Tujuan / Penerima</div>
                  <div class="info-value">{{ $suratKeluar->tujuan_surat }}</div>
                </div>
                <div>
                  <div class="info-label">Perihal</div>
                  <div class="info-value">{{ $suratKeluar->perihal }}</div>
                </div>
              </div>

              <div class="row g-3 mb-4">
                <div class="col-sm-6">
                  <div class="info-label">Penandatangan Resmi</div>
                  <div class="info-value">{{ $suratKeluar->penandatangan ?: ($sekolah->nama_kepala_sekolah ?: 'Aprida, S.Si.') }}</div>
                  <div class="small text-muted">NIP. {{ $sekolah->nip_kepala_sekolah ?: '197904172008012019' }}</div>
                </div>
                <div class="col-sm-6">
                  <div class="info-label">Status Keabsahan</div>
                  <div class="info-value text-success"><i class="bi bi-patch-check-fill me-1"></i> Sah &amp; Tercatat di Buku Agenda</div>
                  <div class="small text-muted">Nomor Agenda: {{ $suratKeluar->nomor_agenda }} / {{ $suratKeluar->tahun_agenda }}</div>
                </div>
              </div>

              <div class="pt-3 border-top">
                <div class="info-label mb-1">Kode Enkripsi Verifikasi Dokumen</div>
                <div class="mono-hash">{{ $suratKeluar->kode_verifikasi_qr }}</div>
                <div class="text-muted mt-2" style="font-size:11.5px;">
                  <i class="bi bi-info-circle me-1"></i> Naskah dinas ini diterbitkan oleh Tata Usaha SMKN 1 Air Naningan melalui Sistem Informasi Tata Usaha &amp; Administrasi Terpadu (SITUAN).
                </div>
              </div>
            </div>
          </div>
        @else
          {{-- INVALID OR NOT FOUND STATE --}}
          <div class="verify-card">
            <div class="p-4 p-md-5 text-center badge-invalid">
              <div class="rounded-circle bg-white d-inline-flex p-3 mb-3 shadow-sm text-danger">
                <i class="bi bi-shield-x fs-1"></i>
              </div>
              <h2 class="h4 fw-bold text-white mb-1">DOKUMEN TIDAK VALID / TIDAK DITEMUKAN</h2>
              <p class="text-white-50 mb-0 small">Kode verifikasi tidak terdaftar pada sistem basis data SMK Negeri 1 Air Naningan.</p>
            </div>
            <div class="p-4 p-md-5">
              <div class="alert alert-danger">
                <h6 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i> Peringatan Keaslian Dokumen:</h6>
                <p class="small mb-0">Dokumen surat fisik atau sertifikat yang Anda pindai mungkin telah diubah, dipalsukan, atau belum terdaftar secara sah pada buku agenda tata usaha sekolah.</p>
              </div>
              <div class="info-label mb-1">Kode yang dipindai:</div>
              <div class="mono-hash text-danger mb-3">{{ $hash }}</div>
              <p class="text-muted small">
                Jika Anda merasa ini adalah kekeliruan, silakan hubungi bagian Tata Usaha SMK Negeri 1 Air Naningan di <strong>{{ $sekolah->email ?: 'smkn1airnaningan@gmail.sch.id' }}</strong>.
              </p>
            </div>
          </div>
        @endif

      </div>
    </div>
  </main>

  {{-- Footer --}}
  <footer class="bg-white border-top py-3 text-center text-muted" style="font-size:12px;">
    &copy; {{ date('Y') }} {{ $sekolah->nama_sekolah ?: 'SMK Negeri 1 Air Naningan' }} &bull; Sistem Informasi Tata Usaha (SITUAN)
  </footer>

</body>
</html>
