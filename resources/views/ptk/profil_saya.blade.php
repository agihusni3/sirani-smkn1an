<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Biodata &amp; Berkas Digital Saya — {{ $guru->nama }}</title>
  @include('partials.styles')
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    /* ─── PTK Modern Portal Layout Tokens ─── */
    .ptk-page {
      max-width: 1360px;
      margin: 0 auto;
      padding-bottom: 40px;
    }
    
    /* ─── Hero Card ─── */
    .ptk-hero-card {
      background: var(--bg-2, #FFFFFF);
      border: 1px solid var(--border, rgba(0,0,0,0.08));
      border-radius: 20px;
      box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
      position: relative;
      overflow: hidden;
      margin-bottom: 24px;
    }
    .ptk-hero-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: linear-gradient(90deg, #2563EB 0%, #3B82F6 50%, #10B981 100%);
    }

    /* ─── Tabs Segmented Bar ─── */
    .ptk-tabs-nav {
      display: inline-flex;
      background: var(--bg-3, #F1F5F9);
      padding: 5px;
      border-radius: 14px;
      gap: 4px;
      border: 1px solid var(--border, rgba(0,0,0,0.06));
    }
    .ptk-tab-btn {
      border: none;
      background: transparent;
      padding: 9px 18px;
      border-radius: 10px;
      font-size: 13.5px;
      font-weight: 700;
      color: var(--text-2, #475569);
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      cursor: pointer;
      text-decoration: none;
    }
    .ptk-tab-btn:hover:not(.active) {
      color: var(--text, #0F172A);
      background: rgba(255, 255, 255, 0.5);
    }
    .ptk-tab-btn.active {
      background: var(--bg-2, #FFFFFF) !important;
      color: var(--navy, #2563EB) !important;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    /* ─── Info Section Cards ─── */
    .ptk-info-card {
      background: var(--bg-2, #FFFFFF);
      border: 1px solid var(--border, rgba(0,0,0,0.08));
      border-radius: 18px;
      box-shadow: 0 2px 12px -2px rgba(15, 23, 42, 0.04);
      height: 100%;
      display: flex;
      flex-direction: column;
    }
    .ptk-info-card-header {
      padding: 18px 22px;
      border-bottom: 1px solid var(--border, rgba(0,0,0,0.06));
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
    }
    .ptk-info-card-body {
      padding: 22px;
      flex: 1;
    }

    /* ─── Modern Key-Value Field Grid ─── */
    .ptk-field-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 16px;
    }
    .ptk-field-item {
      background: var(--surface, #F8FAFC);
      border: 1px solid var(--border, rgba(0,0,0,0.05));
      border-radius: 12px;
      padding: 12px 14px;
      transition: all 0.15s ease;
    }
    .ptk-field-item:hover {
      border-color: rgba(37,99,235,0.25);
      background: #FAFCFF;
    }
    .ptk-field-label {
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      color: var(--text-3, #64748B);
      margin-bottom: 4px;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .ptk-field-value {
      font-size: 13.5px;
      font-weight: 600;
      color: var(--text, #0F172A);
      word-break: break-word;
    }
    .ptk-field-empty {
      color: #94A3B8;
      font-weight: 400;
      font-style: italic;
      font-size: 12.5px;
    }

    /* ─── Document Card ─── */
    .ptk-doc-card {
      background: var(--bg-2, #FFFFFF);
      border: 1px solid var(--border, rgba(0,0,0,0.08));
      border-radius: 14px;
      padding: 16px;
      transition: all 0.2s ease;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      height: 100%;
    }
    .ptk-doc-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px -4px rgba(0,0,0,0.08);
      border-color: #3B82F6;
    }

    /* ─── Metric Card ─── */
    .ptk-stat-tile {
      background: var(--bg-2, #FFFFFF);
      border: 1px solid var(--border, rgba(0,0,0,0.08));
      border-radius: 16px;
      padding: 18px;
      box-shadow: 0 2px 10px -2px rgba(0,0,0,0.04);
      display: flex;
      align-items: center;
      gap: 16px;
    }
    .ptk-stat-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      flex-shrink: 0;
    }
  </style>
</head>
<body>

<div class="app-container">
  @include('partials.sidebar')

  <main class="main-content">
    
    {{-- Header Minimalis SIRANI --}}
    <header class="header no-print">
      <div class="header-title">
        <h1>Biodata &amp; Berkas PTK Saya</h1>
        <p>Ruang Kerja Mandiri &amp; Lemari Berkas Digital Pendidik</p>
      </div>
      @include('partials.header_actions')
    </header>

    <div class="ptk-page">

      {{-- Alert Flash Notifikasi --}}
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-2" role="alert" style="background:#ECFDF5; color:#065F46; border-left:4px solid #10B981!important;">
          <i class="bi bi-check-circle-fill fs-5 text-success"></i>
          <div class="fw-semibold">{{ session('success') }}</div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-2" role="alert" style="background:#FEF2F2; color:#991B1B; border-left:4px solid #EF4444!important;">
          <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
          <div>{{ session('error') }}</div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      {{-- 1. HERO PROFILE CARD (ELEGAN & BERSIH) --}}
      <div class="ptk-hero-card">
        <div class="p-4 p-md-4">
          <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-4">
            
            {{-- Bagian Kiri: Avatar & Identitas Guru --}}
            <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start gap-4 text-center text-sm-start">
              
              {{-- Avatar Foto --}}
              <div class="position-relative">
                <img src="{{ $guru->foto_url }}" alt="{{ $guru->nama }}" class="rounded-circle shadow-sm" style="width:84px; height:84px; object-fit:cover; border:3px solid #FFFFFF; box-shadow:0 4px 12px rgba(0,0,0,0.1)!important;" />
                <span class="position-absolute bottom-0 end-0 p-1.5 rounded-circle border border-2 border-white" style="background: {{ $guru->status === 'aktif' ? '#10B981' : '#94A3B8' }}; width:16px; height:16px;" title="Status: {{ ucfirst($guru->status) }}"></span>
              </div>

              {{-- Detail Nama & Metadata --}}
              <div>
                <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-start gap-2 mb-1.5">
                  <span class="badge rounded-pill px-2.5 py-1" style="background:#EFF6FF; color:#1D4ED8; border:1px solid #BFDBFE; font-size:11.5px; font-weight:700;">
                    <i class="bi bi-person-badge-fill me-1"></i> {{ $guru->label_kepegawaian ?? ($guru->status_kepegawaian ?: 'PTK') }}
                  </span>
                  @if($guru->golongan_ruang)
                    <span class="badge rounded-pill px-2.5 py-1" style="background:#FFFBEB; color:#B45309; border:1px solid #FDE68A; font-size:11.5px; font-weight:700;">
                      Gol. {{ $guru->golongan_ruang }}
                    </span>
                  @endif
                  <span class="badge rounded-pill px-2.5 py-1" style="background:#ECFDF5; color:#047857; border:1px solid #A7F3D0; font-size:11.5px; font-weight:700;">
                    <i class="bi bi-folder-check me-1"></i> {{ $arsips->count() }} Dokumen Terverifikasi
                  </span>
                </div>

                <h2 class="h4 fw-bold mb-1.5" style="color:var(--text, #0F172A); letter-spacing:-0.02em;">
                  {{ $guru->nama_lengkap_gelar ?: $guru->nama }}
                </h2>

                <div class="d-flex flex-wrap justify-content-center justify-content-sm-start gap-3 small" style="color:var(--text-3, #64748B);">
                  <span><i class="bi bi-hash text-muted"></i> NIP: <strong style="color:var(--text, #0F172A);">{{ $guru->nip ?: '—' }}</strong></span>
                  @if($guru->nuptk)
                    <span><i class="bi bi-award text-muted"></i> NUPTK: <strong style="color:var(--text, #0F172A);">{{ $guru->nuptk }}</strong></span>
                  @endif
                  <span><i class="bi bi-briefcase text-muted"></i> Jabatan: <strong style="color:var(--text, #0F172A);">{{ $guru->jabatan ?: 'Guru' }}</strong></span>
                </div>
              </div>

            </div>

            {{-- Bagian Kanan: Tombol Aksi & Simulator Admin --}}
            <div class="d-flex flex-column align-items-stretch align-items-lg-end gap-2.5 w-100 w-lg-auto">
              
              @if(auth()->user()->isAdmin() && $semuaGuru->count() > 0)
                <div class="d-flex align-items-center gap-2 px-3 py-1.5 rounded-3 border" style="background:var(--surface, #F8FAFC); font-size:12px;">
                  <span class="text-muted"><i class="bi bi-eye"></i> Pratinjau PTK:</span>
                  <select class="form-select form-select-sm border-0 bg-transparent fw-semibold" onchange="location.href='?guru_id=' + this.value" style="font-size:12px; max-width:210px; cursor:pointer;">
                    @foreach($semuaGuru as $g)
                      <option value="{{ $g->id }}" {{ $g->id == $guru->id ? 'selected' : '' }}>{{ $g->nama }}</option>
                    @endforeach
                  </select>
                </div>
              @endif

              <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-lg-end">
                <a href="{{ route('kartu.digital.guru', $guru->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary px-3 py-2 fw-bold d-inline-flex align-items-center gap-1.5" style="border-radius:10px;">
                  <i class="bi bi-qr-code"></i> Kartu Digital Barcode
                </a>
                <button type="button" class="btn btn-sm btn-primary px-3 py-2 fw-bold d-inline-flex align-items-center gap-1.5 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalUnggahBerkas" style="border-radius:10px; background:#2563EB; border-color:#2563EB;">
                  <i class="bi bi-cloud-arrow-up-fill"></i> Unggah Berkas Baru
                </button>
              </div>

            </div>

          </div>
        </div>
      </div>

      {{-- 2. TABS NAVIGASI SEGMENTED BAR --}}
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div class="ptk-tabs-nav" id="ptkTab" role="tablist">
          <button class="ptk-tab-btn active" id="tab-biodata-btn" data-bs-toggle="pill" data-bs-target="#tab-biodata" type="button" role="tab">
            <i class="bi bi-person-vcard text-primary"></i> Biodata Kepegawaian
          </button>
          <button class="ptk-tab-btn" id="tab-berkas-btn" data-bs-toggle="pill" data-bs-target="#tab-berkas" type="button" role="tab">
            <i class="bi bi-folder2-open text-warning"></i> Lemari Berkas Digital
            <span class="badge rounded-pill bg-primary" style="font-size:10px; padding:3px 7px;">{{ $arsips->count() }}</span>
          </button>
          <button class="ptk-tab-btn" id="tab-presensi-btn" data-bs-toggle="pill" data-bs-target="#tab-presensi" type="button" role="tab">
            <i class="bi bi-calendar-check text-success"></i> Catatan Presensi Saya
          </button>
        </div>

        <div class="text-muted small d-none d-md-flex align-items-center gap-1.5">
          <i class="bi bi-shield-check text-success"></i>
          <span>Akses Pribadi Terproteksi &bull; Hanya dapat dilihat oleh Anda &amp; Tata Usaha</span>
        </div>
      </div>

      {{-- 3. ISI KONTEN TAB --}}
      <div class="tab-content" id="ptkTabContent">

        {{-- TAB 1: BIODATA KEPEGAWAIAN LENGKAP --}}
        <div class="tab-pane fade show active" id="tab-biodata" role="tabpanel" tabindex="0">
          <div class="row g-4 mb-4">
            
            {{-- Panel 1: Identitas Diri & Kontak --}}
            <div class="col-lg-6">
              <div class="ptk-info-card">
                <div class="ptk-info-card-header">
                  <div class="d-flex align-items-center gap-2">
                    <div class="p-2 rounded-3 bg-primary-subtle text-primary">
                      <i class="bi bi-person-lines-fill"></i>
                    </div>
                    <div>
                      <h3 class="h6 fw-bold mb-0" style="color:var(--text, #0F172A);">Identitas Diri &amp; Kependudukan</h3>
                      <div class="text-muted" style="font-size:11.5px;">Data resmi kependudukan sesuai KTP / Dapodik</div>
                    </div>
                  </div>
                </div>

                <div class="ptk-info-card-body">
                  <div class="ptk-field-grid">
                    
                    <div class="ptk-field-item">
                      <div class="ptk-field-label"><i class="bi bi-person"></i> Nama Lengkap</div>
                      <div class="ptk-field-value">{{ $guru->nama_lengkap ?: $guru->nama }}</div>
                    </div>

                    <div class="ptk-field-item">
                      <div class="ptk-field-label"><i class="bi bi-mortarboard"></i> Gelar Akademik</div>
                      <div class="ptk-field-value">
                        @if($guru->gelar_depan || $guru->gelar_belakang)
                          {{ ($guru->gelar_depan ? $guru->gelar_depan . ' ' : '') . ($guru->gelar_belakang ?: '') }}
                        @else
                          <span class="ptk-field-empty">Belum diisi</span>
                        @endif
                      </div>
                    </div>

                    <div class="ptk-field-item">
                      <div class="ptk-field-label"><i class="bi bi-card-text"></i> NIK (Nomor KTP)</div>
                      <div class="ptk-field-value font-monospace">
                        {{ $guru->nik ?: '—' }}
                      </div>
                    </div>

                    <div class="ptk-field-item">
                      <div class="ptk-field-label"><i class="bi bi-calendar-event"></i> Tempat, Tanggal Lahir</div>
                      <div class="ptk-field-value">
                        @if($guru->tempat_lahir || $guru->tanggal_lahir)
                          {{ $guru->tempat_lahir ?: '—' }}, {{ $guru->tanggal_lahir ? \Carbon\Carbon::parse($guru->tanggal_lahir)->translatedFormat('d F Y') : '—' }}
                        @else
                          <span class="ptk-field-empty">Belum diisi</span>
                        @endif
                      </div>
                    </div>

                    <div class="ptk-field-item">
                      <div class="ptk-field-label"><i class="bi bi-gender-ambiguous"></i> Jenis Kelamin</div>
                      <div class="ptk-field-value">
                        @if($guru->jenis_kelamin === 'L')
                          Laki-laki
                        @elseif($guru->jenis_kelamin === 'P')
                          Perempuan
                        @else
                          <span class="ptk-field-empty">Belum diisi</span>
                        @endif
                      </div>
                    </div>

                    <div class="ptk-field-item">
                      <div class="ptk-field-label"><i class="bi bi-heart"></i> Agama</div>
                      <div class="ptk-field-value">{{ $guru->agama ?: 'Islam' }}</div>
                    </div>

                    <div class="ptk-field-item" style="grid-column: 1 / -1;">
                      <div class="ptk-field-label"><i class="bi bi-whatsapp"></i> Kontak WhatsApp / HP</div>
                      <div class="ptk-field-value d-flex align-items-center gap-2">
                        @if($guru->no_hp)
                          <strong style="color:#059669;">{{ $guru->no_hp }}</strong>
                          <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', $guru->no_hp)) }}" target="_blank" class="badge rounded-pill bg-success-subtle text-success border border-success-subtle text-decoration-none px-2.5 py-1" style="font-size:11px;">
                            <i class="bi bi-chat-dots-fill me-1"></i> Buka WhatsApp
                          </a>
                        @else
                          <span class="ptk-field-empty">Nomor HP belum tercantum</span>
                        @endif
                      </div>
                    </div>

                    <div class="ptk-field-item" style="grid-column: 1 / -1;">
                      <div class="ptk-field-label"><i class="bi bi-geo-alt"></i> Alamat Domisili</div>
                      <div class="ptk-field-value">
                        {{ $guru->alamat ?: 'Belum mengisi alamat tempat tinggal.' }}
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>

            {{-- Panel 2: Kepegawaian & Riwayat Pangkat --}}
            <div class="col-lg-6">
              <div class="ptk-info-card">
                <div class="ptk-info-card-header">
                  <div class="d-flex align-items-center gap-2">
                    <div class="p-2 rounded-3 bg-warning-subtle text-warning-emphasis">
                      <i class="bi bi-briefcase-fill"></i>
                    </div>
                    <div>
                      <h3 class="h6 fw-bold mb-0" style="color:var(--text, #0F172A);">Status &amp; Riwayat Kepegawaian</h3>
                      <div class="text-muted" style="font-size:11.5px;">Pangkat, golongan, dan histori kenaikan berkala</div>
                    </div>
                  </div>
                </div>

                <div class="ptk-info-card-body">
                  <div class="ptk-field-grid">

                    <div class="ptk-field-item">
                      <div class="ptk-field-label"><i class="bi bi-award"></i> Status Pegawai</div>
                      <div class="ptk-field-value">
                        <span class="badge px-2.5 py-1 rounded-pill" style="background:#EFF6FF; color:#1D4ED8; font-weight:700;">
                          {{ $guru->label_kepegawaian ?? ($guru->status_kepegawaian ?: 'Pegawai') }}
                        </span>
                      </div>
                    </div>

                    <div class="ptk-field-item">
                      <div class="ptk-field-label"><i class="bi bi-stars"></i> Pangkat / Gol. Ruang</div>
                      <div class="ptk-field-value">
                        @if($guru->pangkat || $guru->golongan_ruang)
                          <strong>{{ $guru->pangkat ?: '—' }}</strong> ({{ $guru->golongan_ruang ?: '—' }})
                        @else
                          <span class="ptk-field-empty">Belum ditetapkan</span>
                        @endif
                      </div>
                    </div>

                    <div class="ptk-field-item">
                      <div class="ptk-field-label"><i class="bi bi-person-workspace"></i> Tugas / Jabatan</div>
                      <div class="ptk-field-value">{{ $guru->jabatan ?: 'Guru Mata Pelajaran' }}</div>
                    </div>

                    <div class="ptk-field-item">
                      <div class="ptk-field-label"><i class="bi bi-diagram-3"></i> Tugas Tambahan</div>
                      <div class="ptk-field-value">
                        @if($guru->tugas_tambahan)
                          <span class="badge bg-light text-dark border px-2 py-1">{{ $guru->tugas_tambahan }}</span>
                        @else
                          <span class="ptk-field-empty">Tidak ada</span>
                        @endif
                      </div>
                    </div>

                    <div class="ptk-field-item">
                      <div class="ptk-field-label"><i class="bi bi-clock-history"></i> TMT CPNS / Mulai Tugas</div>
                      <div class="ptk-field-value">
                        {{ $guru->tmt_cpns ? \Carbon\Carbon::parse($guru->tmt_cpns)->translatedFormat('d F Y') : '—' }}
                      </div>
                    </div>

                    <div class="ptk-field-item">
                      <div class="ptk-field-label"><i class="bi bi-graph-up-arrow"></i> TMT Pangkat Terakhir</div>
                      <div class="ptk-field-value">
                        {{ $guru->tmt_pangkat ? \Carbon\Carbon::parse($guru->tmt_pangkat)->translatedFormat('d F Y') : '—' }}
                      </div>
                    </div>

                    <div class="ptk-field-item">
                      <div class="ptk-field-label"><i class="bi bi-cash-stack"></i> TMT Berkala (KGB) Terakhir</div>
                      <div class="ptk-field-value">
                        {{ $guru->tmt_kgb_terakhir ? \Carbon\Carbon::parse($guru->tmt_kgb_terakhir)->translatedFormat('d F Y') : '—' }}
                      </div>
                    </div>

                    <div class="ptk-field-item">
                      <div class="ptk-field-label"><i class="bi bi-alarm"></i> Jadwal KGB Berikutnya</div>
                      <div class="ptk-field-value">
                        @if($guru->tmt_kgb_berikutnya)
                          <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill">
                            {{ \Carbon\Carbon::parse($guru->tmt_kgb_berikutnya)->translatedFormat('d F Y') }}
                          </span>
                        @else
                          <span class="ptk-field-empty">Belum dijadwalkan</span>
                        @endif
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>

          </div>

          {{-- Catatan Panduan Pembaruan Data --}}
          <div class="p-3 rounded-4 d-flex align-items-center gap-3 border" style="background:#F8FAFC; border-color:#E2E8F0!important;">
            <div class="p-2 rounded-circle bg-primary-subtle text-primary fs-5">
              <i class="bi bi-info-circle"></i>
            </div>
            <div class="small text-muted">
              <strong>Catatan Pembaruan Biodata:</strong> Jika terdapat ketidaksesuaian data identitas, NIP/NUPTK, atau status kenaikan pangkat di atas, silakan hubungi bagian Kepegawaian Tata Usaha (SITUAN) untuk proses validasi dan sinkronisasi berkas kedinasan.
            </div>
          </div>
        </div>

        {{-- TAB 2: LEMARI BERKAS DIGITAL SAYA --}}
        <div class="tab-pane fade" id="tab-berkas" role="tabpanel" tabindex="0">
          <div class="ptk-info-card">
            
            <div class="ptk-info-card-header">
              <div>
                <h3 class="h6 fw-bold mb-0" style="color:var(--text, #0F172A);">
                  <i class="bi bi-folder-symlink-fill text-primary me-1.5"></i> Lemari Berkas Digital Saya (E-Arsip Pribadi)
                </h3>
                <div class="text-muted" style="font-size:11.5px;">Dokumen digital yang tersimpan aman di server sekolah dan dapat dibuka kapan pun Anda butuhkan.</div>
              </div>

              <button type="button" class="btn btn-sm btn-primary px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalUnggahBerkas" style="border-radius:8px;">
                <i class="bi bi-cloud-arrow-up-fill"></i> Unggah Dokumen Baru
              </button>
            </div>

            <div class="ptk-info-card-body">
              
              @if($arsips->isEmpty())
                {{-- Empty State Lemari Berkas --}}
                <div class="text-center py-5 px-3">
                  <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow-sm" style="width:72px; height:72px; background:#EFF6FF; color:#2563EB;">
                    <i class="bi bi-folder-plus fs-1"></i>
                  </div>
                  <h5 class="fw-bold mb-1" style="color:var(--text, #0F172A);">Belum Ada Berkas Digital Tersimpan</h5>
                  <p class="text-muted small mb-4" style="max-width:440px; margin:0 auto; line-height:1.6;">
                    Simpan scan berkas penting Anda di sini (seperti KTP, SK CPNS/PNS/PPPK, SK Pangkat Terakhir, SK KGB, Ijazah, atau Sertifikat Pendidik) agar tidak repot saat sewaktu-waktu dibutuhkan dinas.
                  </p>
                  <button type="button" class="btn btn-primary px-4 py-2 fw-bold rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalUnggahBerkas">
                    <i class="bi bi-cloud-arrow-up me-1.5"></i> Unggah Berkas Pertama Saya
                  </button>
                </div>
              @else
                
                {{-- Grid Tampilan Kartu Berkas --}}
                <div class="row g-3">
                  @foreach($arsips as $dok)
                    @php
                      $badgeCat = match($dok->kategori_berkas) {
                        'sk_cpns', 'sk_pns', 'sk_pppk' => ['bg' => '#EFF6FF', 'color' => '#1D4ED8', 'border' => '#BFDBFE', 'label' => 'SK Pengangkatan', 'icon' => 'bi-file-earmark-person'],
                        'sk_pangkat_terakhir'          => ['bg' => '#ECFDF5', 'color' => '#047857', 'border' => '#A7F3D0', 'label' => 'SK Pangkat', 'icon' => 'bi-award'],
                        'sk_kgb_terakhir'              => ['bg' => '#FFFBEB', 'color' => '#B45309', 'border' => '#FDE68A', 'label' => 'SK KGB', 'icon' => 'bi-cash-coin'],
                        'ktp', 'kk'                    => ['bg' => '#F1F5F9', 'color' => '#475569', 'border' => '#CBD5E1', 'label' => 'Kependudukan', 'icon' => 'bi-person-badge'],
                        'ijazah', 'transkrip'          => ['bg' => '#FAF5FF', 'color' => '#7E22CE', 'border' => '#E9D5FF', 'label' => 'Pendidikan', 'icon' => 'bi-mortarboard'],
                        'sertifikat_pendidik'          => ['bg' => '#FDF2F8', 'color' => '#BE185D', 'border' => '#FBCFE8', 'label' => 'Serdik', 'icon' => 'bi-patch-check'],
                        'kartu_pegawai'                => ['bg' => '#F0FDFA', 'color' => '#0F766E', 'border' => '#99F6E4', 'label' => 'Karpeg', 'icon' => 'bi-credit-card-2-front'],
                        default                        => ['bg' => '#F8FAFC', 'color' => '#475569', 'border' => '#E2E8F0', 'label' => 'Kedinasan', 'icon' => 'bi-file-earmark-text'],
                      };
                      $fileUrl = asset('storage/' . $dok->file_path);
                      $isPdf = str_ends_with(strtolower($dok->file_path), '.pdf');
                    @endphp
                    
                    <div class="col-md-6 col-xl-4">
                      <div class="ptk-doc-card">
                        
                        <div>
                          <div class="d-flex align-items-center justify-content-between gap-2 mb-2.5">
                            <span class="badge rounded-pill px-2.5 py-1" style="background:{{ $badgeCat['bg'] }}; color:{{ $badgeCat['color'] }}; border:1px solid {{ $badgeCat['border'] }}; font-size:11px; font-weight:700;">
                              <i class="bi {{ $badgeCat['icon'] }} me-1"></i> {{ $badgeCat['label'] }}
                            </span>
                            <span class="badge {{ $isPdf ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary' }} px-2 py-0.5 rounded" style="font-size:10px; font-weight:700;">
                              {{ $isPdf ? 'PDF' : 'GAMBAR' }}
                            </span>
                          </div>

                          <h6 class="fw-bold mb-1" style="color:var(--text, #0F172A); line-height:1.4;">
                            {{ $dok->nama_dokumen }}
                          </h6>

                          <div class="text-muted small font-monospace mb-2" style="font-size:11.5px;">
                            {{ $dok->nomor_dokumen ?: 'Tanpa nomor surat' }}
                          </div>

                          <div class="d-flex align-items-center gap-2 text-muted" style="font-size:11px;">
                            <i class="bi bi-clock"></i>
                            <span>Diunggah: {{ $dok->created_at ? $dok->created_at->translatedFormat('d M Y') : '—' }}</span>
                          </div>
                        </div>

                        <div class="pt-3 mt-3 border-top d-flex align-items-center justify-content-between gap-2">
                          <button type="button" class="btn btn-sm btn-outline-primary px-3 py-1.5 fw-bold rounded-2 d-inline-flex align-items-center gap-1" onclick="bukaPreviewDokumen('{{ $fileUrl }}', '{{ addslashes($dok->nama_dokumen) }}', {{ $isPdf ? 'true' : 'false' }})">
                            <i class="bi bi-eye"></i> Buka
                          </button>

                          <div class="d-flex align-items-center gap-1">
                            <a href="{{ $fileUrl }}" download="{{ $dok->nama_dokumen }}" class="btn btn-sm btn-light border px-2.5 py-1.5 rounded-2 text-dark" title="Unduh File">
                              <i class="bi bi-download"></i>
                            </a>

                            <form action="{{ route('ptk.hapus-berkas', $dok->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus berkas \'{{ addslashes($dok->nama_dokumen) }}\' dari lemari digital Anda?')">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-sm btn-light border border-danger-subtle px-2.5 py-1.5 rounded-2 text-danger" title="Hapus Dokumen">
                                <i class="bi bi-trash"></i>
                              </button>
                            </form>
                          </div>
                        </div>

                      </div>
                    </div>
                  @endforeach
                </div>

              @endif

            </div>
          </div>
        </div>

        {{-- TAB 3: CATATAN PRESENSI SAYA --}}
        <div class="tab-pane fade" id="tab-presensi" role="tabpanel" tabindex="0">
          
          {{-- Stat Tiles --}}
          <div class="row g-3 mb-4">
            
            <div class="col-sm-6 col-lg-3">
              <div class="ptk-stat-tile">
                <div class="ptk-stat-icon" style="background:#ECFDF5; color:#059669;">
                  <i class="bi bi-check2-circle"></i>
                </div>
                <div>
                  <div class="h3 fw-bold mb-0" style="color:#059669;">{{ $stats['hadir'] }}</div>
                  <div class="text-muted small fw-semibold">Hadir Tepat Waktu</div>
                </div>
              </div>
            </div>

            <div class="col-sm-6 col-lg-3">
              <div class="ptk-stat-tile">
                <div class="ptk-stat-icon" style="background:#FFFBEB; color:#D97706;">
                  <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                  <div class="h3 fw-bold mb-0" style="color:#D97706;">{{ $stats['terlambat'] }}</div>
                  <div class="text-muted small fw-semibold">Terlambat</div>
                </div>
              </div>
            </div>

            <div class="col-sm-6 col-lg-3">
              <div class="ptk-stat-tile">
                <div class="ptk-stat-icon" style="background:#EFF6FF; color:#2563EB;">
                  <i class="bi bi-file-earmark-medical"></i>
                </div>
                <div>
                  <div class="h3 fw-bold mb-0" style="color:#2563EB;">{{ $stats['izin'] }}</div>
                  <div class="text-muted small fw-semibold">Izin / Sakit / Dinas</div>
                </div>
              </div>
            </div>

            <div class="col-sm-6 col-lg-3">
              <div class="ptk-stat-tile">
                <div class="ptk-stat-icon" style="background:#F8FAFC; color:#2563EB; border:1px solid #E2E8F0;">
                  <i class="bi bi-shield-check"></i>
                </div>
                <div>
                  <div class="h3 fw-bold mb-0" style="color:#2563EB;">{{ $stats['persen'] }}%</div>
                  <div class="text-muted small fw-semibold">Skor Disiplin ({{ $stats['nama_bulan'] }})</div>
                </div>
              </div>
            </div>

          </div>

          {{-- Tabel Presensi Harian --}}
          <div class="ptk-info-card overflow-hidden">
            <div class="ptk-info-card-header">
              <div>
                <h3 class="h6 fw-bold mb-0" style="color:var(--text, #0F172A);">
                  <i class="bi bi-clock-history text-primary me-1.5"></i> Riwayat Kehadiran Bulan {{ $stats['nama_bulan'] }}
                </h3>
                <div class="text-muted" style="font-size:11.5px;">Rekaman otomatis sensor Smart Gate Presensi gerbang sekolah</div>
              </div>
            </div>

            <div class="card-body p-0">
              @if($absensisBulanIni->isEmpty())
                <div class="text-center py-5 text-muted small">
                  Belum ada rekaman presensi pada bulan ini.
                </div>
              @else
                <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                    <thead class="table-light" style="border-bottom:1.5px solid var(--border, #E2E8F0);">
                      <tr>
                        <th class="py-3 px-4" style="width:60px;">No</th>
                        <th class="py-3 px-3">Hari &amp; Tanggal</th>
                        <th class="py-3 px-3 text-center" style="width:130px;">Jam Masuk</th>
                        <th class="py-3 px-3 text-center" style="width:130px;">Jam Pulang</th>
                        <th class="py-3 px-3 text-center" style="width:140px;">Status</th>
                        <th class="py-3 px-4">Keterangan Sumber</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($absensisBulanIni as $i => $absen)
                        <tr>
                          <td class="py-3 px-4 text-muted text-center">{{ $i + 1 }}</td>
                          <td class="py-3 px-3 fw-bold" style="color:var(--text, #0F172A);">
                            {{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('l, d F Y') }}
                          </td>
                          <td class="py-3 px-3 text-center font-monospace">
                            {{ $absen->jam_masuk ? substr($absen->jam_masuk, 0, 5) . ' WIB' : '—' }}
                          </td>
                          <td class="py-3 px-3 text-center font-monospace">
                            {{ $absen->jam_pulang ? substr($absen->jam_pulang, 0, 5) . ' WIB' : '—' }}
                          </td>
                          <td class="py-3 px-3 text-center">
                            @if($absen->status === 'hadir')
                              <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill">Hadir</span>
                            @elseif($absen->status === 'terlambat')
                              <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1 rounded-pill">Terlambat</span>
                            @elseif($absen->status === 'alpha')
                              <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill">Alpha</span>
                            @else
                              <span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1 rounded-pill">{{ ucfirst($absen->status) }}</span>
                            @endif
                          </td>
                          <td class="py-3 px-4 text-muted small">
                            {{ $absen->keterangan ?: 'Terekam Smart Gate Presensi' }}
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @endif
            </div>
          </div>

        </div>

      </div>

    </div>

  </main>
</div>

{{-- MODAL UNGGAH BERKAS MANDIRI --}}
<div class="modal fade" id="modalUnggahBerkas" tabindex="-1" aria-labelledby="modalUnggahBerkasLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      
      <div class="modal-header border-0 pb-0 pt-4 px-4">
        <div>
          <h5 class="modal-title fw-bold mb-1" id="modalUnggahBerkasLabel" style="color:var(--text, #0F172A);">
            <i class="bi bi-cloud-arrow-up-fill text-primary me-1.5"></i> Unggah Berkas Digital Saya
          </h5>
          <div class="text-muted small">Berkas akan tersimpan aman ke lemari digital E-Arsip Anda.</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="{{ route('ptk.unggah-berkas') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(auth()->user()->isAdmin())
          <input type="hidden" name="guru_id" value="{{ $guru->id }}" />
        @endif

        <div class="modal-body px-4 py-3">
          
          <div class="mb-3">
            <label class="form-label fw-bold small">Kategori Berkas <span class="text-danger">*</span></label>
            <select name="kategori_berkas" class="form-select rounded-3" style="font-size:13px;" required>
              <option value="">-- Pilih Jenis Dokumen --</option>
              <option value="sk_cpns">SK CPNS</option>
              <option value="sk_pns">SK PNS / PPPK Awal</option>
              <option value="sk_pangkat_terakhir">SK Kenaikan Pangkat Terakhir</option>
              <option value="sk_kgb_terakhir">SK Kenaikan Gaji Berkala (KGB) Terakhir</option>
              <option value="ijazah">Ijazah Pendidikan Terakhir</option>
              <option value="transkrip">Transkrip Nilai Akademik</option>
              <option value="sertifikat_pendidik">Sertifikat Pendidik (Serdik)</option>
              <option value="kartu_pegawai">Kartu Pegawai (Karpeg / e-KTP)</option>
              <option value="ktp">KTP Pribadi</option>
              <option value="kk">Kartu Keluarga (KK)</option>
              <option value="lainnya">Dokumen Kedinasan Lainnya</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold small">Nama / Judul Dokumen <span class="text-danger">*</span></label>
            <input type="text" name="nama_dokumen" class="form-control rounded-3" style="font-size:13px;" placeholder="Contoh: SK KGB Periode 2026 atau Ijazah S1" required />
          </div>

          <div class="row g-2 mb-3">
            <div class="col-sm-7">
              <label class="form-label fw-bold small">Nomor Surat / SK <span class="text-muted fw-normal">(Opsional)</span></label>
              <input type="text" name="nomor_dokumen" class="form-control rounded-3" style="font-size:13px;" placeholder="Nomor resmi dokumen" />
            </div>
            <div class="col-sm-5">
              <label class="form-label fw-bold small">Tanggal Dokumen <span class="text-muted fw-normal">(Opsional)</span></label>
              <input type="date" name="tanggal_dokumen" class="form-control rounded-3" style="font-size:13px;" />
            </div>
          </div>

          <div class="mb-2">
            <label class="form-label fw-bold small">Pilih Berkas (File PDF atau Foto) <span class="text-danger">*</span></label>
            <input type="file" name="file_dokumen" class="form-control rounded-3" style="font-size:13px;" accept=".pdf,.jpg,.jpeg,.png" required />
            <div class="form-text small text-muted">Format yang didukung: PDF, JPG, PNG (Maksimal ukuran 10 MB).</div>
          </div>

        </div>

        <div class="modal-footer border-0 px-4 pb-4 pt-1">
          <button type="button" class="btn btn-light px-3 rounded-pill fw-semibold" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold shadow-sm">
            <i class="bi bi-cloud-arrow-up me-1"></i> Simpan ke Lemari Berkas
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- MODAL PREVIEW DOKUMEN (IN-APP PDF & IMAGE VIEWER) --}}
<div class="modal fade" id="modalPreviewDokumen" tabindex="-1" aria-labelledby="modalPreviewDokumenLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="height:88vh;">
      <div class="modal-header py-3 px-4 bg-dark text-white border-0">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
          <h6 class="modal-title fw-bold mb-0 text-white" id="modalPreviewTitle">Pratinjau Dokumen</h6>
        </div>
        <div class="d-flex align-items-center gap-2">
          <a href="#" id="modalPreviewDownloadLink" download="" class="btn btn-sm btn-outline-light rounded-pill px-3 fw-semibold" style="font-size:12px;">
            <i class="bi bi-download me-1"></i> Unduh Berkas
          </a>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
      <div class="modal-body p-0 bg-secondary-subtle d-flex align-items-center justify-content-center" style="height:100%;">
        <div id="modalPreviewContainer" style="width:100%; height:100%;" class="d-flex align-items-center justify-content-center">
          {{-- Dynamic PDF Iframe atau Image tag --}}
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function bukaPreviewDokumen(url, nama, isPdf) {
    document.getElementById('modalPreviewTitle').innerText = nama;
    const downloadLink = document.getElementById('modalPreviewDownloadLink');
    downloadLink.href = url;
    downloadLink.setAttribute('download', nama);

    const container = document.getElementById('modalPreviewContainer');
    container.innerHTML = '';

    if (isPdf) {
      const iframe = document.createElement('iframe');
      iframe.src = url;
      iframe.style.width = '100%';
      iframe.style.height = '100%';
      iframe.style.border = 'none';
      container.appendChild(iframe);
    } else {
      const img = document.createElement('img');
      img.src = url;
      img.alt = nama;
      img.style.maxWidth = '100%';
      img.style.maxHeight = '100%';
      img.style.objectFit = 'contain';
      img.className = 'p-3 shadow-sm rounded';
      container.appendChild(img);
    }

    const modal = new bootstrap.Modal(document.getElementById('modalPreviewDokumen'));
    modal.show();
  }
</script>
</body>
</html>
